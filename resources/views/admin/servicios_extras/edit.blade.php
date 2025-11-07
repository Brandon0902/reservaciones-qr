@php /** @var \App\Models\ExtraService $servicio */ @endphp

<x-app-layout>
  <x-slot name="header">
    <div class="flex items-center justify-between">
      <div class="flex items-center gap-4">
        <a href="{{ route('admin.extra-services.index') }}"
           class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm transition-all duration-200 hover:bg-gray-50 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
          </svg>
          Volver
        </a>
        <div>
          <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100">Editar servicio</h2>
          <nav class="text-sm text-gray-500 dark:text-gray-400 mt-1">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-gray-700 dark:hover:text-gray-300 transition-colors">Dashboard</a>
            <span class="mx-1">/</span>
            <a href="{{ route('admin.extra-services.index') }}" class="hover:text-gray-700 dark:hover:text-gray-300 transition-colors">Servicios extras</a>
            <span class="mx-1">/</span>
            <span>Editar #{{ $servicio->id }}</span>
          </nav>
        </div>
      </div>
    </div>
  </x-slot>

  <div class="py-6 mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
    @if(session('success'))
      <div class="mb-6 rounded-xl border border-emerald-300/50 bg-gradient-to-r from-emerald-50 to-emerald-100/50 px-4 py-3 text-emerald-800 shadow-sm dark:border-emerald-800/50 dark:from-emerald-900/40 dark:to-emerald-900/20 dark:text-emerald-200 ring-1 ring-emerald-500/20">
        <div class="flex items-center gap-2">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          {{ session('success') }}
        </div>
      </div>
    @endif
    @if($errors->any())
      <div class="mb-6 rounded-xl border border-rose-300/50 bg-gradient-to-r from-rose-50 to-rose-100/50 px-4 py-3 text-rose-800 shadow-sm dark:border-rose-800/50 dark:from-rose-900/40 dark:to-rose-900/20 dark:text-rose-200 ring-1 ring-rose-500/20">
        <div class="flex items-center gap-2">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          Hay errores en el formulario. Revisa los campos.
        </div>
      </div>
    @endif

    <div class="rounded-2xl border border-gray-200 bg-gradient-to-br from-white to-gray-50/50 shadow-xl dark:border-gray-700 dark:from-gray-800 dark:to-gray-900/50 ring-1 ring-black/5 p-8 space-y-6">
      <div class="mb-6 flex items-center gap-3 pb-6 border-b border-gray-200 dark:border-gray-700">
        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-500 to-indigo-600 shadow-lg">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
          </svg>
        </div>
        <div>
          <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Editar servicio #{{ $servicio->id }}</h3>
          <p class="text-sm text-gray-500 dark:text-gray-400">Modifica la información del servicio extra</p>
        </div>
      </div>

      <form method="POST" action="{{ route('admin.extra-services.update', $servicio) }}" class="space-y-6">
        @csrf @method('PUT')

        <div class="space-y-2">
          <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 dark:text-gray-300">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
            </svg>
            Nombre del servicio
          </label>
          <input name="name" value="{{ old('name', $servicio->name) }}" required
                 class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-gray-900 placeholder-gray-400 shadow-sm transition-all duration-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:outline-none dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 dark:placeholder-gray-500 dark:focus:border-indigo-400" 
                 placeholder="Ej: Sonido profesional, Iluminación, etc." />
          @error('name') 
            <div class="flex items-center gap-1.5 text-sm text-rose-600 mt-1">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              {{ $message }}
            </div>
          @enderror
        </div>

        <div class="space-y-2">
          <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 dark:text-gray-300">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            Descripción
          </label>
          <textarea name="description" rows="4"
                    class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-gray-900 placeholder-gray-400 shadow-sm transition-all duration-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:outline-none dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 dark:placeholder-gray-500 dark:focus:border-indigo-400"
                    placeholder="Describe las características y detalles del servicio...">{{ old('description', $servicio->description) }}</textarea>
          @error('description') 
            <div class="flex items-center gap-1.5 text-sm text-rose-600 mt-1">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              {{ $message }}
            </div>
          @enderror
        </div>

        <div class="grid sm:grid-cols-2 gap-6">
          <div class="space-y-2">
            <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 dark:text-gray-300">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
              </svg>
              Precio (día)
            </label>
            <div class="relative">
              <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                <span class="text-gray-500 text-sm">$</span>
              </div>
              <input type="number" step="0.01" min="0" name="day_price" value="{{ old('day_price', $servicio->day_price) }}"
                     class="w-full pl-8 pr-4 py-3 rounded-xl border border-gray-300 bg-white text-gray-900 placeholder-gray-400 shadow-sm transition-all duration-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 focus:outline-none dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 dark:placeholder-gray-500 dark:focus:border-emerald-400" />
            </div>
            @error('day_price') 
              <div class="flex items-center gap-1.5 text-sm text-rose-600 mt-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {{ $message }}
              </div>
            @enderror
          </div>
          <div class="space-y-2">
            <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 dark:text-gray-300">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
              </svg>
              Precio (noche)
            </label>
            <div class="relative">
              <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                <span class="text-gray-500 text-sm">$</span>
              </div>
              <input type="number" step="0.01" min="0" name="night_price" value="{{ old('night_price', $servicio->night_price) }}"
                     class="w-full pl-8 pr-4 py-3 rounded-xl border border-gray-300 bg-white text-gray-900 placeholder-gray-400 shadow-sm transition-all duration-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 focus:outline-none dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 dark:placeholder-gray-500 dark:focus:border-blue-400" />
            </div>
            @error('night_price') 
              <div class="flex items-center gap-1.5 text-sm text-rose-600 mt-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {{ $message }}
              </div>
            @enderror
          </div>
        </div>

        <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-200 dark:border-gray-700">
          <a href="{{ route('admin.extra-services.index') }}"
             class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-5 py-2.5 text-sm font-medium text-gray-700 shadow-sm transition-all duration-200 hover:bg-gray-50 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">
            Cancelar
          </a>
          <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-gradient-to-r from-indigo-500 to-indigo-600 px-5 py-2.5 text-sm font-medium text-white shadow-lg transition-all duration-200 hover:from-indigo-600 hover:to-indigo-700 hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
            </svg>
            Guardar cambios
          </button>
        </div>
      </form>

      <div class="pt-6 border-t border-gray-200 dark:border-gray-700">
        <form method="POST" action="{{ route('admin.extra-services.destroy', $servicio) }}"
              onsubmit="return confirm('¿Eliminar este servicio? Esta acción no se puede deshacer.');">
          @csrf @method('DELETE')
          <button type="submit" class="inline-flex items-center gap-2 rounded-lg border border-rose-300 bg-rose-50 px-5 py-2.5 text-sm font-medium text-rose-700 shadow-sm transition-all duration-200 hover:bg-rose-100 hover:shadow-md dark:border-rose-700 dark:bg-rose-900/30 dark:text-rose-300 dark:hover:bg-rose-900/50">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
            Eliminar servicio
          </button>
        </form>
      </div>
    </div>
  </div>
</x-app-layout>
