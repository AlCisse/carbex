<x-layouts.app>
    <x-slot name="header">
        <h1 class="text-xl font-semibold text-gray-900">{{ __('carbex.emissions.title') }}</h1>
    </x-slot>

    <div class="grid gap-6 md:grid-cols-3">
        <x-card>
            <h3 class="text-lg font-medium text-gray-900">Scope 1</h3>
            <p class="text-sm text-gray-500">{{ __('carbex.emissions.scope_1') }}</p>
            <a href="{{ route('emissions.scope', 1) }}" class="mt-4 inline-flex text-sm text-green-600 hover:text-green-700">
                {{ __('carbex.common.view_details') }} &rarr;
            </a>
        </x-card>
        <x-card>
            <h3 class="text-lg font-medium text-gray-900">Scope 2</h3>
            <p class="text-sm text-gray-500">{{ __('carbex.emissions.scope_2') }}</p>
            <a href="{{ route('emissions.scope', 2) }}" class="mt-4 inline-flex text-sm text-green-600 hover:text-green-700">
                {{ __('carbex.common.view_details') }} &rarr;
            </a>
        </x-card>
        <x-card>
            <h3 class="text-lg font-medium text-gray-900">Scope 3</h3>
            <p class="text-sm text-gray-500">{{ __('carbex.emissions.scope_3') }}</p>
            <a href="{{ route('emissions.scope', 3) }}" class="mt-4 inline-flex text-sm text-green-600 hover:text-green-700">
                {{ __('carbex.common.view_details') }} &rarr;
            </a>
        </x-card>
    </div>
</x-layouts.app>
