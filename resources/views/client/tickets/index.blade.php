{{-- resources/views/client/tickets/index.blade.php --}}
<x-app-layout>
  <x-slot name="header">
    <div class="flex items-center justify-between">
      <div>
        <div class="text-sm text-gray-500 dark:text-gray-400">Reservación #{{ $reservation->id }}</div>
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
          Mis boletos — {{ $reservation->event_name ?: 'Evento' }}
        </h2>
      </div>
      <a href="{{ route('client.reservations.my') }}"
         class="px-3 py-1.5 rounded-md bg-slate-200 text-slate-900 hover:bg-slate-300
                dark:bg-slate-700 dark:text-slate-100 dark:hover:bg-slate-600 text-sm">
        ← Mis reservaciones
      </a>
    </div>
  </x-slot>

  {{-- Estilos pequeños para la miniatura/stack de boletos --}}
  <style>
    .ticket-thumb {
      backdrop-filter: blur(2px);
    }
    .stack-layer {
      transform: rotate(-2deg);
      opacity: .6;
    }
    .stack-layer-2 {
      transform: rotate(2deg);
      opacity: .4;
    }
  </style>

  <div class="py-6 mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 space-y-6"
       x-data="{
          openMesa: null,
          toggle(m){ this.openMesa = (this.openMesa===m ? null : m); },
          isOpen(m){ return this.openMesa===m; }
       }">

    {{-- Encabezado con info del evento --}}
    <div class="rounded-2xl border border-white/10 bg-white/5 dark:bg-gray-900/50 p-5">
      <div class="grid sm:grid-cols-2 gap-4">
        <div>
          <div class="text-sm text-gray-400">Evento</div>
          <div class="text-lg font-semibold">{{ $reservation->event_name ?: 'Evento' }}</div>
        </div>
        <div>
          <div class="text-sm text-gray-400">Fecha & Horario</div>
          <div class="text-lg font-semibold">
            {{ optional($reservation->date)->format('d/m/Y') }}
            • {{ $reservation->shift === 'day' ? 'DÍA' : 'NOCHE' }}
            <span class="text-sm text-gray-400">({{ $shiftRanges[$reservation->shift] ?? '—' }})</span>
          </div>
        </div>
        <div class="sm:col-span-2">
          <div class="text-sm text-gray-400">Ubicación</div>
          <div class="text-base font-medium">{{ $address }}</div>
        </div>
      </div>

      <div class="mt-3 text-sm text-gray-400">
        Nota: Los boletos se encuentran <strong>agrupados por mesa</strong> (10 por mesa). Toca una mesa para ver sus boletos y presenta el QR en el acceso.
      </div>
    </div>

    {{-- Acordeón de mesas con miniatura/stack vistoso --}}
    @forelse($grouped as $mesa => $tickets)
      @php
        $mesaKey = 'mesa_'.$mesa;
        $preview = $tickets->first();
        $previewUrl = $preview && $preview->qr_path ? Storage::disk('tickets')->url($preview->qr_path) : null;
        $previewToken = $preview ? \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($preview->token, 0, 6)) : '—';
      @endphp

      <div class="rounded-3xl border border-white/10 bg-gradient-to-br from-slate-900 to-slate-950 text-slate-100 shadow-xl overflow-hidden">

        {{-- Header clicable con mini-boleto + stack --}}
        <button type="button"
                class="w-full relative px-5 py-4 transition group"
                @click="toggle('{{ $mesaKey }}')"
                :aria-expanded="isOpen('{{ $mesaKey }}')"
                aria-controls="panel-{{ $mesaKey }}">
          {{-- capas decorativas (stack) --}}
          <div class="absolute inset-0 pointer-events-none">
            <div class="absolute right-6 top-2 h-16 w-28 rounded-xl bg-white/5 border border-white/10 stack-layer"></div>
            <div class="absolute right-10 top-3 h-16 w-28 rounded-xl bg-white/5 border border-white/10 stack-layer-2"></div>
          </div>

          <div class="relative flex items-center justify-between">
            <div class="flex items-center gap-4">
              <span class="inline-flex h-11 w-11 items-center justify-center rounded-2xl bg-[#6d28d9] text-white shadow">
                <svg viewBox="0 0 24 24" class="h-5 w-5"><path fill="currentColor" d="M12 2l7 4v6c0 5-3 8-7 10C8 20 5 17 5 12V6l7-4zM7 8v4c0 3 2 5 5 6c3-1 5-3 5-6V8l-5-3l-5 3z"/></svg>
              </span>

              <div>
                <div class="text-2xl font-extrabold leading-none">Mesa {{ $mesa }}</div>
                <div class="text-sm text-slate-300">Boletos en este grupo: {{ $tickets->count() }}</div>
              </div>
            </div>

            {{-- Miniatura del boleto (preview) --}}
            <div class="hidden sm:flex items-center gap-3 pr-12">
              <div class="ticket-thumb flex items-center gap-3 rounded-2xl border border-white/10 bg-white/5 px-3 py-2">
                <div class="rounded-lg bg-white p-1.5">
                  @if($previewUrl)
                    <img src="{{ $previewUrl }}" alt="QR" class="h-12 w-12 object-contain">
                  @else
                    <div class="h-12 w-12 grid place-items-center text-xs text-slate-400">QR</div>
                  @endif
                </div>
                <div class="leading-tight">
                  <div class="text-[11px] text-slate-400">Boleto</div>
                  <div class="font-mono text-sm">{{ $previewToken }}</div>
                  <div class="text-[11px] text-slate-400">
                    {{ optional($reservation->date)->format('d/m/Y') }} • {{ $reservation->shift === 'day' ? 'Día' : 'Noche' }}
                  </div>
                </div>
              </div>
            </div>

            {{-- Chevron animado --}}
            <div class="absolute right-4 top-1/2 -translate-y-1/2 flex items-center gap-2">
              <span class="hidden md:inline text-xs text-slate-300">ver {{ $tickets->count() }} boletos</span>
              <svg class="h-5 w-5 text-slate-200 transition-transform duration-300"
                   :class="isOpen('{{ $mesaKey }}') ? 'rotate-180' : 'rotate-0'"
                   viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z" clip-rule="evenodd"/>
              </svg>
            </div>
          </div>
        </button>

        {{-- Panel con transición (grid de boletos) --}}
        <div id="panel-{{ $mesaKey }}"
             x-show="isOpen('{{ $mesaKey }}')"
             x-transition:enter="transition ease-out duration-250"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             class="px-5 pb-6 pt-1 border-t border-white/10">
          <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($tickets as $t)
              @php
                $url = $t->qr_path ? Storage::disk('tickets')->url($t->qr_path) : null;
              @endphp

              {{-- Tarjeta-boleto (sin estado visible) --}}
              <div class="rounded-2xl overflow-hidden border border-white/10 bg-gradient-to-br from-slate-900 to-slate-950 text-slate-100 shadow-xl relative">
                {{-- branding / header --}}
                <div class="px-5 pt-5 pb-3 border-b border-white/10 flex items-center justify-between">
                  <div class="flex items-center gap-3">
                    <span class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-[#6d28d9] text-white shadow">
                      <svg viewBox="0 0 24 24" class="h-5 w-5"><path fill="currentColor" d="M12 2l7 4v6c0 5-3 8-7 10C8 20 5 17 5 12V6l7-4zM7 8v4c0 3 2 5 5 6c3-1 5-3 5-6V8l-5-3l-5 3z"/></svg>
                    </span>
                    <div>
                      <div class="text-sm text-slate-300">Salón de eventos el Polvorín</div>
                      <div class="text-xs text-slate-400">Reservaciones & QR</div>
                    </div>
                  </div>
                </div>

                {{-- cuerpo --}}
                <div class="p-5 grid grid-cols-5 gap-4">
                  {{-- Datos (col-span-3) --}}
                  <div class="col-span-5 sm:col-span-3 space-y-2">
                    <div class="text-xs text-slate-400">Evento</div>
                    <div class="text-lg font-bold leading-tight">{{ $reservation->event_name ?: 'Evento' }}</div>

                    <div class="grid grid-cols-2 gap-3 mt-2">
                      <div>
                        <div class="text-xs text-slate-400">Fecha</div>
                        <div class="font-medium">{{ optional($reservation->date)->format('d/m/Y') }}</div>
                      </div>
                      <div>
                        <div class="text-xs text-slate-400">Horario</div>
                        <div class="font-medium">
                          {{ $reservation->shift === 'day' ? 'DÍA' : 'NOCHE' }}
                          <span class="text-xs text-slate-400">({{ $shiftRanges[$reservation->shift] ?? '—' }})</span>
                        </div>
                      </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3 mt-2">
                      <div>
                        <div class="text-xs text-slate-400">Mesa</div>
                        <div class="font-semibold">Mesa {{ $t->id_mesa }}</div>
                      </div>
                      <div>
                        <div class="text-xs text-slate-400">Boleto</div>
                        <div class="font-mono text-sm">{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($t->token, 0, 8)) }}</div>
                      </div>
                    </div>

                    <div class="mt-2">
                      <div class="text-xs text-slate-400">Ubicación</div>
                      <div class="text-sm">{{ $address }}</div>
                    </div>
                  </div>

                  {{-- QR (col-span-2) --}}
                  <div class="col-span-5 sm:col-span-2">
                    @if($url)
                      <div class="rounded-xl bg-white p-2">
                        <img src="{{ $url }}" alt="QR" class="w-full h-auto rounded-lg">
                      </div>
                      <div class="mt-2 text-[11px] text-slate-400 text-center">Escanea para validar acceso</div>
                    @else
                      <div class="rounded-xl border border-dashed border-white/20 h-full grid place-items-center text-sm text-slate-400">
                        QR no disponible
                      </div>
                    @endif
                  </div>
                </div>

                {{-- pie (acciones) --}}
                <div class="px-5 pb-5 flex items-center justify-between border-t border-white/10">
                  <div class="text-[11px] text-slate-400">
                    Emitido: {{ optional($t->issued_at)->format('d/m/Y H:i') }}
                  </div>
                  <div class="flex gap-2">
                    <button onclick="printTicket(this)" class="px-3 py-1.5 rounded-md border border-white/10 hover:bg-white/5 text-sm">
                      Imprimir
                    </button>
                    <a href="{{ $url }}" download class="px-3 py-1.5 rounded-md bg-[#6d28d9] text-white hover:bg-[#6d28d9]/90 text-sm">
                      Descargar QR
                    </a>
                  </div>
                </div>
              </div>
            @endforeach
          </div>
        </div>
      </div>
    @empty
      <div class="rounded-xl border border-dashed border-gray-300 dark:border-gray-700 p-8 text-center">
        <div class="text-gray-600 dark:text-gray-300">Aún no hay boletos emitidos para esta reservación.</div>
      </div>
    @endforelse
  </div>

  {{-- Print helper (imprime solo la tarjeta) --}}
  <script>
    function printTicket(btn){
      const card = btn.closest('.rounded-2xl');
      if(!card) return;

      // Resolvemos la URL del CSS con el facade de Vite desde Blade
      const cssHref = @json(\Illuminate\Support\Facades\Vite::asset('resources/css/app.css'));

      const w = window.open('', '_blank', 'width=900,height=900');
      w.document.write(`
        <html>
          <head>
            <title>Imprimir boleto</title>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            ${cssHref ? `<link rel="stylesheet" href="${cssHref}">` : ''}
            <style>body{padding:16px;background:#0b1220;color:#fff}</style>
          </head>
          <body>${card.outerHTML}</body>
        </html>
      `);
      w.document.close();

      // Fallback para modo dev: si no se cargó CSS, clonamos estilos del documento actual
      w.addEventListener('load', () => {
        const hasStyles = w.document.querySelectorAll('link[rel="stylesheet"],style').length > 0;
        if(!hasStyles){
          const styles = document.querySelectorAll('link[rel="stylesheet"],style');
          styles.forEach(node => {
            try { w.document.head.appendChild(node.cloneNode(true)); } catch(e){}
          });
        }
        w.focus();
        w.print();
        w.close();
      });
    }
  </script>
</x-app-layout>
