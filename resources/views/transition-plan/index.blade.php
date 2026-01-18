<x-layouts.app>
    <x-slot name="header">
        <h1 class="text-xl font-semibold text-gray-900">{{ __('linscarbon.transition_plan.title') }}</h1>
    </x-slot>

    <div class="space-y-6">
        <livewire:transition-plan.action-list />
    </div>
</x-layouts.app>
