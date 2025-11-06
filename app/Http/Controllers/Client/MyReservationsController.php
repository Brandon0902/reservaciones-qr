<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MyReservationsController extends Controller
{
    public function index(Request $request): View
    {
        $u = $request->user();

        $rows = Reservation::query()
            ->withCount('tickets')
            ->where('user_id', $u->id)
            ->orderByDesc('id')
            ->paginate(12);

        return view('client.reservations.index', compact('rows'));
    }

    public function tickets(Request $request, Reservation $reservation): View
    {
        // Seguridad: solo dueño
        abort_unless($reservation->user_id === $request->user()->id, 403);

        $reservation->load(['tickets' => function ($q) {
            $q->orderBy('id_mesa')->orderBy('id');
        }]);

        // Agrupar por mesa
        $grouped = $reservation->tickets->groupBy('id_mesa');

        // Horarios por turno (visuales)
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
}
