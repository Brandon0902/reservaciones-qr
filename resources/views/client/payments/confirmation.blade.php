<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100">Pago en revisión</h2>
  </x-slot>

  <div class="py-10 mx-auto max-w-2xl px-4 sm:px-6 lg:px-8">
    @if (session('success'))
      <div class="mb-4 rounded bg-emerald-500/10 text-emerald-200 p-3">{{ session('success') }}</div>
    @endif

    <div class="rounded-2xl border border-emerald-500/20 bg-emerald-500/10 p-6 text-emerald-100">
      <p class="mb-2">¡Gracias! Hemos recibido tu comprobante.</p>
      <p class="mb-2">Tu pago quedó <strong>pendiente de validación</strong>. Te avisaremos por correo cuando sea aprobado.</p>
      <p>Reservación: <strong>{{ $reservation->event_name }}</strong> — {{ \Illuminate\Support\Carbon::parse($reservation->date)->format('d/m/Y') }}.</p>
    </div>
  </div>
</x-app-layout>
