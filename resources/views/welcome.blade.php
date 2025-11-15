@php
  use App\Enums\UserRole;

  $me      = auth()->user();
  $isAuth  = (bool) $me;
  $isAdmin = $me && (
      ($me->role instanceof UserRole && $me->role === UserRole::ADMIN) || $me->role === 'admin'
  );

  $clientCreate = route('client.reservations.create');

  // CTA principal del hero/tabla:
  // - Invitado: login con next a crear reserva
  // - Cliente:  directo a crear reserva
  // - Admin:    dashboard admin (no debe ir a client)
  $ctaUrl = $isAuth
      ? ($isAdmin ? route('admin.dashboard') : $clientCreate)
      : route('login', ['next' => $clientCreate]);
@endphp
<!DOCTYPE html>
<html lang="es" class="h-full scroll-smooth">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Salón de eventos el Polvorín — Home</title>

  {{-- Google Font: Poppins --}}
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

  {{-- Tailwind (Breeze/Vite) --}}
  @vite(['resources/css/app.css','resources/js/app.js'])

  <style>
    :root { --brand:#6d28d9; --brand-2:#a78bfa; }
    html, body { font-family: 'Poppins', system-ui, -apple-system, Segoe UI, Roboto, 'Helvetica Neue', Arial, 'Noto Sans', 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol', sans-serif; }
    .glass { backdrop-filter: blur(10px); background: rgba(255,255,255,.08); }
    .shine { position: relative; overflow:hidden; }
    .shine::after{
      content:''; position:absolute; inset:-150% -50% auto; height:300%;
      background: linear-gradient(120deg, transparent 45%, rgba(255,255,255,.25) 50%, transparent 55%);
      transform: rotate(12deg);
      animation: shine 6s linear infinite;
    }
    @keyframes shine { 0%{transform:translateX(-60%) rotate(12deg);} 100%{transform:translateX(60%) rotate(12deg);} }
  </style>
</head>
<body class="min-h-full bg-gradient-to-b from-slate-950 via-slate-900 to-slate-950 text-slate-100">

  {{-- ===== Navbar global (usa resources/views/layouts/navigation.blade.php) ===== --}}
  @include('layouts.navigation')

  {{-- ====== Hero ====== --}}
  <section class="relative overflow-hidden">
    <div class="absolute inset-0 -z-10 opacity-30">
      <div class="h-[520px] bg-[radial-gradient(1200px_400px_at_50%_-20%,rgba(167,139,250,.35),transparent)]"></div>
    </div>

    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-16 sm:py-24 grid lg:grid-cols-2 gap-10 items-center">
      <div>
        <span class="inline-flex items-center gap-2 text-xs tracking-wide uppercase text-[--brand-2]">
          <span class="h-1.5 w-1.5 rounded-full bg-[--brand-2]"></span> Nuevo 2025
        </span>
        <h1 class="mt-3 text-4xl sm:text-5xl font-extrabold leading-tight">
          El lugar perfecto para tu evento, con accesos por <span class="text-[--brand-2]">código QR</span>
        </h1>
        <p class="mt-4 text-slate-300 text-lg">
          Elige tu horario (matutino o nocturno), agrega extras y confirma tu fecha en minutos.
        </p>

        <div class="mt-8 flex flex-wrap items-center gap-3">
          <a href="{{ $ctaUrl }}"
             class="shine inline-flex items-center gap-2 rounded-xl bg-[--brand] px-5 py-3 font-medium hover:translate-y-[-1px] transition">
            Reservar ahora
            <svg viewBox="0 0 24 24" class="h-5 w-5"><path fill="currentColor" d="M5 12h11l-4-4l1.4-1.4L20.8 14l-7.4 7.4L12 20l4-4H5z"/></svg>
          </a>
          <a href="#precios" class="inline-flex items-center gap-2 rounded-xl px-5 py-3 border border-white/10 hover:bg-white/5">
            Ver precios
          </a>
        </div>

        <div class="mt-6 text-xs text-slate-400">
          * Si no tienes cuenta te pediremos registrarte al confirmar la reserva.
        </div>
      </div>

      <div class="relative">
        <div class="aspect-[4/3] rounded-2xl border border-white/10 glass shadow-2xl p-4">
          <div class="h-full w-full rounded-xl bg-gradient-to-br from-slate-800 to-slate-900 grid place-items-center text-center p-8">
            <div>
              <div class="text-sm text-slate-400 mb-2">Preview</div>
              <div class="text-2xl font-semibold">Escanea. Entra. Disfruta.</div>
              <div class="mt-2 text-slate-400">Tus invitados reciben un QR único para el acceso.</div>
            </div>
          </div>
        </div>
        <div class="absolute -bottom-4 -left-4 rotate-1 rounded-xl bg-emerald-500/20 text-emerald-200 text-xs px-3 py-1 border border-emerald-400/20">
          Confirmación inmediata
        </div>
      </div>
    </div>
  </section>

  {{-- ====== Precios por horario ====== --}}
  <section id="precios" class="py-16 sm:py-24 border-t border-white/10">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
      <div class="text-center max-w-3xl mx-auto">
        <h2 class="text-3xl sm:text-4xl font-bold">Precios ajustados por horarios</h2>
        <p class="mt-3 text-slate-400">Valores ilustrativos (MXN). Los extras son opcionales y se agregan durante la reserva.</p>
      </div>

      <div class="mt-10 overflow-x-auto rounded-2xl border border-white/10 bg-white/5 shadow">
        <table class="min-w-full text-left text-sm">
          <thead class="text-slate-200">
            <tr class="border-b border-white/10 bg-white/5">
              <th class="px-4 py-3 font-semibold">Horarios</th>
              <th class="px-4 py-3 font-semibold">Renta Básica<br><span class="text-xs text-slate-400">(Local + Mobiliario)</span></th>
              <th class="px-4 py-3 font-semibold">DJ<br><span class="text-xs text-slate-400">(Opcional)</span></th>
              <th class="px-4 py-3 font-semibold">Meseros<br><span class="text-xs text-slate-400">(Opcional)</span></th>
              <th class="px-4 py-3 font-semibold">Fotógrafo<br><span class="text-xs text-slate-400">(Opcional)</span></th>
              <th class="px-4 py-3 font-semibold text-right">Acción</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-white/10 text-slate-300">
            <tr class="hover:bg-white/5">
              <td class="px-4 py-4">
                <div class="font-medium">Matutino</div>
                <div class="text-xs text-slate-400">(10am—4pm)</div>
              </td>
              <td class="px-4 py-4 font-semibold">$5,000 MXN</td>
              <td class="px-4 py-4">$3,000 MXN</td>
              <td class="px-4 py-4">$1,800 MXN</td>
              <td class="px-4 py-4">$2,000 MXN</td>
              <td class="px-4 py-4 text-right">
                <a href="{{ $ctaUrl }}" class="inline-flex rounded-lg bg-[--brand] hover:bg-[--brand]/90 px-3 py-1.5 text-white">Reservar</a>
              </td>
            </tr>
            <tr class="hover:bg-white/5">
              <td class="px-4 py-4">
                <div class="font-medium">Nocturno</div>
                <div class="text-xs text-slate-400">(7pm—2am)</div>
              </td>
              <td class="px-4 py-4 font-semibold">$6,000 MXN</td>
              <td class="px-4 py-4">$5,000 MXN</td>
              <td class="px-4 py-4">$3,000 MXN</td>
              <td class="px-4 py-4">$3,000 MXN</td>
              <td class="px-4 py-4 text-right">
                <a href="{{ $ctaUrl }}" class="inline-flex rounded-lg border border-white/20 hover:bg-white/10 px-3 py-1.5">Reservar</a>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <p class="mt-4 text-xs text-slate-400">
        * Los precios pueden variar según fecha y disponibilidad. Se muestran con fines demostrativos.
      </p>
    </div>
  </section>

    {{-- ====== Extras (ilustrativo) ====== --}}
  <section id="extras" class="py-16 sm:py-24 border-t border-white/10">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
      <div class="text-center max-w-2xl mx-auto">
        <h2 class="text-3xl sm:text-4xl font-bold">Servicios extra</h2>
        <p class="mt-3 text-slate-400">Potencia tu experiencia con complementos opcionales.</p>
      </div>

      @if(isset($extras) && $extras->count())
        <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
          @foreach ($extras as $extra)
            <div class="rounded-2xl border border-white/10 bg-white/5 p-5">
              <div class="text-lg font-semibold">{{ $extra->name }}</div>

              @if($extra->description)
                <div class="mt-1 text-sm text-slate-400">
                  {{ $extra->description }}
                </div>
              @endif

              <div class="mt-4 text-sm text-slate-300 space-y-1">
                <div>
                  <span class="text-xs text-slate-400">Matutino</span><br>
                  <span class="text-base font-extrabold">
                    ${{ number_format($extra->day_price, 2) }} MXN
                  </span>
                </div>
                <div>
                  <span class="text-xs text-slate-400">Nocturno</span><br>
                  <span class="text-base font-extrabold">
                    ${{ number_format($extra->night_price, 2) }} MXN
                  </span>
                </div>
              </div>

              <a href="{{ $ctaUrl }}"
                 class="mt-4 inline-flex justify-center rounded-lg border border-white/10 px-3 py-1.5 hover:bg-white/5 text-sm">
                Agregar al reservar
              </a>
            </div>
          @endforeach
        </div>
      @else
        <p class="mt-8 text-center text-sm text-slate-400">
          Aún no hay servicios extra configurados. Pronto verás las opciones disponibles.
        </p>
      @endif
    </div>
  </section>


  {{-- ====== FAQ ====== --}}
  <section id="faq" class="py-16 sm:py-24 border-t border-white/10">
    <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
      <h2 class="text-3xl sm:text-4xl font-bold text-center">Preguntas frecuentes</h2>
      <div class="mt-8 divide-y divide-white/10 rounded-2xl border border-white/10 bg-white/5">
        @foreach ([['¿Necesito cuenta para reservar?','Puedes ver precios sin cuenta. Al confirmar, te pediremos registrarte o iniciar sesión.'],['¿Cómo funciona el acceso con QR?','Cada invitado recibe un código único. En la entrada se valida en segundos para evitar duplicados.'],['¿Puedo cambiar mi horario?','Sí, sujeto a disponibilidad y políticas vigentes.']] as [$q,$a])
          <details class="group open:bg-white/5 p-5">
            <summary class="cursor-pointer list-none font-medium flex items-center justify-between">
              <span>{{ $q }}</span>
              <span class="text-slate-400 group-open:rotate-180 transition">
                <svg viewBox="0 0 24 24" class="h-5 w-5"><path fill="currentColor" d="M7 10l5 5l5-5z"/></svg>
              </span>
            </summary>
            <p class="mt-2 text-slate-300">{{ $a }}</p>
          </details>
        @endforeach
      </div>

      <div class="mt-8 text-center">
        <a href="{{ $ctaUrl }}" class="inline-flex items-center gap-2 rounded-xl bg-[--brand] px-5 py-3 font-medium hover:bg-[--brand]/90">
          Comenzar mi reserva
        </a>
      </div>
    </div>
  </section>

  {{-- ====== Footer ====== --}}
  <footer class="border-t border-white/10 py-8">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 text-sm text-slate-400 flex flex-col sm:flex-row items-center justify-between gap-3">
      <div>© {{ date('Y') }} Salón de eventos el Polvorín. Todos los derechos reservados.</div>
      <div class="flex items-center gap-4">
        <a href="#precios" class="hover:text-slate-200">Precios</a>
        <a href="#extras" class="hover:text-slate-200">Extras</a>
        <a href="#faq" class="hover:text-slate-200">FAQ</a>
      </div>
    </div>
  </footer>
</body>
</html>
