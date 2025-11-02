<?php

namespace App\Models;

use App\Enums\TicketStatus; // enum: unused | used | expired | canceled
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'reservation_id',
        'qr_payload',   // guardamos JSON como TEXT (cast array)
        'status',       // enum
        'issued_at',
        'used_at',
        'id_mesa',
    ];

    protected $casts = [
        'issued_at'  => 'datetime',
        'used_at'    => 'datetime',
        'qr_payload' => 'array',
        'status'     => TicketStatus::class,
    ];

    /* ========= Relaciones ========= */

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }
}
