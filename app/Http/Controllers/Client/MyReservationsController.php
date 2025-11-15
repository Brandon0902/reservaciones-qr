<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MyReservationsController extends Controller
{
    public function index(Request $request)
    {
        $u = $request->user();

        $rows = Reservation::query()
            ->withCount('tickets')
            ->where('user_id', $u->id)
            ->orderByDesc('id')
            ->paginate(12);

        return view('client.reservations.index', compact('rows'));
    }


    public function show(Request $request, Reservation $reservation)
    {
        // Seguridad: que la reservación sea del cliente actual
        abort_unless($reservation->user_id === $request->user()->id, 403);

        // Cargamos servicios extra (y lo que quieras)
        $reservation->load('extraServices');

        return view('client.reservations.show', [
            'reservation' => $reservation,
        ]);
    }

    public function tickets(Request $request, Reservation $reservation)
    {
        abort_unless($reservation->user_id === $request->user()->id, 403);

        $reservation->load(['tickets' => function ($q) {
            $q->orderBy('id_mesa')->orderBy('id');
        }]);

        $grouped = $reservation->tickets->groupBy('id_mesa');

        $shiftRanges = [
            'day'   => '10:00–16:00',
            'night' => '19:00–02:00',
        ];

        $address = 'Dirección: Av. Jesus Michel Gonzalez 3232, Paseo del Prado, 45610 San Pedro Tlaquepaque, Jal.';

        return view('client.tickets.index', [
            'reservation' => $reservation,
            'grouped'     => $grouped,
            'shiftRanges' => $shiftRanges,
            'address'     => $address,
        ]);
    }

    /**
     * NUEVO: versión imprimible del boleto (misma tarjeta que ves en pantalla).
     * Si llamas con ?auto=1 abre automáticamente el diálogo de impresión.
     */
    public function printTicket(Request $request, Reservation $reservation, Ticket $ticket)
    {
        // Seguridad
        abort_unless($reservation->user_id === $request->user()->id, 403);
        abort_unless($ticket->reservation_id === $reservation->id, 403);

        $shiftRanges = [
            'day'   => '10:00–16:00',
            'night' => '19:00–02:00',
        ];
        $address = 'Dirección: Av. Jesus Michel Gonzalez 3232, Paseo del Prado, 45610 San Pedro Tlaquepaque, Jal.';

        // URL absoluta del QR (para impresión)
        $qrUrl = $ticket->qr_path ? Storage::disk('tickets')->url($ticket->qr_path) : null;

        return view('client.tickets.print', [
            'reservation' => $reservation,
            'ticket'      => $ticket,
            'shiftRanges' => $shiftRanges,
            'address'     => $address,
            'qrUrl'       => $qrUrl,
            'autoPrint'   => $request->boolean('auto', false),
        ]);
    }

    /**
     * Alias para que la ruta client.tickets.print funcione
     * y pueda mantenerse el nombre del route.
     */
    public function print(Request $request, Reservation $reservation, Ticket $ticket)
    {
        return $this->printTicket($request, $reservation, $ticket);
    }
}
