<div>
    <div class="space-y-8">
        <!-- Current Plan & Usage Overview -->
        <x-card>
            <x-slot name="header">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-medium text-gray-900">{{ __('linscarbon.settings.ai.usage_title') }}</h2>
                        <p class="mt-1 text-sm text-gray-500">{{ __('linscarbon.settings.ai.usage_desc') }}</p>
                    </div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                        {{ $subscriptionLimits['plan_label'] ?? 'Free' }}
                    </span>
                </div>
            </x-slot>

            <!-- Usage Stats -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Tokens Used -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-gray-600">{{ __('linscarbon.settings.ai.tokens_used') }}</span>
                        <svg class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <div class="text-2xl font-bold text-gray-900">
                        {{ number_format($usageStats['total_tokens'] ?? 0) }}
                    </div>
                    @if ($usageStats['limit'])
                        <div class="mt-2">
                            <div class="flex justify-between text-xs text-gray-500 mb-1">
                                <span>{{ __('linscarbon.settings.ai.of_limit', ['limit' => number_format($usageStats['limit'])]) }}</span>
                                <span>{{ $usageStats['usage_percent'] }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="h-2 rounded-full {{ $usageStats['usage_percent'] > 80 ? 'bg-red-500' : ($usageStats['usage_percent'] > 50 ? 'bg-yellow-500' : 'bg-green-500') }}"
                                     style="width: {{ min(100, $usageStats['usage_percent']) }}%"></div>
                            </div>
                        </div>
                    @else
                        <p class="mt-2 text-xs text-gray-500">{{ __('linscarbon.settings.ai.unlimited') }}</p>
                    @endif
                </div>

                <!-- Requests This Month -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-gray-600">{{ __('linscarbon.settings.ai.requests_month') }}</span>
                        <svg class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                    </div>
                    <div class="text-2xl font-bold text-gray-900">
                        {{ number_format($usageStats['requests'] ?? 0) }}
                    </div>
                    @if ($subscriptionLimits['monthly_requests'] ?? null)
                        <p class="mt-2 text-xs text-gray-500">
                            {{ __('linscarbon.settings.ai.of_requests', ['limit' => number_format($subscriptionLimits['monthly_requests'])]) }}
                        </p>
                    @else
                        <p class="mt-2 text-xs text-gray-500">{{ __('linscarbon.settings.ai.unlimited') }}</p>
                    @endif
                </div>

                <!-- Remaining Requests -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-gray-600">{{ __('linscarbon.settings.ai.remaining_requests') }}</span>
                        <svg class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="text-2xl font-bold {{ ($usageStats['remaining_requests'] ?? 0) < 20 ? 'text-red-600' : 'text-gray-900' }}">
                        @if ($subscriptionLimits['monthly_requests'] ?? null)
                            {{ number_format($usageStats['remaining_requests'] ?? 0) }}
                        @else
                            <span class="text-green-600">∞</span>
                        @endif
                    </div>
                    <p class="mt-2 text-xs text-gray-500">{{ __('linscarbon.settings.ai.requests_available') }}</p>
                </div>
            </div>
        </x-card>

        <!-- Assigned AI Model -->
        <x-card>
            <x-slot name="header">
                <h2 class="text-lg font-medium text-gray-900">{{ __('linscarbon.settings.ai.your_model') }}</h2>
                <p class="mt-1 text-sm text-gray-500">{{ __('linscarbon.settings.ai.your_model_desc') }}</p>
            </x-slot>

            <div class="flex items-center justify-between p-4 rounded-lg border border-blue-200 bg-blue-50">
                <div class="flex items-center">
                    <div class="w-12 h-12 rounded-lg bg-blue-100 flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <span class="text-lg font-semibold text-gray-900">{{ $assignedModelLabel }}</span>
                        <p class="text-sm text-gray-600">{{ __('linscarbon.settings.ai.model_for_plan', ['plan' => $subscriptionLimits['plan_label'] ?? 'Free']) }}</p>
                    </div>
                </div>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    {{ __('linscarbon.common.active') }}
                </span>
            </div>
        </x-card>

        <!-- AI Features Available -->
        <x-card>
            <x-slot name="header">
                <h2 class="text-lg font-medium text-gray-900">{{ __('linscarbon.settings.ai.features') }}</h2>
                <p class="mt-1 text-sm text-gray-500">{{ __('linscarbon.settings.ai.features_for_plan') }}</p>
            </x-slot>

            <div class="space-y-4">
                <!-- Chat Widget -->
                <div class="flex items-center justify-between p-4 rounded-lg border {{ $this->hasFeature('chat_widget') ? 'border-green-200 bg-green-50' : 'border-gray-200 bg-gray-50' }}">
                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-lg {{ $this->hasFeature('chat_widget') ? 'bg-green-100' : 'bg-gray-100' }} flex items-center justify-center mr-3">
                            <svg class="w-5 h-5 {{ $this->hasFeature('chat_widget') ? 'text-green-600' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                        </div>
                        <div>
                            <span class="font-medium {{ $this->hasFeature('chat_widget') ? 'text-gray-900' : 'text-gray-500' }}">
                                {{ __('linscarbon.settings.ai.chat_widget') }}
                            </span>
                            <p class="text-sm {{ $this->hasFeature('chat_widget') ? 'text-gray-600' : 'text-gray-400' }}">
                                {{ __('linscarbon.settings.ai.chat_widget_desc') }}
                            </p>
                        </div>
                    </div>
                    @if ($this->hasFeature('chat_widget'))
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            {{ __('linscarbon.common.active') }}
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                            {{ __('linscarbon.common.unavailable') }}
                        </span>
                    @endif
                </div>

                <!-- Emission Helper -->
                <div class="flex items-center justify-between p-4 rounded-lg border {{ $this->hasFeature('emission_helper') ? 'border-green-200 bg-green-50' : 'border-gray-200 bg-gray-50' }}">
                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-lg {{ $this->hasFeature('emission_helper') ? 'bg-green-100' : 'bg-gray-100' }} flex items-center justify-center mr-3">
                            <svg class="w-5 h-5 {{ $this->hasFeature('emission_helper') ? 'text-green-600' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        <div>
                            <span class="font-medium {{ $this->hasFeature('emission_helper') ? 'text-gray-900' : 'text-gray-500' }}">
                                {{ __('linscarbon.settings.ai.emission_helper') }}
                            </span>
                            <p class="text-sm {{ $this->hasFeature('emission_helper') ? 'text-gray-600' : 'text-gray-400' }}">
                                {{ __('linscarbon.settings.ai.emission_helper_desc') }}
                            </p>
                        </div>
                    </div>
                    @if ($this->hasFeature('emission_helper'))
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            {{ __('linscarbon.common.active') }}
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                            {{ __('linscarbon.common.unavailable') }}
                        </span>
                    @endif
                </div>

                <!-- Document Extraction -->
                <div class="flex items-center justify-between p-4 rounded-lg border {{ $this->hasFeature('document_extraction') ? 'border-green-200 bg-green-50' : 'border-gray-200 bg-gray-50' }}">
                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-lg {{ $this->hasFeature('document_extraction') ? 'bg-green-100' : 'bg-gray-100' }} flex items-center justify-center mr-3">
                            <svg class="w-5 h-5 {{ $this->hasFeature('document_extraction') ? 'text-green-600' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div>
                            <span class="font-medium {{ $this->hasFeature('document_extraction') ? 'text-gray-900' : 'text-gray-500' }}">
                                {{ __('linscarbon.settings.ai.document_extraction') }}
                            </span>
                            <p class="text-sm {{ $this->hasFeature('document_extraction') ? 'text-gray-600' : 'text-gray-400' }}">
                                {{ __('linscarbon.settings.ai.document_extraction_desc') }}
                            </p>
                        </div>
                    </div>
                    @if ($this->hasFeature('document_extraction'))
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            {{ __('linscarbon.common.active') }}
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                            {{ __('linscarbon.common.unavailable') }}
                        </span>
                    @endif
                </div>
            </div>
        </x-card>

        <!-- Upgrade CTA -->
        @if (($subscriptionLimits['plan'] ?? 'free') !== 'enterprise')
            <div class="rounded-lg bg-gradient-to-r from-green-500 to-emerald-600 p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-medium">{{ __('linscarbon.settings.ai.upgrade_title') }}</h3>
                        <p class="mt-1 text-sm text-green-100">{{ __('linscarbon.settings.ai.upgrade_desc') }}</p>
                    </div>
                    <a href="{{ route('settings.billing') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-green-600 bg-white hover:bg-green-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-green-600 focus:ring-white">
                        {{ __('linscarbon.settings.ai.view_plans') }}
                        <svg class="ml-2 -mr-1 w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
