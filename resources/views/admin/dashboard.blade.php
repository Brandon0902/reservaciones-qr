<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
      {{ __('Dashboard Admin') }}
    </h2>
  </x-slot>

  <div class="mx-auto max-w-screen-2xl px-4 sm:px-6 lg:px-8 py-6">
    <div class="flex gap-6">
      {{-- Sidebar --}}
      @include('admin.partials.sidebar')

      {{-- Main --}}
      <main class="flex-1">
        {{-- Tarjetas --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
          <x-stat-card title="Usuarios (total)" :value="$totalUsers" />
          <x-stat-card title="Admins" :value="$admins" />
          <x-stat-card title="Validators" :value="$validators" />
          <x-stat-card title="Customers" :value="$customers" />
        </div>

        {{-- Placeholder de contenido adicional --}}
        <div class="mt-6 rounded-xl border border-dashed border-gray-300 dark:border-gray-700 p-6 text-sm text-gray-500 dark:text-gray-400">
          Aquí puedes agregar métricas de reservaciones, pagos pendientes, últimos comprobantes, etc.
        </div>
      </main>
    </div>
  </div>
</x-app-layout>
