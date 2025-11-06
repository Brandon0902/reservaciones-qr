{{-- resources/views/admin/payments/index.blade.php --}}
<x-app-layout>
  {{-- ===== Header ===== --}}
  <x-slot name="header">
    <div class="flex items-center justify-between gap-4">
      <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
        Pagos — Revisión y Aprobación
      </h2>
      <a href="{{ route('admin.dashboard') }}"
         class="px-3 py-1.5 rounded-md bg-slate-200 text-slate-900 hover:bg-slate-300
                dark:bg-slate-700 dark:text-slate-100 dark:hover:bg-slate-600 text-sm">
        ← Dashboard
      </a>
    </div>
  </x-slot>

  <div class="py-6 mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 space-y-6">

    {{-- ===== Flash messages ===== --}}
    @foreach (['success'=>'emerald', 'warning'=>'amber', 'info'=>'sky', 'error'=>'rose'] as $key => $color)
      @if (session($key))
        <div class="rounded-lg bg-{{ $color }}-50 p-3 text-{{ $color }}-800 dark:bg-{{ $color }}-900/40 dark:text-{{ $color }}-200 ring-1 ring-{{ $color }}-500/20">
          {{ session($key) }}
        </div>
      @endif
    @endforeach

    {{-- ===== Filtros ===== --}}
    <div class="rounded-xl bg-white dark:bg-gray-800 shadow p-4">
      <form method="GET" action="{{ route('admin.payments.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-3 items-end">
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

        <div class="md:col-span-2">
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Buscar</label>
          <input type="text" name="q" value="{{ old('q', $q ?? '') }}"
                 placeholder="Folio / referencia / evento / nota…"
                 class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 focus:ring-violet-500 focus:border-violet-500">
        </div>

        <div class="md:col-span-1">
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Estado</label>
          <select name="status" class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 focus:ring-violet-500 focus:border-violet-500">
            @foreach($statusOptions as $val => $label)
              <option value="{{ $val }}" @selected(($status ?? '') === $val)>{{ $label }}</option>
            @endforeach
          </select>
        </div>

        <div class="md:col-span-2 flex gap-2">
          <button type="submit"
                  class="inline-flex items-center justify-center rounded-lg px-4 py-2 bg-violet-600 text-white hover:bg-violet-700 shadow">
            Filtrar
          </button>
          <a href="{{ route('admin.payments.index') }}"
             class="inline-flex items-center justify-center rounded-lg px-4 py-2 bg-slate-200 text-slate-900 hover:bg-slate-300 dark:bg-slate-700 dark:text-slate-100 dark:hover:bg-slate-600">
            Limpiar
          </a>
        </div>
      </form>
    </div>

    {{-- ===== Tabla ===== --}}
    <div class="rounded-xl overflow-hidden bg-white dark:bg-gray-800 shadow">
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
          <thead class="bg-gray-50 dark:bg-gray-900/40">
            <tr>
              <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-300">ID</th>
              <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-300">Fecha</th>
              <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-300">Reserva</th>
              <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-300">Cliente</th>
              <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-300">Monto</th>
              <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-300">Método</th>
              <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-300">Estado</th>
              <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-300">Comprobante</th>
              <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-300">Acciones</th>
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

            <tr>
              <td class="px-4 py-3 text-sm text-gray-800 dark:text-gray-100">#{{ $row->id }}</td>
              <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                {{ $row->created_at?->timezone(config('app.timezone'))->format('Y-m-d H:i') ?? '—' }}
              </td>
              <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
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
              <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                @if($user)
                  <div class="font-medium">{{ $user->name }}</div>
                  <div class="text-xs text-gray-500">{{ $user->email }}</div>
                @else
                  <span class="text-xs italic text-gray-500">—</span>
                @endif
              </td>
              <td class="px-4 py-3 text-sm text-gray-800 dark:text-gray-100 font-semibold">
                ${{ number_format($row->amount, 2, '.', ',') }} {{ $row->currency }}
              </td>
              <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                {{ $row->method?->value ?? '—' }}
              </td>
              <td class="px-4 py-3 text-sm">
                <span class="inline-flex items-center rounded-full px-2 py-0.5 {{ $badgeMap[$row->status->value] ?? 'bg-gray-100 text-gray-800 ring-1 ring-gray-500/20 dark:bg-gray-900/40 dark:text-gray-200' }}">
                  {{ strtoupper($row->status->value) }}
                </span>
                @if($row->approved_by)
                  <div class="mt-1 text-[11px] text-gray-500 dark:text-gray-400">
                    Aprobado: {{ $row->approved_at?->format('d/m/Y H:i') }}
                  </div>
                @endif
              </td>
              <td class="px-4 py-3 text-sm">
                @if ($receiptUrl)
                  <a href="{{ $receiptUrl }}" target="_blank"
                     class="inline-flex items-center rounded-md px-2 py-1 text-xs bg-slate-200 text-slate-900 hover:bg-slate-300 dark:bg-slate-700 dark:text-slate-100 dark:hover:bg-slate-600">
                    Ver comprobante
                  </a>
                @else
                  <span class="text-xs text-gray-500">No adjunto</span>
                @endif
              </td>
              <td class="px-4 py-3 text-sm text-right">
                {{-- Select para cambiar estado --}}
                <form method="POST"
                      action="{{ route('admin.payments.update-status', $row) }}"
                      onsubmit="const v=this.querySelector('select').value; if(['paid','refunded','rejected'].includes(v)){return confirm('¿Confirmas cambiar el estado a '+v.toUpperCase()+'?');} return true;"
                      class="inline-flex items-center gap-2">
                  @csrf
                  @method('patch')

                  <select name="status"
                          class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 focus:ring-violet-500 focus:border-violet-500 text-sm">
                    @foreach($allStatuses as $val => $label)
                      <option value="{{ $val }}" @selected($row->status->value === $val)>{{ $label }}</option>
                    @endforeach
                  </select>

                  <button type="submit"
                          class="px-2.5 py-1.5 rounded-md bg-indigo-600 text-white hover:bg-indigo-700">
                    Aplicar
                  </button>
                </form>

                {{-- (Opcional) Botones rápidos se pueden dejar, pero el select ya cubre todo. --}}
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="9" class="px-4 py-10 text-center text-sm text-gray-500 dark:text-gray-400">
                No hay pagos para mostrar.
              </td>
            </tr>
          @endforelse
          </tbody>
        </table>
      </div>

      {{-- Paginación --}}
      <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
        {{ $rows->links() }}
      </div>
    </div>
  </div>
</x-app-layout>
