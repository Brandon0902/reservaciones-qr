<x-app-layout>
  <x-slot name="header">
    <div class="flex items-center justify-between">
      <div class="flex items-center gap-3">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100">
          Subir comprobante de pago
        </h2>
        <span class="hidden sm:inline-flex items-center rounded-full bg-emerald-500/10 px-3 py-0.5 text-xs font-medium text-emerald-300 border border-emerald-400/30">
          Reserva: {{ $reservation->event_name }}
        </span>
      </div>

      <a href="{{ route('client.dashboard') }}"
         class="px-3 py-1.5 rounded-md bg-slate-200 text-slate-900 hover:bg-slate-300
                dark:bg-slate-700 dark:text-slate-100 dark:hover:bg-slate-600 text-sm">
        ← Volver
      </a>
    </div>
  </x-slot>

  {{-- Animaciones locales --}}
  <style>
    @keyframes float-card {
      0%, 100% { transform: translateY(0); }
      50%      { transform: translateY(-8px); }
    }
    @keyframes pulse-ring {
      0%   { transform: scale(0.9); opacity: 0.6; }
      70%  { transform: scale(1.25); opacity: 0; }
      100% { transform: scale(0.9); opacity: 0; }
    }
    .card-float {
      animation: float-card 5s ease-in-out infinite;
    }
    .ring-pulse::before {
      content: "";
      position: absolute;
      inset: 0;
      border-radius: 9999px;
      border: 2px solid rgba(45, 212, 191, 0.4);
      animation: pulse-ring 2.8s ease-out infinite;
    }
  </style>

  <div class="py-8 mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
    {{-- Avisos / errores --}}
    @if (session('warning'))
      <div class="mb-4 rounded-xl bg-amber-500/10 text-amber-100 px-4 py-3 border border-amber-500/40 flex items-start gap-3">
        <span class="mt-0.5">
          <svg class="w-4 h-4" viewBox="0 0 24 24"><path fill="currentColor" d="M12 2L2 20h20L12 2Zm0 4.8L18.2 18H5.8L12 6.8ZM11 10v4h2v-4h-2Zm0 5v2h2v-2h-2Z"/></svg>
        </span>
        <span class="text-sm">{{ session('warning') }}</span>
      </div>
    @endif

    @if ($errors->any())
      <div class="mb-4 rounded-xl bg-rose-500/10 text-rose-100 px-4 py-3 border border-rose-500/40">
        <div class="flex items-start gap-3">
          <svg class="w-4 h-4 mt-0.5" viewBox="0 0 24 24"><path fill="currentColor" d="M11 7h2v7h-2zm0 9h2v2h-2z"/><path fill="currentColor" d="M1 21h22L12 2L1 21zm3.47-2L12 5.3L19.53 19H4.47z"/></svg>
          <ul class="list-disc list-inside text-sm space-y-1">
            @foreach ($errors->all() as $e)
              <li>{{ $e }}</li>
            @endforeach
          </ul>
        </div>
      </div>
    @endif

    {{-- Contenedor principal --}}
    <div class="grid gap-8 lg:grid-cols-[minmax(0,1.25fr)_minmax(0,1fr)] items-start">
      {{-- Lado izquierdo: tarjeta animada + resumen --}}
      <div class="space-y-5">
        <div class="relative overflow-hidden rounded-2xl border border-emerald-400/30 bg-gradient-to-br from-slate-900 via-slate-900 to-slate-950 px-6 py-5 shadow-xl">
          <div class="absolute inset-x-0 -top-24 h-40 bg-[radial-gradient(circle_at_top,rgba(52,211,153,0.35),transparent)] pointer-events-none"></div>

          <div class="relative flex items-center gap-4">
            {{-- Icono / tarjeta animada --}}
            <div class="relative">
              <div class="ring-pulse absolute inset-0 rounded-full"></div>
              <div class="relative flex h-14 w-14 items-center justify-center rounded-full bg-emerald-500/20 border border-emerald-400/50 shadow-lg card-float">
                <svg class="w-7 h-7 text-emerald-300" viewBox="0 0 24 24">
                  <path fill="currentColor" d="M4 6a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v3H4V6Zm0 5h16v7a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-7Zm3 3v2h5v-2H7Z"/>
                  <path fill="currentColor" d="M9.75 15.75L8 14l-1.5 1.5L9.75 18l3.75-3.75L12 12.75z"/>
                </svg>
              </div>
            </div>

            <div class="relative">
              <p class="text-xs uppercase tracking-wide text-emerald-300/90 font-semibold">
                Paso final
              </p>
              <h1 class="mt-1 text-lg font-semibold text-slate-50">
                Sube tu comprobante para confirmar el evento
              </h1>
              <p class="mt-1 text-sm text-slate-300">
                En cuanto validemos el pago, tu reservación quedará confirmada y recibirás un correo con los detalles y tus accesos.
              </p>
            </div>
          </div>

          {{-- Resumen de la reserva --}}
          <div class="relative mt-4 rounded-xl bg-slate-900/70 border border-white/5 px-4 py-3 text-sm text-slate-200">
            <div class="flex items-center justify-between gap-3">
              <div>
                <div class="text-xs uppercase tracking-wide text-slate-400">Evento</div>
                <div class="font-medium">{{ $reservation->event_name }}</div>
              </div>
              <div class="text-right">
                <div class="text-xs uppercase tracking-wide text-slate-400">Fecha</div>
                <div class="font-medium">
                  {{ \Illuminate\Support\Carbon::parse($reservation->date)->format('d/m/Y') }}
                </div>
              </div>
            </div>
            <p class="mt-2 text-xs text-slate-400">
              Tienes <span class="font-semibold text-emerald-300">12 horas</span> a partir de la creación de la reserva para subir tu comprobante.
            </p>
          </div>
        </div>
      </div>

      {{-- Lado derecho: formulario --}}
      <div class="rounded-2xl border border-white/10 bg-white/5 px-5 py-5 text-slate-100 shadow-lg">
        <h3 class="text-sm font-semibold text-slate-200 mb-3 flex items-center gap-2">
          <svg class="w-4 h-4 text-violet-300" viewBox="0 0 24 24"><path fill="currentColor" d="M3 5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v3H3V5Zm0 5h18v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-9Zm3 4v2h5v-2H6Z"/></svg>
          Datos del pago
        </h3>

        <form method="POST"
              action="{{ route('client.payments.proof.store', $reservation) }}"
              enctype="multipart/form-data"
              class="space-y-4">
          @csrf

          {{-- Método de pago --}}
          <div>
            <label class="block text-slate-300 text-sm font-medium mb-1">Método de pago</label>
            <div class="flex flex-wrap gap-3">
              <label class="inline-flex items-center gap-2 rounded-xl border border-white/10 bg-slate-900/40 px-3 py-1.5 text-sm cursor-pointer hover:border-violet-400/60">
                <input type="radio" name="method" value="deposit" class="accent-violet-600">
                Depósito
              </label>
              <label class="inline-flex items-center gap-2 rounded-xl border border-white/10 bg-slate-900/40 px-3 py-1.5 text-sm cursor-pointer hover:border-violet-400/60">
                <input type="radio" name="method" value="transfer" class="accent-violet-600">
                Transferencia
              </label>
            </div>
          </div>

          {{-- Archivo --}}
          <div>
            <label for="receipt" class="block text-slate-300 text-sm font-medium mb-1">
              Comprobante <span class="text-xs text-slate-400">(PDF / JPG / PNG)</span>
            </label>
            <div class="flex items-center gap-3">
              <input id="receipt" name="receipt" type="file"
                     accept=".pdf,.jpg,.jpeg,.png"
                     class="block w-full rounded-xl border border-white/15 bg-slate-900/60 px-3 py-2 text-sm file:mr-3 file:rounded-lg file:border-0 file:bg-violet-600 file:px-3 file:py-1.5 file:text-xs file:font-medium file:text-white hover:border-violet-400/70">
            </div>
            <p class="mt-1 text-xs text-slate-400">
              Asegúrate de que los datos del monto, fecha y referencia sean legibles.
            </p>
          </div>

          {{-- Notas --}}
          <div>
            <label for="notes" class="block text-slate-300 text-sm font-medium mb-1">
              Notas para el equipo (opcional)
            </label>
            <textarea id="notes" name="notes" rows="3"
                      class="block w-full rounded-xl border border-white/10 bg-slate-900/60 px-3 py-2 text-sm focus:border-violet-400/70 focus:ring-0"></textarea>
            <p class="mt-1 text-xs text-slate-400">
              Puedes indicarnos, por ejemplo, el nombre del titular de la cuenta desde la que realizaste el pago.
            </p>
          </div>

          <div class="flex items-center justify-between pt-1 gap-3">
            <p class="text-[11px] text-slate-400">
              Al enviar tu comprobante iniciaremos la revisión. Te avisaremos por correo cuando el pago sea aprobado.
            </p>
            <x-primary-button class="rounded-xl px-5">
              Enviar comprobante
            </x-primary-button>
          </div>
        </form>
      </div>
    </div>
  </div>
</x-app-layout>
