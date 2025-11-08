{{-- resources/views/layouts/navigation.blade.php --}}
@php
  use Illuminate\Support\Facades\Auth;
  use App\Enums\UserRole;

  $me = Auth::user();
  $displayName = $me?->full_name ?: ($me?->name ?: ($me?->email ?? 'Invitado'));

  $isAdmin = $me && (
      ($me->role instanceof UserRole && $me->role === UserRole::ADMIN)
      || $me->role === 'admin'
  );

  $clientCreate = route('client.reservations.create');

  $reserveUrl = Auth::check()
      ? ($isAdmin ? route('admin.dashboard') : $clientCreate)
      : route('login', ['next' => $clientCreate]);

  $loginUrl    = route('login');
  $registerUrl = route('register');
@endphp

<nav x-data="{ open: false }" class="sticky top-0 z-40 border-b border-white/10 bg-slate-950/70 backdrop-blur">
  <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between h-16">
      <div class="flex items-center gap-8">
        <a href="{{ route('home') }}" class="flex items-center gap-3 group">
          <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-[#6d28d9] text-white shadow">
            <svg viewBox="0 0 24 24" class="h-5 w-5"><path fill="currentColor" d="M12 2l7 4v6c0 5-3 8-7 10C8 20 5 17 5 12V6l7-4zM7 8v4c0 3 2 5 5 6c3-1 5-3 5-6V8l-5-3l-5 3z"/></svg>
          </span>
          <div class="leading-tight">
            <div class="font-semibold -mb-1 text-slate-100">Salón de eventos el Polvorín</div>
            <div class="text-xs text-slate-400">Reservaciones &amp; QR</div>
          </div>
        </a>

        <div class="hidden sm:flex items-center gap-4">
          <a href="{{ route('home') }}#precios" class="px-3 py-2 text-sm rounded-md hover:bg-white/5 text-slate-300">Precios</a>
          <a href="{{ route('home') }}#extras"  class="px-3 py-2 text-sm rounded-md hover:bg-white/5 text-slate-300">Servicios extra</a>
          <a href="{{ route('home') }}#faq"     class="px-3 py-2 text-sm rounded-md hover:bg-white/5 text-slate-300">FAQ</a>
        </div>
      </div>

      <div class="hidden sm:flex items-center gap-3">
        @auth
          @unless($isAdmin)
            <a href="{{ route('client.reservations.my') }}"
               class="inline-flex items-center gap-2 rounded-xl border border-white/10 hover:bg-white/5 px-4 py-2 text-sm text-slate-200">
              Mis reservaciones
            </a>
          @endunless
        @endauth

        <a href="{{ $reserveUrl }}"
           class="hidden md:inline-flex items-center gap-2 rounded-xl bg-[#6d28d9] hover:bg-[#6d28d9]/90 px-4 py-2 text-sm font-medium text-white">
          Reservar
        </a>

        @auth
          <div class="relative">
            <x-dropdown align="right" width="56">
              <x-slot name="trigger">
                <button class="inline-flex items-center gap-3 px-3 py-2 rounded-xl bg-white/5 text-sm text-slate-200 hover:bg-white/10">
                  <div class="text-left">
                    <div class="leading-tight font-medium">{{ $displayName }}</div>
                    <div class="text-[11px] text-slate-400">{{ $me->email }}</div>
                  </div>
                  <svg class="h-4 w-4 text-slate-300" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                    <path fill="currentColor" d="M5.3 7.3a1 1 0 0 1 1.4 0L10 10.6l3.3-3.3a1 1 0 1 1 1.4 1.4L10 13.4L5.3 8.7a1 1 0 0 1 0-1.4z"/>
                  </svg>
                </button>
              </x-slot>

              <x-slot name="content">
                <form method="POST" action="{{ route('logout') }}">
                  @csrf
                  <x-dropdown-link :href="route('logout')"
                    onclick="event.preventDefault(); this.closest('form').submit();">
                    Cerrar sesión
                  </x-dropdown-link>
                </form>
              </x-slot>
            </x-dropdown>
          </div>
        @endauth

        @guest
          <a href="{{ $loginUrl }}" class="px-3 py-2 text-sm rounded-xl bg-white/5 hover:bg-white/10 text-slate-200">Acceder</a>
          <a href="{{ $registerUrl }}" class="px-3 py-2 text-sm rounded-xl border border-white/10 hover:bg-white/5 text-slate-200">Crear cuenta</a>
        @endguest
      </div>

      <div class="-me-2 flex items-center sm:hidden">
        <button @click="open = ! open"
                class="inline-flex items-center justify-center p-2 rounded-md text-slate-300 hover:text-white hover:bg-white/10 focus:outline-none transition">
          <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
            <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex"
                  stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M4 6h16M4 12h16M4 18h16" />
            <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden"
                  stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
    </div>
  </div>

  <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden border-t border-white/10 bg-slate-950/90 backdrop-blur">
    <div class="px-4 pt-3 pb-4 space-y-1">
      <a href="{{ route('home') }}#precios" class="block px-3 py-2 text-slate-300 hover:bg-white/5 rounded">Precios</a>
      <a href="{{ route('home') }}#extras"  class="block px-3 py-2 text-slate-300 hover:bg-white/5 rounded">Servicios extra</a>
      <a href="{{ route('home') }}#faq"     class="block px-3 py-2 text-slate-300 hover:bg-white/5 rounded">FAQ</a>

      @auth
        @unless($isAdmin)
          <a href="{{ route('client.reservations.my') }}" class="block px-3 py-2 text-slate-100 bg-white/5 hover:bg-white/10 rounded">
            Mis reservaciones
          </a>
        @endunless
      @endauth

      <a href="{{ $reserveUrl }}" class="block px-3 py-2 text-slate-100 bg-[#6d28d9]/80 hover:bg-[#6d28d9] rounded">
        Reservar
      </a>
    </div>

    <div class="border-t border-white/10 px-4 py-3">
      @auth
        <div class="mb-2">
          <div class="font-medium text-slate-100">{{ $displayName }}</div>
          <div class="text-sm text-slate-400">{{ $me->email }}</div>
        </div>
        <div class="space-y-1">
          <form method="POST" action="{{ route('logout') }}"> @csrf
            <button type="submit" class="w-full text-left px-3 py-2 rounded hover:bg-white/5">Cerrar sesión</button>
          </form>
        </div>
      @endauth

      @guest
        <div class="space-y-2">
          <a href="{{ $loginUrl }}"    class="block px-3 py-2 rounded bg-white/5 hover:bg-white/10 text-slate-200">Acceder</a>
          <a href="{{ $registerUrl }}" class="block px-3 py-2 rounded border border-white/10 hover:bg-white/5 text-slate-200">Crear cuenta</a>
        </div>
      @endguest
    </div>
  </div>
</nav>
