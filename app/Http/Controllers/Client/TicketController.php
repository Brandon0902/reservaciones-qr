<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\View\View;

class TicketController extends Controller
{
    /**
     * Muestra un boleto individual en una vista bonita.
     */
    public function show(Request $request, Ticket $ticket): View
    {
        // Seguridad: sólo el dueño puede ver su boleto
        abort_unless($ticket->reservation && $ticket->reservation->user_id === $request->user()->id, 403);

        // Asegurar que el QR exista en disco (lo genera si falta)
        $qrUrl = $this->ensureQrStored($ticket);

        // Datos auxiliares para la plantilla
        $reservation = $ticket->reservation()->with('user')->first();
        $shiftRanges = ['day' => '10:00–16:00', 'night' => '19:00–02:00'];
        $address = 'Dirección: Av. Jesus Michel Gonzalez 3232, Paseo del Prado, 45610 San Pedro Tlaquepaque, Jal.';

        return view('client.tickets.show', [
            'ticket'      => $ticket,
            'reservation' => $reservation,
            'qrUrl'       => $qrUrl,
            'shiftRanges' => $shiftRanges,
            'address'     => $address,
        ]);
    }

    /**
     * Descarga del archivo del QR del boleto (SVG por defecto).
     */
    public function download(Request $request, Ticket $ticket): StreamedResponse
    {
        abort_unless($ticket->reservation && $ticket->reservation->user_id === $request->user()->id, 403);

        // Asegurar QR en disco
        $this->ensureQrStored($ticket);

        $disk = Storage::disk('tickets');
        $path = $ticket->qr_path ?: '';
        abort_unless($path && $disk->exists($path), 404, 'Archivo de QR no encontrado.');

        $filename = 'ticket-'.$ticket->id.'.svg';
        return $disk->download($path, $filename, [
            'Content-Type' => 'image/svg+xml',
        ]);
    }

    /**
     * Genera (si hace falta) el SVG del QR y devuelve la URL pública.
     *
     * Convención de ruta: tickets/{reservation_id}/{ticket_id}.svg
     */
    private function ensureQrStored(Ticket $ticket): string
    {
        $disk = Storage::disk('tickets');

        // Si ya tenemos ruta y existe, devolvemos URL
        if ($ticket->qr_path && $disk->exists($ticket->qr_path)) {
            return $disk->url($ticket->qr_path);
        }

        // Payload para el QR (tomamos token + metadata básica)
        $payload = $ticket->qr_payload ?? [];
        $payload['t']  = $payload['t']  ?? ($ticket->token ?? null);
        $payload['id'] = $payload['id'] ?? $ticket->id;
        $payload['r']  = $payload['r']  ?? $ticket->reservation_id;

        // Generar SVG
        $svg = QrCode::format('svg')
            ->size(512)
            ->margin(1)
            ->generate(json_encode($payload, JSON_UNESCAPED_UNICODE));

        // Guardar en una ruta estable por reservación/boletos
        $path = 'tickets/'.$ticket->reservation_id.'/'.$ticket->id.'.svg';
        $disk->put($path, $svg);

        // Persistir la ruta si cambió
        if ($ticket->qr_path !== $path) {
            $ticket->qr_path = $path;
            $ticket->save();
        }

        return $disk->url($path);
    }
}
