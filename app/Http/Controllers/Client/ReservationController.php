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

    /** Estados que bloquean el día/turno */
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

        // ====== Parseo robusto de fecha ======
        $raw = (string) $request->input('date'); // debe venir en Y-m-d
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $raw)) {
            return back()->withInput()->with('error', 'Fecha inválida (formato). Selecciona una fecha del calendario.');
        }

        try {
            $date = Carbon::createFromFormat('Y-m-d', $raw, $tz)->startOfDay();
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', 'Fecha inválida.');
        }

        $today = Carbon::today($tz);
        $min   = $today->copy()->addDays(self::MIN_DAYS_AHEAD)->startOfDay();
        if ($date->lt($min)) {
            $minText = $min->format('d/m/Y');
            return back()->withInput()->with('error', "La fecha debe reservarse con al menos 7 días de anticipación. Primer día disponible: {$minText}.");
        }

        // ====== Ocupación por turno ======
        $shift = (string) $request->input('shift'); // 'day' | 'night'

        $turnBusy = Reservation::query()
            ->whereDate('date', $date->toDateString())
            ->where('shift', $shift)
            ->whereIn('status', $this->blockingStatuses())
            ->exists();

        if ($turnBusy) {
            $msg = $shift === 'day'
                ? 'El turno de día ya está ocupado para esa fecha.'
                : 'El turno de noche ya está ocupado para esa fecha.';
            return back()->withInput()->with('error', $msg);
        }

        // ====== Cálculos de precio ======
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

        // ===== Persistencia =====
        $reservation = null;
        $payment     = null;

        DB::transaction(function () use ($request, $u, $base, $discount, $total, $pivotData, $date, $shift, &$reservation, &$payment) {

            $sourceEnum = $request->enum('source', ReservationSource::class) ?? ReservationSource::OTHER;

            // Horas fijas por turno (se ignoran las que vengan del formulario)
            [$start, $end] = $shift === 'day'
                ? ['10:00', '16:00']
                : ['19:00', '02:00'];

            $reservation = Reservation::create([
                'user_id'         => $u->id,
                'event_name'      => (string) $request->input('event_name'),
                'date'            => $date,
                'shift'           => $shift,
                'start_time'      => $start,
                'end_time'        => $end,
                'headcount'       => (int) $request->input('headcount'),
                'status'          => ReservationStatus::PENDING,
                'base_price'      => $base,
                'discount_amount' => $discount,
                'total_amount'    => $total,
                'balance_amount'  => $total,
                'source'          => $sourceEnum,
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

        return redirect()
            ->route('client.payments.proof', $reservation)
            ->with('success', '¡Reserva creada! Ahora elige tu método de pago y sube tu comprobante.');
    }

    /** Detalle */
    public function show(Reservation $reservation): View
    {
        $this->authorize('view', $reservation);
        $reservation->load('extras');
        return view('client.reservations.show', compact('reservation'));
    }

    /**
     * === API: fechas/turnos ocupados para el datepicker ===
     * Respuesta:
     * {
     *   "today":"YYYY-MM-DD",
     *   "min_days_ahead":8,
     *   "busy": { "YYYY-MM-DD":["day","night"], ... },
     *   "full": ["YYYY-MM-DD", ...]
     * }
     */
    public function bookedDates(Request $request)
    {
        $tz = config('app.timezone');
        $today = Carbon::today($tz);

        $rows = Reservation::query()
            ->whereDate('date', '>=', $today->toDateString())
            ->whereIn('status', $this->blockingStatuses())
            ->get(['date','shift']);

        $busy = [];
        foreach ($rows as $r) {
            $d = Carbon::parse($r->date, $tz)->toDateString();
            $busy[$d] = $busy[$d] ?? [];
            if (!in_array($r->shift, $busy[$d], true)) {
                $busy[$d][] = $r->shift; // 'day' o 'night'
            }
        }

        $full = [];
        foreach ($busy as $d => $turns) {
            sort($turns);
            if ($turns === ['day','night']) {
                $full[] = $d;
            }
        }

        return response()->json([
            'today'          => $today->toDateString(),
            'min_days_ahead' => self::MIN_DAYS_AHEAD,
            'busy'           => $busy,
            'full'           => $full,
        ]);
    }
}
