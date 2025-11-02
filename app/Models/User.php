<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Mass-assignable attributes.
     *
     * @var list<string>
     */
    protected $fillable = [
        'full_name',
        'email',
        'phone',
        'role',     // enum: admin | validator | customer
        'password',
    ];

    /**
     * Hidden attributes for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Attribute casting.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'role'              => UserRole::class,
        ];
    }

    /* ========= Relaciones ========= */

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function approvedPayments(): HasMany
    {
        return $this->hasMany(Payment::class, 'approved_by');
    }

    /* ========== Helpers ========== */

    public function isAdmin(): bool
    {
        return $this->role === UserRole::ADMIN;
    }
}
