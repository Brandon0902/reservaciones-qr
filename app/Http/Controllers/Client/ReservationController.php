<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\StoreReservationRequest;
use App\Models\ExtraService;
use App\Models\Reservation;
use App\Models\Payment;
use App\Enums\ReservationStatus;
use App\Enums\ReservationSource;
use App\Enums\PaymentStatus;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    private const DAY_BASE        = 5000; // MXN
    private const NIGHT_BASE      = 6000; // MXN
    /** Anticipación estricta: primer día válido es hoy + 8 */
    private const MIN_DAYS_AHEAD  = 8;

    /** Estados que bloquean el día completo del salón */
    private function blockingStatuses(): array
    {
        return [
            ReservationStatus::PENDING,
            ReservationStatus::CONFIRMED,
            ReservationStatus::CHECKED_IN,
            ReservationStatus::COMPLETED,
        ];
    }

    /** Formulario */
    public function create(): View
    {
        $extras = ExtraService::query()
            ->select('id','name','day_price','night_price')
            ->orderBy('name')
            ->get();

        return view('client.reservations.create', [
            'extras'        => $extras,
            'dayBase'       => self::DAY_BASE,
            'nightBase'     => self::NIGHT_BASE,
            'sourceOptions' => [
                'in_person' => 'En persona',
                'phone'     => 'Teléfono',
                'whatsapp'  => 'WhatsApp',
                'web'       => 'Web',
                'other'     => 'Otro',
            ],
        ]);
    }

    /** Alta de reserva + creación del pago 'created' */
    public function store(StoreReservationRequest $request): RedirectResponse
    {
        $u  = $request->user();
        $tz = config('app.timezone');

        // ====== LOG: payload entrante ======
        $raw = (string) $request->input('date'); // debe venir en Y-m-d
        if (config('app.debug')) {
            Log::debug('RC.store: incoming payload', [
                'date_raw' => $raw,
                'len'      => strlen($raw),
                'hex'      => bin2hex($raw),
                'payload'  => $request->except(['_token']),
            ]);
        }

        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $raw)) {
            Log::warning('RC.store: date no regex Y-m-d', ['raw' => $raw, 'hex'=>bin2hex($raw)]);
            return back()->withInput()->with('error', 'Fecha inválida (formato). Selecciona una fecha del calendario.');
        }

        // ====== Parseo robusto de fecha ======
        try {
            $parsed = Carbon::createFromFormat('Y-m-d', $raw, $tz);
            if ($parsed === false) {
                Log::warning('RC.store: Carbon::createFromFormat=false', ['raw'=>$raw]);
                return back()->withInput()->with('error', 'Fecha inválida. Selecciona desde el calendario.');
            }
            $date = $parsed->copy()->startOfDay();
        } catch (\Throwable $e) {
            Log::error('RC.store: excepción al parsear', ['raw'=>$raw,'hex'=>bin2hex($raw),'err'=>$e->getMessage()]);
            return back()->withInput()->with('error', 'Fecha inválida.');
        }

        $today = Carbon::today($tz);
        $min   = $today->copy()->addDays(self::MIN_DAYS_AHEAD)->startOfDay();

        if (config('app.debug')) {
            Log::debug('RC.store: comparación de fechas', [
                'parsed_date' => $date->toDateString(),
                'today'       => $today->toDateString(),
                'min_allowed' => $min->toDateString(),
            ]);
        }

        if ($date->lt($min)) {
            $minText = $min->format('d/m/Y');
            Log::info('RC.store: fecha < min', ['date'=>$date->toDateString(),'min'=>$min->toDateString()]);
            return back()->withInput()->with('error', "La fecha debe reservarse con al menos 7 días de anticipación. Primer día disponible: {$minText}.");
        }

        // ====== Ocupación del día ======
        $busyQuery = Reservation::query()
            ->whereDate('date', $date->toDateString())
            ->whereIn('status', $this->blockingStatuses());

        $isBusy = $busyQuery->exists();

        if (config('app.debug')) {
            Log::debug('RC.store: ocupación', [
                'date'       => $date->toDateString(),
                'busy_count' => (clone $busyQuery)->count(),
                'is_busy'    => $isBusy,
            ]);
        }

        if ($isBusy) {
            return back()->withInput()->with('error', 'La fecha seleccionada ya está ocupada.');
        }

        // ====== Cálculos de precio ======
        $shift = (string) $request->input('shift');
        $base  = $shift === 'day' ? self::DAY_BASE : self::NIGHT_BASE;

        $extrasInput = collect($request->input('extras', []))
            ->map(fn($e) => [
                'id'  => (int)($e['id'] ?? 0),
                'qty' => max(1, (int)($e['qty'] ?? 1)),
            ])
            ->filter(fn($e) => $e['id'] > 0)
            ->values();

        $extrasModels = ExtraService::whereIn('id', $extrasInput->pluck('id'))->get()->keyBy('id');

        $extrasTotal = 0.0;
        $pivotData   = [];

        foreach ($extrasInput as $e) {
            $m = $extrasModels[$e['id']] ?? null;
            if (!$m) continue;

            $unit = $shift === 'day' ? (float)$m->day_price : (float)$m->night_price;
            $tot  = $unit * $e['qty'];
            $extrasTotal += $tot;

            $pivotData[$e['id']] = [
                'quantity'    => $e['qty'],
                'unit_price'  => $unit,
                'total_price' => $tot,
            ];
        }

        $discount = (float)($request->input('discount_amount') ?: 0);
        $total    = max(0, $base + $extrasTotal - $discount);

        if (config('app.debug')) {
            Log::debug('RC.store: montos', [
                'shift'        => $shift,
                'base'         => $base,
                'extras_total' => $extrasTotal,
                'discount'     => $discount,
                'total'        => $total,
                'extras_input' => $extrasInput,
            ]);
        }

        // ===== Persistencia =====
        $reservation = null;
        $payment     = null;

        DB::transaction(function () use ($request, $u, $base, $discount, $total, $pivotData, $date, &$reservation, &$payment) {

            // ⚠️ OBTENER ENUM CORRECTO DESDE EL REQUEST
            $sourceEnum = $request->enum('source', ReservationSource::class) ?? ReservationSource::OTHER;

            $reservation = Reservation::create([
                'user_id'         => $u->id,
                'event_name'      => (string) $request->input('event_name'),
                'date'            => $date,
                'shift'           => (string) $request->input('shift'),
                'start_time'      => (string) $request->input('start_time'),
                'end_time'        => (string) $request->input('end_time'),
                'headcount'       => (int) $request->input('headcount'),
                'status'          => ReservationStatus::PENDING,
                'base_price'      => $base,
                'discount_amount' => $discount,
                'total_amount'    => $total,
                'balance_amount'  => $total,
                'source'          => $sourceEnum, // ✅ enum real, no Stringable
                'notes'           => (string) $request->input('notes'),
            ]);

            if (method_exists($reservation, 'extras') && !empty($pivotData)) {
                $reservation->extras()->sync($pivotData);
            }

            $payment = Payment::create([
                'reservation_id' => $reservation->id,
                'amount'         => $total,
                'currency'       => 'MXN',
                'status'         => PaymentStatus::CREATED,
                'payment_due_at' => now()->addHours(12),
                'notes'          => 'Pago iniciado: el cliente debe elegir método y subir comprobante.',
            ]);
        });

        if (config('app.debug')) {
            Log::debug('RC.store: creados', [
                'reservation_id' => $reservation?->id,
                'payment_id'     => $payment?->id,
            ]);
        }

        return redirect()
            ->route('client.payments.proof', $reservation)
            ->with('success', '¡Reserva creada! Ahora elige tu método de pago y sube tu comprobante.');
    }

    /** Detalle de una reserva */
    public function show(Reservation $reservation): View
    {
        $this->authorize('view', $reservation);
        $reservation->load('extras');
        return view('client.reservations.show', compact('reservation'));
    }

    /** === API: fechas ocupadas para el datepicker === */
    public function bookedDates(Request $request)
    {
        $tz = config('app.timezone');
        $today = Carbon::today($tz);

        $dates = Reservation::query()
            ->whereDate('date', '>=', $today->toDateString())
            ->whereIn('status', $this->blockingStatuses())
            ->pluck('date')
            ->map(fn($d) => Carbon::parse($d, $tz)->toDateString())
            ->unique()
            ->values()
            ->all();

        return response()->json([
            'today'          => $today->toDateString(),
            'min_days_ahead' => self::MIN_DAYS_AHEAD,
            'reserved'       => $dates,
        ]);
    }
}
