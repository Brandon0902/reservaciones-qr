@php /** @var \App\Models\ExtraService $servicio */ @endphp

<x-app-layout>
  <x-slot name="header">
    <div class="flex items-center justify-between">
      <div>
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100">Editar servicio</h2>
        <nav class="text-sm text-gray-500 dark:text-gray-400 mt-1">
          <a href="{{ route('dashboard') }}" class="hover:underline">Dashboard</a>
          <span class="mx-1">/</span>
          <a href="{{ route('admin.extra-services.index') }}" class="hover:underline">Servicios extras</a>
          <span class="mx-1">/</span>
          <span>Editar #{{ $servicio->id }}</span>
        </nav>
      </div>
      <a href="{{ route('admin.extra-services.index') }}"
         class="px-3 py-2 rounded-md bg-slate-200 text-slate-900 hover:bg-slate-300 dark:bg-slate-700 dark:text-slate-100 dark:hover:bg-slate-600 text-sm">
        ← Volver
      </a>
    </div>
  </x-slot>

  <div class="py-6 mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
    @if(session('success'))
      <div class="mb-4 rounded-lg bg-emerald-50 p-3 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-200 ring-1 ring-emerald-500/20">
        {{ session('success') }}
      </div>
    @endif
    @if($errors->any())
      <div class="mb-4 rounded-lg bg-rose-50 p-3 text-rose-800 dark:bg-rose-900/40 dark:text-rose-200 ring-1 ring-rose-500/20">
        Hay errores en el formulario. Revisa los campos.
      </div>
    @endif

    <div class="rounded-xl bg-white dark:bg-gray-800 shadow ring-1 ring-black/5 p-6 space-y-6">
      <form method="POST" action="{{ route('admin.extra-services.update', $servicio) }}" class="space-y-5">
        @csrf @method('PUT')

        <div>
          <label class="block text-sm text-gray-700 dark:text-gray-300">Nombre</label>
          <input name="name" value="{{ old('name', $servicio->name) }}" required
                 class="mt-1 w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100" />
          @error('name') <div class="text-sm text-rose-600 mt-1">{{ $message }}</div> @enderror
        </div>

        <div>
          <label class="block text-sm text-gray-700 dark:text-gray-300">Descripción</label>
          <textarea name="description" rows="4"
                    class="mt-1 w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">{{ old('description', $servicio->description) }}</textarea>
          @error('description') <div class="text-sm text-rose-600 mt-1">{{ $message }}</div> @enderror
        </div>

        <div class="grid sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm text-gray-700 dark:text-gray-300">Precio (día)</label>
            <input type="number" step="0.01" min="0" name="day_price" value="{{ old('day_price', $servicio->day_price) }}"
                   class="mt-1 w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100" />
            @error('day_price') <div class="text-sm text-rose-600 mt-1">{{ $message }}</div> @enderror
          </div>
          <div>
            <label class="block text-sm text-gray-700 dark:text-gray-300">Precio (noche)</label>
            <input type="number" step="0.01" min="0" name="night_price" value="{{ old('night_price', $servicio->night_price) }}"
                   class="mt-1 w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100" />
            @error('night_price') <div class="text-sm text-rose-600 mt-1">{{ $message }}</div> @enderror
          </div>
        </div>

        <div class="flex items-center justify-end gap-2">
          <a href="{{ route('admin.extra-services.index') }}"
             class="px-3 py-2 rounded-md bg-slate-200 text-slate-900 hover:bg-slate-300 dark:bg-slate-700 dark:text-slate-100 dark:hover:bg-slate-600 text-sm">
            Cancelar
          </a>
          <button class="px-3 py-2 rounded-md bg-indigo-600 text-white hover:bg-indigo-700 text-sm">
            Guardar cambios
          </button>
        </div>
      </form>

      <form method="POST" action="{{ route('admin.extra-services.destroy', $servicio) }}"
            onsubmit="return confirm('¿Eliminar este servicio?');">
        @csrf @method('DELETE')
        <button class="px-3 py-2 rounded-md bg-rose-600 text-white hover:bg-rose-700 text-sm">
          Eliminar servicio
        </button>
      </form>
    </div>
  </div>
</x-app-layout>
