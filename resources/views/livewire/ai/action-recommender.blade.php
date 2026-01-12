<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
    {{-- Header --}}
    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-emerald-600 to-emerald-700 rounded-t-xl">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-white/10 rounded-lg">
                    <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-white">{{ __('carbex.ai.recommendations_title') }}</h2>
                    @if($aiAvailable)
                        <p class="text-xs text-emerald-100">{{ __('carbex.ai.powered_by', ['provider' => $providerName]) }}</p>
                    @endif
                </div>
            </div>
            <div class="flex items-center gap-2">
                @if($hasAnalyzed)
                    <button
                        type="button"
                        wire:click="refresh"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-emerald-100 hover:text-white hover:bg-white/10 rounded-lg transition-colors"
                        wire:loading.attr="disabled"
                    >
                        <svg class="w-4 h-4" wire:loading.class="animate-spin" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        {{ __('carbex.ai.refresh') }}
                    </button>
                @endif
                @if($assessmentYear)
                    <span class="px-3 py-1 text-sm font-medium text-white bg-white/20 rounded-full">
                        {{ __('carbex.ai.assessment_year', ['year' => $assessmentYear]) }}
                    </span>
                @endif
            </div>
        </div>
    </div>

    {{-- Content --}}
    <div class="p-6">
        @if(!$aiAvailable)
            {{-- AI Not Configured --}}
            <div class="text-center py-12">
                <div class="mx-auto w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('carbex.ai.not_configured') }}</h3>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400 max-w-md mx-auto">
                    {{ __('carbex.ai.configure_to_use') }}
                </p>
                <a href="{{ route('settings') }}" class="mt-4 inline-flex items-center gap-2 text-sm font-medium text-emerald-600 hover:text-emerald-700">
                    {{ __('carbex.ai.configure_ai') }}
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>

        @elseif(!$hasAnalyzed)
            {{-- Not yet analyzed --}}
            <div class="text-center py-12">
                <div class="mx-auto w-16 h-16 bg-emerald-100 dark:bg-emerald-900/30 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('carbex.ai.ready_to_analyze') }}</h3>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400 max-w-md mx-auto">
                    {{ __('carbex.ai.analyze_description') }}
                </p>
                <button
                    type="button"
                    wire:click="analyze"
                    wire:loading.attr="disabled"
                    class="mt-6 inline-flex items-center gap-2 px-6 py-3 bg-emerald-600 text-white font-medium rounded-lg hover:bg-emerald-700 transition-colors disabled:opacity-50"
                >
                    <svg class="w-5 h-5" wire:loading.class="animate-spin" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    <span wire:loading.remove wire:target="analyze">{{ __('carbex.ai.start_analysis') }}</span>
                    <span wire:loading wire:target="analyze">{{ __('carbex.ai.analyzing') }}</span>
                </button>
            </div>

        @elseif($isLoading)
            {{-- Loading state --}}
            <div class="text-center py-12">
                <div class="animate-spin mx-auto w-12 h-12 border-4 border-emerald-200 border-t-emerald-600 rounded-full mb-4"></div>
                <p class="text-gray-500 dark:text-gray-400">{{ __('carbex.ai.analyzing_emissions') }}</p>
            </div>

        @else
            {{-- Results --}}
            <div class="space-y-8">
                {{-- Summary Stats --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4">
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('carbex.dashboard.total_emissions') }}</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $this->formatEmissions($totalEmissions) }}</p>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4">
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('carbex.ai.recommendations_count') }}</p>
                        <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ count($recommendations) }}</p>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4">
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('carbex.ai.potential_reduction') }}</p>
                        <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">
                            {{ array_sum(array_column($recommendations, 'impact')) }}%
                        </p>
                    </div>
                </div>

                {{-- Insights --}}
                @if(count($insights) > 0)
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                            <svg class="w-5 h-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                            </svg>
                            {{ __('carbex.ai.key_insights') }}
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            @foreach($insights as $insight)
                                <div class="p-4 rounded-lg border {{ $this->getSeverityClass($insight['severity']) }}">
                                    <h4 class="font-medium">{{ $insight['title'] }}</h4>
                                    <p class="text-sm mt-1 opacity-80">{{ $insight['message'] }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Recommendations --}}
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                            <svg class="w-5 h-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                            </svg>
                            {{ __('carbex.ai.recommended_actions') }}
                        </h3>
                        @if(count($selectedRecommendations) > 0)
                            <button
                                type="button"
                                wire:click="addToTransitionPlan"
                                class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors"
                            >
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                {{ __('carbex.ai.add_selected', ['count' => count($selectedRecommendations)]) }}
                            </button>
                        @endif
                    </div>

                    <div class="space-y-4">
                        @forelse($recommendations as $index => $recommendation)
                            <div
                                class="border rounded-lg p-4 transition-all cursor-pointer {{ in_array($index, $selectedRecommendations) ? 'border-emerald-500 bg-emerald-50 dark:bg-emerald-900/20' : 'border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600' }}"
                                wire:click="toggleSelection({{ $index }})"
                            >
                                <div class="flex items-start gap-4">
                                    {{-- Checkbox --}}
                                    <div class="flex-shrink-0 pt-1">
                                        <div class="w-5 h-5 rounded border-2 flex items-center justify-center {{ in_array($index, $selectedRecommendations) ? 'bg-emerald-600 border-emerald-600' : 'border-gray-300 dark:border-gray-600' }}">
                                            @if(in_array($index, $selectedRecommendations))
                                                <svg class="w-3 h-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                                </svg>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Content --}}
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-start justify-between gap-4">
                                            <div>
                                                <h4 class="font-semibold text-gray-900 dark:text-white">
                                                    {{ $recommendation['number'] ?? $index + 1 }}. {{ $recommendation['title'] }}
                                                </h4>
                                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                                    {{ $recommendation['description'] }}
                                                </p>
                                            </div>
                                            <div class="flex items-center gap-1">
                                                {{-- View Details Button --}}
                                                <button
                                                    type="button"
                                                    wire:click.stop="showDetails({{ $index }})"
                                                    class="flex-shrink-0 p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors"
                                                    title="{{ __('carbex.ai.view_details') }}"
                                                >
                                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                </button>
                                                {{-- Add Action Button --}}
                                                <button
                                                    type="button"
                                                    wire:click.stop="addSingleAction({{ $index }})"
                                                    class="flex-shrink-0 p-2 text-emerald-600 hover:bg-emerald-100 dark:hover:bg-emerald-900/30 rounded-lg transition-colors"
                                                    title="{{ __('carbex.ai.add_action') }}"
                                                >
                                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>

                                        {{-- Badges --}}
                                        <div class="flex flex-wrap items-center gap-2 mt-3">
                                            @if(isset($recommendation['impact']))
                                                <span class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400 rounded-full">
                                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                                    </svg>
                                                    -{{ $recommendation['impact'] }}%
                                                </span>
                                            @endif

                                            @if(isset($recommendation['cost_label']))
                                                <span class="inline-flex items-center px-2 py-1 text-xs font-medium bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 rounded-full">
                                                    {{ $recommendation['cost_label'] }}
                                                </span>
                                            @endif

                                            @if(isset($recommendation['difficulty']))
                                                <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full {{ $this->getDifficultyClass($recommendation['difficulty']) }}">
                                                    {{ $recommendation['difficulty_label'] ?? ucfirst($recommendation['difficulty']) }}
                                                </span>
                                            @endif

                                            @if(isset($recommendation['timeline']))
                                                <span class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400 rounded-full">
                                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    {{ $recommendation['timeline'] }}
                                                </span>
                                            @endif

                                            @if(!empty($recommendation['scopes']))
                                                <span class="inline-flex items-center px-2 py-1 text-xs font-medium bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400 rounded-full">
                                                    Scope {{ implode(', ', $recommendation['scopes']) }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                                <svg class="mx-auto w-12 h-12 mb-2 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                                {{ __('carbex.ai.no_recommendations') }}
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- Top Categories --}}
                @if(count($topCategories) > 0)
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                            <svg class="w-5 h-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                            {{ __('carbex.ai.top_emission_categories') }}
                        </h3>
                        <div class="space-y-2">
                            @foreach($topCategories as $category)
                                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-900 rounded-lg">
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                        {{ $category['code'] }}
                                    </span>
                                    <span class="text-sm font-semibold text-gray-900 dark:text-white">
                                        {{ $this->formatEmissions($category['total_kg']) }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        @endif
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="mx-6 mb-6 px-4 py-3 bg-green-100 border border-green-200 text-green-800 rounded-lg" role="alert">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mx-6 mb-6 px-4 py-3 bg-red-100 border border-red-200 text-red-800 rounded-lg" role="alert">
            {{ session('error') }}
        </div>
    @endif

    @if(session('warning'))
        <div class="mx-6 mb-6 px-4 py-3 bg-yellow-100 border border-yellow-200 text-yellow-800 rounded-lg" role="alert">
            {{ session('warning') }}
        </div>
    @endif

    {{-- Detail Modal --}}
    @if($showDetailModal && $detailRecommendation)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                {{-- Background overlay --}}
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeDetails"></div>

                {{-- Centering spacer --}}
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                {{-- Modal panel --}}
                <div class="relative inline-block align-bottom bg-white dark:bg-gray-800 rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                    {{-- Header --}}
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-emerald-600 to-emerald-700">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-white" id="modal-title">
                                {{ __('carbex.ai.recommendation_details') }}
                            </h3>
                            <button type="button" wire:click="closeDetails" class="text-white/80 hover:text-white">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Content --}}
                    <div class="px-6 py-5 space-y-5">
                        {{-- Title --}}
                        <div>
                            <h4 class="text-xl font-bold text-gray-900 dark:text-white">
                                {{ $detailRecommendation['number'] ?? '' }}. {{ $detailRecommendation['title'] }}
                            </h4>
                        </div>

                        {{-- Description --}}
                        @if(!empty($detailRecommendation['description']))
                            <div>
                                <h5 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                    {{ __('carbex.ai.detail.description') }}
                                </h5>
                                <p class="text-gray-600 dark:text-gray-400">
                                    {{ $detailRecommendation['description'] }}
                                </p>
                            </div>
                        @endif

                        {{-- Metrics Grid --}}
                        <div class="grid grid-cols-2 gap-4">
                            {{-- Impact --}}
                            @if(isset($detailRecommendation['impact']))
                                <div class="bg-emerald-50 dark:bg-emerald-900/20 rounded-lg p-4">
                                    <p class="text-sm text-emerald-600 dark:text-emerald-400 font-medium">
                                        {{ __('carbex.ai.detail.impact') }}
                                    </p>
                                    <p class="text-2xl font-bold text-emerald-700 dark:text-emerald-300 mt-1">
                                        -{{ $detailRecommendation['impact'] }}%
                                    </p>
                                    <p class="text-xs text-emerald-600/70 dark:text-emerald-400/70 mt-1">
                                        {{ __('carbex.ai.detail.co2_reduction') }}
                                    </p>
                                </div>
                            @endif

                            {{-- Cost --}}
                            <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4">
                                <p class="text-sm text-gray-600 dark:text-gray-400 font-medium">
                                    {{ __('carbex.ai.detail.cost') }}
                                </p>
                                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">
                                    {{ $detailRecommendation['cost_label'] ?? '€€' }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-500 mt-1">
                                    {{ __('carbex.ai.detail.investment') }}
                                </p>
                            </div>

                            {{-- Difficulty --}}
                            <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4">
                                <p class="text-sm text-gray-600 dark:text-gray-400 font-medium">
                                    {{ __('carbex.ai.detail.difficulty') }}
                                </p>
                                <p class="text-lg font-bold mt-1 {{ $this->getDifficultyClass($detailRecommendation['difficulty'] ?? 'medium') }} px-3 py-1 rounded-full inline-block">
                                    {{ $detailRecommendation['difficulty_label'] ?? ucfirst($detailRecommendation['difficulty'] ?? 'medium') }}
                                </p>
                            </div>

                            {{-- Timeline --}}
                            @if(!empty($detailRecommendation['timeline']))
                                <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
                                    <p class="text-sm text-blue-600 dark:text-blue-400 font-medium">
                                        {{ __('carbex.ai.detail.timeline') }}
                                    </p>
                                    <p class="text-lg font-bold text-blue-700 dark:text-blue-300 mt-1">
                                        {{ $detailRecommendation['timeline'] }}
                                    </p>
                                </div>
                            @endif
                        </div>

                        {{-- Scopes --}}
                        @if(!empty($detailRecommendation['scopes']))
                            <div>
                                <h5 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                    {{ __('carbex.ai.detail.affected_scopes') }}
                                </h5>
                                <div class="flex gap-2">
                                    @foreach($detailRecommendation['scopes'] as $scope)
                                        <span class="inline-flex items-center px-3 py-1.5 text-sm font-medium bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400 rounded-full">
                                            Scope {{ $scope }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Footer --}}
                    <div class="px-6 py-4 bg-gray-50 dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700 flex justify-end gap-3">
                        <button
                            type="button"
                            wire:click="closeDetails"
                            class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                        >
                            {{ __('carbex.common.close') }}
                        </button>
                        <button
                            type="button"
                            wire:click="addSingleAction({{ $detailIndex }})"
                            class="px-4 py-2 text-sm font-medium text-white bg-emerald-600 rounded-lg hover:bg-emerald-700 transition-colors inline-flex items-center gap-2"
                        >
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            {{ __('carbex.ai.add_to_plan') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
