@props(['title' => '','value' => '—','hint' => null])

<div class="rounded-xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-4 shadow-sm">
  <div class="text-sm text-gray-500 dark:text-gray-400">{{ $title }}</div>
  <div class="mt-1 text-2xl font-semibold text-gray-900 dark:text-gray-100">
    {{ $value }}
  </div>
  @if($hint)
    <div class="mt-1 text-xs text-gray-400">{{ $hint }}</div>
  @endif
</div>
