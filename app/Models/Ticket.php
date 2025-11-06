<?php

namespace App\Models;

use App\Enums\TicketStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'reservation_id',
        'token',       // ← UUID único del boleto
        'qr_payload',  // ← JSON interno (no va en el QR)
        'status',      // enum
        'issued_at',
        'used_at',
        'id_mesa',
        'qr_path',     // ← archivo generado del QR (opcional)
    ];

    protected $casts = [
        'issued_at'  => 'datetime',
        'used_at'    => 'datetime',
        'qr_payload' => 'array',
        'status'     => TicketStatus::class,
    ];

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }
}
