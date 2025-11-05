<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExtraService extends Model
{
    protected $table = 'extra_services';

    protected $fillable = [
        'name',
        'description',
        'day_price',
        'night_price',
    ];

    protected $casts = [
        'day_price'   => 'decimal:2',
        'night_price' => 'decimal:2',
    ];

    // Helpers de presentación
    public function getDayPriceMoneyAttribute(): string
    {
        return '$' . number_format((float)$this->day_price, 2, '.', ',');
    }

    public function getNightPriceMoneyAttribute(): string
    {
        return '$' . number_format((float)$this->night_price, 2, '.', ',');
    }
}
