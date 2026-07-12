@props([
    'padding' => true,
])

<div {{ $attributes->merge(['class' => 'bg-white shadow-sm ring-1 ring-gray-900/5 rounded-lg' . ($padding ? ' p-6' : '')]) }}>
    @isset($header)
    <div class="border-b border-gray-200 pb-4 mb-4">
        {{ $header }}
    </div>
    @endisset

    {{ $slot }}

    @isset($footer)
    <div class="border-t border-gray-200 pt-4 mt-4">
        {{ $footer }}
    </div>
    @endisset
</div>
