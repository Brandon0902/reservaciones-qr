@php
  $is = fn(string $name) => request()->routeIs($name)
    ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-950/40 dark:text-indigo-200'
    : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800';
@endphp

<aside class="w-64 shrink-0 border-r border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900">
  <div class="p-4 border-b border-gray-200 dark:border-gray-800">
    <div class="font-semibold text-lg text-gray-800 dark:text-gray-100">Admin</div>
    <div class="text-xs text-gray-500">Reservaciones & QR</div>
  </div>

  <nav class="p-2 space-y-1">
    <a href="{{ route('admin.dashboard') }}"
       class="flex items-center gap-2 px-3 py-2 rounded-md {{ $is('admin.dashboard') }}">
      <span class="i-lucide-layout-dashboard w-4 h-4"></span>
      <span>Dashboard</span>
    </a>

    <a href="{{ route('admin.users.index') }}"
       class="flex items-center gap-2 px-3 py-2 rounded-md {{ $is('admin.users.*') }}">
      <span class="i-lucide-users w-4 h-4"></span>
      <span>Usuarios</span>
    </a>

    {{-- Redirige a pagos pendientes por defecto --}}
    <a href="{{ route('admin.payments.index', ['status' => 'pending']) }}"
       class="flex items-center gap-2 px-3 py-2 rounded-md {{ $is('admin.payments.*') }}">
      <span class="i-lucide-badge-dollar-sign w-4 h-4"></span>
      <span>Pagos</span>
    </a>

    {{-- Servicios extras --}}
    <a href="{{ route('admin.extra-services.index') }}"
       class="flex items-center gap-2 px-3 py-2 rounded-md {{ $is('admin.extra-services.*') }}">
      <span class="i-lucide-briefcase w-4 h-4"></span>
      <span>Servicios extras</span>
    </a>
  </nav>
</aside>
