<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view. If "next" is present, set intended URL.
     */
    public function create(Request $request): View
    {
        if ($next = $request->query('next')) {
            $request->session()->put('url.intended', $next);
        }
        return view('auth.register', ['next' => $request->query('next')]);
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'email'     => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'phone'     => ['nullable', 'string', 'max:30'],
            'password'  => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Registro público -> rol por defecto: CUSTOMER
        $user = User::create([
            'full_name' => (string) $request->string('full_name'),
            'email'     => (string) $request->string('email'),
            'phone'     => (string) $request->string('phone'),
            'role'      => UserRole::CUSTOMER,
            'password'  => Hash::make((string) $request->string('password')),
        ]);

        event(new Registered($user));
        Auth::login($user);

        // Respetar "intended" solo si apunta a /client/*
        $intended = $request->session()->pull('url.intended');
        $path     = $intended ? (parse_url($intended, PHP_URL_PATH) ?? '') : '';
        if ($intended && str_starts_with($path, '/client')) {
            return redirect()->to($intended);
        }

        // Fallback: Home (ya no mandamos a client.dashboard)
        return redirect()->to(route('home', absolute: false));
    }
}
