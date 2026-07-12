@props([
    'variant' => 'default',
    'size' => 'md',
])

@php
$variants = [
    'default' => 'bg-gray-100 text-gray-700',
    'primary' => 'bg-green-100 text-green-700',
    'success' => 'bg-emerald-100 text-emerald-700',
    'warning' => 'bg-yellow-100 text-yellow-800',
    'danger' => 'bg-red-100 text-red-700',
    'info' => 'bg-blue-100 text-blue-700',
];

$sizes = [
    'sm' => 'px-2 py-0.5 text-xs',
    'md' => 'px-2.5 py-0.5 text-sm',
    'lg' => 'px-3 py-1 text-sm',
];

$classes = 'inline-flex items-center rounded-full font-medium ' .
    ($variants[$variant] ?? $variants['default']) . ' ' .
    ($sizes[$size] ?? $sizes['md']);
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</span>
