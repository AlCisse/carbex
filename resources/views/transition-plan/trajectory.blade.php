<x-layouts.app>
    <x-slot name="header">
        <h1 class="text-xl font-semibold text-gray-900">{{ __('carbex.transition_plan.edit_trajectory') }}</h1>
    </x-slot>

    <div class="space-y-6">
        <livewire:transition-plan.trajectory-page />
    </div>
</x-layouts.app>
