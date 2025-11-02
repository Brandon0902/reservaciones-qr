<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminOnly
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        $isAdmin = $user && (
            ($user->role instanceof UserRole && $user->role === UserRole::ADMIN)
            || ($user->role === 'admin') // por si el cast no aplicara
        );

        abort_unless($isAdmin, 403, 'This action is unauthorized.');

        return $next($request);
    }
}
