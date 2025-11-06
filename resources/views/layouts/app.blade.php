{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Reservaciones & QR') }}</title>

    {{-- Fonts (Poppins para mantener el look) --}}
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    {{-- Scripts / Styles --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
      :root { --brand:#6d28d9; --brand-2:#a78bfa; }
      html, body { font-family: 'Poppins', system-ui, -apple-system, Segoe UI, Roboto, 'Helvetica Neue', Arial, 'Noto Sans', sans-serif; }
    </style>
  </head>
  <body class="min-h-screen bg-gradient-to-b from-slate-950 via-slate-900 to-slate-950 text-slate-100 antialiased">

    {{-- Top navigation (ya la estilizamos antes) --}}
    @include('layouts.navigation')

    {{-- Header opcional (SIN fondo blanco) --}}
    @if (isset($header))
      <header class="bg-transparent">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-4">
          {{ $header }}
        </div>
      </header>
    @endif

    {{-- Contenido --}}
    <main class="min-h-[calc(100vh-4rem)]">
      {{ $slot }}
    </main>

  </body>
</html>
