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
     * Show login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle login.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        $user = Auth::user();

        // Bloquear completamente a VALIDATOR
        if ($user->role === UserRole::VALIDATOR || $user->role === 'validator') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()->withErrors([
                'email' => 'Tu cuenta de validador no tiene acceso a la aplicación web.',
            ]);
        }

        // Redirecciones permitidas
        return match ($user->role) {
            UserRole::ADMIN      => redirect()->intended(route('admin.dashboard', absolute: false)),
            UserRole::CUSTOMER   => redirect()->intended(route('client.dashboard', absolute: false)),
            default              => redirect()->intended(route('dashboard', absolute: false)),
        };
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
