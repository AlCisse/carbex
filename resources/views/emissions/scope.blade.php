<x-layouts.app>
    <x-slot name="header">
        <h1 class="text-xl font-semibold text-gray-900">{{ __('linscarbon.emissions.scope_title', ['scope' => $scope]) }}</h1>
    </x-slot>

    <x-card>
        <p class="text-gray-500">{{ __('linscarbon.emissions.scope_coming_soon', ['scope' => $scope]) }}</p>
    </x-card>
</x-layouts.app>
