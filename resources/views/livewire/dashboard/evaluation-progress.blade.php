<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6" dusk="evaluation-progress">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-gray-900">{{ __('linscarbon.evaluation.title') }}</h3>
        <span class="text-sm text-gray-500">
            {{ $stats['completed'] }}/{{ $stats['total'] }} {{ __('linscarbon.evaluation.completed') }}
        </span>
    </div>

    <div class="space-y-6">
        <!-- Setup Section -->
        <div>
            <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-3">
                {{ __('linscarbon.evaluation.setup') }}
            </h4>
            <div class="space-y-2">
                @foreach($groupedSteps['setup'] as $step)
                    <a href="{{ $step['route'] }}" class="flex items-center p-3 rounded-lg hover:bg-gray-50 transition-colors group">
                        <!-- Status Icon -->
                        @if($step['status'] === 'completed')
                            <span class="flex-shrink-0 w-6 h-6 rounded-full bg-green-100 flex items-center justify-center">
                                <svg class="w-4 h-4 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </span>
                        @else
                            <span class="flex-shrink-0 w-6 h-6 rounded-full border-2 border-gray-300"></span>
                        @endif

                        <!-- Content -->
                        <div class="ml-3 flex-1">
                            <p class="text-sm font-medium text-gray-900 group-hover:text-green-600">{{ $step['name'] }}</p>
                            @if($step['description'])
                                <p class="text-xs text-gray-500">{{ Str::limit($step['description'], 60) }}</p>
                            @endif
                        </div>

                        <!-- Arrow -->
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Scope 1 -->
        @if($groupedSteps['scope1']->count() > 0)
        <div>
            <h4 class="text-sm font-medium text-orange-600 uppercase tracking-wide mb-3 flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z" />
                </svg>
                Scope 1 - {{ __('linscarbon.scopes.scope1_name') }}
            </h4>
            <div class="space-y-2">
                @foreach($groupedSteps['scope1'] as $step)
                    <a href="{{ $step['route'] }}" class="flex items-center p-3 rounded-lg hover:bg-orange-50 transition-colors group">
                        @if($step['status'] === 'completed')
                            <span class="flex-shrink-0 w-6 h-6 rounded-full bg-green-100 flex items-center justify-center">
                                <svg class="w-4 h-4 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </span>
                        @else
                            <span class="flex-shrink-0 w-6 h-6 rounded-full border-2 border-orange-300"></span>
                        @endif
                        <div class="ml-3 flex-1">
                            <p class="text-sm font-medium text-gray-900 group-hover:text-orange-600">{{ $step['name'] }}</p>
                        </div>
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Scope 2 -->
        @if($groupedSteps['scope2']->count() > 0)
        <div>
            <h4 class="text-sm font-medium text-yellow-600 uppercase tracking-wide mb-3 flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
                Scope 2 - {{ __('linscarbon.scopes.scope2_name') }}
            </h4>
            <div class="space-y-2">
                @foreach($groupedSteps['scope2'] as $step)
                    <a href="{{ $step['route'] }}" class="flex items-center p-3 rounded-lg hover:bg-yellow-50 transition-colors group">
                        @if($step['status'] === 'completed')
                            <span class="flex-shrink-0 w-6 h-6 rounded-full bg-green-100 flex items-center justify-center">
                                <svg class="w-4 h-4 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </span>
                        @else
                            <span class="flex-shrink-0 w-6 h-6 rounded-full border-2 border-yellow-300"></span>
                        @endif
                        <div class="ml-3 flex-1">
                            <p class="text-sm font-medium text-gray-900 group-hover:text-yellow-600">{{ $step['name'] }}</p>
                        </div>
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Scope 3 -->
        @if($groupedSteps['scope3']->count() > 0)
        <div>
            <h4 class="text-sm font-medium text-blue-600 uppercase tracking-wide mb-3 flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Scope 3 - {{ __('linscarbon.scopes.scope3_name') }}
            </h4>
            <div class="space-y-2 max-h-64 overflow-y-auto">
                @foreach($groupedSteps['scope3'] as $step)
                    <a href="{{ $step['route'] }}" class="flex items-center p-3 rounded-lg hover:bg-blue-50 transition-colors group">
                        @if($step['status'] === 'completed')
                            <span class="flex-shrink-0 w-6 h-6 rounded-full bg-green-100 flex items-center justify-center">
                                <svg class="w-4 h-4 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </span>
                        @else
                            <span class="flex-shrink-0 w-6 h-6 rounded-full border-2 border-blue-300"></span>
                        @endif
                        <div class="ml-3 flex-1">
                            <p class="text-sm font-medium text-gray-900 group-hover:text-blue-600">{{ $step['name'] }}</p>
                        </div>
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>
