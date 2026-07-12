<x-layouts.app>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h1 class="text-xl font-semibold text-gray-900">{{ __('linscarbon.emissions.title') }}</h1>
            <div class="flex gap-3">
                <button dusk="import-button" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                    </svg>
                    {{ __('linscarbon.emissions.import') }}
                </button>
            </div>
        </div>
    </x-slot>

    <div class="grid gap-6 md:grid-cols-3">
        <x-card dusk="scope-1-tab">
            <h3 class="text-lg font-medium text-gray-900">Scope 1</h3>
            <p class="text-sm text-gray-500">{{ __('linscarbon.emissions.scope_1') }}</p>
            <a href="{{ route('emissions.scope', 1) }}" class="mt-4 inline-flex text-sm text-green-600 hover:text-green-700">
                {{ __('linscarbon.common.view_details') }} &rarr;
            </a>
        </x-card>
        <x-card dusk="scope-2-tab">
            <h3 class="text-lg font-medium text-gray-900">Scope 2</h3>
            <p class="text-sm text-gray-500">{{ __('linscarbon.emissions.scope_2') }}</p>
            <a href="{{ route('emissions.scope', 2) }}" class="mt-4 inline-flex text-sm text-green-600 hover:text-green-700">
                {{ __('linscarbon.common.view_details') }} &rarr;
            </a>
        </x-card>
        <x-card>
            <h3 class="text-lg font-medium text-gray-900">Scope 3</h3>
            <p class="text-sm text-gray-500">{{ __('linscarbon.emissions.scope_3') }}</p>
            <a href="{{ route('emissions.scope', 3) }}" class="mt-4 inline-flex text-sm text-green-600 hover:text-green-700">
                {{ __('linscarbon.common.view_details') }} &rarr;
            </a>
        </x-card>
    </div>

    {{-- Import Modal --}}
    <div x-data="{ showImport: false }" @keydown.escape.window="showImport = false">
        <div x-show="showImport" x-cloak class="fixed inset-0 z-50 overflow-y-auto" dusk="import-modal">
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75" @click="showImport = false"></div>
                <div class="relative bg-white rounded-lg shadow-xl max-w-lg w-full p-6">
                    <h3 class="text-lg font-semibold mb-4">{{ __('linscarbon.emissions.import_title') }}</h3>
                    <p class="text-sm text-gray-500 mb-4">{{ __('linscarbon.emissions.import_description') }}</p>
                    <div class="flex justify-end gap-3">
                        <button @click="showImport = false" class="px-4 py-2 border border-gray-300 rounded-md text-sm">{{ __('linscarbon.cancel') }}</button>
                        <button class="px-4 py-2 bg-green-600 text-white rounded-md text-sm">{{ __('linscarbon.emissions.import') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
