<x-layouts.app>
    <x-slot name="header">
        <h1 class="text-xl font-semibold text-gray-900">{{ __('carbex.assessments.my_assessments') }}</h1>
    </x-slot>

    <div class="space-y-6">
        <livewire:assessments.assessment-list />
    </div>
</x-layouts.app>
