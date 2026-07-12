@props(['active' => false])

@php
$classes = $active
    ? 'border-green-500 text-gray-900 inline-flex items-center border-b-2 px-1 pt-1 text-sm font-medium'
    : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center border-b-2 px-1 pt-1 text-sm font-medium';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
