@props(['active' => false])

@php
$classes = $active
    ? 'block border-l-4 border-green-500 bg-green-50 py-2 pl-3 pr-4 text-base font-medium text-green-700'
    : 'block border-l-4 border-transparent py-2 pl-3 pr-4 text-base font-medium text-gray-600 hover:border-gray-300 hover:bg-gray-50 hover:text-gray-800';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
