<?php

namespace App\Models;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'reservation_id',
        'method',               // enum: deposit | transfer
        'amount',
        'currency',
        'status',               // enum: created | pending | approved | paid | rejected | refunded
        'payment_due_at',
        'txn_ref',
        'receipt_ref',
        'receipt_uploaded_at',
        'approved_by',
        'approved_at',
        'paid_at',
        'notes',
    ];

    protected $casts = [
        'payment_due_at'      => 'datetime',
        'receipt_uploaded_at' => 'datetime',
        'approved_at'         => 'datetime',
        'paid_at'             => 'datetime',
        'method'              => PaymentMethod::class,
        'status'              => PaymentStatus::class,
    ];

    /* ========= Relaciones ========= */

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
