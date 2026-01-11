<x-layouts.app>
    <x-slot name="header">
        <h1 class="text-xl font-semibold text-gray-900">{{ __('carbex.transactions.title') }}</h1>
    </x-slot>

    <div class="space-y-6">
        <livewire:transactions.transaction-list />
    </div>
</x-layouts.app>
