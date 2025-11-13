@php
  use Illuminate\Support\Str;
  use Illuminate\Support\Facades\Storage;

  $brand     = '#6d28d9';           // primario (morado)
  $brand2    = '#a78bfa';           // acento
  $bgDark    = '#0b1220';           // fondo oscuro
  $ink       = '#e5e7eb';           // texto claro
  $muted     = '#94a3b8';           // texto tenue
  $border    = 'rgba(255,255,255,.12)';

  $turnoLabel = ($reservation->shift === 'day')
      ? 'DÍA (10:00–16:00)'
      : 'NOCHE (19:00–02:00)';

  $token     = Str::upper(Str::substr($ticket->token ?? '', 0, 8));
  $mesaLabel = 'Mesa ' . ($ticket->id_mesa ?? '—');

  // Resolver QR a ruta absoluta local (dompdf lee mejor file://)
  $disk  = Storage::disk('tickets');
  $qrAbs = ($ticket->qr_path && $disk->exists($ticket->qr_path)) ? $disk->path($ticket->qr_path) : null;
  $qrSrc = $qrAbs ? 'file://' . $qrAbs : null;

  $dateHuman = optional($reservation->date)->format('d/m/Y');
@endphp
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Boleto {{ $token ?: '—' }}</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <style>
    /* Fuerza UNA sola hoja A5 horizontal, centrada, sin márgenes de página */
    @page { margin: 0; size: A5 landscape; }

    * { box-sizing: border-box; }
    body {
      margin: 0;
      width: 210mm;      /* A5 landscape: 210x148mm */
      height: 148mm;
      display: flex;
      align-items: center;
      justify-content: center;
      background: {{ $bgDark }};
      color: {{ $ink }};
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Ubuntu, "Helvetica Neue", Arial, "Noto Sans", sans-serif;
      -webkit-print-color-adjust: exact;
      print-color-adjust: exact;
      font-size: 12px;
      line-height: 1.45;
    }

    .ticket {
      width: 95%;
      height: 90%;
      page-break-inside: avoid; /* ¡no cortar! */
      background: linear-gradient(135deg, #0f172a 0%, #0b1220 60%, #060b17 100%);
      border: 1px solid {{ $border }};
      border-radius: 16px;
      overflow: hidden;
      box-shadow: 0 10px 40px rgba(0,0,0,.35);
      display: flex;
      flex-direction: column;
    }

    .brand-bar {
      display: flex; align-items: center; justify-content: space-between;
      padding: 12px 16px;
      background: linear-gradient(90deg, {{ $brand }} 0%, {{ $brand2 }} 100%);
      color: #fff;
      flex: 0 0 auto;
    }
    .brand-left { display:flex; align-items:center; gap:10px; }
    .logo {
      width: 32px; height: 32px; border-radius: 10px;
      display:grid; place-items:center;
      background: rgba(255,255,255,.16);
    }
    .logo svg { width:18px; height:18px; fill:#fff; }
    .brand-title { font-weight: 700; letter-spacing:.2px; }
    .brand-sub   { font-size: 11px; opacity: .9; }
    .tag {
      display:inline-block; padding: 6px 10px; border-radius: 999px;
      background: rgba(167,139,250,.14); color: #fff; border: 1px solid rgba(255,255,255,.28);
      font-size: 11px; font-weight: 600;
    }

    .body { padding: 14px 16px; flex: 1 1 auto; }
    .grid { display:grid; grid-template-columns: 2fr 1fr; gap:14px; height: 100%; }
    .section-title { font-size: 11px; color: {{ $muted }}; text-transform:uppercase; letter-spacing:.5px; margin-bottom:6px; }
    .event-name { font-size: 20px; font-weight: 800; line-height:1.15; margin: 2px 0 6px; }
    .pair { display:grid; grid-template-columns: 1fr 1fr; gap:10px; margin-top:8px; }
    .card {
      border: 1px solid {{ $border }};
      border-radius: 12px;
      padding: 10px;
      background: rgba(255,255,255,.03);
    }
    .mono { font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace; }
    .big { font-size:15px; font-weight:700; }

    .qr-wrap {
      background:#fff; border-radius: 12px; padding: 8px; text-align:center;
      border: 1px solid rgba(0,0,0,.06);
    }
    .qr-wrap img { width: 100%; height: auto; border-radius: 8px; display:block; }
    .qr-caption { font-size: 10px; color:#111; margin-top:6px; }

    .divider {
      margin: 12px 0;
      height: 10px;
      position: relative;
      background: repeating-linear-gradient(90deg, transparent 0 14px, rgba(255,255,255,.06) 14px 24px);
      border-top: 1px dashed {{ $border }};
      border-bottom: 1px dashed {{ $border }};
    }

    .row { display:flex; align-items:center; gap:12px; }
    .footer {
      padding: 10px 16px;
      display:flex; align-items:center; justify-content:space-between;
      color: {{ $muted }};
      font-size: 11px;
      flex: 0 0 auto;
      border-top: 1px solid {{ $border }};
      background: rgba(255,255,255,.02);
    }
    .muted { color: {{ $muted }}; }
  </style>
</head>
<body>
  <div class="ticket">
    {{-- CINTA SUPERIOR --}}
    <div class="brand-bar">
      <div class="brand-left">
        <div class="logo">
          <svg viewBox="0 0 24 24"><path d="M12 2l7 4v6c0 5-3 8-7 10C8 20 5 17 5 12V6l7-4zM7 8v4c0 3 2 5 5 6c3-1 5-3 5-6V8l-5-3l-5 3z"/></svg>
        </div>
        <div>
          <div class="brand-title">Salón de eventos el Polvorín</div>
          <div class="brand-sub">Reservaciones & boletos QR</div>
        </div>
      </div>
      <span class="tag">Boleto {{ $token ?: '—' }}</span>
    </div>

    {{-- CUERPO --}}
    <div class="body">
      <div class="grid">
        {{-- Izquierda --}}
        <div>
          <div class="section-title">Evento</div>
          <div class="event-name">{{ $reservation->event_name ?: 'Evento' }}</div>

          <div class="pair">
            <div class="card">
              <div class="section-title" style="margin-bottom:4px">Fecha</div>
              <div class="big">{{ $dateHuman }}</div>
            </div>
            <div class="card">
              <div class="section-title" style="margin-bottom:4px">Horario</div>
              <div class="big">{{ $turnoLabel }}</div>
            </div>
          </div>

          <div class="pair">
            <div class="card">
              <div class="section-title" style="margin-bottom:4px">Mesa</div>
              <div class="big">{{ $mesaLabel }}</div>
            </div>
            <div class="card">
              <div class="section-title" style="margin-bottom:4px">Código</div>
              <div class="big mono">{{ $token ?: '—' }}</div>
            </div>
          </div>

          <div class="card" style="margin-top:10px">
            <div class="section-title" style="margin-bottom:6px">Ubicación</div>
            <div>{{ $address }}</div>
          </div>
        </div>

        {{-- Derecha --}}
        <div>
          <div class="section-title">Acceso</div>
          <div class="qr-wrap">
            @if($qrSrc)
              <img src="{{ $qrSrc }}" alt="QR del boleto">
              <div class="qr-caption mono">Escanea en el acceso</div>
            @else
              <div style="padding:36px 8px;color:#111">QR no disponible</div>
            @endif
          </div>

          <div class="card" style="margin-top:10px">
            <div class="section-title" style="margin-bottom:6px">Indicaciones</div>
            <ul style="margin:0; padding-left:16px;">
              <li>Presenta el QR en la entrada.</li>
              <li>Mantén este PDF a la mano (impreso o en tu celular).</li>
              <li>Válido solo para la fecha y horario indicados.</li>
            </ul>
          </div>
        </div>
      </div>

      <div class="divider"></div>

      <div class="row" style="justify-content:space-between;">
        <span class="muted">Reservación #{{ $reservation->id }}</span>
        <span class="muted">Gracias por tu preferencia</span>
      </div>
    </div>

    {{-- PIE --}}
    <div class="footer">
      <span>© {{ now()->year }} Salón de eventos el Polvorín</span>
      <span class="muted">Este boleto es intransferible.</span>
    </div>
  </div>
</body>
</html>
