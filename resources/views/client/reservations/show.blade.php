{{-- resources/views/client/reservations/show.blade.php --}}
@php
  use Illuminate\Support\Carbon;

  $date = isset($reservation->date) ? Carbon::parse($reservation->date) : null;
  $formattedDate = $date ? $date->format('d/m/Y') : '—';

  $shift = $reservation->shift ?? null;
  $shiftLabel = match ($shift) {
      'day'   => 'Matutino (10:00–16:00)',
      'night' => 'Nocturno (19:00–02:00)',
      default => 'Horario por definir',
  };

  // === Status (coherente con el index) ===
  $statusValue = $reservation->status->value ?? (string)($reservation->status ?? '');
  $statusMetaMap = [
      'pending'    => ['label' => 'Pendiente de pago',         'class' => 'bg-amber-500/10 border-amber-400/40 text-amber-100'],
      'confirmed'  => ['label' => 'Confirmada',                'class' => 'bg-sky-500/10 border-sky-400/40 text-sky-100'],
      'checked_in' => ['label' => 'En progreso en salón',      'class' => 'bg-indigo-500/10 border-indigo-400/40 text-indigo-100'],
      'completed'  => ['label' => 'Completada',                'class' => 'bg-emerald-500/10 border-emerald-400/40 text-emerald-100'],
      'canceled'   => ['label' => 'Cancelada',                 'class' => 'bg-rose-500/10 border-rose-400/40 text-rose-100'],
  ];
  $statusMeta = $statusMetaMap[$statusValue] ?? [
      'label' => 'En proceso',
      'class' => 'bg-slate-900/80 border-white/15 text-slate-100',
  ];

  // Servicios extra de ESTA reservación (relación many-to-many)
  $extras = $reservation->extraServices ?? collect();
@endphp

<x-app-layout>
  <x-slot name="header">
    <div class="flex items-center justify-between gap-4">
      <div>
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100">
          Detalle de reservación
        </h2>
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
          {{ $reservation->event_name }} · {{ $formattedDate }}
        </p>
      </div>

      <a href="{{ route('client.reservations.my') }}"
         class="px-3 py-1.5 rounded-md bg-slate-200 text-slate-900 hover:bg-slate-300
                dark:bg-slate-700 dark:text-slate-100 dark:hover:bg-slate-600 text-sm">
        ← Mis reservaciones
      </a>
    </div>
  </x-slot>

  {{-- Animaciones globales --}}
  <style>
    @keyframes float-card {
      0%, 100% { transform: translateY(0); }
      50%      { transform: translateY(-8px); }
    }
    @keyframes shine {
      0%   { transform: translateX(-120%); }
      100% { transform: translateX(120%);  }
    }
    .card-float {
      animation: float-card 6s ease-in-out infinite;
    }
    .shine-effect {
      position: relative;
      overflow: hidden;
    }
    .shine-effect::after {
      content: "";
      position: absolute;
      inset: -150%;
      width: 40%;
      background: linear-gradient(120deg,
        transparent,
        rgba(255,255,255,0.25),
        transparent);
      transform: skewX(-20deg);
      animation: shine 7s linear infinite;
    }

    details.extra-card[open] .extra-icon-wrap {
      transform: translateY(-2px) scale(1.08);
      box-shadow: 0 18px 45px rgba(15,23,42,0.7);
    }
    .extra-icon-wrap {
      transition: transform .25s ease, box-shadow .25s ease, background-color .25s ease;
    }
  </style>

  <div class="py-8 mx-auto max-w-5xl px-4 sm:px-6 lg:px-8 space-y-8">
    <div class="grid gap-8 lg:grid-cols-[minmax(0,1.4fr)_minmax(0,1fr)] items-start">
      {{-- Tarjeta principal --}}
      <div class="relative overflow-hidden rounded-3xl border border-violet-400/25 bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950 px-6 sm:px-8 py-7 shadow-2xl card-float shine-effect">
        <div class="absolute inset-x-0 -top-32 h-44 bg-[radial-gradient(circle_at_top,rgba(167,139,250,0.42),transparent)] pointer-events-none"></div>

        <div class="relative flex flex-col gap-5">
          <div class="flex items-center gap-4">
            <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-violet-500 text-slate-950 shadow-xl">
              {{-- Icono de evento/pago --}}
              <svg class="w-8 h-8" viewBox="0 0 24 24">
                <path fill="currentColor" d="M7 3h10a2 2 0 0 1 2 2v3H5V5a2 2 0 0 1 2-2Zm-2 8h14v6a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2v-6Zm4 2v2h4v-2H9Z"/>
                <path fill="currentColor" d="M9.75 16.75L8 15l-1.5 1.5L9.75 19.75l3.75-3.75L12 14.25z"/>
              </svg>
            </div>
            <div>
              <p class="text-xs uppercase tracking-wide text-violet-200/80 font-semibold">
                Reservación creada
              </p>
              <h1 class="mt-1 text-2xl font-semibold text-slate-50">
                {{ $reservation->event_name }}
              </h1>
              <p class="mt-1 text-sm text-slate-300">
                Aquí puedes revisar el estado de tu evento, el horario contratado y acceder a tus boletos y pagos.
              </p>
            </div>
          </div>

          {{-- Timeline / estado --}}
          <div class="mt-3 grid gap-3 sm:grid-cols-3 text-xs sm:text-sm text-slate-100">
            <div class="rounded-2xl border border-white/10 bg-slate-900/70 px-4 py-3">
              <div class="text-[11px] uppercase tracking-wide text-slate-400">Fecha</div>
              <div class="mt-1 font-medium">{{ $formattedDate }}</div>
            </div>
            <div class="rounded-2xl border border-white/10 bg-slate-900/70 px-4 py-3">
              <div class="text-[11px] uppercase tracking-wide text-slate-400">Horario</div>
              <div class="mt-1 font-medium">{{ $shiftLabel }}</div>
            </div>
            <div class="rounded-2xl border px-4 py-3 {{ $statusMeta['class'] }}">
              <div class="text-[11px] uppercase tracking-wide">
                Estado
              </div>
              <div class="mt-1 font-semibold">
                {{ $statusMeta['label'] }}
              </div>
              <div class="mt-0.5 text-[11px] opacity-80">
                {{ strtoupper($statusValue ?: 'in_progress') }}
              </div>
            </div>
          </div>

          {{-- Nota de flujo --}}
          <div class="mt-4 rounded-2xl border border-white/10 bg-slate-900/60 px-4 py-3 text-xs sm:text-sm text-slate-300">
            <p>
              1. Reserva creada ·
              2. Sube tu comprobante ·
              3. Validamos el pago ·
              4. Recibes tus boletos QR y confirmación por correo.
            </p>
          </div>
        </div>
      </div>

      {{-- Panel lateral --}}
      <div class="space-y-4">
        <div class="rounded-2xl border border-white/10 bg-white/5 px-5 py-4 text-slate-100 shadow-lg">
          <h3 class="text-sm font-semibold text-slate-200 mb-3 flex items-center gap-2">
            <svg class="w-4 h-4 text-violet-300" viewBox="0 0 24 24">
              <path fill="currentColor" d="M3 5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v3H3V5Zm0 5h18v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-9Zm3 4v2h5v-2H6Z"/>
            </svg>
            Acciones rápidas
          </h3>

          <div class="space-y-2 text-sm">
            <a href="{{ route('client.payments.proof', $reservation) }}"
               class="flex items-center justify-between rounded-xl bg-violet-600/90 hover:bg-violet-500 px-4 py-2.5 transition">
              <span>Subir o cambiar comprobante</span>
              <svg class="w-4 h-4" viewBox="0 0 24 24">
                <path fill="currentColor" d="M5 12h11l-4-4l1.4-1.4L20.8 12l-7.4 7.4L12 18l4-4H5z"/>
              </svg>
            </a>

            <a href="{{ route('client.reservations.tickets', $reservation) }}"
               class="flex items-center justify-between rounded-xl border border-white/15 bg-slate-900/60 hover:bg-slate-900 px-4 py-2.5 transition">
              <span>Ver boletos de esta reservación</span>
              <svg class="w-4 h-4" viewBox="0 0 24 24">
                <path fill="currentColor" d="M4 6a2 2 0 0 1 2-2h9l5 5v9a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6Zm11 0v3h3l-3-3Z"/>
              </svg>
            </a>
          </div>
        </div>

        {{-- Info adicional --}}
        <div class="rounded-2xl border border-white/10 bg-white/5 px-5 py-4 text-xs sm:text-sm text-slate-200 space-y-2">
          <h3 class="text-sm font-semibold text-slate-100 mb-1">Información de la reservación</h3>

          @if(!empty($reservation->guest_count ?? null))
            <p><span class="text-slate-400">Invitados estimados:</span> {{ $reservation->guest_count }}</p>
          @endif

          @if(!empty($reservation->total_amount ?? null))
            <p>
              <span class="text-slate-400">Monto total:</span>
              ${{ number_format($reservation->total_amount, 2) }} MXN
            </p>
          @endif

          @if(!empty($reservation->notes ?? null))
            <div class="pt-1">
              <p class="text-slate-400 mb-1">Notas para el evento:</p>
              <p class="text-slate-200">{{ $reservation->notes }}</p>
            </div>
          @endif

          @if(empty($reservation->guest_count ?? null) && empty($reservation->total_amount ?? null) && empty($reservation->notes ?? null))
            <p class="text-slate-400">
              No hay información adicional guardada para esta reservación.
            </p>
          @endif
        </div>
      </div>
    </div>

    {{-- ====== Servicios extra en esta reservación ====== --}}
    @php
      $extras = $reservation->extraServices ?? collect();
    @endphp

    @if($extras->isNotEmpty())
      {{-- Estilos específicos de extras (meseros / DJ / foto) --}}
      <style>
        /* Iconito mesero cuando el extra está abierto */
        @keyframes waiter-walk-icon {
          0%   { transform: translateX(-4px); }
          50%  { transform: translateX(6px) translateY(-2px); }
          100% { transform: translateX(-4px); }
        }
        .extra-icon-anim {
          animation: waiter-walk-icon 1.4s ease-in-out infinite;
          animation-play-state: paused;
        }
        details.extra-anim[open] .extra-icon-anim {
          animation-play-state: running;
        }

        /* Icono DJ pequeño */
        @keyframes dj-disc-spin {
          to { transform: rotate(360deg); }
        }
        .dj-disc {
          animation: dj-disc-spin 1.8s linear infinite;
          transform-origin: center;
        }
        @keyframes dj-eq {
          0%   { transform: scaleY(0.4); }
          50%  { transform: scaleY(1); }
          100% { transform: scaleY(0.4); }
        }
        .dj-bar {
          animation: dj-eq 1.1s ease-in-out infinite;
          transform-origin: bottom center;
        }

        /* Icono cámara pequeño */
        @keyframes camera-bounce {
          0%,100% { transform: translateY(0); }
          50%     { transform: translateY(-2px); }
        }
        .camera-icon {
          animation: camera-bounce 1.4s ease-in-out infinite;
          transform-origin: center bottom;
        }

        /* Overlay central: visibilidad */
        details.extra-anim .waiter-overlay {
          opacity: 0;
          transform: scale(.95);
          pointer-events: none;
          transition: opacity .25s ease-out, transform .25s ease-out;
        }
        details.extra-anim[open] .waiter-overlay {
          opacity: 1;
          transform: scale(1);
        }

        /* Escena mesero */
        @keyframes waiter-scene-move {
          0%   { transform: translateX(-10px); }
          45%  { transform: translateX(10px); }
          75%  { transform: translateX(18px); }
          100% { transform: translateX(18px); }
        }
        .waiter-scene {
          animation: waiter-scene-move 2.4s ease-in-out infinite;
          transform-origin: center bottom;
        }
        @keyframes plate-hand {
          0%, 70% { opacity: 1; }
          80%,100%{ opacity: 0; }
        }
        @keyframes plate-table {
          0%, 70% { opacity: 0; transform: translateY(4px); }
          80%    { opacity: 1; transform: translateY(0); }
          100%   { opacity: 1; transform: translateY(0); }
        }
        .plate-hand {
          animation: plate-hand 2.4s linear infinite;
        }
        .plate-table {
          animation: plate-table 2.4s linear infinite;
          transform-origin: center;
        }

        /* Glow genérico */
        @keyframes tray-glow {
          0%,100% { opacity: 0.25; transform: scale(1); }
          50%     { opacity: 0.9;  transform: scale(1.08); }
        }
        .tray-glow {
          animation: tray-glow 2.4s ease-in-out infinite;
          transform-origin: center;
        }

        /* DJ grande */
        @keyframes dj-head-bounce {
          0%,100% { transform: translateY(0); }
          50%     { transform: translateY(-4px); }
        }
        .dj-head {
          animation: dj-head-bounce 1.4s ease-in-out infinite;
          transform-origin: center;
        }

        /* Cámara grande */
        @keyframes photo-flash {
          0%,60% { opacity: .15; }
          70%    { opacity: 1; }
          82%    { opacity: .35; }
          100%   { opacity: .15; }
        }
        .photo-flash {
          animation: photo-flash 1.8s ease-in-out infinite;
        }
      </style>

      <div class="mt-8 rounded-3xl border border-white/10 bg-white/5 px-5 py-4 text-slate-100 shadow-xl">
        <h3 class="text-sm sm:text-base font-semibold mb-1 flex items-center gap-2">
          <span class="inline-flex h-7 w-7 items-center justify-center rounded-xl bg-[#6d28d9]/15 text-[#c4b5fd]">
            <svg class="w-4 h-4" viewBox="0 0 24 24">
              <path fill="currentColor" d="M4 6a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v4H4V6Zm0 6h18v6a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-6Z" />
            </svg>
          </span>
          Servicios extra en esta reservación
        </h3>
        <p class="text-xs text-slate-400 mb-4">
          Da clic en cada servicio para ver detalles y el costo según tu horario.
        </p>

        <div class="space-y-3">
          @foreach($extras as $extra)
            @php
              $shift    = $reservation->shift === 'night' ? 'night' : 'day';
              $price    = $shift === 'night' ? $extra->night_price : $extra->day_price;
              $priceStr = '$' . number_format((float)$price, 2, '.', ',') . ' MXN';
              $qty      = $extra->pivot->quantity ?? 1;
              $total    = $extra->pivot->total_price ?? ($price * $qty);
              $totalStr = '$' . number_format((float)$total, 2, '.', ',') . ' MXN';

              $nameLower = mb_strtolower($extra->name);

              $isWaiter  = str_contains($nameLower, 'mesero');
              $isDj      = str_contains($nameLower, 'dj');
              // Soporta "Fotógrafo", "Fotografo", "Cabina de fotos", etc.
              $isPhoto   = str_contains($nameLower, 'foto')
                          || str_contains($nameLower, 'fotóg')
                          || str_contains($nameLower, 'fotogra')
                          || str_contains($nameLower, 'cabina')
                          || str_contains($nameLower, 'photo');

              $shiftLabelShort = $shift === 'night' ? 'horario nocturno' : 'horario matutino';
            @endphp

            <details
              class="extra-anim group rounded-2xl border border-white/10 bg-slate-950/60 hover:bg-slate-950/80 px-4 py-3 transition"
              data-extra-details
            >
              <summary class="flex cursor-pointer items-center justify-between gap-3 list-none">
                <div class="flex items-center gap-3">
                  <div class="relative flex h-10 w-10 items-center justify-center rounded-2xl bg-[#6d28d9]/20">
                    {{-- Icono pequeño según tipo --}}
                    @if($isWaiter)
                      {{-- Mini mesero --}}
                      <svg class="w-6 h-6 extra-icon-anim" viewBox="0 0 40 40">
                        <circle cx="20" cy="20" r="20" fill="#020617"/>
                        <rect x="23" y="24" width="9" height="2" rx="1" fill="#e5e7eb"/>
                        <rect x="24" y="26" width="1.5" height="5" rx=".75" fill="#e5e7eb"/>
                        <rect x="30" y="26" width="1.5" height="5" rx=".75" fill="#e5e7eb"/>
                        <circle cx="15" cy="15" r="3" fill="#e5e7eb"/>
                        <rect x="14" y="18" width="2" height="6" fill="#e5e7eb"/>
                        <path d="M15 24v5M17 24l2 5" stroke="#e5e7eb" stroke-width="1.5" stroke-linecap="round"/>
                        <path d="M16 19l5-2" stroke="#e5e7eb" stroke-width="1.5" stroke-linecap="round"/>
                        <rect x="20" y="16" width="6" height="1.7" rx=".8" fill="#e5e7eb"/>
                      </svg>
                    @elseif($isDj)
                      {{-- Icono DJ --}}
                      <svg class="w-6 h-6" viewBox="0 0 40 40">
                        <circle cx="20" cy="20" r="20" fill="#020617"/>
                        <circle class="dj-disc" cx="15" cy="19" r="7" fill="#e5e7eb"/>
                        <circle cx="15" cy="19" r="2" fill="#020617"/>
                        <g transform="translate(23,13)" fill="#e5e7eb">
                          <rect class="dj-bar" x="0" y="4" width="2" height="10"/>
                          <rect class="dj-bar" x="4" y="1" width="2" height="13" style="animation-delay:.15s"/>
                          <rect class="dj-bar" x="8" y="3" width="2" height="11" style="animation-delay:.3s"/>
                        </g>
                      </svg>
                    @elseif($isPhoto)
                      {{-- Icono cámara --}}
                      <svg class="w-6 h-6 camera-icon" viewBox="0 0 40 40">
                        <circle cx="20" cy="20" r="20" fill="#020617"/>
                        <rect x="10" y="14" width="20" height="12" rx="3" fill="#e5e7eb"/>
                        <rect x="14" y="11" width="6" height="4" rx="1.5" fill="#e5e7eb"/>
                        <circle cx="20" cy="20" r="4" fill="#020617"/>
                        <circle cx="20" cy="20" r="2" fill="#e5e7eb"/>
                        <rect x="23" y="15" width="3" height="2" rx=".7" fill="#020617"/>
                      </svg>
                    @else
                      {{-- Fallback --}}
                      <span class="text-xl extra-icon-anim">⭐</span>
                    @endif

                    <span class="absolute inset-0 rounded-2xl bg-white/5 opacity-0 group-hover:opacity-100 transition"></span>
                  </div>
                  <div class="text-left">
                    <div class="text-sm font-semibold">
                      {{ $extra->name }}
                    </div>
                    <div class="text-xs text-slate-400">
                      {{ $priceStr }} <span class="opacity-70">({{ $shiftLabelShort }})</span>
                    </div>
                  </div>
                </div>

                <div class="flex items-center gap-3 text-xs text-slate-400">
                  <span class="hidden sm:inline">Click para detalles</span>
                  <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-white/5">
                    <svg class="w-3.5 h-3.5 text-slate-300 group-open:rotate-180 transition-transform" viewBox="0 0 24 24">
                      <path fill="currentColor" d="M7 10l5 5l5-5z" />
                    </svg>
                  </span>
                </div>
              </summary>

              <div class="mt-3 border-top border-white/10 pt-3 text-xs sm:text-sm text-slate-200 space-y-2">
                @if($extra->description)
                  <p class="text-slate-300">
                    {{ $extra->description }}
                  </p>
                @endif

                <div class="flex flex-wrap items-center gap-3 text-[13px]">
                  <span class="inline-flex items-center gap-1 rounded-full bg-slate-900/70 px-2.5 py-1">
                    <span class="text-slate-400">Cantidad:</span>
                    <span class="font-semibold">{{ $qty }}</span>
                  </span>
                  <span class="inline-flex items-center gap-1 rounded-full bg-slate-900/70 px-2.5 py-1">
                    <span class="text-slate-400">Total de este servicio:</span>
                    <span class="font-semibold text-emerald-300">{{ $totalStr }}</span>
                  </span>
                </div>

                <div class="mt-2 h-1.5 w-full rounded-full bg-slate-900/70 overflow-hidden">
                  <div class="h-full w-2/3 bg-gradient-to-r from-[#6d28d9] via-[#a855f7] to-emerald-400 animate-pulse"></div>
                </div>
              </div>

              {{-- Overlay central según tipo --}}
              <div class="waiter-overlay fixed inset-0 z-40 flex items-center justify-center">
                <div class="pointer-events-none relative rounded-3xl border border-violet-400/40 bg-slate-950/85 px-8 py-6 shadow-2xl max-w-md w-[90%]">
                  <div class="absolute -top-8 inset-x-0 flex justify-center">
                    <div class="tray-glow h-16 w-16 rounded-full bg-gradient-to-br from-[#6d28d9] via-[#a855f7] to-emerald-400 blur-[3px] opacity-60"></div>
                  </div>

                  <div class="relative flex flex-col items-center gap-4">
                    @if($isWaiter)
                      {{-- Escena MESEROS --}}
                      <svg class="w-44 h-24 text-slate-100" viewBox="0 0 120 60" fill="none">
                        <rect x="8" y="46" width="104" height="3" fill="currentColor" opacity=".18" />
                        <g class="table">
                          <rect x="70" y="30" width="32" height="4" rx="1" fill="currentColor" opacity=".95" />
                          <rect x="72" y="34" width="3.5" height="11" rx="1" fill="currentColor" opacity=".9" />
                          <rect x="96.5" y="34" width="3.5" height="11" rx="1" fill="currentColor" opacity=".9" />
                        </g>
                        <g class="plate-table">
                          <ellipse cx="86" cy="28" rx="7" ry="2.2" fill="currentColor" opacity=".95" />
                          <ellipse cx="86" cy="27.2" rx="4" ry="1.3" fill="#0f172a" />
                        </g>
                        <g class="waiter-scene">
                          <g class="waiter-body">
                            <circle cx="28" cy="20" r="5" fill="currentColor" />
                            <path d="M22 30c0-2.7 2.2-4.4 6-4.4s6 1.7 6 4.4v9H22v-9Z"
                                  fill="currentColor" />
                            <path d="M25 39v9M31 39v9" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" />
                            <path d="M31 28l10-6" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" />
                          </g>
                          <g class="plate-hand">
                            <ellipse cx="42" cy="21" rx="7" ry="2.2" fill="currentColor" />
                            <ellipse cx="42" cy="20.2" rx="4" ry="1.3" fill="#0f172a" />
                          </g>
                        </g>
                      </svg>
                      <div class="text-center space-y-1">
                        <p class="text-sm font-semibold text-slate-100">
                          Preparando <span class="text-violet-300">{{ $extra->name }}</span>
                        </p>
                        <p class="text-xs text-slate-400">
                          Este servicio forma parte de tu evento. Podrás verlo en tus boletos y en el resumen de pago.
                        </p>
                      </div>
                    @elseif($isDj)
                      {{-- Escena DJ --}}
                      <svg class="w-44 h-28 text-slate-100" viewBox="0 0 120 70" fill="none">
                        <rect x="8" y="56" width="104" height="3" fill="currentColor" opacity=".18" />
                        <g transform="translate(18,20)">
                          <rect x="0" y="16" width="84" height="16" rx="3" fill="currentColor" opacity=".9" />
                          <circle class="dj-disc" cx="18" cy="24" r="8" fill="#020617"/>
                          <circle cx="18" cy="24" r="5" fill="#e5e7eb"/>
                          <circle cx="18" cy="24" r="2" fill="#020617"/>

                          <rect x="34" y="20" width="3" height="8" rx="1" class="dj-bar" fill="#e5e7eb" />
                          <rect x="40" y="18" width="3" height="10" rx="1" class="dj-bar" fill="#e5e7eb" style="animation-delay:.15s"/>
                          <rect x="46" y="22" width="3" height="6" rx="1" class="dj-bar" fill="#e5e7eb" style="animation-delay:.3s"/>

                          <g transform="translate(60,0)" class="dj-head">
                            <circle cx="6" cy="8" r="4" fill="#e5e7eb"/>
                            <rect x="3" y="12" width="6" height="6" rx="2" fill="#e5e7eb"/>
                            <rect x="-1" y="6" width="3" height="6" rx="1.2" fill="#e5e7eb" opacity=".9"/>
                            <rect x="10" y="6" width="3" height="6" rx="1.2" fill="#e5e7eb" opacity=".9"/>
                          </g>
                        </g>
                      </svg>
                      <div class="text-center space-y-1">
                        <p class="text-sm font-semibold text-slate-100">
                          Ambientando <span class="text-violet-300">{{ $extra->name }}</span>
                        </p>
                        <p class="text-xs text-slate-400">
                          Luces, música y buen ambiente listos para tu evento.
                        </p>
                      </div>
                    @elseif($isPhoto)
                      {{-- Escena FOTÓGRAFO --}}
                      <svg class="w-40 h-28 text-slate-100" viewBox="0 0 120 70" fill="none">
                        <rect x="12" y="54" width="96" height="3" fill="currentColor" opacity=".18" />
                        <circle class="photo-flash" cx="82" cy="16" r="6" fill="#e5e7eb" opacity=".2"/>
                        <g transform="translate(24,24)">
                          <rect x="0" y="6" width="56" height="24" rx="4" fill="#e5e7eb"/>
                          <rect x="8" y="0" width="14" height="8" rx="3" fill="#e5e7eb"/>
                          <circle cx="28" cy="18" r="9" fill="#020617"/>
                          <circle cx="28" cy="18" r="5" fill="#e5e7eb"/>
                          <circle cx="28" cy="18" r="2" fill="#020617"/>
                          <rect x="42" y="9" width="5" height="3" rx="1" fill="#020617"/>
                        </g>
                      </svg>
                      <div class="text-center space-y-1">
                        <p class="text-sm font-semibold text-slate-100">
                          Capturando <span class="text-violet-300">{{ $extra->name }}</span>
                        </p>
                        <p class="text-xs text-slate-400">
                          Cada momento importante quedará en tus fotos y recuerdos.
                        </p>
                      </div>
                    @else
                      {{-- Overlay genérico --}}
                      <svg class="w-16 h-16 text-slate-100" viewBox="0 0 64 64" fill="none">
                        <circle cx="32" cy="32" r="20" fill="currentColor" opacity=".1"/>
                        <path d="M32 18v14l8 8" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                      </svg>
                      <div class="text-center space-y-1">
                        <p class="text-sm font-semibold text-slate-100">
                          Preparando <span class="text-violet-300">{{ $extra->name }}</span>
                        </p>
                        <p class="text-xs text-slate-400">
                          Este servicio forma parte de tu evento. Podrás verlo en tu resumen de pago.
                        </p>
                      </div>
                    @endif
                  </div>
                </div>
              </div>
            </details>
          @endforeach
        </div>
      </div>
    @endif
  </div>

  {{-- Script: sólo un servicio extra abierto a la vez --}}
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const extraDetails = document.querySelectorAll('details.extra-anim[data-extra-details]');

      extraDetails.forEach((detailsEl) => {
        detailsEl.addEventListener('toggle', function () {
          if (this.open) {
            extraDetails.forEach((other) => {
              if (other !== this) {
                other.open = false;
              }
            });
          }
        });
      });
    });
  </script>
</x-app-layout>
