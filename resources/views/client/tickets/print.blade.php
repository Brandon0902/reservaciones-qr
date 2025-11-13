@php
  use Illuminate\Support\Str;
  $token     = Str::upper(Str::substr($ticket->token ?? '', 0, 8));
  $turnoText = $reservation->shift === 'day' ? 'DÍA (10:00–16:00)' : 'NOCHE (19:00–02:00)';
@endphp
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Boleto {{ $token }}</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="{{ Vite::asset('resources/css/app.css') }}">

  <style>
    /* ——— Impresión ——— */
    @page { size: A4; margin: 12mm; }
    html, body { background: #0b1220; }
    @media print {
      html, body { background: #ffffff !important; }
      .no-print { display: none !important; }
      .print-card { box-shadow: none !important; border-color: #e5e7eb !important; }
    }
    .sheet { max-width: 900px; margin: 24px auto; padding: 12px; }

    /* Evitar saltos dentro de la tarjeta y sus secciones */
    .print-card, .no-break { break-inside: avoid; page-break-inside: avoid; }

    /* Mini QR en header (esquina superior derecha) */
    .qr-mini {
      width: 110px; height: 110px;           /* ajusta 96–128px si quieres */
      border-radius: 12px; background: #fff;
      padding: 8px; display: grid; place-items: center;
    }
    .qr-mini img { width: 100%; height: auto; display: block; }
  </style>
</head>
<body class="text-slate-100">
  <div class="sheet">

    {{-- Barra superior (no se imprime) --}}
    <div class="no-print mb-4 flex items-center justify-between">
      <div class="text-sm text-slate-300">
        Vista de impresión — Reservación #{{ $reservation->id }}
      </div>
      <div class="flex gap-2">
        <button onclick="window.print()"
                class="inline-flex items-center gap-2 rounded-md bg-indigo-600 px-3 py-1.5 text-white hover:bg-indigo-500">
          <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M6 9V2h12v7h2a2 2 0 0 1 2 2v6h-4v4H8v-4H4v-6a2 2 0 0 1 2-2h0Zm2-5v5h8V4H8Zm0 14v2h8v-2H8Z"/></svg>
          Imprimir / Guardar como PDF
        </button>
        <a href="{{ route('client.reservations.tickets', $reservation) }}"
           class="inline-flex items-center gap-2 rounded-md border border-white/10 px-3 py-1.5 hover:bg-white/5">
          ← Volver
        </a>
      </div>
    </div>

    {{-- Tarjeta --}}
    <div class="print-card rounded-2xl overflow-hidden border border-white/10 bg-gradient-to-br from-slate-900 to-slate-950 text-slate-100 shadow-2xl no-break">
      {{-- Header con mini QR a la derecha --}}
      <div class="px-5 pt-5 pb-3 border-b border-white/10 flex items-start justify-between">
        <div class="flex items-center gap-3">
          <span class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-[#6d28d9] text-white shadow">
            <svg viewBox="0 0 24 24" class="h-5 w-5"><path fill="currentColor" d="M12 2l7 4v6c0 5-3 8-7 10C8 20 5 17 5 12V6l7-4zM7 8v4c0 3 2 5 5 6c3-1 5-3 5-6V8l-5-3l-5 3z"/></svg>
          </span>
          <div>
            <div class="text-sm text-slate-300">Salón de eventos el Polvorín</div>
            <div class="text-xs text-slate-400">Reservaciones & QR</div>
          </div>
        </div>

        <div class="flex flex-col items-end gap-2">
          <div class="text-xs uppercase tracking-wide bg-white/10 border border-white/10 px-2 py-1 rounded-md">
            Boleto {{ $token }}
          </div>
          @if($qrUrl)
            <div class="qr-mini">
              <img src="{{ $qrUrl }}" alt="QR">
            </div>
            <div class="text-[10px] text-slate-400 mt-1 text-right">Escanea para validar acceso</div>
          @endif
        </div>
      </div>

      {{-- Cuerpo (solo datos, sin QR grande) --}}
      <div class="p-6 grid grid-cols-5 gap-6 no-break">
        <div class="col-span-5 md:col-span-5 space-y-3">
          <div>
            <div class="text-xs text-slate-400">Evento</div>
            <div class="text-2xl font-extrabold leading-tight">{{ $reservation->event_name ?: 'Evento' }}</div>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div class="rounded-xl border border-white/10 bg-white/5 p-3">
              <div class="text-xs text-slate-400">Fecha</div>
              <div class="text-lg font-semibold">{{ optional($reservation->date)->format('d/m/Y') }}</div>
            </div>
            <div class="rounded-xl border border-white/10 bg-white/5 p-3">
              <div class="text-xs text-slate-400">Horario</div>
              <div class="text-lg font-semibold">{{ $turnoText }}</div>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div class="rounded-xl border border-white/10 bg-white/5 p-3">
              <div class="text-xs text-slate-400">Mesa</div>
              <div class="text-lg font-semibold">Mesa {{ $ticket->id_mesa }}</div>
            </div>
            <div class="rounded-xl border border-white/10 bg-white/5 p-3">
              <div class="text-xs text-slate-400">Código</div>
              <div class="text-lg font-semibold font-mono">{{ $token }}</div>
            </div>
          </div>

          <div class="rounded-xl border border-white/10 bg-white/5 p-3">
            <div class="text-xs text-slate-400">Ubicación</div>
            <div class="text-sm">
              {{ $address }}
            </div>
          </div>
        </div>
      </div>

      {{-- Pie --}}
      <div class="px-6 pb-6 flex items-center justify-between border-t border-white/10 no-break">
        <div class="text-[11px] text-slate-400">
          Reservación #{{ $reservation->id }} — Emitido: {{ optional($ticket->issued_at)->format('d/m/Y H:i') }}
        </div>
        <div class="text-[11px] text-slate-400">
          © {{ now()->year }} Salón de eventos el Polvorín
        </div>
      </div>
    </div>

  </div>

  @if($autoPrint)
    <script>window.addEventListener('load', () => setTimeout(() => window.print(), 150));</script>
  @endif
</body>
</html>
