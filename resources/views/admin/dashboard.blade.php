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
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
          <x-stat-card 
            title="Usuarios (total)" 
            :value="$totalUsers" 
            color="blue"
            icon='<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>'
          />
          <x-stat-card 
            title="Admins" 
            :value="$admins" 
            color="rose"
            icon='<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>'
          />
          <x-stat-card 
            title="Validators" 
            :value="$validators" 
            color="amber"
            icon='<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>'
          />
          <x-stat-card 
            title="Customers" 
            :value="$customers" 
            color="emerald"
            icon='<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>'
          />
        </div>

        {{-- Placeholder de contenido adicional --}}
        <div class="mt-8 rounded-2xl border border-dashed border-gray-300 dark:border-gray-700 bg-gradient-to-br from-gray-50/50 to-transparent dark:from-gray-800/30 dark:to-transparent p-8 text-center">
          <div class="mx-auto max-w-md">
            <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
            <p class="text-sm font-medium text-gray-600 dark:text-gray-300 mb-1">Área de métricas adicionales</p>
            <p class="text-xs text-gray-500 dark:text-gray-400">
              Aquí puedes agregar métricas de reservaciones, pagos pendientes, últimos comprobantes, etc.
            </p>
          </div>
        </div>
      </main>
    </div>
  </div>
</x-app-layout>
