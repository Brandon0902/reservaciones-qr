<x-app-layout>
  <x-slot name="header">
    <div class="flex items-center justify-between">
      <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
        Mis reservaciones
      </h2>
      <a href="{{ route('client.reservations.create') }}"
         class="px-3 py-1.5 rounded-lg bg-[#6d28d9] text-white hover:bg-[#6d28d9]/90 text-sm">
        Nueva reservación
      </a>
    </div>
  </x-slot>

  <div class="py-6 mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 space-y-6">
    @if(session('success'))
      <div class="rounded-lg bg-emerald-50 p-3 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-200 ring-1 ring-emerald-500/20">
        {{ session('success') }}
      </div>
    @endif

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
      @forelse ($rows as $r)
        <div class="rounded-2xl bg-white dark:bg-gray-800 border border-gray-200/50 dark:border-gray-700/50 p-5 hover:shadow-lg transition">
          <div class="text-sm text-gray-500 dark:text-gray-400">#{{ $r->id }}</div>
          <div class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">
            {{ $r->event_name ?: 'Reservación' }}
          </div>
          <div class="mt-1 text-sm text-gray-600 dark:text-gray-300">
            {{ optional($r->date)->format('d/m/Y') }} • {{ $r->shift === 'day' ? 'DÍA' : 'NOCHE' }}
          </div>

          <div class="mt-3">
            @php
              $badge = [
                'pending'   => 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-200',
                'confirmed' => 'bg-sky-100 text-sky-800 dark:bg-sky-900/40 dark:text-sky-200',
                'checked_in'=> 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/40 dark:text-indigo-200',
                'completed' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-200',
                'canceled'  => 'bg-rose-100 text-rose-800 dark:bg-rose-900/40 dark:text-rose-200',
              ][$r->status->value] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-900/40 dark:text-gray-200';
            @endphp
            <span class="inline-flex rounded-full px-2 py-0.5 text-xs {{ $badge }}">
              {{ strtoupper($r->status->value) }}
            </span>
          </div>

          <div class="mt-4 flex items-center justify-between">
            <div class="text-sm text-gray-500 dark:text-gray-400">
              Boletos: {{ $r->tickets_count ?: 0 }}
            </div>
            <div class="flex gap-2">
              <a href="{{ route('client.reservations.show', $r) }}"
                 class="px-3 py-1.5 rounded-md bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 text-sm">
                Detalle
              </a>

              @if($r->tickets_count > 0)
                <a href="{{ route('client.reservations.tickets', $r) }}"
                   class="px-3 py-1.5 rounded-md bg-[#6d28d9] text-white hover:bg-[#6d28d9]/90 text-sm">
                  Mis boletos
                </a>
              @endif
            </div>
          </div>
        </div>
      @empty
        <div class="col-span-full">
          <div class="rounded-xl border border-dashed border-gray-300 dark:border-gray-700 p-8 text-center">
            <div class="text-gray-600 dark:text-gray-300">Aún no tienes reservaciones.</div>
            <a href="{{ route('client.reservations.create') }}" class="mt-3 inline-flex rounded-lg bg-[#6d28d9] text-white px-4 py-2 hover:bg-[#6d28d9]/90">Crear reservación</a>
          </div>
        </div>
      @endforelse
    </div>

    <div>
      {{ $rows->links() }}
    </div>
  </div>
</x-app-layout>
