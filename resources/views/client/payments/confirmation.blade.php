<x-app-layout>
  <x-slot name="header">
    <div class="flex items-center justify-between">
      <div>
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100">
          Pago en revisión
        </h2>
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
          Estamos validando tu comprobante de pago.
        </p>
      </div>

      <a href="{{ route('client.reservations.my') }}"
         class="px-3 py-1.5 rounded-md bg-slate-200 text-slate-900 hover:bg-slate-300
                dark:bg-slate-700 dark:text-slate-100 dark:hover:bg-slate-600 text-sm">
        Ver mis reservaciones
      </a>
    </div>
  </x-slot>

  {{-- Animaciones locales --}}
  <style>
    @keyframes pop-check {
      0%   { transform: scale(0); opacity: 0; }
      60%  { transform: scale(1.1); opacity: 1; }
      100% { transform: scale(1); opacity: 1; }
    }
    @keyframes float-card {
      0%, 100% { transform: translateY(0); }
      50%      { transform: translateY(-6px); }
    }
    @keyframes pulse-ring {
      0%   { transform: scale(0.9); opacity: 0.6; }
      70%  { transform: scale(1.3); opacity: 0; }
      100% { transform: scale(0.9); opacity: 0; }
    }
    .check-pop    { animation: pop-check 0.7s ease-out forwards; }
    .card-float   { animation: float-card 6s ease-in-out infinite; }
    .ring-pulse::before {
      content: "";
      position: absolute;
      inset: 0;
      border-radius: 9999px;
      border: 2px solid rgba(52, 211, 153, 0.5);
      animation: pulse-ring 3s ease-out infinite;
    }
  </style>

  <div class="py-10 mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
    @if (session('success'))
      <div class="mb-5 inline-flex items-center gap-2 rounded-full bg-emerald-500/10 px-4 py-1.5 text-xs font-medium text-emerald-200 border border-emerald-500/40">
        <svg class="w-4 h-4" viewBox="0 0 24 24">
          <path fill="currentColor" d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
        </svg>
        <span>{{ session('success') }}</span>
      </div>
    @endif

    <div class="relative overflow-hidden rounded-3xl border border-emerald-400/30 bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950 px-6 sm:px-10 py-8 shadow-2xl">
      {{-- Glow superior --}}
      <div class="absolute inset-x-0 -top-28 h-40 bg-[radial-gradient(circle_at_top,rgba(52,211,153,0.35),transparent)] pointer-events-none"></div>

      <div class="relative flex flex-col items-center text-center gap-4">
        {{-- Check animado --}}
        <div class="relative mb-2">
          <div class="ring-pulse absolute inset-0 rounded-full"></div>
          <div class="relative flex h-20 w-20 items-center justify-center rounded-full bg-emerald-500/20 border border-emerald-400/70 shadow-xl card-float">
            <div class="check-pop flex h-14 w-14 items-center justify-center rounded-full bg-emerald-500 text-slate-950">
              <svg class="w-8 h-8" viewBox="0 0 24 24">
                <path fill="currentColor" d="M9 16.17L4.83 12 3.41 13.41 9 19 21 7l-1.41-1.41z"/>
              </svg>
            </div>
          </div>
        </div>

        <div>
          <h1 class="text-2xl sm:text-3xl font-semibold text-slate-50">
            ¡Comprobante recibido!
          </h1>
          <p class="mt-2 text-sm sm:text-base text-slate-300 max-w-xl">
            Tu pago quedó <span class="font-semibold text-emerald-200">pendiente de validación</span>.
            Nuestro equipo revisará el comprobante y te avisaremos por correo cuando sea aprobado.
          </p>
        </div>

        {{-- Resumen de la reserva --}}
        <div class="mt-5 w-full max-w-xl rounded-2xl bg-slate-900/70 border border-white/10 px-5 py-4 text-left text-sm text-slate-100">
          <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
              <div class="text-xs uppercase tracking-wide text-slate-400">Reservación</div>
              <div class="font-medium">{{ $reservation->event_name }}</div>
            </div>
            <div class="sm:text-right">
              <div class="text-xs uppercase tracking-wide text-slate-400">Fecha del evento</div>
              <div class="font-medium">
                {{ \Illuminate\Support\Carbon::parse($reservation->date)->format('d/m/Y') }}
              </div>
            </div>
          </div>
          <p class="mt-3 text-xs text-slate-400">
            Si detectamos algún detalle con tu comprobante, te contactaremos por el correo asociado a tu cuenta.
          </p>
        </div>

        {{-- Acciones --}}
        <div class="mt-6 flex flex-wrap items-center justify-center gap-3">
          <a href="{{ route('client.reservations.my') }}"
             class="inline-flex items-center gap-2 rounded-xl bg-emerald-500 px-5 py-2.5 text-sm font-medium text-slate-950 hover:bg-emerald-400 transition">
            Ver estado de mi reservación
            <svg class="w-4 h-4" viewBox="0 0 24 24">
              <path fill="currentColor" d="M12 4a8 8 0 1 0 8 8a8.01 8.01 0 0 0-8-8Zm1 11h-2v-4h2Zm0-6h-2V7h2Z"/>
            </svg>
          </a>

          <a href="{{ route('home') }}"
             class="inline-flex items-center gap-2 rounded-xl border border-white/15 px-5 py-2.5 text-sm text-slate-200 hover:bg-white/5 transition">
            Volver al inicio
          </a>
        </div>
      </div>
    </div>
  </div>
</x-app-layout>
