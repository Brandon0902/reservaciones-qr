@props(['disabled' => false])

<input
  @disabled($disabled)
  {{ $attributes->merge([
      'class' => '
        block w-full rounded-md shadow-sm
        border border-gray-300 focus:border-indigo-500 focus:ring-indigo-500
        bg-white text-gray-900 placeholder-gray-400
        dark:border-white/15 dark:bg-white/10 dark:text-white dark:placeholder-gray-400
        dark:focus:border-indigo-400 dark:focus:ring-indigo-400
      '
  ]) }}
/>
