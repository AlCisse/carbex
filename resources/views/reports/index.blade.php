<x-layouts.app>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h1 class="text-xl font-semibold text-gray-900">{{ __('linscarbon.reports.title') }}</h1>
            <a href="{{ route('reports.create') }}" class="inline-flex items-center rounded-md bg-green-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-500">
                {{ __('linscarbon.reports.generate') }}
            </a>
        </div>
    </x-slot>

    <x-card>
        <p class="text-gray-500">{{ __('linscarbon.reports.no_reports') }}</p>
    </x-card>
</x-layouts.app>
