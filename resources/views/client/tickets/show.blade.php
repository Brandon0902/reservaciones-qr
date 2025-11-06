{{-- resources/views/client/tickets/show.blade.php --}}
<x-app-layout>
  <x-slot name="header">
    <div class="flex items-center justify-between">
      <div class="flex items-center gap-3">
        <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-[#6d28d9] text-white shadow">
          <svg viewBox="0 0 24 24" class="h-5 w-5"><path fill="currentColor" d="M12 2l7 4v6c0 5-3 8-7 10C8 20 5 17 5 12V6l7-4zM7 8v4c0 3 2 5 5 6c3-1 5-3 5-6V8l-5-3l-5 3z"/></svg>
        </span>
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100">
          Boleto #{{ $ticket->id }}
        </h2>
      </div>

      <div class="flex gap-2 print:hidden">
        <a href="{{ route('client.reservations.tickets.index', $reservation) }}"
           class="px-3 py-1.5 rounded-md bg-white/10 hover:bg-white/20 text-sm text-slate-100">
          ← Mis boletos
        </a>
        <a href="{{ route('client.tickets.download', $ticket) }}"
           class="px-3 py-1.5 rounded-md bg-[#6d28d9] hover:bg-[#6d28d9]/90 text-sm text-white">
          Descargar QR
        </a>
        <button onclick="window.print()"
                class="px-3 py-1.5 rounded-md border border-white/15 hover:bg-white/10 text-sm text-slate-100">
          Imprimir
        </button>
      </div>
    </div>
  </x-slot>

  <style>
    @media print {
      body { background: #fff !important; }
      nav, header, .print:hidden, .no-print { display:none !important; }
      .ticket-sheet { box-shadow:none !important; border:1px solid #ddd !important; }
    }
  </style>

  <div class="py-6 mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
    {{-- Tarjeta del boleto --}}
    <div class="ticket-sheet rounded-2xl bg-white/60 dark:bg-slate-900/70 backdrop-blur border border-white/10 shadow-xl overflow-hidden">
      {{-- Encabezado --}}
      <div class="p-6 sm:p-7 bg-gradient-to-r from-[#6d28d9]/10 via-transparent to-transparent">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
          <div>
            <div class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">Evento</div>
            <div class="text-2xl font-bold text-slate-900 dark:text-white">{{ $reservation->event_name }}</div>
          </div>
          <div class="flex items-center gap-2">
            @php
              $status = $ticket->status?->value ?? 'unused';
              $badge = [
                'unused'   => 'bg-emerald-500/15 text-emerald-300 border-emerald-500/20',
                'used'     => 'bg-sky-500/15 text-sky-300 border-sky-500/20',
                'expired'  => 'bg-amber-500/15 text-amber-300 border-amber-500/20',
                'canceled' => 'bg-rose-500/15 text-rose-300 border-rose-500/20',
              ][$status] ?? 'bg-slate-500/15 text-slate-300 border-slate-500/20';
            @endphp
            <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs border {{ $badge }}">
              <span class="h-1.5 w-1.5 rounded-full bg-current opacity-70"></span>
              {{ strtoupper($status) }}
            </span>
            <span class="hidden sm:inline text-xs text-slate-500 dark:text-slate-400">Boleto #{{ $ticket->id }}</span>
          </div>
        </div>
      </div>

      {{-- Cuerpo --}}
      <div class="p-6 sm:p-8 grid lg:grid-cols-2 gap-6">
        {{-- Datos del evento / boleto --}}
        <div class="space-y-4">
          <div class="rounded-xl border border-white/10 bg-white/40 dark:bg-white/5 p-4">
            <dl class="grid grid-cols-1 gap-3 text-sm">
              <div class="flex items-start justify-between gap-4">
                <dt class="text-slate-500 dark:text-slate-400">Fecha</dt>
                <dd class="font-medium text-slate-900 dark:text-slate-100">
                  {{ \Illuminate\Support\Carbon::parse($reservation->date)->isoFormat('dddd D [de] MMMM YYYY') }}
                </dd>
              </div>
              <div class="flex items-start justify-between gap-4">
                <dt class="text-slate-500 dark:text-slate-400">Turno</dt>
                <dd class="font-medium capitalize">
                  {{ $reservation->shift === 'night' ? 'Nocturno' : 'Matutino' }}
                  <span class="ml-1 text-slate-500 dark:text-slate-400">({{ $shiftRanges[$reservation->shift] ?? '' }})</span>
                </dd>
              </div>
              <div class="flex items-start justify-between gap-4">
                <dt class="text-slate-500 dark:text-slate-400">Mesa</dt>
                <dd class="font-semibold text-slate-900 dark:text-white">
                  {{ $ticket->id_mesa ? ('Mesa '.$ticket->id_mesa) : 'General' }}
                </dd>
              </div>
              <div class="flex items-start justify-between gap-4">
                <dt class="text-slate-500 dark:text-slate-400">Ubicación</dt>
                <dd class="text-right">
                  <div class="font-medium text-slate-900 dark:text-slate-100">Salón de eventos el Polvorín</div>
                  <div class="text-xs text-slate-500 dark:text-slate-400 leading-snug">
                    Dirección: Av. Jesus Michel Gonzalez 3232, Paseo del Prado, 45610 San Pedro Tlaquepaque, Jal.
                  </div>
                </dd>
              </div>
              @if($ticket->issued_at)
              <div class="flex items-start justify-between gap-4">
                <dt class="text-slate-500 dark:text-slate-400">Emitido</dt>
                <dd class="text-slate-900 dark:text-slate-100">
                  {{ \Illuminate\Support\Carbon::parse($ticket->issued_at)->isoFormat('DD/MM/YYYY HH:mm') }}
                </dd>
              </div>
              @endif
            </dl>
          </div>

          {{-- Nota / instrucciones --}}
          <div class="rounded-xl border border-emerald-500/20 bg-emerald-500/10 p-4 text-sm text-emerald-200">
            Presenta este QR en la entrada. Cada código es <span class="font-semibold">único</span> y
            se invalida al primer uso.
          </div>
        </div>

        {{-- QR grande --}}
        <div class="flex items-center justify-center">
          <div class="rounded-2xl border border-white/10 bg-white/60 dark:bg-white/5 p-4">
            <img src="{{ $qrUrl }}" alt="QR del boleto {{ $ticket->id }}"
                 class="block h-[340px] w-[340px] sm:h-[380px] sm:w-[380px] object-contain select-none"
                 draggable="false">
          </div>
        </div>
      </div>

      {{-- Pie y código de control (opcional) --}}
      <div class="px-6 sm:px-8 pb-6 sm:pb-8">
        <div class="mt-2 text-[11px] text-slate-500 dark:text-slate-400 text-center">
          Código de control: TKT-{{ str_pad((string)$ticket->id, 6, '0', STR_PAD_LEFT) }}
          • RES-{{ str_pad((string)$reservation->id, 6, '0', STR_PAD_LEFT) }}
        </div>
      </div>
    </div>

    {{-- Acciones secundarias --}}
    <div class="mt-6 flex justify-center gap-3 print:hidden">
      <a href="{{ route('client.reservations.tickets.index', $reservation) }}"
         class="px-3 py-2 rounded-lg bg-white/10 hover:bg-white/20 text-slate-100 text-sm">
        Ver todos mis boletos
      </a>
      <a href="{{ route('client.tickets.download', $ticket) }}"
         class="px-3 py-2 rounded-lg bg-[#6d28d9] hover:bg-[#6d28d9]/90 text-white text-sm">
        Descargar QR
      </a>
      <button onclick="window.print()"
              class="px-3 py-2 rounded-lg border border-white/15 hover:bg-white/10 text-slate-100 text-sm">
        Imprimir
      </button>
    </div>
  </div>
</x-app-layout>
