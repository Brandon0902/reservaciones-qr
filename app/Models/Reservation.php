<?php

namespace App\Models;

use App\Enums\ReservationSource;
use App\Enums\ReservationStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'start_time',
        'end_time',
        'headcount',
        'status',            // enum: pending | confirmed | canceled | checked_in | completed
        'hold_expires_at',
        'base_price',
        'discount_amount',
        'total_amount',
        'balance_amount',
        'extra_service_id',
        'source',            // enum: in_person | phone | whatsapp | web | other
        'notes',
    ];

    protected $casts = [
        'date'            => 'date',
        'hold_expires_at' => 'datetime',
        'status'          => ReservationStatus::class,
        'source'          => ReservationSource::class,
    ];

    /* ========= Relaciones ========= */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function extraService(): BelongsTo
    {
        return $this->belongsTo(ExtraService::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }
}
