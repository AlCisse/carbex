<div>
    @if (session('success'))
        <x-alert type="success" dismissible class="mb-6">
            {{ session('success') }}
        </x-alert>
    @endif

    @if (session('error'))
        <x-alert type="error" dismissible class="mb-6">
            {{ session('error') }}
        </x-alert>
    @endif

    <div class="space-y-8">
        <!-- AI Status Overview -->
        <x-card>
            <x-slot name="header">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-medium text-gray-900">{{ __('carbex.settings.ai.status') }}</h2>
                        <p class="mt-1 text-sm text-gray-500">{{ __('carbex.settings.ai.status_desc') }}</p>
                    </div>
                    @php
                        $hasAvailable = collect($availableProviders)->contains('available', true);
                    @endphp
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $hasAvailable ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        <span class="w-2 h-2 mr-2 rounded-full {{ $hasAvailable ? 'bg-green-500' : 'bg-red-500' }}"></span>
                        {{ $hasAvailable ? __('carbex.settings.ai.connected') : __('carbex.settings.ai.not_connected') }}
                    </span>
                </div>
            </x-slot>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach ($availableProviders as $key => $provider)
                    <div class="rounded-lg border {{ $provider['available'] ? 'border-green-200 bg-green-50' : 'border-gray-200 bg-gray-50' }} p-4">
                        <div class="flex items-center justify-between">
                            <span class="font-medium text-gray-900">{{ $provider['name'] }}</span>
                            @if ($provider['available'])
                                <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            @else
                                <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                            @endif
                        </div>
                        <p class="mt-1 text-xs {{ $provider['available'] ? 'text-green-600' : 'text-gray-500' }}">
                            {{ $provider['available'] ? __('carbex.settings.ai.configured') : __('carbex.settings.ai.not_configured_status') }}
                        </p>
                    </div>
                @endforeach
            </div>
        </x-card>

        <!-- API Keys Configuration -->
        <x-card>
            <x-slot name="header">
                <h2 class="text-lg font-medium text-gray-900">{{ __('carbex.settings.ai.api_keys') }}</h2>
                <p class="mt-1 text-sm text-gray-500">{{ __('carbex.settings.ai.api_keys_desc') }}</p>
            </x-slot>

            <div class="space-y-6">
                <!-- Anthropic -->
                <div class="rounded-lg border border-gray-200 p-4">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-lg bg-orange-100 flex items-center justify-center mr-3">
                                <span class="text-orange-600 font-bold text-sm">A</span>
                            </div>
                            <div>
                                <h3 class="font-medium text-gray-900">Anthropic (Claude)</h3>
                                <p class="text-xs text-gray-500">{{ __('carbex.settings.ai.anthropic_desc') }}</p>
                            </div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" wire:model.live="anthropicEnabled" class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-green-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-600"></div>
                        </label>
                    </div>
                    <div class="flex gap-2">
                        <input
                            type="password"
                            wire:model="anthropicApiKey"
                            placeholder="sk-ant-api03-..."
                            class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm"
                        >
                        <button
                            wire:click="saveApiKey('anthropic')"
                            class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                        >
                            {{ __('carbex.common.save') }}
                        </button>
                        @if ($availableProviders['anthropic']['available'] ?? false)
                            <button
                                wire:click="testConnection('anthropic')"
                                class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                            >
                                {{ __('carbex.settings.ai.test') }}
                            </button>
                        @endif
                    </div>
                    @if ($anthropicEnabled)
                        <div class="mt-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('carbex.settings.ai.model') }}</label>
                            <select wire:model="anthropicModel" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm">
                                @foreach ($providerModels['anthropic'] ?? [] as $model => $label)
                                    <option value="{{ $model }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                </div>

                <!-- OpenAI -->
                <div class="rounded-lg border border-gray-200 p-4">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-lg bg-emerald-100 flex items-center justify-center mr-3">
                                <span class="text-emerald-600 font-bold text-sm">O</span>
                            </div>
                            <div>
                                <h3 class="font-medium text-gray-900">OpenAI (GPT)</h3>
                                <p class="text-xs text-gray-500">{{ __('carbex.settings.ai.openai_desc') }}</p>
                            </div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" wire:model.live="openaiEnabled" class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-green-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-600"></div>
                        </label>
                    </div>
                    <div class="flex gap-2">
                        <input
                            type="password"
                            wire:model="openaiApiKey"
                            placeholder="sk-..."
                            class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm"
                        >
                        <button
                            wire:click="saveApiKey('openai')"
                            class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                        >
                            {{ __('carbex.common.save') }}
                        </button>
                        @if ($availableProviders['openai']['available'] ?? false)
                            <button
                                wire:click="testConnection('openai')"
                                class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                            >
                                {{ __('carbex.settings.ai.test') }}
                            </button>
                        @endif
                    </div>
                    @if ($openaiEnabled)
                        <div class="mt-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('carbex.settings.ai.model') }}</label>
                            <select wire:model="openaiModel" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm">
                                @foreach ($providerModels['openai'] ?? [] as $model => $label)
                                    <option value="{{ $model }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                </div>

                <!-- Google -->
                <div class="rounded-lg border border-gray-200 p-4">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center mr-3">
                                <span class="text-blue-600 font-bold text-sm">G</span>
                            </div>
                            <div>
                                <h3 class="font-medium text-gray-900">Google (Gemini)</h3>
                                <p class="text-xs text-gray-500">{{ __('carbex.settings.ai.google_desc') }}</p>
                            </div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" wire:model.live="googleEnabled" class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-green-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-600"></div>
                        </label>
                    </div>
                    <div class="flex gap-2">
                        <input
                            type="password"
                            wire:model="googleApiKey"
                            placeholder="AIza..."
                            class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm"
                        >
                        <button
                            wire:click="saveApiKey('google')"
                            class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                        >
                            {{ __('carbex.common.save') }}
                        </button>
                        @if ($availableProviders['google']['available'] ?? false)
                            <button
                                wire:click="testConnection('google')"
                                class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                            >
                                {{ __('carbex.settings.ai.test') }}
                            </button>
                        @endif
                    </div>
                    @if ($googleEnabled)
                        <div class="mt-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('carbex.settings.ai.model') }}</label>
                            <select wire:model="googleModel" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm">
                                @foreach ($providerModels['google'] ?? [] as $model => $label)
                                    <option value="{{ $model }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                </div>

                <!-- DeepSeek -->
                <div class="rounded-lg border border-gray-200 p-4">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-lg bg-purple-100 flex items-center justify-center mr-3">
                                <span class="text-purple-600 font-bold text-sm">D</span>
                            </div>
                            <div>
                                <h3 class="font-medium text-gray-900">DeepSeek</h3>
                                <p class="text-xs text-gray-500">{{ __('carbex.settings.ai.deepseek_desc') }}</p>
                            </div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" wire:model.live="deepseekEnabled" class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-green-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-600"></div>
                        </label>
                    </div>
                    <div class="flex gap-2">
                        <input
                            type="password"
                            wire:model="deepseekApiKey"
                            placeholder="sk-..."
                            class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm"
                        >
                        <button
                            wire:click="saveApiKey('deepseek')"
                            class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                        >
                            {{ __('carbex.common.save') }}
                        </button>
                        @if ($availableProviders['deepseek']['available'] ?? false)
                            <button
                                wire:click="testConnection('deepseek')"
                                class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                            >
                                {{ __('carbex.settings.ai.test') }}
                            </button>
                        @endif
                    </div>
                    @if ($deepseekEnabled)
                        <div class="mt-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('carbex.settings.ai.model') }}</label>
                            <select wire:model="deepseekModel" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm">
                                @foreach ($providerModels['deepseek'] ?? [] as $model => $label)
                                    <option value="{{ $model }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                </div>
            </div>
        </x-card>

        <!-- General Settings -->
        <form wire:submit="saveSettings">
            <x-card>
                <x-slot name="header">
                    <h2 class="text-lg font-medium text-gray-900">{{ __('carbex.settings.ai.general') }}</h2>
                    <p class="mt-1 text-sm text-gray-500">{{ __('carbex.settings.ai.general_desc') }}</p>
                </x-slot>

                <div class="space-y-6">
                    <!-- Default Provider -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('carbex.settings.ai.default_provider') }}</label>
                        <select wire:model="defaultProvider" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm">
                            <option value="anthropic">Anthropic (Claude)</option>
                            <option value="openai">OpenAI (GPT)</option>
                            <option value="google">Google (Gemini)</option>
                            <option value="deepseek">DeepSeek</option>
                        </select>
                        <p class="mt-1 text-xs text-gray-500">{{ __('carbex.settings.ai.default_provider_desc') }}</p>
                    </div>

                    <!-- Generation Parameters -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('carbex.settings.ai.max_tokens') }}</label>
                            <input
                                type="number"
                                wire:model="maxTokens"
                                min="256"
                                max="32000"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm"
                            >
                            <p class="mt-1 text-xs text-gray-500">{{ __('carbex.settings.ai.max_tokens_desc') }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('carbex.settings.ai.temperature') }}</label>
                            <input
                                type="number"
                                wire:model="temperature"
                                min="0"
                                max="2"
                                step="0.1"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm"
                            >
                            <p class="mt-1 text-xs text-gray-500">{{ __('carbex.settings.ai.temperature_desc') }}</p>
                        </div>
                    </div>
                </div>
            </x-card>

            <!-- Feature Flags -->
            <x-card class="mt-8">
                <x-slot name="header">
                    <h2 class="text-lg font-medium text-gray-900">{{ __('carbex.settings.ai.features') }}</h2>
                    <p class="mt-1 text-sm text-gray-500">{{ __('carbex.settings.ai.features_desc') }}</p>
                </x-slot>

                <div class="space-y-4">
                    <label class="flex items-center justify-between p-4 rounded-lg border border-gray-200 cursor-pointer hover:bg-gray-50">
                        <div>
                            <span class="font-medium text-gray-900">{{ __('carbex.settings.ai.chat_widget') }}</span>
                            <p class="text-sm text-gray-500">{{ __('carbex.settings.ai.chat_widget_desc') }}</p>
                        </div>
                        <input type="checkbox" wire:model="chatWidgetEnabled" class="h-5 w-5 rounded border-gray-300 text-green-600 focus:ring-green-500">
                    </label>

                    <label class="flex items-center justify-between p-4 rounded-lg border border-gray-200 cursor-pointer hover:bg-gray-50">
                        <div>
                            <span class="font-medium text-gray-900">{{ __('carbex.settings.ai.emission_helper') }}</span>
                            <p class="text-sm text-gray-500">{{ __('carbex.settings.ai.emission_helper_desc') }}</p>
                        </div>
                        <input type="checkbox" wire:model="emissionHelperEnabled" class="h-5 w-5 rounded border-gray-300 text-green-600 focus:ring-green-500">
                    </label>

                    <label class="flex items-center justify-between p-4 rounded-lg border border-gray-200 cursor-pointer hover:bg-gray-50">
                        <div>
                            <span class="font-medium text-gray-900">{{ __('carbex.settings.ai.document_extraction') }}</span>
                            <p class="text-sm text-gray-500">{{ __('carbex.settings.ai.document_extraction_desc') }}</p>
                        </div>
                        <input type="checkbox" wire:model="documentExtractionEnabled" class="h-5 w-5 rounded border-gray-300 text-green-600 focus:ring-green-500">
                    </label>
                </div>
            </x-card>

            <!-- Submit Button -->
            <div class="mt-6 flex justify-end">
                <x-button type="submit" wire:loading.attr="disabled">
                    <span wire:loading.remove>{{ __('carbex.common.save') }}</span>
                    <span wire:loading>{{ __('carbex.common.saving') }}</span>
                </x-button>
            </div>
        </form>

        <!-- Help Section -->
        <div class="rounded-lg bg-blue-50 border border-blue-200 p-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800">{{ __('carbex.settings.ai.help_title') }}</h3>
                    <div class="mt-2 text-sm text-blue-700">
                        <ul class="list-disc pl-5 space-y-1">
                            <li><a href="https://console.anthropic.com/" target="_blank" class="underline hover:text-blue-900">Anthropic Console</a> - {{ __('carbex.settings.ai.get_anthropic_key') }}</li>
                            <li><a href="https://platform.openai.com/api-keys" target="_blank" class="underline hover:text-blue-900">OpenAI Platform</a> - {{ __('carbex.settings.ai.get_openai_key') }}</li>
                            <li><a href="https://aistudio.google.com/app/apikey" target="_blank" class="underline hover:text-blue-900">Google AI Studio</a> - {{ __('carbex.settings.ai.get_google_key') }}</li>
                            <li><a href="https://platform.deepseek.com/" target="_blank" class="underline hover:text-blue-900">DeepSeek Platform</a> - {{ __('carbex.settings.ai.get_deepseek_key') }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
