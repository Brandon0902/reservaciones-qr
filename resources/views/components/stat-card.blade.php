@props(['title' => '','value' => '—','hint' => null, 'icon' => null, 'color' => 'indigo'])

@php
  $colorClasses = [
    'indigo' => 'from-indigo-500/20 via-indigo-500/10 to-transparent border-indigo-500/20 dark:from-indigo-500/30 dark:via-indigo-500/20 dark:border-indigo-500/30',
    'blue' => 'from-blue-500/20 via-blue-500/10 to-transparent border-blue-500/20 dark:from-blue-500/30 dark:via-blue-500/20 dark:border-blue-500/30',
    'emerald' => 'from-emerald-500/20 via-emerald-500/10 to-transparent border-emerald-500/20 dark:from-emerald-500/30 dark:via-emerald-500/20 dark:border-emerald-500/30',
    'amber' => 'from-amber-500/20 via-amber-500/10 to-transparent border-amber-500/20 dark:from-amber-500/30 dark:via-amber-500/20 dark:border-amber-500/30',
    'rose' => 'from-rose-500/20 via-rose-500/10 to-transparent border-rose-500/20 dark:from-rose-500/30 dark:via-rose-500/20 dark:border-rose-500/30',
  ];
  $iconColorClasses = [
    'indigo' => 'text-indigo-400 dark:text-indigo-300',
    'blue' => 'text-blue-400 dark:text-blue-300',
    'emerald' => 'text-emerald-400 dark:text-emerald-300',
    'amber' => 'text-amber-400 dark:text-amber-300',
    'rose' => 'text-rose-400 dark:text-rose-300',
  ];
  $gradientClass = $colorClasses[$color] ?? $colorClasses['indigo'];
  $iconColorClass = $iconColorClasses[$color] ?? $iconColorClasses['indigo'];
@endphp

<div class="group relative overflow-hidden rounded-2xl border bg-gradient-to-br {{ $gradientClass }} p-6 shadow-lg transition-all duration-300 hover:scale-[1.02] hover:shadow-xl dark:bg-gray-900/50">
  {{-- Efecto de brillo sutil --}}
  <div class="absolute inset-0 bg-gradient-to-br from-white/5 to-transparent opacity-0 transition-opacity duration-300 group-hover:opacity-100"></div>
  
  <div class="relative z-10">
    <div class="flex items-start justify-between">
      <div class="flex-1">
        <div class="mb-2 flex items-center gap-2">
          @if($icon)
            <div class="rounded-lg bg-white/10 p-2 backdrop-blur-sm {{ $iconColorClass }}">
              {!! $icon !!}
            </div>
          @endif
          <div class="text-sm font-medium text-gray-600 dark:text-gray-300">{{ $title }}</div>
        </div>
        <div class="mt-3 text-4xl font-bold tracking-tight text-gray-900 dark:text-gray-100">
          {{ $value }}
        </div>
        @if($hint)
          <div class="mt-2 text-xs text-gray-500 dark:text-gray-400">{{ $hint }}</div>
        @endif
      </div>
    </div>
    
    {{-- Decoración de esquina --}}
    <div class="absolute -right-4 -top-4 h-24 w-24 rounded-full bg-gradient-to-br from-white/10 to-transparent opacity-50 blur-xl"></div>
  </div>
</div>
