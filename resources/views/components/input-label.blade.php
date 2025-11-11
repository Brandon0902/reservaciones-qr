@props(['value'])

<label {{ $attributes->merge([
    'class' => 'block text-sm font-medium
                text-gray-800 dark:text-gray-200'
]) }}>
    {{ $value ?? $slot }}
</label>
