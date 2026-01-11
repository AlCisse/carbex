<x-layouts.app>
    <x-slot name="title">{{ __('carbex.ai.analysis_title') }}</x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Page Header --}}
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                    {{ __('carbex.ai.analysis_title') }}
                </h1>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    {{ __('carbex.ai.analysis_description') }}
                </p>
            </div>

            {{-- AI Action Recommender Component --}}
            <livewire:a-i.a-i-action-recommender />

            {{-- Additional Info Section --}}
            <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- How it works --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="p-2 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg">
                            <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                            </svg>
                        </div>
                        <h3 class="font-semibold text-gray-900 dark:text-white">{{ __('carbex.ai.how_it_works') }}</h3>
                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        {{ __('carbex.ai.how_it_works_description') }}
                    </p>
                </div>

                {{-- Data Sources --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
                            </svg>
                        </div>
                        <h3 class="font-semibold text-gray-900 dark:text-white">{{ __('carbex.ai.data_sources') }}</h3>
                    </div>
                    <ul class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
                        <li>{{ __('carbex.ai.data_source_emissions') }}</li>
                        <li>{{ __('carbex.ai.data_source_sector') }}</li>
                        <li>{{ __('carbex.ai.data_source_benchmarks') }}</li>
                    </ul>
                </div>

                {{-- Privacy --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="p-2 bg-purple-100 dark:bg-purple-900/30 rounded-lg">
                            <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <h3 class="font-semibold text-gray-900 dark:text-white">{{ __('carbex.ai.privacy') }}</h3>
                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        {{ __('carbex.ai.privacy_description') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
