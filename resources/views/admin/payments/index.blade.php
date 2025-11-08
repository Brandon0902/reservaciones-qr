{{-- resources/views/admin/payments/index.blade.php --}}
<x-app-layout>
  {{-- ===== Header con back + migas, mismo look & feel ===== --}}
  <x-slot name="header">
    <div class="flex items-center justify-between">
      <div class="flex items-center gap-4">
        <a href="{{ route('admin.dashboard') }}"
           class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm transition-all duration-200 hover:bg-gray-50 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
          </svg>
          Volver
        </a>
        <div>
          <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100">Pagos — Revisión y Aprobación</h2>
          <nav class="text-sm text-gray-500 dark:text-gray-400 mt-1">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-gray-700 dark:hover:text-gray-300 transition-colors">Dashboard</a>
            <span class="mx-1">/</span>
            <span>Pagos</span>
          </nav>
        </div>
      </div>
    </div>
  </x-slot>

  <div class="py-6 mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 space-y-6">

    {{-- ===== Flash messages (con misma estética de alert) ===== --}}
    @php
      $flashMap = [
        'success' => ['from' => 'emerald-50', 'to' => 'emerald-100/50', 'text' => 'emerald-800', 'darkFrom' => 'emerald-900/40', 'darkTo' => 'emerald-900/20', 'darkText' => 'emerald-200', 'ring' => 'emerald-500/20', 'border' => 'emerald-300/50', 'darkBorder' => 'emerald-800/50'],
        'warning' => ['from' => 'amber-50',   'to' => 'amber-100/50',   'text' => 'amber-800',   'darkFrom' => 'amber-900/40',   'darkTo' => 'amber-900/20',   'darkText' => 'amber-200',   'ring' => 'amber-500/20',   'border' => 'amber-300/50',   'darkBorder' => 'amber-800/50'],
        'info'    => ['from' => 'sky-50',     'to' => 'sky-100/50',     'text' => 'sky-800',     'darkFrom' => 'sky-900/40',     'darkTo' => 'sky-900/20',     'darkText' => 'sky-200',     'ring' => 'sky-500/20',     'border' => 'sky-300/50',     'darkBorder' => 'sky-800/50'],
        'error'   => ['from' => 'rose-50',    'to' => 'rose-100/50',    'text' => 'rose-800',    'darkFrom' => 'rose-900/40',    'darkTo' => 'rose-900/20',    'darkText' => 'rose-200',    'ring' => 'rose-500/20',    'border' => 'rose-300/50',    'darkBorder' => 'rose-800/50'],
      ];
    @endphp

    @foreach ($flashMap as $key => $sty)
      @if (session($key))
        <div class="rounded-xl border bg-gradient-to-r from-{{ $sty['from'] }} to-{{ $sty['to'] }} px-4 py-3 text-{{ $sty['text'] }} shadow-sm ring-1 ring-{{ $sty['ring'] }} dark:border-{{ $sty['darkBorder'] }} dark:from-{{ $sty['darkFrom'] }} dark:to-{{ $sty['darkTo'] }} dark:text-{{ $sty['darkText'] }} border-{{ $sty['border'] }}">
          <div class="flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            {{ session($key) }}
          </div>
        </div>
      @endif
    @endforeach

    {{-- ===== Tarjeta de filtros (match con “Nuevo servicio extra”) ===== --}}
    <div class="rounded-2xl border border-gray-200 bg-gradient-to-br from-white to-gray-50/50 shadow-xl dark:border-gray-700 dark:from-gray-800 dark:to-gray-900/50 ring-1 ring-black/5 p-6">
      <div class="mb-6 flex items-center gap-3 pb-4 border-b border-gray-200 dark:border-gray-700">
        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-500 to-indigo-600 shadow-lg">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h10M5 6h14" />
          </svg>
        </div>
        <div>
          <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Filtros</h3>
          <p class="text-sm text-gray-500 dark:text-gray-400">Refina la búsqueda por texto y estado</p>
        </div>
      </div>

      <form method="GET" action="{{ route('admin.payments.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
        @php
          $statusOptions = [
            ''           => 'Todos',
            'created'    => 'CREATED',
            'pending'    => 'PENDING',
            'paid'       => 'PAID',
            'rejected'   => 'REJECTED',
            'refunded'   => 'REFUNDED',
          ];
        @endphp

        <div class="md:col-span-2 space-y-2">
          <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 dark:text-gray-300">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M11 18a7 7 0 100-14 7 7 0 000 14z"/>
            </svg>
            Buscar
          </label>
          <input type="text" name="q" value="{{ old('q', $q ?? '') }}"
                 placeholder="Folio / referencia / evento / nota…"
                 class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-gray-900 placeholder-gray-400 shadow-sm transition-all duration-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:outline-none dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 dark:placeholder-gray-500 dark:focus:border-indigo-400">
        </div>

        <div class="md:col-span-1 space-y-2">
          <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 dark:text-gray-300">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h7"/>
            </svg>
            Estado
          </label>
          <select name="status"
                  class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-gray-900 shadow-sm transition-all duration-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:outline-none dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100">
            @foreach($statusOptions as $val => $label)
              <option value="{{ $val }}" @selected(($status ?? '') === $val)>{{ $label }}</option>
            @endforeach
          </select>
        </div>

        <div class="md:col-span-2 flex gap-2 mt-6 md:mt-8">
          <button type="submit"
                  class="inline-flex items-center gap-2 rounded-lg bg-gradient-to-r from-emerald-500 to-emerald-600 px-5 py-2.5 text-sm font-medium text-white shadow-lg transition-all duration-200 hover:from-emerald-600 hover:to-emerald-700 hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M12 5l7 7-7 7"/>
            </svg>
            Filtrar
          </button>
          <a href="{{ route('admin.payments.index') }}"
             class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-5 py-2.5 text-sm font-medium text-gray-700 shadow-sm transition-all duration-200 hover:bg-gray-50 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">
            Limpiar
          </a>
        </div>
      </form>
    </div>

    {{-- ===== Tabla en tarjeta “premium” ===== --}}
    <div class="rounded-2xl border border-gray-200 bg-gradient-to-br from-white to-gray-50/50 shadow-xl dark:border-gray-700 dark:from-gray-800 dark:to-gray-900/50 ring-1 ring-black/5 overflow-hidden">
      <div class="px-6 pt-6 pb-3 flex items-center gap-3">
        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-br from-violet-500 to-violet-600 shadow">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-2.21 0-4 1.79-4 4v6h8v-6c0-2.21-1.79-4-4-4zM6 12H4m16 0h-2M6 20h12"/>
          </svg>
        </div>
        <div>
          <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Listado de pagos</h3>
          <p class="text-sm text-gray-500 dark:text-gray-400">Administra estados y verifica comprobantes</p>
        </div>
      </div>

      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
          <thead class="bg-gray-50/70 dark:bg-gray-900/40">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-300">ID</th>
              <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-300">Fecha</th>
              <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-300">Reserva</th>
              <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-300">Cliente</th>
              <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-300">Monto</th>
              <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-300">Método</th>
              <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-300">Estado</th>
              <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-300">Comprobante</th>
              <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-300">Acciones</th>
            </tr>
          </thead>

          <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
          @php
            $badgeMap = [
              'created'  => 'bg-slate-100 text-slate-800 ring-1 ring-slate-500/20 dark:bg-slate-900/40 dark:text-slate-200',
              'pending'  => 'bg-amber-100 text-amber-800 ring-1 ring-amber-500/20 dark:bg-amber-900/40 dark:text-amber-200',
              'paid'     => 'bg-emerald-100 text-emerald-800 ring-1 ring-emerald-500/20 dark:bg-emerald-900/40 dark:text-emerald-200',
              'rejected' => 'bg-rose-100 text-rose-800 ring-1 ring-rose-500/20 dark:bg-rose-900/40 dark:text-rose-200',
              'refunded' => 'bg-sky-100 text-sky-800 ring-1 ring-sky-500/20 dark:bg-sky-900/40 dark:text-sky-200',
            ];
            $allStatuses = ['created'=>'CREATED','pending'=>'PENDING','paid'=>'PAID','rejected'=>'REJECTED','refunded'=>'REFUNDED'];
          @endphp

          @forelse ($rows as $row)
            @php
              $reservation = $row->reservation;
              $user        = $reservation?->user;
              $receiptUrl  = $row->receipt_ref
                               ? (\Illuminate\Support\Facades\Storage::disk('receipts')->exists($row->receipt_ref)
                                  ? \Illuminate\Support\Facades\Storage::disk('receipts')->url($row->receipt_ref)
                                  : null)
                               : null;
            @endphp

            <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-900/20 transition">
              <td class="px-6 py-3 text-sm text-gray-800 dark:text-gray-100">#{{ $row->id }}</td>

              <td class="px-6 py-3 text-sm text-gray-700 dark:text-gray-300">
                {{ $row->created_at?->timezone(config('app.timezone'))->format('Y-m-d H:i') ?? '—' }}
              </td>

              <td class="px-6 py-3 text-sm text-gray-700 dark:text-gray-300">
                @if($reservation)
                  <div class="font-medium text-gray-900 dark:text-gray-100">
                    {{ $reservation->event_name ?: 'Reservación #'.$reservation->id }}
                  </div>
                  <div class="text-xs text-gray-500 dark:text-gray-400">
                    {{ $reservation->date?->format('d/m/Y') }} • {{ strtoupper($reservation->shift) }}
                  </div>
                @else
                  <span class="text-xs italic text-gray-500">Sin reserva</span>
                @endif
              </td>

              <td class="px-6 py-3 text-sm text-gray-700 dark:text-gray-300">
                @if($user)
                  <div class="font-medium">{{ $user->name }}</div>
                  <div class="text-xs text-gray-500">{{ $user->email }}</div>
                @else
                  <span class="text-xs italic text-gray-500">—</span>
                @endif
              </td>

              <td class="px-6 py-3 text-sm text-gray-800 dark:text-gray-100 font-semibold">
                ${{ number_format($row->amount, 2, '.', ',') }} {{ $row->currency }}
              </td>

              <td class="px-6 py-3 text-sm text-gray-700 dark:text-gray-300">
                {{ $row->method?->value ?? '—' }}
              </td>

              <td class="px-6 py-3 text-sm">
                <span class="inline-flex items-center rounded-full px-2 py-0.5 {{ $badgeMap[$row->status->value] ?? 'bg-gray-100 text-gray-800 ring-1 ring-gray-500/20 dark:bg-gray-900/40 dark:text-gray-200' }}">
                  {{ strtoupper($row->status->value) }}
                </span>
                @if($row->approved_by)
                  <div class="mt-1 text-[11px] text-gray-500 dark:text-gray-400">
                    Aprobado: {{ $row->approved_at?->format('d/m/Y H:i') }}
                  </div>
                @endif
              </td>

              <td class="px-6 py-3 text-sm">
                @if ($receiptUrl)
                  <a href="{{ $receiptUrl }}" target="_blank"
                     class="inline-flex items-center gap-1.5 rounded-md px-2.5 py-1.5 text-xs bg-slate-200 text-slate-900 hover:bg-slate-300 dark:bg-slate-700 dark:text-slate-100 dark:hover:bg-slate-600 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Ver comprobante
                  </a>
                @else
                  <span class="text-xs text-gray-500">No adjunto</span>
                @endif
              </td>

              <td class="px-6 py-3 text-sm text-right">
                <form method="POST"
                      action="{{ route('admin.payments.update-status', $row) }}"
                      onsubmit="const v=this.querySelector('select').value; if(['paid','refunded','rejected'].includes(v)){return confirm('¿Confirmas cambiar el estado a '+v.toUpperCase()+'?');} return true;"
                      class="inline-flex items-center gap-2">
                  @csrf
                  @method('patch')

                  <select name="status"
                          class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                    @foreach($allStatuses as $val => $label)
                      <option value="{{ $val }}" @selected($row->status->value === $val)>{{ $label }}</option>
                    @endforeach
                  </select>

                  <button type="submit"
                          class="inline-flex items-center gap-2 rounded-lg bg-gradient-to-r from-indigo-600 to-indigo-700 px-3.5 py-2 text-sm font-medium text-white shadow transition hover:from-indigo-700 hover:to-indigo-800">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                    Aplicar
                  </button>
                </form>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="9" class="px-6 py-12 text-center text-sm text-gray-500 dark:text-gray-400">
                No hay pagos para mostrar.
              </td>
            </tr>
          @endforelse
          </tbody>
        </table>
      </div>

      {{-- Paginación --}}
      <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
        {{ $rows->links() }}
      </div>
    </div>
  </div>
</x-app-layout>
