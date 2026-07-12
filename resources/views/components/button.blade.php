@props([
    'type' => 'submit',
    'variant' => 'primary',
    'size' => 'md',
    'disabled' => false,
])

@php
$baseClasses = 'inline-flex items-center justify-center font-semibold rounded-md shadow-sm focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-colors';

$variants = [
    'primary' => 'bg-green-600 text-white hover:bg-green-500 focus-visible:outline-green-600',
    'secondary' => 'bg-white text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50',
    'danger' => 'bg-red-600 text-white hover:bg-red-500 focus-visible:outline-red-600',
    'ghost' => 'bg-transparent text-gray-700 hover:bg-gray-100',
];

$sizes = [
    'xs' => 'px-2 py-1 text-xs',
    'sm' => 'px-2.5 py-1.5 text-sm',
    'md' => 'px-3 py-2 text-sm',
    'lg' => 'px-4 py-2 text-base',
    'xl' => 'px-6 py-3 text-base',
];

$classes = $baseClasses . ' ' . ($variants[$variant] ?? $variants['primary']) . ' ' . ($sizes[$size] ?? $sizes['md']);
@endphp

<button
    type="{{ $type }}"
    {{ $disabled ? 'disabled' : '' }}
    {{ $attributes->merge(['class' => $classes]) }}
>
    {{ $slot }}
</button>
