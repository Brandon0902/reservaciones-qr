{{-- resources/views/layouts/guest.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Reservaciones & QR') }}</title>

    {{-- Fonts (Poppins como en home) --}}
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    {{-- Vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
      :root { --brand:#6d28d9; --brand-2:#a78bfa; }
      html, body {
        font-family: 'Poppins', system-ui, -apple-system, Segoe UI, Roboto,
                     'Helvetica Neue', Arial, 'Noto Sans', 'Apple Color Emoji',
                     'Segoe UI Emoji', 'Segoe UI Symbol', sans-serif;
      }
    </style>
  </head>
  <body class="min-h-screen bg-gradient-to-b from-slate-950 via-slate-900 to-slate-950 text-slate-100 antialiased">

    {{-- Mini navbar público para coherencia visual --}}
    <header class="border-b border-white/10 bg-slate-950/70 backdrop-blur">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 h-14 flex items-center justify-between">
        <a href="{{ route('home') }}" class="flex items-center gap-3">
          <img
            src="{{ asset('images/logo_polvorin.png') }}"
            alt="Salón de eventos el Polvorín"
            class="h-9 w-9 object-contain"
          >
          <div class="leading-tight">
            <div class="font-semibold -mb-1">Salón de eventos el Polvorín</div>
            <div class="text-xs text-slate-400">Reservaciones & QR</div>
          </div>
        </a>
        <a href="{{ route('home') }}" class="text-sm px-3 py-1.5 rounded bg-white/5 hover:bg-white/10">
          Inicio
        </a>
      </div>
    </header>

    {{-- Contenedor principal tipo "glass" --}}
    <main class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-10">
      <div class="mx-auto w-full sm:max-w-md rounded-2xl border border-white/10 bg-white/5 shadow-lg p-6">
        {{ $slot }}
      </div>
      <p class="mt-6 text-center text-xs text-slate-400">
        © {{ date('Y') }} Salón de eventos el Polvorín.
      </p>
    </main>
  </body>
</html>
