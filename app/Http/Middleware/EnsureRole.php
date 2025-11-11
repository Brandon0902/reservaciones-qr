<?php
// app/Http/Middleware/EnsureRole.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureRole
{
    public function handle(Request $request, Closure $next, string $role)
    {
        $user = $request->user();
        if (!$user) return response()->json(['message' => 'No autenticado.'], 401);

        $userRole = $user->role instanceof \App\Enums\UserRole ? $user->role->value : $user->role;
        if ($userRole !== $role) {
            return response()->json(['message' => 'No autorizado.'], 403);
        }

        return $next($request);
    }
}
