<x-layouts.app>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">
                    {{ __('linscarbon.gamification.title') }}
                </h1>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                    {{ __('linscarbon.gamification.subtitle') }}
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <livewire:gamification.badge-display />
        </div>
    </div>

    {{-- Informations sur le système de gamification --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-12">
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-lg p-6">
            <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">
                {{ __('linscarbon.gamification.how_it_works') }}
            </h3>

            <div class="grid md:grid-cols-3 gap-6">
                {{-- Étape 1 --}}
                <div class="text-center">
                    <div class="w-12 h-12 mx-auto mb-3 bg-emerald-100 dark:bg-emerald-900/30 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h4 class="font-medium text-slate-900 dark:text-white mb-1">{{ __('linscarbon.gamification.step1_title') }}</h4>
                    <p class="text-sm text-slate-500 dark:text-slate-400">{{ __('linscarbon.gamification.step1_desc') }}</p>
                </div>

                {{-- Étape 2 --}}
                <div class="text-center">
                    <div class="w-12 h-12 mx-auto mb-3 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <h4 class="font-medium text-slate-900 dark:text-white mb-1">{{ __('linscarbon.gamification.step2_title') }}</h4>
                    <p class="text-sm text-slate-500 dark:text-slate-400">{{ __('linscarbon.gamification.step2_desc') }}</p>
                </div>

                {{-- Étape 3 --}}
                <div class="text-center">
                    <div class="w-12 h-12 mx-auto mb-3 bg-purple-100 dark:bg-purple-900/30 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                        </svg>
                    </div>
                    <h4 class="font-medium text-slate-900 dark:text-white mb-1">{{ __('linscarbon.gamification.step3_title') }}</h4>
                    <p class="text-sm text-slate-500 dark:text-slate-400">{{ __('linscarbon.gamification.step3_desc') }}</p>
                </div>
            </div>

            {{-- Liste des badges --}}
            <div class="mt-8 pt-6 border-t border-slate-200 dark:border-slate-700">
                <h4 class="font-medium text-slate-900 dark:text-white mb-4">{{ __('linscarbon.gamification.badge_categories') }}</h4>
                <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="p-4 bg-emerald-50 dark:bg-emerald-900/20 rounded-lg">
                        <div class="flex items-center gap-2 mb-2">
                            <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="font-medium text-emerald-800 dark:text-emerald-300">{{ __('linscarbon.gamification.category.assessment') }}</span>
                        </div>
                        <p class="text-sm text-emerald-600 dark:text-emerald-400">{{ __('linscarbon.gamification.category.assessment_desc') }}</p>
                    </div>

                    <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                        <div class="flex items-center gap-2 mb-2">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6" />
                            </svg>
                            <span class="font-medium text-blue-800 dark:text-blue-300">{{ __('linscarbon.gamification.category.reduction') }}</span>
                        </div>
                        <p class="text-sm text-blue-600 dark:text-blue-400">{{ __('linscarbon.gamification.category.reduction_desc') }}</p>
                    </div>

                    <div class="p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg">
                        <div class="flex items-center gap-2 mb-2">
                            <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <span class="font-medium text-yellow-800 dark:text-yellow-300">{{ __('linscarbon.gamification.category.engagement') }}</span>
                        </div>
                        <p class="text-sm text-yellow-600 dark:text-yellow-400">{{ __('linscarbon.gamification.category.engagement_desc') }}</p>
                    </div>

                    <div class="p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg">
                        <div class="flex items-center gap-2 mb-2">
                            <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                            </svg>
                            <span class="font-medium text-purple-800 dark:text-purple-300">{{ __('linscarbon.gamification.category.expert') }}</span>
                        </div>
                        <p class="text-sm text-purple-600 dark:text-purple-400">{{ __('linscarbon.gamification.category.expert_desc') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
