<?php

namespace App\Providers;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [];

    public function boot(): void
    {
        Gate::define('admin-only', function (?User $user): bool {
            if (!$user) return false;

            // Si es enum:
            if ($user->role instanceof UserRole) {
                return $user->role === UserRole::ADMIN;
            }

            // Si llega como string (por cualquier motivo):
            return $user->role === 'admin';
        });
    }
}
