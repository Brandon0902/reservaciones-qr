<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Enums\PaymentStatus;
use App\Enums\ReservationStatus;
use App\Services\TicketIssuer; // ← Servicio
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PaymentApprovalController extends Controller
{
    public function __construct(private TicketIssuer $tickets)
    {
        $this->middleware(['auth', 'admin.only']);
    }

    /** Bandeja de pagos con filtros simples */
    public function index(Request $request): View
    {
        $status = $request->string('status')->toString(); // created|pending|paid|rejected|refunded
        $q      = trim((string) $request->get('q', ''));

        $rows = Payment::query()
            ->with(['reservation.user'])
            ->when($status !== '', fn($qq) => $qq->where('status', $status))
            ->when($q !== '', function ($qq) use ($q) {
                $qq->where(function ($w) use ($q) {
                    $w->where('txn_ref', 'like', "%{$q}%")
                      ->orWhere('receipt_ref', 'like', "%{$q}%")
                      ->orWhereHas('reservation', function ($r) use ($q) {
                          $r->where('event_name', 'like', "%{$q}%")
                            ->orWhere('notes', 'like', "%{$q}%");
                      });
                });
            })
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.payments.index', compact('rows', 'status', 'q'));
    }

    /** Aprobar pago → PAID + reserva COMPLETED → emitir boletos */
    public function approve(Payment $payment, Request $request): RedirectResponse
    {
        abort_unless(in_array($payment->status, [PaymentStatus::PENDING, PaymentStatus::CREATED]), 403, 'El pago no está pendiente.');

        DB::transaction(function () use ($payment, $request) {
            $payment->update([
                'status'      => PaymentStatus::PAID,
                'approved_by' => Auth::id(),
                'approved_at' => now(),
                'paid_at'     => now(),
                'notes'       => trim($payment->notes . "\n[Aprobado] " . ($request->input('notes') ?? '')),
            ]);

            $reservation = $payment->reservation()->lockForUpdate()->first();
            if ($reservation) {
                $reservation->update([
                    'status'         => ReservationStatus::COMPLETED,
                    'balance_amount' => 0,
                ]);

                // Emitir boletos si aún no existen (idempotente)
                $this->tickets->issueForReservation($reservation);
            }
        });

        return back()->with('success', 'Pago aprobado, reservación COMPLETED y boletos emitidos.');
    }

    /** Rechazar pago */
    public function reject(Payment $payment, Request $request): RedirectResponse
    {
        abort_unless(in_array($payment->status, [PaymentStatus::PENDING, PaymentStatus::CREATED]), 403, 'El pago no está pendiente.');

        $payment->update([
            'status' => PaymentStatus::REJECTED,
            'notes'  => trim($payment->notes . "\n[Rechazado] " . ($request->input('notes') ?? '')),
        ]);

        return back()->with('warning', 'Pago rechazado.');
    }

    /** Reembolso */
    public function refund(Payment $payment, Request $request): RedirectResponse
    {
        abort_unless($payment->status === PaymentStatus::PAID, 403, 'Solo pagos pagados pueden reembolsarse.');

        DB::transaction(function () use ($payment, $request) {
            $payment->update([
                'status' => PaymentStatus::REFUNDED,
                'notes'  => trim($payment->notes . "\n[Reembolso] " . ($request->input('notes') ?? '')),
//              'paid_at' => null, // opcional, según tu negocio
            ]);

            $reservation = $payment->reservation()->lockForUpdate()->first();
            if ($reservation && $reservation->status === ReservationStatus::COMPLETED) {
                // Al reembolsar, regresamos a CONFIRMED (tu regla actual)
                $reservation->update(['status' => ReservationStatus::CONFIRMED]);

                // No cancelamos tickets aquí automáticamente; podrías marcarlos como CANCELED si lo deseas.
                // foreach ($reservation->tickets as $t) { $t->update(['status' => TicketStatus::CANCELED]); }
            }
        });

        return back()->with('info', 'Pago marcado como reembolsado.');
    }

    /**
     * Cambiar estado desde un select (created|pending|paid|rejected|refunded)
     * y aplicar efectos colaterales. Si se establece PAID, emitimos boletos.
     */
    public function updateStatus(Request $request, Payment $payment): RedirectResponse
    {
        $target = (string) $request->input('status', '');
        $valid  = array_column(PaymentStatus::cases(), 'value');

        if (!in_array($target, $valid, true)) {
            return back()->with('error', 'Estado inválido.');
        }

        $targetStatus = PaymentStatus::from($target);

        DB::transaction(function () use ($payment, $targetStatus, $request) {
            $update = ['status' => $targetStatus];

            if ($targetStatus === PaymentStatus::PAID) {
                $update['approved_by'] = Auth::id();
                $update['approved_at'] = now();
                $update['paid_at']     = now();
                $update['notes']       = trim($payment->notes . "\n[Set status → PAID] " . ($request->input('notes') ?? ''));
            } elseif ($targetStatus === PaymentStatus::REFUNDED) {
                $update['notes'] = trim($payment->notes . "\n[Set status → REFUNDED] " . ($request->input('notes') ?? ''));
            } elseif ($targetStatus === PaymentStatus::REJECTED) {
                $update['notes'] = trim($payment->notes . "\n[Set status → REJECTED] " . ($request->input('notes') ?? ''));
            } else {
                $update['notes'] = trim($payment->notes . "\n[Set status → " . strtoupper($targetStatus->value) . "] " . ($request->input('notes') ?? ''));
            }

            $payment->update($update);

            $reservation = $payment->reservation()->lockForUpdate()->first();
            if ($reservation) {
                match ($targetStatus) {
                    PaymentStatus::PAID     => $reservation->update(['status' => ReservationStatus::COMPLETED, 'balance_amount' => 0]),
                    PaymentStatus::REFUNDED => $reservation->update(['status' => ReservationStatus::CONFIRMED]),
                    default                 => null,
                };

                // Si ahora quedó en PAID + COMPLETED → emitir boletos
                if ($targetStatus === PaymentStatus::PAID && $reservation->status === ReservationStatus::COMPLETED) {
                    $this->tickets->issueForReservation($reservation);
                }
            }
        });

        return back()->with('success', 'Estado de pago actualizado.');
    }
}
