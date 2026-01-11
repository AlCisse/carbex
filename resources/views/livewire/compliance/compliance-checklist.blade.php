<div>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-semibold text-gray-900 dark:text-white">{{ __('carbex.compliance.title') }}</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('carbex.compliance.subtitle') }}</p>
            </div>
            <div class="flex items-center space-x-2">
                <span class="text-sm text-gray-500 dark:text-gray-400">{{ __('carbex.compliance.overall') }}:</span>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                    @if($this->getOverallCompletion() >= 75) bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                    @elseif($this->getOverallCompletion() >= 50) bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                    @else bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                    @endif">
                    {{ $this->getOverallCompletion() }}%
                </span>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Organization Profile Summary -->
        <x-card>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-medium text-gray-900 dark:text-white">{{ $organizationProfile['name'] ?? '-' }}</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        {{ __('carbex.sectors.' . ($organizationProfile['sector'] ?? 'other')) }} -
                        {{ $organizationProfile['employee_count'] ?? 0 }} {{ __('carbex.auth.employees') }}
                    </p>
                </div>
                <div class="text-right">
                    @if($organizationProfile['has_csrd_obligation'] ?? false)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                            {{ __('carbex.compliance.csrd_applicable') }}
                        </span>
                    @endif
                </div>
            </div>
        </x-card>

        <!-- Upcoming Deadlines -->
        @if(count($deadlines) > 0)
        <x-card>
            <x-slot name="header">
                <h2 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('carbex.compliance.upcoming_deadlines') }}</h2>
            </x-slot>

            <div class="space-y-3">
                @foreach($deadlines as $deadline)
                <div class="flex items-center justify-between p-3 rounded-lg
                    @if($deadline['urgent']) bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700
                    @else bg-gray-50 dark:bg-gray-700/50
                    @endif">
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0">
                            @if($deadline['urgent'])
                                <svg class="w-5 h-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            @else
                                <svg class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            @endif
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $deadline['title'] }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $deadline['framework'] }} - {{ $deadline['description'] }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $deadline['date']->format('d/m/Y') }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $deadline['date']->diffForHumans() }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </x-card>
        @endif

        <!-- Frameworks Overview -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @foreach($frameworks as $key => $framework)
            @if($framework['applicable'])
            <x-card class="cursor-pointer hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0 w-10 h-10 rounded-lg flex items-center justify-center
                            @if($framework['color'] === 'blue') bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400
                            @elseif($framework['color'] === 'green') bg-green-100 text-green-600 dark:bg-green-900/30 dark:text-green-400
                            @elseif($framework['color'] === 'teal') bg-teal-100 text-teal-600 dark:bg-teal-900/30 dark:text-teal-400
                            @elseif($framework['color'] === 'purple') bg-purple-100 text-purple-600 dark:bg-purple-900/30 dark:text-purple-400
                            @else bg-emerald-100 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400
                            @endif">
                            @if($framework['icon'] === 'document-text')
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            @elseif($framework['icon'] === 'shield-check')
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                            @elseif($framework['icon'] === 'cog')
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            @else
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            @endif
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white">{{ $framework['name'] }}</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $framework['full_name'] }}</p>
                        </div>
                    </div>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">{{ $framework['description'] }}</p>
                <div class="flex items-center justify-between">
                    <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-2 mr-3">
                        <div class="h-2 rounded-full
                            @if($this->getCompletionPercentage($key) >= 75) bg-green-500
                            @elseif($this->getCompletionPercentage($key) >= 50) bg-yellow-500
                            @else bg-red-500
                            @endif"
                            style="width: {{ $this->getCompletionPercentage($key) }}%"></div>
                    </div>
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300 whitespace-nowrap">{{ $this->getCompletionPercentage($key) }}%</span>
                </div>
            </x-card>
            @endif
            @endforeach
        </div>

        <!-- Detailed Checklists -->
        @foreach($checklist as $frameworkKey => $frameworkData)
        <x-card>
            <x-slot name="header">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-white">{{ $frameworkData['name'] }}</h2>
                    <span class="text-sm text-gray-500 dark:text-gray-400">
                        {{ collect($frameworkData['items'])->where('completed', true)->count() }}/{{ count($frameworkData['items']) }} {{ __('carbex.compliance.completed') }}
                    </span>
                </div>
            </x-slot>

            <div class="space-y-3">
                @foreach($frameworkData['items'] as $item)
                <div class="flex items-start p-3 rounded-lg
                    @if($item['completed']) bg-green-50 dark:bg-green-900/20
                    @else bg-gray-50 dark:bg-gray-700/50
                    @endif">
                    <div class="flex-shrink-0 mt-0.5">
                        @if($item['completed'])
                            <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                        @else
                            <svg class="w-5 h-5 text-gray-300 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        @endif
                    </div>
                    <div class="ml-3 flex-1">
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-medium text-gray-900 dark:text-white">
                                <span class="text-gray-500 dark:text-gray-400 mr-2">[{{ $item['code'] }}]</span>
                                {{ $item['title'] }}
                            </p>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                @if($item['priority'] === 'high') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                @elseif($item['priority'] === 'medium') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                @else bg-gray-100 text-gray-800 dark:bg-gray-600 dark:text-gray-200
                                @endif">
                                {{ ucfirst($item['priority']) }}
                            </span>
                        </div>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $item['description'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </x-card>
        @endforeach
    </div>
</div>
