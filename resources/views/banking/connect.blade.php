<x-layouts.app>
    <x-slot name="header">
        <h1 class="text-xl font-semibold text-gray-900">{{ __('carbex.banking.connect') }}</h1>
    </x-slot>

    <div class="space-y-6">
        <livewire:banking.connection-wizard />
    </div>
</x-layouts.app>
