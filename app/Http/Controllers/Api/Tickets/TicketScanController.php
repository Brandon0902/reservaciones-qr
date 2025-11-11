<?php

namespace App\Http\Controllers\Api\Tickets;

use App\Enums\TicketStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Tickets\ScanTicketRequest;
use App\Models\Ticket;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class TicketScanController extends Controller
{
    /**
     * @OA\Post(
     *   path="/validator/tickets/scan",
     *   tags={"Validator"},
     *   summary="Escaneo/validación de boleto por token (QR)",
     *   description="Busca el boleto por token. Si está UNUSED y es válido, lo marca como USED y devuelve su estado e id de mesa.",
     *   security={{"bearerAuth":{}}},
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       required={"token"},
     *       @OA\Property(property="token", type="string", format="uuid", example="35be6a8c-4b8c-4a7a-a8a7-7d9b7b4f3b19")
     *     )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="OK",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="changed", type="boolean", example=true),
     *       @OA\Property(property="status", type="string", enum={"unused","used","expired","canceled"}, example="used"),
     *       @OA\Property(property="id_mesa", type="integer", example=3),
     *       @OA\Property(property="used_at", type="string", format="date-time", nullable=true, example="2025-11-10T01:23:45Z"),
     *       @OA\Property(property="ticket_id", type="integer", example=128),
     *       @OA\Property(property="reservation_id", type="integer", example=77),
     *       @OA\Property(property="event_name", type="string", example="XV Años Camila"),
     *       @OA\Property(property="date", type="string", format="date", example="2025-11-20"),
     *       @OA\Property(property="shift", type="string", example="night"),
     *       @OA\Property(property="message", type="string", example="Boleto validado y marcado como usado.")
     *     )
     *   ),
     *   @OA\Response(
     *     response=404,
     *     description="No encontrado",
     *     @OA\JsonContent(@OA\Property(property="message", type="string", example="Boleto no encontrado."))
     *   ),
     *   @OA\Response(
     *     response=422,
     *     description="Validación fallida",
     *     @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="The token field must be a valid UUID."),
     *       @OA\Property(property="errors", type="object")
     *     )
     *   )
     * )
     */
    public function scan(ScanTicketRequest $request): JsonResponse
    {
        $token = $request->input('token');

        /** @var Ticket|null $ticket */
        $ticket = Ticket::with('reservation')->where('token', $token)->first();

        if (!$ticket) {
            return response()->json(['message' => 'Boleto no encontrado.'], 404);
        }

        // Snapshot de respuesta base
        $meta = [
            'ticket_id'      => $ticket->id,
            'reservation_id' => $ticket->reservation_id,
            'event_name'     => optional($ticket->reservation)->event_name,
            'date'           => optional($ticket->reservation?->date)->toDateString(),
            'shift'          => optional($ticket->reservation)->shift,
        ];

        // Si la fecha del evento ya pasó y el boleto sigue UNUSED, lo marcamos como EXPIRED.
        $today = Carbon::now()->startOfDay();
        $eventDay = optional($ticket->reservation?->date)?->startOfDay();
        if ($ticket->status === TicketStatus::UNUSED && $eventDay && $eventDay->lt($today)) {
            $ticket->status = TicketStatus::EXPIRED;
            $ticket->save();
        }

        // No forzamos 409; siempre devolvemos 200 con "changed" para UX de escaneo.
        if ($ticket->status === TicketStatus::UNUSED) {
            // Marcar como usado
            $ticket->status  = TicketStatus::USED;
            $ticket->used_at = Carbon::now();
            // Si tienes columna used_by (nullable), aquí puedes setear: $ticket->used_by = $request->user()->id;
            $ticket->save();

            return response()->json([
                'changed'  => true,
                'status'   => $ticket->status->value,
                'id_mesa'  => (int) $ticket->id_mesa,
                'used_at'  => optional($ticket->used_at)?->toIso8601String(),
                'message'  => 'Boleto validado y marcado como usado.',
            ] + $meta, 200);
        }

        // Ya estaba en USED/EXPIRED/CANCELED
        $messages = [
            TicketStatus::USED->value     => 'Este boleto ya fue usado.',
            TicketStatus::EXPIRED->value  => 'Este boleto está expirado.',
            TicketStatus::CANCELED->value => 'Este boleto está cancelado.',
        ];

        $msg = $messages[$ticket->status->value] ?? 'Estado actual del boleto.';
        return response()->json([
            'changed'  => false,
            'status'   => $ticket->status->value,
            'id_mesa'  => (int) $ticket->id_mesa,
            'used_at'  => optional($ticket->used_at)?->toIso8601String(),
            'message'  => $msg,
        ] + $meta, 200);
    }
}
