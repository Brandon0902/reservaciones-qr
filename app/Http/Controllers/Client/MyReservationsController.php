<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

use SimpleSoftwareIO\QrCode\Facades\QrCode;

// ✅ Spatie PDF
use Spatie\LaravelPdf\Facades\Pdf as SpatiePdf;
use Spatie\LaravelPdf\Enums\Unit;
use Spatie\Browsershot\Browsershot;

use App\Mail\TicketLinkMail;

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
        abort_unless($reservation->user_id === $request->user()->id, 403);

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

    public function printTicket(Request $request, Reservation $reservation, Ticket $ticket)
    {
        abort_unless($reservation->user_id === $request->user()->id, 403);
        abort_unless($ticket->reservation_id === $reservation->id, 403);

        $shiftRanges = [
            'day'   => '10:00–16:00',
            'night' => '19:00–02:00',
        ];
        $address = 'Dirección: Av. Jesus Michel Gonzalez 3232, Paseo del Prado, 45610 San Pedro Tlaquepaque, Jal.';

        $qrUrl = $this->ensureQrStored($ticket);

        return view('client.tickets.print', [
            'reservation' => $reservation,
            'ticket'      => $ticket,
            'shiftRanges' => $shiftRanges,
            'address'     => $address,
            'qrUrl'       => $qrUrl,
            'autoPrint'   => $request->boolean('auto', false),
        ]);
    }

    public function print(Request $request, Reservation $reservation, Ticket $ticket)
    {
        return $this->printTicket($request, $reservation, $ticket);
    }

    public function pdf(Request $request, Reservation $reservation, Ticket $ticket)
    {
        return $this->printTicket($request, $reservation, $ticket);
    }

    public function emailOne(Request $request)
    {
        $data = $request->validate([
            'reservation_id' => ['required', 'integer'],
            'ticket_id'      => ['required', 'integer'],
            'emails'         => ['required', 'string', 'max:2000'],
            'message'        => ['nullable', 'string', 'max:1000'],
        ]);

        $ticket = Ticket::with('reservation')
            ->where('id', $data['ticket_id'])
            ->where('reservation_id', $data['reservation_id'])
            ->firstOrFail();

        abort_unless($ticket->reservation && $ticket->reservation->user_id === $request->user()->id, 403);

        $emails = collect(explode(',', $data['emails']))
            ->map(fn($e) => trim($e))
            ->filter()
            ->unique()
            ->values();

        $invalid = $emails->filter(fn($e) => !filter_var($e, FILTER_VALIDATE_EMAIL))->values();
        if ($invalid->isNotEmpty()) {
            return response()->json([
                'message' => 'Hay correos inválidos: ' . $invalid->implode(', ')
            ], 422);
        }

        $qrUrl = $this->ensureQrStored($ticket);

        // Data URI del QR (SVG) para PDF
        $qrDataUri = null;
        try {
            if ($ticket->qr_path && Storage::disk('tickets')->exists($ticket->qr_path)) {
                $svg = Storage::disk('tickets')->get($ticket->qr_path);
                $qrDataUri = 'data:image/svg+xml;base64,' . base64_encode($svg);
            }
        } catch (\Throwable $e) {
            $qrDataUri = null;
        }

        $publicUrl = URL::temporarySignedRoute(
            'tickets.public',
            now()->addDays(7),
            ['ticket' => $ticket->id]
        );

        $reservation  = $ticket->reservation;
        $shiftRanges  = ['day' => '10:00–16:00', 'night' => '19:00–02:00'];
        $address      = 'Dirección: Av. Jesus Michel Gonzalez 3232, Paseo del Prado, 45610 San Pedro Tlaquepaque, Jal.';
        $code         = strtoupper(substr((string)$ticket->token, 0, 8));
        $pdfFilename  = "boleto-{$code}.pdf";

        // ✅ PDF “recortado” al tamaño del boleto (180mm x 105mm), sin margen
        $tmpDir = storage_path('app/tmp');
        if (!is_dir($tmpDir)) {
            @mkdir($tmpDir, 0755, true);
        }
        $tmpPdfPath = $tmpDir . '/ticket-' . Str::uuid() . '.pdf';

        // ✅ Forzar Chrome (deb) + entorno headless para www-data
        SpatiePdf::view('pdf.ticket', [
                'reservation' => $reservation,
                'ticket'      => $ticket,
                'shiftRanges' => $shiftRanges,
                'address'     => $address,
                'qrDataUri'   => $qrDataUri,
            ])
            ->paperSize(180, 105, Unit::Millimeter)
            ->margins(0, 0, 0, 0)
            ->withBrowsershot(function (Browsershot $b) {
                $b->setChromePath(env('BROWSERSHOT_CHROME_PATH', '/usr/bin/google-chrome'))
                  ->noSandbox()
                  ->setEnvironmentOptions([
                      'HOME'            => '/tmp',
                      'XDG_CACHE_HOME'  => '/tmp',
                      'XDG_CONFIG_HOME' => '/tmp',
                  ])
                  ->setOption('args', [
                      '--disable-dev-shm-usage',
                      '--no-first-run',
                      '--no-default-browser-check',
                      '--user-data-dir=/tmp/chrome-wwwdata',
                      '--disk-cache-dir=/tmp/chrome-cache',
                      '--disable-crash-reporter',
                      '--disable-features=Crashpad',
                  ]);
            })
            ->save($tmpPdfPath);

        $pdfBinary = file_get_contents($tmpPdfPath);
        @unlink($tmpPdfPath);

        foreach ($emails as $to) {
            Mail::to($to)->send(new TicketLinkMail(
                ticket: $ticket,
                publicUrl: $publicUrl,
                messageText: $data['message'] ?? null,
                qrUrl: $qrUrl,
                pdfBinary: $pdfBinary,
                pdfFilename: $pdfFilename,
            ));
        }

        return response()->json([
            'message' => 'Correo enviado correctamente (con PDF adjunto).'
        ]);
    }

    public function publicTicket(Request $request, Ticket $ticket)
    {
        $reservation = Reservation::findOrFail($ticket->reservation_id);

        $shiftRanges = [
            'day'   => '10:00–16:00',
            'night' => '19:00–02:00',
        ];

        $address = 'Dirección: Av. Jesus Michel Gonzalez 3232, Paseo del Prado, 45610 San Pedro Tlaquepaque, Jal.';
        $qrUrl = $this->ensureQrStored($ticket);

        return view('tickets.public', [
            'ticket'      => $ticket,
            'reservation' => $reservation,
            'shiftRanges' => $shiftRanges,
            'address'     => $address,
            'qrUrl'       => $qrUrl,
        ]);
    }

    private function ensureQrStored(Ticket $ticket): ?string
    {
        $disk = Storage::disk('tickets');

        if ($ticket->qr_path && $disk->exists($ticket->qr_path)) {
            return $disk->url($ticket->qr_path);
        }

        if (empty($ticket->token)) {
            return null;
        }

        $payload = $ticket->qr_payload ?? [];
        $payload['t']  = $payload['t']  ?? $ticket->token;
        $payload['id'] = $payload['id'] ?? $ticket->id;
        $payload['r']  = $payload['r']  ?? $ticket->reservation_id;

        $svg = QrCode::format('svg')
            ->size(512)
            ->margin(1)
            ->generate(json_encode($payload, JSON_UNESCAPED_UNICODE));

        $path = 'tickets/' . $ticket->reservation_id . '/' . $ticket->id . '.svg';
        $disk->put($path, $svg);

        $ticket->qr_path = $path;
        $ticket->save();

        return $disk->url($path);
    }
}
