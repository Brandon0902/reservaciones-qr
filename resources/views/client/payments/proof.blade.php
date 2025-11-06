<x-app-layout>
  <x-slot name="header">
    <div class="flex items-center justify-between">
      <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100">Subir comprobante</h2>
      <a href="{{ route('client.dashboard') }}" class="px-3 py-1.5 rounded-md bg-slate-200 text-slate-900 hover:bg-slate-300
         dark:bg-slate-700 dark:text-slate-100 dark:hover:bg-slate-600 text-sm">← Volver</a>
    </div>
  </x-slot>

  <div class="py-6 mx-auto max-w-2xl px-4 sm:px-6 lg:px-8">
    @if (session('warning'))
      <div class="mb-4 rounded bg-amber-500/10 text-amber-200 p-3">{{ session('warning') }}</div>
    @endif
    @if ($errors->any())
      <div class="mb-4 rounded bg-rose-500/10 text-rose-200 p-3">
        <ul class="list-disc list-inside text-sm">
          @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
        </ul>
      </div>
    @endif

    <div class="rounded-2xl border border-white/10 bg-white/5 p-5 text-slate-100">
      <div class="text-sm text-slate-300 mb-3">
        Tu reservación: <strong>{{ $reservation->event_name }}</strong> — {{ \Illuminate\Support\Carbon::parse($reservation->date)->format('d/m/Y') }}.
        Tienes <strong>12 horas</strong> para subir tu comprobante.
      </div>

      <form method="POST" action="{{ route('client.payments.proof.store', $reservation) }}" enctype="multipart/form-data" class="space-y-4">
        @csrf

        <div>
          <label class="block text-slate-300 text-sm font-medium mb-1">Método de pago</label>
          <div class="flex gap-4">
            <label class="inline-flex items-center gap-2">
              <input type="radio" name="method" value="deposit" class="accent-violet-600"> Depósito
            </label>
            <label class="inline-flex items-center gap-2">
              <input type="radio" name="method" value="transfer" class="accent-violet-600"> Transferencia
            </label>
          </div>
        </div>

        <div>
          <label for="receipt" class="block text-slate-300 text-sm font-medium mb-1">Comprobante (PDF/JPG/PNG)</label>
          <input id="receipt" name="receipt" type="file"
                 accept=".pdf,.jpg,.jpeg,.png"
                 class="block w-full rounded border-white/10 bg-slate-900/40 px-3 py-2">
        </div>

        <div>
          <label for="notes" class="block text-slate-300 text-sm font-medium mb-1">Notas (opcional)</label>
          <textarea id="notes" name="notes" rows="3" class="block w-full rounded border-white/10 bg-slate-900/40"></textarea>
        </div>

        <div class="flex justify-end">
          <x-primary-button class="rounded-xl px-5">Enviar comprobante</x-primary-button>
        </div>
      </form>
    </div>
  </div>
</x-app-layout>
