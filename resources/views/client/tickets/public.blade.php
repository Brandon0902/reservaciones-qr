<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Boleto</title>
  @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-950 text-slate-100 p-6">
  <div class="max-w-2xl mx-auto rounded-2xl border border-white/10 bg-white/5 p-6">
    <div class="flex items-start justify-between gap-4">
      <div>
        <div class="text-sm text-slate-300">Salón de eventos el Polvorín</div>
        <h1 class="text-xl font-bold mt-1">{{ $reservation->event_name ?: 'Evento' }}</h1>
        <div class="text-sm text-slate-300 mt-2">
          {{ optional($reservation->date)->format('d/m/Y') }}
          • {{ $reservation->shift === 'day' ? 'DÍA' : 'NOCHE' }}
          <span class="text-slate-400">({{ $shiftRanges[$reservation->shift] ?? '—' }})</span>
        </div>
        <div class="text-sm text-slate-300 mt-2">
          Boleto: <span class="font-mono">{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($ticket->token,0,8)) }}</span>
        </div>
        <div class="mt-3 text-sm text-slate-300">{{ $address }}</div>
      </div>

      <div class="shrink-0 rounded-xl bg-white p-2">
        @if($qrUrl)
          <img src="{{ $qrUrl }}" alt="QR" class="h-32 w-32 object-contain">
        @else
          <div class="h-32 w-32 grid place-items-center text-slate-700">QR</div>
        @endif
      </div>
    </div>

    <div class="mt-6 flex gap-2">
      <button onclick="window.print()"
              class="px-4 py-2 rounded-md bg-indigo-600 hover:bg-indigo-500 text-white">
        Imprimir / Guardar PDF
      </button>
    </div>
  </div>
</body>
</html>
