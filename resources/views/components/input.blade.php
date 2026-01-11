@props([
    'type' => 'text',
    'name' => '',
    'label' => '',
    'placeholder' => '',
    'required' => false,
    'disabled' => false,
    'error' => null,
    'hint' => null,
])

<div>
    @if($label)
    <label for="{{ $name }}" class="block text-sm font-medium leading-6 text-gray-900">
        {{ $label }}
        @if($required)<span class="text-red-500">*</span>@endif
    </label>
    @endif

    <div class="mt-2">
        <input
            type="{{ $type }}"
            name="{{ $name }}"
            id="{{ $name }}"
            placeholder="{{ $placeholder }}"
            {{ $required ? 'required' : '' }}
            {{ $disabled ? 'disabled' : '' }}
            {{ $attributes->merge([
                'class' => 'block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-green-600 sm:text-sm sm:leading-6 disabled:bg-gray-50 disabled:text-gray-500 ' . ($error ? 'ring-red-300 focus:ring-red-500' : 'ring-gray-300')
            ]) }}
        >
    </div>

    @if($hint && !$error)
    <p class="mt-1 text-xs text-gray-500">{{ $hint }}</p>
    @endif

    @if($error)
    <p class="mt-2 text-sm text-red-600">{{ $error }}</p>
    @endif
</div>
