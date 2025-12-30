@props([
    'variant' => 'default', // default, premium
    'showText' => true,
    'size' => 'md', // sm, md, lg
    'dark' => false, // for dark backgrounds
    'textClass' => '',
])

@php
    $sizes = [
        'sm' => ['container' => 'w-6 h-6', 'icon' => 'w-3 h-3', 'circle' => 'w-4 h-4', 'text' => 'text-base'],
        'md' => ['container' => 'w-8 h-8', 'icon' => 'w-4 h-4', 'circle' => 'w-5 h-5', 'text' => 'text-lg'],
        'lg' => ['container' => 'w-10 h-10', 'icon' => 'w-5 h-5', 'circle' => 'w-6 h-6', 'text' => 'text-xl'],
    ];
    $s = $sizes[$size] ?? $sizes['md'];
    $textColor = $dark ? 'text-white' : '';
    $textStyle = $dark ? '' : 'color: var(--text-primary);';
@endphp

<div {{ $attributes->merge(['class' => 'flex items-center space-x-2']) }}>
    @if($variant === 'premium')
        {{-- Premium variant with gradient and white circle --}}
        <div class="{{ $s['container'] }} rounded-lg flex items-center justify-center" style="background: linear-gradient(135deg, var(--accent, #0d9488) 0%, #14b8a6 100%);">
            <div class="{{ $s['circle'] }} rounded-full flex items-center justify-center" style="background: rgba(255,255,255,0.35);">
                <svg class="{{ $s['icon'] }} text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
            </div>
        </div>
    @else
        {{-- Default variant --}}
        <div class="{{ $s['container'] }} rounded-lg flex items-center justify-center bg-green-600">
            <svg class="{{ $s['icon'] }} text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
            </svg>
        </div>
    @endif

    @if($showText)
        <span class="{{ $s['text'] }} font-bold {{ $textColor }} {{ $textClass }}" @if($textStyle) style="{{ $textStyle }}" @endif>Carbex</span>
    @endif
</div>
