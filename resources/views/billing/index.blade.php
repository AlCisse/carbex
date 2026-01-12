<x-layouts.app>
    <x-slot name="header">
        <h1 class="text-xl font-semibold text-gray-900">{{ __('carbex.billing.subscription') }}</h1>
    </x-slot>

    <div class="space-y-6">
        <livewire:billing.plan-selector />
    </div>
</x-layouts.app>
