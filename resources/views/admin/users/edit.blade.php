<x-app-layout>
  <x-slot name="header">
    <div class="flex items-center justify-between">
      <div class="flex items-center gap-4">
        <a href="{{ route('admin.users.index') }}" 
           class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm transition-all duration-200 hover:bg-gray-50 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
          </svg>
          Volver
        </a>
        <div>
          <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100">Editar usuario</h2>
          <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Modifica la información del usuario</p>
        </div>
      </div>
    </div>
  </x-slot>

  <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
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

    <div class="rounded-2xl border border-gray-200 bg-gradient-to-br from-white to-gray-50/50 shadow-xl dark:border-gray-700 dark:from-gray-800 dark:to-gray-900/50 ring-1 ring-black/5 p-8">
      <div class="mb-6 flex items-center gap-3 pb-6 border-b border-gray-200 dark:border-gray-700">
        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-500 to-indigo-600 shadow-lg">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
          </svg>
        </div>
        <div>
          <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Editar usuario: {{ $user->full_name }}</h3>
          <p class="text-sm text-gray-500 dark:text-gray-400">Modifica la información del usuario</p>
        </div>
      </div>

      <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-6">
        @csrf @method('PUT')

        <div class="space-y-2">
          <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 dark:text-gray-300">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
            Nombre completo
          </label>
          <input type="text" name="full_name" value="{{ old('full_name', $user->full_name) }}" required
                 class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-gray-900 placeholder-gray-400 shadow-sm transition-all duration-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:outline-none dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 dark:placeholder-gray-500 dark:focus:border-indigo-400" 
                 placeholder="Ej: Juan Pérez" />
          <x-input-error :messages="$errors->get('full_name')" class="mt-1" />
        </div>

        <div class="grid sm:grid-cols-2 gap-6">
          <div class="space-y-2">
            <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 dark:text-gray-300">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
              </svg>
              Correo electrónico
            </label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                   class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-gray-900 placeholder-gray-400 shadow-sm transition-all duration-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:outline-none dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 dark:placeholder-gray-500 dark:focus:border-indigo-400" 
                   placeholder="usuario@ejemplo.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
          </div>

          <div class="space-y-2">
            <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 dark:text-gray-300">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
              </svg>
              Teléfono (opcional)
            </label>
            <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                   class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-gray-900 placeholder-gray-400 shadow-sm transition-all duration-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:outline-none dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 dark:placeholder-gray-500 dark:focus:border-indigo-400" 
                   placeholder="3312345678" />
            <x-input-error :messages="$errors->get('phone')" class="mt-1" />
          </div>
        </div>

        <div class="space-y-2">
          <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 dark:text-gray-300">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
            </svg>
            Rol del usuario
          </label>
          <select name="role" required
                  class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-900 shadow-sm transition-all duration-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:outline-none dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100">
            @foreach($roles as $r)
              <option value="{{ $r->value }}" @selected(old('role', $user->role->value)===$r->value)>{{ ucfirst($r->value) }}</option>
            @endforeach
          </select>
          <x-input-error :messages="$errors->get('role')" class="mt-1" />
        </div>

        <div class="rounded-xl border border-amber-200 bg-amber-50/50 p-4 dark:border-amber-800 dark:bg-amber-900/10">
          <div class="flex items-start gap-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-amber-600 dark:text-amber-400 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div class="flex-1">
              <h4 class="text-sm font-semibold text-amber-900 dark:text-amber-200 mb-1">Cambiar contraseña (opcional)</h4>
              <p class="text-xs text-amber-700 dark:text-amber-300">Deja estos campos vacíos si no deseas cambiar la contraseña</p>
            </div>
          </div>
        </div>

        <div class="grid sm:grid-cols-2 gap-6">
          <div class="space-y-2">
            <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 dark:text-gray-300">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
              </svg>
              Nueva contraseña
            </label>
            <input type="password" name="password"
                   class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-gray-900 placeholder-gray-400 shadow-sm transition-all duration-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:outline-none dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 dark:placeholder-gray-500 dark:focus:border-indigo-400" 
                   placeholder="Dejar vacío para mantener" />
            <x-input-error :messages="$errors->get('password')" class="mt-1" />
          </div>

          <div class="space-y-2">
            <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 dark:text-gray-300">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              Confirmar contraseña
            </label>
            <input type="password" name="password_confirmation"
                   class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-gray-900 placeholder-gray-400 shadow-sm transition-all duration-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:outline-none dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 dark:placeholder-gray-500 dark:focus:border-indigo-400" 
                   placeholder="Confirmar nueva contraseña" />
          </div>
        </div>

        <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-200 dark:border-gray-700">
          <a href="{{ route('admin.users.index') }}"
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
    </div>
  </div>
</x-app-layout>
