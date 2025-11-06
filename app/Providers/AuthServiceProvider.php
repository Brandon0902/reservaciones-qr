<?php

namespace App\Providers;

use App\Enums\UserRole;
use App\Models\User;
use App\Models\Reservation;
use App\Policies\ReservationPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Mapea modelos → policies
     */
    protected $policies = [
        Reservation::class => ReservationPolicy::class,
    ];

    public function boot(): void
    {
        // Gate para áreas de administrador
        Gate::define('admin-only', function (?User $user): bool {
            if (!$user) return false;

            // Si es enum:
            if ($user->role instanceof UserRole) {
                return $user->role === UserRole::ADMIN;
            }

            // Si llega como string (fallback):
            return $user->role === 'admin';
        });
    }
}
