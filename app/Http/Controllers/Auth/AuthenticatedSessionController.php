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
            // Guardamos la URL a la que queremos volver tras login (solo si viene explícita)
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
        if (($user->role instanceof UserRole && $user->role === UserRole::VALIDATOR) || $user->role === 'validator') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()->withErrors([
                'email' => 'Tu cuenta de validador no tiene acceso a la aplicación web.',
            ])->onlyInput('email');
        }

        $isAdmin = ($user->role instanceof UserRole && $user->role === UserRole::ADMIN) || $user->role === 'admin';

        if ($isAdmin) {
            // ⚠️ IMPORTANTE: ignorar cualquier intended previo y limpiar la sesión
            $request->session()->forget('url.intended');
            return redirect()->to(route('admin.dashboard', absolute: false));
        }

        // Cliente (u otro rol permitido):
        // Respetar "intended" solo si es una URL interna hacia /client/*
        $intended = $request->session()->pull('url.intended');
        $path = $intended ? (parse_url($intended, PHP_URL_PATH) ?? '') : '';
        if ($intended && str_starts_with($path, '/client')) {
            return redirect()->to($intended);
        }

        // Fallback: al home (welcome). Ya NO mandamos a client.dashboard.
        return redirect()->to(route('home', absolute: false));
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
