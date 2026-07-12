@props([
    'type' => 'info',
    'dismissible' => false,
])

@php
$types = [
    'info' => [
        'bg' => 'bg-blue-50',
        'border' => 'border-blue-400',
        'text' => 'text-blue-800',
        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />',
    ],
    'success' => [
        'bg' => 'bg-green-50',
        'border' => 'border-green-400',
        'text' => 'text-green-800',
        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />',
    ],
    'warning' => [
        'bg' => 'bg-yellow-50',
        'border' => 'border-yellow-400',
        'text' => 'text-yellow-800',
        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />',
    ],
    'error' => [
        'bg' => 'bg-red-50',
        'border' => 'border-red-400',
        'text' => 'text-red-800',
        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />',
    ],
];

$config = $types[$type] ?? $types['info'];
@endphp

<div x-data="{ show: true }" x-show="show" class="rounded-md {{ $config['bg'] }} border-l-4 {{ $config['border'] }} p-4">
    <div class="flex">
        <div class="flex-shrink-0">
            <svg class="h-5 w-5 {{ $config['text'] }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                {!! $config['icon'] !!}
            </svg>
        </div>
        <div class="ml-3 flex-1">
            <p class="text-sm {{ $config['text'] }}">
                {{ $slot }}
            </p>
        </div>
        @if($dismissible)
        <div class="ml-auto pl-3">
            <button @click="show = false" type="button" class="{{ $config['text'] }} hover:opacity-75">
                <span class="sr-only">Dismiss</span>
                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
                </svg>
            </button>
        </div>
        @endif
    </div>
</div>
