{{-- resources/views/auth/register.blade.php --}}
<x-guest-layout>
    <form method="POST" action="{{ route('register', request()->only('next')) }}">
        @csrf
        <input type="hidden" name="next" value="{{ request('next') }}">

        {{-- Nombre completo --}}
        <div>
            <x-input-label for="full_name" value="Nombre completo" />
            <x-text-input id="full_name" class="block mt-1 w-full"
                          type="text" name="full_name" :value="old('full_name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('full_name')" class="mt-2" />
        </div>

        {{-- Email --}}
        <div class="mt-4">
            <x-input-label for="email" value="Email" />
            <x-text-input id="email" class="block mt-1 w-full"
                          type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        {{-- Teléfono (opcional) --}}
        <div class="mt-4">
            <x-input-label for="phone" value="Teléfono (opcional)" />
            <x-text-input id="phone" class="block mt-1 w-full"
                          type="text" name="phone" :value="old('phone')" autocomplete="tel" />
            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
        </div>

        {{-- Password --}}
        <div class="mt-4">
            <x-input-label for="password" value="Contraseña" />
            <x-text-input id="password" class="block mt-1 w-full"
                          type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        {{-- Confirmación --}}
        <div class="mt-4">
            <x-input-label for="password_confirmation" value="Confirmar contraseña" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                          type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between mt-6">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
               href="{{ route('login', request()->only('next')) }}">
                ¿Ya tienes cuenta? Inicia sesión
            </a>

            <x-primary-button class="ms-4">
                Crear cuenta
            </x-primary-button>
        </div>

        @if(request('next'))
          <p class="mt-3 text-xs text-gray-500">
            Al registrarte te enviaremos directamente a completar tu reservación.
          </p>
        @endif
    </form>
</x-guest-layout>
