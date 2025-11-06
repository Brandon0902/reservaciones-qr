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
            'full_name' => $request->string('full_name'),
            'email'     => $request->string('email'),
            'phone'     => $request->string('phone'),
            'role'      => UserRole::CUSTOMER,
            'password'  => Hash::make($request->string('password')),
        ]);

        event(new Registered($user));

        Auth::login($user);

        // Fallback de seguridad: si no hay intended, ve al dashboard cliente
        $fallback = route('client.dashboard', absolute: false);

        return redirect()->intended($fallback);
    }
}
