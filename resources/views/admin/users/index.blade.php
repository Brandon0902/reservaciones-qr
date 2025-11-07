<x-app-layout>
  <x-slot name="header">
    <div class="flex items-center justify-between">
      <div class="flex items-center gap-4">
        <a href="{{ route('admin.dashboard') }}" 
           class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm transition-all duration-200 hover:bg-gray-50 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
          </svg>
          Volver al Dashboard
        </a>
        <div>
          <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100">Usuarios</h2>
          <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Gestiona los usuarios del sistema</p>
        </div>
      </div>

      <a href="{{ route('admin.users.create') }}"
         class="inline-flex items-center gap-2 rounded-lg bg-gradient-to-r from-indigo-500 to-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-lg transition-all duration-200 hover:from-indigo-600 hover:to-indigo-700 hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
        </svg>
        Nuevo usuario
      </a>
    </div>
  </x-slot>

  <div class="mx-auto max-w-screen-2xl px-4 sm:px-6 lg:px-8">
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

    @php
      $badge = function ($role) {
        $map = [
          'admin' => [
            'bg' => 'bg-gradient-to-r from-rose-500 to-rose-600',
            'text' => 'text-white',
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>'
          ],
          'validator' => [
            'bg' => 'bg-gradient-to-r from-amber-500 to-amber-600',
            'text' => 'text-white',
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>'
          ],
          'customer' => [
            'bg' => 'bg-gradient-to-r from-indigo-500 to-indigo-600',
            'text' => 'text-white',
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>'
          ],
        ];
        $roleData = $map[$role] ?? ['bg' => 'bg-gray-500', 'text' => 'text-white', 'icon' => ''];
        return "<span class=\"inline-flex items-center gap-1 rounded-full px-3 py-1 text-xs font-semibold shadow-md {$roleData['bg']} {$roleData['text']}\">{$roleData['icon']} " . ucfirst($role) . "</span>";
      };
      
      $getInitials = function($name) {
        $words = explode(' ', $name);
        $initials = '';
        foreach ($words as $word) {
          if (!empty($word)) {
            $initials .= strtoupper(substr($word, 0, 1));
            if (strlen($initials) >= 2) break;
          }
        }
        return $initials ?: strtoupper(substr($name, 0, 2));
      };
    @endphp

    {{-- Estadísticas rápidas --}}
    <div class="mb-6 grid grid-cols-1 sm:grid-cols-3 gap-4">
      <div class="rounded-xl border border-gray-200 bg-gradient-to-br from-blue-50 to-blue-100/50 p-4 shadow-sm dark:border-gray-700 dark:from-blue-900/20 dark:to-blue-900/10">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total usuarios</p>
            <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $users->total() }}</p>
          </div>
          <div class="rounded-lg bg-blue-500/20 p-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
          </div>
        </div>
      </div>
      <div class="rounded-xl border border-gray-200 bg-gradient-to-br from-indigo-50 to-indigo-100/50 p-4 shadow-sm dark:border-gray-700 dark:from-indigo-900/20 dark:to-indigo-900/10">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">En esta página</p>
            <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $users->count() }}</p>
          </div>
          <div class="rounded-lg bg-indigo-500/20 p-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
          </div>
        </div>
      </div>
      <div class="rounded-xl border border-gray-200 bg-gradient-to-br from-emerald-50 to-emerald-100/50 p-4 shadow-sm dark:border-gray-700 dark:from-emerald-900/20 dark:to-emerald-900/10">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Página actual</p>
            <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $users->currentPage() }}</p>
          </div>
          <div class="rounded-lg bg-emerald-500/20 p-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
          </div>
        </div>
      </div>
    </div>

    {{-- Lista de usuarios mejorada --}}
    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-xl dark:border-gray-700 dark:bg-gray-800 ring-1 ring-black/5">
      <div class="border-b border-gray-200 bg-gradient-to-r from-gray-50 to-gray-100/50 px-6 py-4 dark:border-gray-700 dark:from-gray-900/50 dark:to-gray-800/50">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-3">
            <div class="rounded-lg bg-indigo-100 p-2 dark:bg-indigo-900/30">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
              </svg>
            </div>
            <div>
              <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Lista de usuarios</h3>
              <p class="text-xs text-gray-500 dark:text-gray-400">{{ $users->total() }} usuarios registrados</p>
            </div>
          </div>
        </div>
      </div>

      <div class="divide-y divide-gray-200 dark:divide-gray-700">
        @forelse($users as $u)
          <div class="group px-6 py-5 transition-all duration-200 hover:bg-gradient-to-r hover:from-indigo-50/50 hover:to-transparent dark:hover:from-indigo-900/10">
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-4 flex-1 min-w-0">
                {{-- Avatar --}}
                <div class="relative flex-shrink-0">
                  <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-500 to-indigo-600 text-lg font-bold text-white shadow-lg ring-2 ring-white dark:ring-gray-800">
                    {{ $getInitials($u->full_name) }}
                  </div>
                  <div class="absolute -bottom-1 -right-1 flex h-5 w-5 items-center justify-center rounded-full bg-white ring-2 ring-white dark:bg-gray-800 dark:ring-gray-800">
                    @if((is_string($u->role) ? $u->role : $u->role->value) === 'admin')
                      <div class="h-3 w-3 rounded-full bg-gradient-to-r from-rose-500 to-rose-600"></div>
                    @elseif((is_string($u->role) ? $u->role : $u->role->value) === 'validator')
                      <div class="h-3 w-3 rounded-full bg-gradient-to-r from-amber-500 to-amber-600"></div>
                    @else
                      <div class="h-3 w-3 rounded-full bg-gradient-to-r from-indigo-500 to-indigo-600"></div>
                    @endif
                  </div>
                </div>

                {{-- Información del usuario --}}
                <div class="flex-1 min-w-0">
                  <div class="flex items-center gap-2 flex-wrap">
                    <h4 class="text-base font-semibold text-gray-900 dark:text-gray-100 truncate">
                      {{ $u->full_name }}
                    </h4>
                    {!! $badge(is_string($u->role) ? $u->role : $u->role->value) !!}
                  </div>
                  <div class="mt-2 flex flex-wrap items-center gap-3 text-sm text-gray-600 dark:text-gray-400">
                    <div class="flex items-center gap-1.5">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                      </svg>
                      <span class="truncate">{{ $u->email }}</span>
                    </div>
                    @if($u->phone)
                      <div class="flex items-center gap-1.5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                        </svg>
                        <span>{{ $u->phone }}</span>
                      </div>
                    @endif
                  </div>
                </div>
              </div>

              {{-- Acciones --}}
              <div class="flex items-center gap-2 ml-4">
                <a href="{{ route('admin.users.edit', $u) }}"
                   class="inline-flex items-center gap-1.5 rounded-lg border border-indigo-300 bg-indigo-50 px-4 py-2 text-xs font-medium text-indigo-700 shadow-sm transition-all duration-200 hover:bg-indigo-100 hover:shadow-md dark:border-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300 dark:hover:bg-indigo-900/50">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                  </svg>
                  Editar
                </a>
                <form method="POST" action="{{ route('admin.users.destroy', $u) }}"
                      onsubmit="return confirm('¿Estás seguro de eliminar a {{ $u->full_name }}? Esta acción no se puede deshacer.');" class="inline">
                  @csrf @method('DELETE')
                  <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg border border-rose-300 bg-rose-50 px-4 py-2 text-xs font-medium text-rose-700 shadow-sm transition-all duration-200 hover:bg-rose-100 hover:shadow-md dark:border-rose-700 dark:bg-rose-900/30 dark:text-rose-300 dark:hover:bg-rose-900/50">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Eliminar
                  </button>
                </form>
              </div>
            </div>
          </div>
        @empty
          <div class="px-6 py-12 text-center">
            <div class="mx-auto max-w-md">
              <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-16 w-16 text-gray-400 dark:text-gray-500 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
              </svg>
              <p class="text-base font-semibold text-gray-900 dark:text-gray-100 mb-1">No hay usuarios registrados</p>
              <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Comienza creando tu primer usuario</p>
              <a href="{{ route('admin.users.create') }}"
                 class="inline-flex items-center gap-2 rounded-lg bg-gradient-to-r from-indigo-500 to-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-lg transition-all duration-200 hover:from-indigo-600 hover:to-indigo-700 hover:shadow-xl">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                Crear primer usuario
              </a>
            </div>
          </div>
        @endforelse
      </div>
    </div>

    <div class="mt-6">
      {{ $users->links() }}
    </div>
  </div>
</x-app-layout>
