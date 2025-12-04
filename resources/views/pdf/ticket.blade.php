@php
  use Illuminate\Support\Str;

  $token = Str::upper(Str::substr($ticket->token ?? '', 0, 8));
  $turnoText = $reservation->shift === 'day' ? 'DÍA' : 'NOCHE';
  $rango     = $shiftRanges[$reservation->shift] ?? '';

  $logoFile = public_path('images/logo_polvorin.png');
  $hasLogo  = is_file($logoFile);
@endphp
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800&display=swap" rel="stylesheet">

  <style>
    /* ✅ Más alto para que JAMÁS se encime el footer */
    @page { size: 180mm 112mm; margin: 0; }

    * { box-sizing: border-box; }

    html, body{
      margin:0; padding:0;
      width: 180mm;
      height: auto; /* ✅ mejor que height fijo */
      font-family: "Manrope","DejaVu Sans",Arial,sans-serif;
      font-size: 12px;
      -webkit-print-color-adjust: exact;
      print-color-adjust: exact;
    }

    body { background:#0f172a; color:#f8fafc; }

    .ticket{
      width: 180mm;
      height: 112mm; /* ✅ coincide con @page */
      overflow: hidden;

      display:flex;
      flex-direction:column;

      background:
        radial-gradient(900px 220px at 20% -10%, rgba(109,40,217,.55), transparent 60%),
        radial-gradient(700px 200px at 115% 20%, rgba(99,102,241,.35), transparent 55%),
        linear-gradient(135deg, #0b1220 0%, #0f172a 55%, #0b1220 100%);

      border: 1px solid rgba(148,163,184,.22);
    }

    .top-accent{
      height: 6px;
      background: linear-gradient(90deg, #6d28d9, #6366f1, #22c55e);
      opacity:.9;
      flex: 0 0 auto;
    }

    .header{
      padding: 8mm 10mm 5mm 10mm;
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap: 10px;
      border-bottom: 1px solid rgba(148,163,184,.18);
      flex: 0 0 auto;
    }

    .brandRow{
      display:flex;
      align-items:center;
      gap:10px;
      min-width:0;
    }

    .logoFrame{
      width: 38px;
      height: 38px;
      border-radius: 12px;
      background: rgba(255,255,255,.08);
      border: 1px solid rgba(148,163,184,.22);
      display:flex;
      align-items:center;
      justify-content:center;
      overflow:hidden;
      flex: 0 0 auto;
    }
    .logoFrame img{
      width: 32px;
      height: 32px;
      object-fit: contain;
      display:block;
    }

    .brand .name{ font-weight:800; letter-spacing:.02em; font-size:12px; margin:0; }
    .brand .sub{ margin:2px 0 0 0; font-size:10px; color:#cbd5e1; opacity:.85; }

    .badge{
      font-size:10px;
      font-weight:800;
      letter-spacing:.12em;
      text-transform:uppercase;
      padding:7px 10px;
      border-radius:12px;
      background: rgba(17,28,49,.85);
      border: 1px solid rgba(148,163,184,.22);
      white-space:nowrap;
      flex: 0 0 auto;
    }

    /* ✅ content ocupa el “resto” del alto, footer queda abajo */
    .content{
      padding: 6mm 10mm 5mm 10mm;
      display:grid;
      grid-template-columns: 62% 38%;
      gap: 9mm;
      align-items:start;
      flex: 1 1 auto;
      min-height: 0;
    }

    .label{ font-size:10px; color:#cbd5e1; opacity:.75; margin:0 0 4px 0; }
    .title{
      margin:0 0 8px 0;
      font-size:18px;
      font-weight:800;
      line-height:1.12;
      overflow:hidden;
      display:-webkit-box;
      -webkit-box-orient: vertical;
      -webkit-line-clamp: 2;
    }

    .grid2{
      display:grid;
      grid-template-columns: 1fr 1fr;
      gap:8px;
      margin-bottom:8px;
    }

    .kv{
      background: rgba(17,28,49,.80);
      border: 1px solid rgba(148,163,184,.18);
      border-radius:14px;
      padding:10px;
    }
    .kv .v{
      font-size:13px;
      font-weight:700;
      margin-top:2px;
      line-height:1.2;
    }

    .code{ letter-spacing:.18em; }

    /* ✅ reduce overflow real (Spatie/Chrome a veces ignora line-clamp) */
    .addr .v{
      font-size:12px;
      font-weight:600;
      line-height:1.3;
      max-height: 2.6em; /* 2 líneas reales */
      overflow:hidden;
    }

    .qrWrap{ text-align:center; }
    .qrBox{
      display:inline-block;
      background:#fff;
      border-radius:18px;
      padding:10px;
      border:1px solid #e5e7eb;
      box-shadow: 0 8px 24px rgba(0,0,0,.25);
    }
    .qrImg{ width: 162px; height: 162px; }
    .qrHint{ margin-top:6px; font-size:10px; color:#cbd5e1; opacity:.75; }

    .footer{
      padding: 4mm 10mm 5mm 10mm;
      display:flex;
      justify-content:space-between;
      gap:10px;
      font-size:9px;
      line-height:1.25;
      color:#cbd5e1;
      opacity:.75;
      border-top: 1px solid rgba(148,163,184,.16);
      flex: 0 0 auto;
    }

    .footer .left{
      overflow:hidden;
      white-space:nowrap;
      text-overflow:ellipsis;
      min-width:0;
      flex: 1 1 auto;
    }
    .footer .right{
      flex: 0 0 auto;
      white-space:nowrap;
    }
  </style>
</head>
<body>
  <div class="ticket">
    <div class="top-accent"></div>

    <div class="header">
      <div class="brandRow">
        <div class="logoFrame">
          @if($hasLogo)
            {{-- ✅ SIN comillas, para que no imprima texto raro --}}
            <img src=@inlinedImage($logoFile) alt="Logo">
          @endif
        </div>

        <div class="brand">
          <p class="name">Salón de eventos el Polvorín</p>
          <p class="sub">Reservaciones & QR</p>
        </div>
      </div>

      <div class="badge">BOLETO {{ $token }}</div>
    </div>

    <div class="content">
      <div>
        <p class="label">Evento</p>
        <p class="title">{{ $reservation->event_name ?: 'Evento' }}</p>

        <div class="grid2">
          <div class="kv">
            <div class="label">Fecha</div>
            <div class="v">{{ optional($reservation->date)->format('d/m/Y') }}</div>
          </div>

          <div class="kv">
            <div class="label">Horario</div>
            <div class="v">{{ $turnoText }} @if($rango) ({{ $rango }}) @endif</div>
          </div>

          <div class="kv">
            <div class="label">Mesa</div>
            <div class="v">Mesa {{ $ticket->id_mesa }}</div>
          </div>

          <div class="kv">
            <div class="label">Código</div>
            <div class="v code">{{ $token }}</div>
          </div>
        </div>

        <div class="kv addr">
          <div class="label">Ubicación</div>
          <div class="v">{{ $address }}</div>
        </div>
      </div>

      <div class="qrWrap">
        @if(!empty($qrDataUri))
          <div class="qrBox">
            <img class="qrImg" src="{{ $qrDataUri }}" alt="QR">
          </div>
          <div class="qrHint">Escanea para validar acceso</div>
        @else
          <div class="qrBox">
            <div style="width:162px;height:162px;line-height:162px;color:#111;">QR</div>
          </div>
        @endif
      </div>
    </div>

    <div class="footer">
      <div class="left">
        Reservación #{{ $reservation->id }} — Emitido: {{ optional($ticket->issued_at)->format('d/m/Y H:i') }}
      </div>
      <div class="right">© {{ now()->year }} Salón de eventos el Polvorín</div>
    </div>
  </div>
</body>
</html>
