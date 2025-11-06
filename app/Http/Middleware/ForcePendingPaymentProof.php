<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Enums\PaymentStatus;

class ForcePendingPaymentProof
{
    public function handle(Request $request, Closure $next): Response
    {
        $u = $request->user();
        if (!$u) return $next($request);

        // OJO: usar 'payments' (plural), que sí existe en Reservation
        $reservation = \App\Models\Reservation::query()
            ->where('user_id', $u->id)
            ->whereHas('payments', fn ($q) => $q->where('status', PaymentStatus::CREATED))
            ->latest('id')
            ->first();

        // Evitar loop: permitir las rutas de comprobante/confirmación
        if ($reservation && ! $request->routeIs('client.payments.proof*') && ! $request->routeIs('client.payments.confirmation')) {
            return redirect()
                ->route('client.payments.proof', $reservation)
                ->with('warning', 'Tienes un pago iniciado. Completa el método y sube tu comprobante.');
        }

        return $next($request);
    }
}
