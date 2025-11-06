<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Show login view. If "next" is present, set intended URL.
     */
    public function create(Request $request): View
    {
        if ($next = $request->query('next')) {
            // Guardamos la URL a la que queremos volver tras login
            $request->session()->put('url.intended', $next);
        }
        return view('auth.login', ['next' => $request->query('next')]);
    }

    /**
     * Handle login.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        $user = Auth::user();

        // Bloquear a VALIDATOR
        if ($user->role === UserRole::VALIDATOR || $user->role === 'validator') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()->withErrors([
                'email' => 'Tu cuenta de validador no tiene acceso a la aplicación web.',
            ]);
        }

        // Fallback según rol, pero respetando la URL intended (si existe)
        $fallback = match ($user->role) {
            UserRole::ADMIN    => route('admin.dashboard', absolute: false),
            UserRole::CUSTOMER => route('client.dashboard', absolute: false),
            default            => route('dashboard', absolute: false),
        };

        return redirect()->intended($fallback);
    }

    /**
     * Logout.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
