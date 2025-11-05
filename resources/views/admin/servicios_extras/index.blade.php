<x-app-layout>
  <x-slot name="header">
    <div class="flex items-center justify-between">
      <div>
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100">Servicios extras</h2>
        <nav class="text-sm text-gray-500 dark:text-gray-400 mt-1">
          <a href="{{ route('dashboard') }}" class="hover:underline">Dashboard</a>
          <span class="mx-1">/</span>
          <span>Servicios extras</span>
        </nav>
      </div>
      <a href="{{ route('admin.extra-services.create') }}"
         class="px-3 py-2 rounded-md bg-emerald-600 text-white hover:bg-emerald-700 text-sm">
        + Nuevo servicio
      </a>
    </div>
  </x-slot>

  <div class="py-6 mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

    @if(session('success'))
      <div class="mb-4 rounded-lg bg-emerald-50 p-3 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-200 ring-1 ring-emerald-500/20">
        {{ session('success') }}
      </div>
    @endif
    @if($errors->any())
      <div class="mb-4 rounded-lg bg-rose-50 p-3 text-rose-800 dark:bg-rose-900/40 dark:text-rose-200 ring-1 ring-rose-500/20">
        Revisar formulario. Hay errores.
      </div>
    @endif

    <form method="GET" class="mb-4 grid grid-cols-1 sm:grid-cols-6 gap-3">
      <div class="sm:col-span-5">
        <input name="q" value="{{ $q }}"
               placeholder="Buscar por nombre o descripción…"
               class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100" />
      </div>
      <div class="sm:col-span-1">
        <button class="w-full px-3 py-2 rounded-md bg-slate-200 text-slate-900 hover:bg-slate-300 dark:bg-slate-700 dark:text-slate-100 dark:hover:bg-slate-600 text-sm">
          Filtrar
        </button>
      </div>
    </form>

    <div class="overflow-hidden rounded-lg bg-white dark:bg-gray-800 shadow ring-1 ring-black/5">
      <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
        <thead class="bg-gray-50 dark:bg-gray-900/30">
          <tr>
            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 dark:text-gray-300">ID</th>
            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 dark:text-gray-300">Nombre</th>
            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 dark:text-gray-300">Precio (día)</th>
            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 dark:text-gray-300">Precio (noche)</th>
            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 dark:text-gray-300">Descripción</th>
            <th class="px-4 py-2 text-right text-xs font-semibold text-gray-600 dark:text-gray-300">Acciones</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
          @forelse($rows as $r)
            <tr>
              <td class="px-4 py-2 text-sm text-gray-700 dark:text-gray-200">{{ $r->id }}</td>
              <td class="px-4 py-2 text-sm font-medium text-gray-900 dark:text-gray-100">{{ $r->name }}</td>
              <td class="px-4 py-2 text-sm text-gray-700 dark:text-gray-200 whitespace-nowrap">{{ $r->day_price_money }}</td>
              <td class="px-4 py-2 text-sm text-gray-700 dark:text-gray-200 whitespace-nowrap">{{ $r->night_price_money }}</td>
              <td class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300">
                <div class="line-clamp-2">{{ $r->description }}</div>
              </td>
              <td class="px-4 py-2">
                <div class="flex items-center justify-end gap-2">
                  <a href="{{ route('admin.extra-services.edit', $r) }}"
                     class="px-2 py-1 text-xs rounded bg-slate-200 hover:bg-slate-300 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-900 dark:text-slate-100">
                    Editar
                  </a>
                  <form method="POST" action="{{ route('admin.extra-services.destroy', $r) }}"
                        onsubmit="return confirm('¿Eliminar este servicio?');">
                    @csrf @method('DELETE')
                    <button class="px-2 py-1 text-xs rounded bg-rose-600 text-white hover:bg-rose-700">
                      Eliminar
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="px-4 py-6 text-center text-sm text-gray-500 dark:text-gray-400">
                No hay servicios registrados.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="mt-4">
      {{ $rows->links() }}
    </div>
  </div>
</x-app-layout>
