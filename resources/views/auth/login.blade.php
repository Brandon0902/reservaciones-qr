{{-- resources/views/auth/login.blade.php --}}
@php
  $next = request('next');
@endphp

<x-guest-layout>
  {{-- Status de sesión (Breeze) --}}
  <x-auth-session-status class="mb-4" :status="session('status')" />

  <div class="max-w-md mx-auto">
    {{-- Encabezado / Branding --}}
    <div class="text-center mb-6">
      <a href="{{ route('home') }}" class="inline-flex items-center gap-2">
        <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-[#6d28d9] text-white shadow">
          <svg viewBox="0 0 24 24" class="h-5 w-5"><path fill="currentColor" d="M12 2l7 4v6c0 5-3 8-7 10C8 20 5 17 5 12V6l7-4zM7 8v4c0 3 2 5 5 6c3-1 5-3 5-6V8l-5-3l-5 3z"/></svg>
        </span>
        <div class="text-left">
          <div class="font-semibold leading-tight">Salón de eventos el Polvorín</div>
          <div class="text-xs text-gray-500">Reservaciones & QR</div>
        </div>
      </a>
      <h1 class="mt-4 text-2xl font-bold tracking-tight">Inicia sesión</h1>
      <p class="mt-1 text-sm text-gray-500">
        Accede para continuar con tu reservación.
      </p>
    </div>

    {{-- Tarjeta --}}
    <div class="rounded-2xl border border-white/10 bg-white/5 dark:bg-gray-900/60 p-6 shadow-lg">
      <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf
        {{-- Mantener el next (por si el controlador lo quiere leer en futuro) --}}
        @if($next)
          <input type="hidden" name="next" value="{{ $next }}">
        @endif

        {{-- Email --}}
        <div>
          <x-input-label for="email" value="Email" />
          <x-text-input id="email" class="block mt-1 w-full"
                        type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
          <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        {{-- Password --}}
        <div>
          <x-input-label for="password" value="Contraseña" />
          <x-text-input id="password" class="block mt-1 w-full"
                        type="password" name="password" required autocomplete="current-password" />
          <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        {{-- Remember + Forgot --}}
        <div class="flex items-center justify-between pt-1">
          <label for="remember_me" class="inline-flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300">
            <input id="remember_me" type="checkbox"
                   class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                   name="remember">
            Recuérdame
          </label>

          @if (Route::has('password.request'))
            <a class="text-sm text-indigo-600 hover:text-indigo-500"
               href="{{ route('password.request') }}">
              ¿Olvidaste tu contraseña?
            </a>
          @endif
        </div>

        {{-- CTA principal --}}
        <x-primary-button class="w-full justify-center">
          Iniciar sesión
        </x-primary-button>
      </form>

      {{-- Divider --}}
      <div class="flex items-center gap-3 my-6">
        <div class="h-px w-full bg-white/10"></div>
        <span class="text-xs text-gray-500">o</span>
        <div class="h-px w-full bg-white/10"></div>
      </div>

      {{-- CTA de registro (con next) --}}
      <a href="{{ route('register', request()->only('next')) }}"
         class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-white/15 px-4 py-2.5
                text-sm font-medium hover:bg-white/5 transition">
        ¿No tienes cuenta? <span class="font-semibold text-indigo-400">Registrarme ahora</span>
      </a>

      @if($next)
        <p class="mt-3 text-xs text-gray-500 text-center">
          Al crear tu cuenta te enviaremos directamente a completar tu reservación.
        </p>
      @endif
    </div>
  </div>
</x-guest-layout>
