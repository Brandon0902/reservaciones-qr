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

  <style>
    .ticket-thumb { backdrop-filter: blur(2px); }
    .stack-layer { transform: rotate(-2deg); opacity: .6; }
    .stack-layer-2 { transform: rotate(2deg); opacity: .4; }
    [x-cloak] { display: none !important; }
  </style>

  <div class="py-6 mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 space-y-6"
       x-data="ticketsUI()">

    {{-- Encabezado evento --}}
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
        Nota: Los boletos se encuentran <strong>agrupados por mesa</strong> (10 por mesa).
      </div>
    </div>

    {{-- Acordeón por mesa --}}
    @forelse($grouped as $mesa => $tickets)
      @php
        $mesaKey = 'mesa_'.$mesa;
        $preview = $tickets->first();
        $previewUrl = $preview && $preview->qr_path ? Storage::disk('tickets')->url($preview->qr_path) : null;
        $previewToken = $preview ? \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($preview->token, 0, 6)) : '—';
      @endphp

      <div class="rounded-3xl border border-white/10 bg-gradient-to-br from-slate-900 to-slate-950 text-slate-100 shadow-xl overflow-hidden">

        {{-- Header clicable --}}
        <button type="button"
                class="w-full relative px-5 py-4 transition group"
                @click="toggle('{{ $mesaKey }}')"
                :aria-expanded="isOpen('{{ $mesaKey }}')"
                aria-controls="panel-{{ $mesaKey }}">
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

            {{-- Mini preview --}}
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

        {{-- Panel con grid de boletos --}}
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
                // NUEVO: link a la vista imprimible (abre el diálogo de impresión)
                $printRoute = route('client.tickets.print', [$reservation->id, $t->id]) . '?auto=1';
              @endphp

              <div class="rounded-2xl overflow-hidden border border-white/10 bg-gradient-to-br from-slate-900 to-slate-950 text-slate-100 shadow-xl relative">
                {{-- branding --}}
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

                  {{-- QR --}}
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

                {{-- acciones --}}
                <div class="px-5 pb-5 flex flex-wrap items-center gap-2 justify-between border-t border-white/10">
                  <div class="text-[11px] text-slate-400">
                    Emitido: {{ optional($t->issued_at)->format('d/m/Y H:i') }}
                  </div>

                  <div class="flex flex-wrap gap-2">

                    {{-- Imprimir tarjeta (popup) --}}
                    <button onclick="printTicket(this)"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md border border-white/10 hover:bg-white/5 text-sm">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 9V3h12v6M6 18H5a3 3 0 0 1-3-3v-3a3 3 0 0 1 3-3h14a3 3 0 0 1 3 3v3a3 3 0 0 1-3 3h-1M16 18v3H8v-3"/></svg>
                      Imprimir
                    </button>

                    {{-- PDF (vista imprimible con auto print) --}}
                    <a href="{{ $printRoute }}" target="_blank" rel="noopener"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md bg-indigo-600 text-white hover:bg-indigo-500 text-sm">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 5v10m0 0l-4-4m4 4l4-4M6 19h12"/></svg>
                      PDF
                    </a>

                    {{-- WhatsApp (link a vista imprimible) --}}
                    <button type="button"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md bg-emerald-700 text-white hover:bg-emerald-600 text-sm"
                            @click="shareTicketWhatsapp({
                              token: @js($t->token),
                              printUrl: @js($printRoute),
                              qrUrl: @js($url)
                            })">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M20.52 3.48A11.93 11.93 0 0 0 12.06 0C5.45 0 .06 5.39.06 12a11.9 11.9 0 0 0 1.62 6l-1.68 6 6.18-1.62A12 12 0 1 0 20.52 3.48Zm-8.46 18a9.5 9.5 0 0 1-4.86-1.33l-.35-.2-3.67.96.98-3.58-.22-.37A9.6 9.6 0 1 1 12.06 21.5Zm5.6-6.88c-.3-.15-1.76-.86-2.04-.96-.28-.1-.48-.15-.68.15-.2.3-.78.95-.95 1.15-.17.2-.35.22-.65.07-.3-.15-1.27-.47-2.42-1.5-.89-.8-1.49-1.8-1.66-2.1-.17-.3-.02-.46.13-.6.14-.14.3-.35.45-.52.15-.17.2-.3.3-.5.1-.2.05-.37-.02-.52-.07-.15-.68-1.64-.93-2.25-.24-.58-.49-.5-.68-.5h-.58c-.2 0-.52.07-.8.37-.28.3-1.07 1.04-1.07 2.54s1.1 2.94 1.26 3.15c.15.2 2.16 3.31 5.24 4.53.73.31 1.3.5 1.74.64.73.23 1.4.2 1.93.12.59-.09 1.76-.72 2-1.42.25-.7.25-1.3.17-1.42-.08-.12-.27-.2-.57-.35Z"/></svg>
                      WhatsApp
                    </button>

                    {{-- Email: abre modal por boleto --}}
                    <button type="button"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md bg-[#6d28d9] text-white hover:bg-[#6d28d9]/90 text-sm"
                            @click="$dispatch('open-email-one', {
                              reservationId: {{ $reservation->id }},
                              ticketId: {{ $t->id }}
                            })">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4h16v16H4z"/><path stroke-linecap="round" stroke-linejoin="round" d="m22 6-10 7L2 6"/></svg>
                      Correo
                    </button>

                    {{-- Descargar QR imagen --}}
                    @if($url)
                      <a href="{{ $url }}" download
                         class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md border border-white/10 hover:bg-white/5 text-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 3h8v8H3zM13 3h8v8h-8zM3 13h8v8H3zM16 16h5"/></svg>
                        QR
                      </a>
                    @endif
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

  {{-- Modal: enviar un boleto por correo --}}
  <div x-data="emailOneModal()" x-on:open-email-one.window="open($event.detail)"
       x-show="isOpen" x-cloak
       class="fixed inset-0 z-[80] grid place-items-center bg-black/60">
    <div class="w-full max-w-lg rounded-2xl bg-slate-900 text-slate-100 border border-white/10 p-5">
      <div class="flex items-center justify-between mb-3">
        <h3 class="text-lg font-semibold">Enviar boleto por correo</h3>
        <button class="text-slate-300 hover:text-white" @click="close()">✕</button>
      </div>

      <form @submit.prevent="submit()">
        <input type="hidden" x-model="reservationId">
        <input type="hidden" x-model="ticketId">

        <label class="text-sm text-slate-300">Destinatarios (separados por coma)</label>
        <input type="text" x-model="emails"
               class="mt-1 w-full rounded-md border border-white/10 bg-slate-800 px-3 py-2"
               placeholder="correo1@ejemplo.com, correo2@ejemplo.com" required>

        <label class="mt-3 text-sm text-slate-300">Mensaje (opcional)</label>
        <textarea x-model="message"
                  class="mt-1 w-full rounded-md border border-white/10 bg-slate-800 px-3 py-2"
                  rows="3" placeholder="Te envío tu boleto en PDF."></textarea>

        <div class="mt-5 flex items-center justify-end gap-2">
          <button type="button" @click="close()"
                  class="px-3 py-1.5 rounded-md border border-white/10 hover:bg-white/5 text-sm">Cancelar</button>
          <button type="submit" :disabled="loading"
                  class="px-4 py-1.5 rounded-md bg-indigo-600 hover:bg-indigo-500 text-white text-sm">
            <span x-show="!loading">Enviar</span>
            <span x-show="loading" class="inline-flex items-center gap-2">
              <svg class="h-4 w-4 animate-spin" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none" opacity=".25"/><path d="M22 12a10 10 0 0 1-10 10" fill="currentColor"/></svg>
              Enviando…
            </span>
          </button>
        </div>

        <template x-if="error">
          <div class="mt-3 rounded-md border border-rose-500/20 bg-rose-500/10 p-3 text-rose-100 text-sm" x-text="error"></div>
        </template>
        <template x-if="success">
          <div class="mt-3 rounded-md border border-emerald-500/20 bg-emerald-500/10 p-3 text-emerald-100 text-sm" x-text="success"></div>
        </template>
      </form>
    </div>
  </div>

  {{-- Print helper + Alpine --}}
  <script>
    function printTicket(btn){
      const card = btn.closest('.rounded-2xl');
      if(!card) return;

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
      w.addEventListener('load', () => {
        const hasStyles = w.document.querySelectorAll('link[rel="stylesheet"],style').length > 0;
        if(!hasStyles){
          const styles = document.querySelectorAll('link[rel="stylesheet"],style');
          styles.forEach(node => { try { w.document.head.appendChild(node.cloneNode(true)); } catch(e){} });
        }
        w.focus(); w.print(); w.close();
      });
    }

    function ticketsUI(){
      return {
        openMesa: null,
        toggle(m){ this.openMesa = (this.openMesa===m ? null : m); },
        isOpen(m){ return this.openMesa===m; },
        shareTicketWhatsapp({ token, printUrl, qrUrl }){
          const evento = @json($reservation->event_name ?: 'Evento');
          const fecha  = @json(optional($reservation->date)->format('d/m/Y'));
          const turno  = @json($reservation->shift === 'day' ? 'DÍA (10:00–16:00)' : 'NOCHE (19:00–02:00)');
          const code   = (token || '').toUpperCase().slice(0,8);
          const lines = [
            `Evento: ${evento}`,
            `Fecha: ${fecha} • ${turno}`,
            ``,
            `Boleto ${code}`,
            `PDF: ${printUrl}`,
          ];
          if(qrUrl) lines.push(`QR: ${qrUrl}`);
          const text = encodeURIComponent(lines.join('\n'));
          window.open(`https://wa.me/?text=${text}`, '_blank', 'noopener');
        }
      }
    }

    function emailOneModal(){
      return {
        isOpen: false,
        reservationId: null,
        ticketId: null,
        emails: '',
        message: '',
        loading: false,
        error: '',
        success: '',
        open({reservationId, ticketId}) {
          this.isOpen = true;
          this.reservationId = reservationId;
          this.ticketId = ticketId;
          this.emails = '';
          this.message = 'Te envío tu boleto en PDF.';
          this.error = '';
          this.success = '';
        },
        close(){ this.isOpen = false; },
        async submit(){
          this.loading = true; this.error=''; this.success='';
          try {
            const res = await fetch(`{{ route('client.tickets.email.one') }}`, {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': @json(csrf_token()),
              },
              body: JSON.stringify({
                reservation_id: this.reservationId,
                ticket_id: this.ticketId,
                emails: this.emails,
                message: this.message,
              })
            });
            const data = await res.json();
            if(!res.ok) throw new Error(data.message || 'No se pudo enviar el correo.');
            this.success = data.message ?? 'Enviado correctamente.';
          } catch(e){
            this.error = e.message || 'Error al enviar.';
          } finally {
            this.loading = false;
          }
        }
      }
    }
  </script>
</x-app-layout>
