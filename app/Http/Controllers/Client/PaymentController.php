<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\StorePaymentProofRequest;
use App\Models\Reservation;
use App\Models\Payment;
use App\Enums\PaymentStatus;
use App\Enums\PaymentMethod;
use App\Enums\ReservationStatus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class PaymentController extends Controller
{
    /**
     * Form para elegir método y subir comprobante.
     */
    public function create(Reservation $reservation): View|RedirectResponse
    {
        // Solo el dueño puede ver/editar su reservación
        $this->authorize('update', $reservation);

        // Busca el pago en estado CREATED; si no existe, usa el último
        $payment = $reservation->payments()
            ->where('status', PaymentStatus::CREATED)
            ->latest('id')
            ->first();

        if (!$payment) {
            $payment = $reservation->payments()->latest('id')->first();
        }

        // Si no hay ningún pago, crea uno en CREATED (seguro para el flujo)
        if (!$payment) {
            $payment = $reservation->payments()->create([
                'amount'         => $reservation->total_amount,
                'currency'       => 'MXN',
                'status'         => PaymentStatus::CREATED,
                'method'         => null,
                'payment_due_at' => now()->addHours(12),
                'notes'          => 'Pago iniciado desde pantalla de comprobante.',
            ]);
        }

        // Si ya no está en CREATED y tiene comprobante, manda a confirmación
        if ($payment->status !== PaymentStatus::CREATED && $payment->receipt_ref) {
            return redirect()->route('client.payments.confirmation', $reservation);
        }

        return view('client.payments.proof', [
            'reservation' => $reservation,
            'payment'     => $payment,
        ]);
    }

    /**
     * Guarda método y comprobante.
     */
    public function store(StorePaymentProofRequest $request, Reservation $reservation): RedirectResponse
    {
        $this->authorize('update', $reservation);

        // Debe existir un pago en CREATED
        $payment = $reservation->payments()
            ->where('status', PaymentStatus::CREATED)
            ->latest('id')
            ->firstOrFail();

        // Subir archivo al disco 'receipts' (configurado en filesystems)
        $file = $request->file('receipt');
        $path = $file->store('', 'receipts'); // p.ej. storage/app/receipts

        // Actualiza pago
        $payment->update([
            'method'              => $request->enum('method', PaymentMethod::class) ?? PaymentMethod::DEPOSIT,
            'receipt_ref'         => $path,
            'receipt_uploaded_at' => now(),
            'status'              => PaymentStatus::PENDING, // queda a revisión del admin
            'notes'               => (string) $request->input('notes', ''),
        ]);

        // Opcional: mover la reserva a CONFIRMED (si tu flujo lo requiere)
        $reservation->update([
            'status' => ReservationStatus::CONFIRMED,
        ]);

        Log::info('Payment proof uploaded', [
            'reservation_id' => $reservation->id,
            'payment_id'     => $payment->id,
            'method'         => $payment->method?->value,
            'status'         => $payment->status->value,
        ]);

        return redirect()
            ->route('client.payments.confirmation', $reservation)
            ->with('success', '¡Comprobante recibido! Tu pago quedó pendiente de validación.');
    }

    /**
     * Pantalla de confirmación.
     */
    public function confirmation(Reservation $reservation): View
    {
        $this->authorize('view', $reservation);

        $payment = $reservation->payments()->latest('id')->first();

        return view('client.payments.confirmation', [
            'reservation' => $reservation,
            'payment'     => $payment,
        ]);
    }
}
