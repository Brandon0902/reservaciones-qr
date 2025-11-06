<?php

namespace App\Models;

use App\Enums\ReservationSource;
use App\Enums\ReservationStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'event_name',       // NUEVO
        'date',
        'shift',            // day | night
        'start_time',
        'end_time',
        'headcount',
        'status',
        'hold_expires_at',
        'base_price',
        'discount_amount',
        'total_amount',
        'balance_amount',
        'extra_service_id', // legacy, opcional
        'source',
        'notes',
    ];

    protected $casts = [
        'date'            => 'date',
        'hold_expires_at' => 'datetime',
        'status'          => ReservationStatus::class,
        'source'          => ReservationSource::class,
    ];

    /* ========= Relaciones ========= */
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function extraService(): BelongsTo { return $this->belongsTo(ExtraService::class); }

    /** Múltiples extras seleccionados en la reserva */
    public function extras(): BelongsToMany
    {
        return $this->belongsToMany(ExtraService::class, 'reservation_extra_service')
            ->withPivot(['quantity','unit_price','total_price'])
            ->withTimestamps();
    }

    public function payments(): HasMany { return $this->hasMany(Payment::class); }
    public function tickets(): HasMany { return $this->hasMany(Ticket::class); }
}
