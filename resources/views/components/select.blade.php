@props([
    'name' => '',
    'label' => '',
    'required' => false,
    'disabled' => false,
    'error' => null,
    'placeholder' => null,
])

<div>
    @if($label)
    <label for="{{ $name }}" class="block text-sm font-medium leading-6 text-gray-900">
        {{ $label }}
        @if($required)<span class="text-red-500">*</span>@endif
    </label>
    @endif

    <div class="mt-2">
        <select
            name="{{ $name }}"
            id="{{ $name }}"
            {{ $required ? 'required' : '' }}
            {{ $disabled ? 'disabled' : '' }}
            {{ $attributes->merge([
                'class' => 'block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset focus:ring-2 focus:ring-inset focus:ring-green-600 sm:text-sm sm:leading-6 disabled:bg-gray-50 disabled:text-gray-500 ' . ($error ? 'ring-red-300 focus:ring-red-500' : 'ring-gray-300')
            ]) }}
        >
            @if($placeholder)
            <option value="">{{ $placeholder }}</option>
            @endif
            {{ $slot }}
        </select>
    </div>

    @if($error)
    <p class="mt-2 text-sm text-red-600">{{ $error }}</p>
    @endif
</div>
