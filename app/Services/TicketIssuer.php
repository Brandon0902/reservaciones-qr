<?php

namespace App\Services;

use App\Models\Reservation;
use App\Models\Ticket;
use App\Enums\TicketStatus;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use RuntimeException;

class TicketIssuer
{
    /** Reglas de negocio */
    private const TABLES            = 7;   // total de mesas
    private const SEATS_PER_TABLE   = 10;  // boletos por mesa
    private const VENUE_CAPACITY    = self::TABLES * self::SEATS_PER_TABLE; // 70

    /**
     * Emite boletos para la reservación si aún no existen.
     * Idempotente: si ya hay tickets, no vuelve a crearlos.
     *
     * @return int Cantidad de boletos emitidos (nuevos)
     * @throws RuntimeException si headcount excede capacidad del venue
     */
    public function issueForReservation(Reservation $reservation): int
    {
        $headcount = (int) $reservation->headcount;

        if ($headcount < 1) {
            return 0;
        }

        if ($headcount > self::VENUE_CAPACITY) {
            throw new RuntimeException("La reservación excede la capacidad del recinto (máximo " . self::VENUE_CAPACITY . ").");
        }

        // Si ya existen boletos, no crear de nuevo.
        if ($reservation->tickets()->exists()) {
            return 0;
        }

        $now        = now();
        $created    = 0;

        for ($i = 0; $i < $headcount; $i++) {
            $tableNo  = $this->tableForIndex($i);         // 1..7
            $token    = (string) Str::uuid();             // token único
            $filename = $token . '.svg';                  // archivo del QR

            // El QR debe contener solo el token (seguro y pequeño).
            $qrData   = json_encode(['t' => $token], JSON_UNESCAPED_SLASHES);

            $svg = QrCode::format('svg')
                ->size(512)
                ->margin(1)
                ->generate($qrData);

            // Guardar archivo en el disco 'tickets'
            Storage::disk('tickets')->put($filename, $svg);

            // Payload guardado en BD (metadatos de auditoría, no van dentro del QR)
            $payload = [
                't' => $token,
                'r' => $reservation->id,
                'e' => (string) $reservation->event_name,
                'd' => optional($reservation->date)->toDateString(),
                's' => (string) $reservation->shift,
                'm' => $tableNo,
                'i' => $now->toIso8601String(),
            ];

            Ticket::create([
                'reservation_id' => $reservation->id,
                'token'          => $token,
                'qr_payload'     => $payload,
                'status'         => TicketStatus::UNUSED,
                'issued_at'      => $now,
                'used_at'        => null,
                'id_mesa'        => $tableNo,
                'qr_path'        => $filename, // relativo al disco 'tickets'
            ]);

            $created++;
        }

        return $created;
    }

    /**
     * Asigna mesa por lote de 10 boletos: 1-10 → mesa 1, 11-20 → mesa 2, ...
     * Índice base 0.
     */
    private function tableForIndex(int $index): int
    {
        $table = intdiv($index, self::SEATS_PER_TABLE) + 1;
        return min($table, self::TABLES);
    }
}
