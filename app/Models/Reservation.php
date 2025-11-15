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
        'event_name',       // nombre del evento
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
        'extra_service_id', // legacy, opcional (un solo extra)
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

    /** Cliente dueño de la reservación */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Extra "legacy" (una sola columna extra_service_id).
     * Lo puedes ir eliminando cuando ya no lo uses.
     */
    public function extraService(): BelongsTo
    {
        return $this->belongsTo(ExtraService::class);
    }

    /**
     * Relación base para múltiples servicios extra a través de la tabla pivote.
     * Tabla pivote: reservation_extra_service
     * Columnas pivote: reservation_id, extra_service_id, quantity, unit_price, total_price
     */
    protected function extraServicesRelation(): BelongsToMany
    {
        return $this->belongsToMany(ExtraService::class, 'reservation_extra_service')
            ->withPivot(['quantity', 'unit_price', 'total_price'])
            ->withTimestamps();
    }

    /**
     * Nombre "nuevo" que estamos usando en las vistas/controladores:
     * $reservation->extraServices
     */
    public function extraServices(): BelongsToMany
    {
        return $this->extraServicesRelation();
    }

    /**
     * Alias de compatibilidad si en algún lugar aún usas $reservation->extras
     */
    public function extras(): BelongsToMany
    {
        return $this->extraServicesRelation();
    }

    /** Pagos ligados a la reservación */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /** Boletos generados para la reservación */
    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }
}
