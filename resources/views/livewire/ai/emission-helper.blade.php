<div>
    {{-- AI Helper Button --}}
    <button
        type="button"
        wire:click="openHelper"
        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-lg hover:bg-emerald-100 transition-colors"
        title="{{ __('linscarbon.ai.ai_help') }}"
    >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
        </svg>
        <span>{{ __('linscarbon.ai.ai_help') }}</span>
    </button>

    {{-- Sliding Panel --}}
    <div
        x-data="{ open: @entangle('isOpen') }"
        x-show="open"
        x-cloak
        class="fixed inset-0 z-50 overflow-hidden"
        aria-labelledby="slide-over-title"
        role="dialog"
        aria-modal="true"
    >
        {{-- Backdrop --}}
        <div
            x-show="open"
            x-transition:enter="ease-in-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in-out duration-300"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="absolute inset-0 bg-gray-500 bg-opacity-50 transition-opacity"
            @click="$wire.closeHelper()"
        ></div>

        {{-- Panel --}}
        <div class="fixed inset-y-0 right-0 flex max-w-full pl-10">
            <div
                x-show="open"
                x-transition:enter="transform transition ease-in-out duration-300"
                x-transition:enter-start="translate-x-full"
                x-transition:enter-end="translate-x-0"
                x-transition:leave="transform transition ease-in-out duration-300"
                x-transition:leave-start="translate-x-0"
                x-transition:leave-end="translate-x-full"
                class="w-screen max-w-md"
            >
                <div class="flex flex-col h-full bg-white shadow-xl">
                    {{-- Header --}}
                    <div class="px-4 py-4 bg-gradient-to-r from-emerald-600 to-emerald-700 sm:px-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h2 class="text-lg font-semibold text-white" id="slide-over-title">
                                    {{ __('linscarbon.ai.ai_help') }}
                                </h2>
                                @if($aiAvailable)
                                    <p class="text-xs text-emerald-100 mt-0.5">
                                        {{ $providerName }} @if($modelName) - {{ $modelName }} @endif
                                    </p>
                                @endif
                            </div>
                            <button
                                type="button"
                                class="text-emerald-100 hover:text-white transition-colors"
                                wire:click="closeHelper"
                            >
                                <span class="sr-only">{{ __('linscarbon.ai.close') }}</span>
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        {{-- Category context --}}
                        @if($categoryCode)
                            <div class="mt-3 px-3 py-2 bg-white/10 rounded-lg">
                                <p class="text-xs text-emerald-100">{{ __('linscarbon.ai.helper.current_category') }}</p>
                                <p class="text-sm font-medium text-white">{{ $categoryCode }} - {{ $categoryName }}</p>
                            </div>
                        @endif
                    </div>

                    {{-- Content --}}
                    <div class="flex-1 overflow-y-auto">
                        @if(!$aiAvailable)
                            {{-- AI Not Available --}}
                            <div class="p-6">
                                <div class="text-center py-8">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                    <h3 class="mt-2 text-sm font-medium text-gray-900">{{ __('linscarbon.ai.helper.not_configured') }}</h3>
                                    <p class="mt-1 text-sm text-gray-500">
                                        {{ __('linscarbon.ai.helper.configure_api_key') }}
                                    </p>
                                    <a href="{{ route('settings') }}" class="mt-4 inline-flex items-center gap-1 text-sm text-emerald-600 hover:text-emerald-700">
                                        {{ __('linscarbon.ai.helper.configure_ai') }}
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        @else
                            {{-- Suggestions Section --}}
                            @if(count($suggestions) > 0)
                                <div class="p-4 border-b">
                                    <h3 class="text-sm font-medium text-gray-900 mb-3">
                                        {{ __('linscarbon.ai.helper.suggested_sources') }}
                                    </h3>
                                    <div class="space-y-2">
                                        @foreach($suggestions as $index => $suggestion)
                                            <button
                                                type="button"
                                                wire:click="applySuggestion({{ $index }})"
                                                class="w-full text-left px-3 py-2 bg-gray-50 hover:bg-emerald-50 rounded-lg border border-gray-200 hover:border-emerald-300 transition-colors"
                                            >
                                                <p class="text-sm font-medium text-gray-900">{{ $suggestion['suggestion'] }}</p>
                                                <p class="text-xs text-gray-500 mt-0.5">
                                                    {{ $suggestion['description'] }}
                                                    @if(isset($suggestion['typical_unit']))
                                                        <span class="text-emerald-600">({{ $suggestion['typical_unit'] }})</span>
                                                    @endif
                                                </p>
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            {{-- Category Suggestion --}}
                            @if($categorySuggestion)
                                <div class="p-4 border-b bg-blue-50">
                                    <h3 class="text-sm font-medium text-blue-900 mb-2">
                                        {{ __('linscarbon.ai.helper.suggested_category') }}
                                    </h3>
                                    <div class="bg-white rounded-lg border border-blue-200 p-3">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <p class="font-medium text-gray-900">
                                                    {{ $categorySuggestion['category_code'] }} - {{ $categorySuggestion['category_name'] }}
                                                </p>
                                                <p class="text-xs text-gray-500 mt-1">
                                                    {{ $categorySuggestion['reasoning'] }}
                                                </p>
                                            </div>
                                            <div class="text-right">
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $categorySuggestion['confidence'] >= 0.7 ? 'bg-green-100 text-green-700' : ($categorySuggestion['confidence'] >= 0.5 ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                                                    {{ number_format($categorySuggestion['confidence'] * 100) }}%
                                                </span>
                                            </div>
                                        </div>
                                        <button
                                            type="button"
                                            wire:click="applyCategory"
                                            class="mt-3 w-full px-3 py-1.5 text-sm font-medium text-blue-700 bg-blue-100 hover:bg-blue-200 rounded-lg transition-colors"
                                        >
                                            {{ __('linscarbon.ai.helper.use_category') }}
                                        </button>
                                    </div>
                                </div>
                            @endif

                            {{-- Factor Suggestion --}}
                            @if($factorSuggestion)
                                <div class="p-4 border-b bg-purple-50">
                                    <h3 class="text-sm font-medium text-purple-900 mb-2">
                                        {{ __('linscarbon.ai.helper.suggested_factor') }}
                                    </h3>
                                    <div class="bg-white rounded-lg border border-purple-200 p-3">
                                        <p class="font-medium text-gray-900">{{ $factorSuggestion['name'] }}</p>
                                        <p class="text-sm text-gray-600 mt-1">
                                            {{ number_format($factorSuggestion['value'], 4) }} kgCO2e/{{ $factorSuggestion['unit'] }}
                                        </p>
                                        <p class="text-xs text-gray-500 mt-1">Source: {{ $factorSuggestion['source'] }}</p>
                                        <button
                                            type="button"
                                            wire:click="applyFactor"
                                            class="mt-3 w-full px-3 py-1.5 text-sm font-medium text-purple-700 bg-purple-100 hover:bg-purple-200 rounded-lg transition-colors"
                                        >
                                            {{ __('linscarbon.ai.helper.use_factor') }}
                                        </button>
                                    </div>
                                </div>
                            @endif

                            {{-- Quick Actions --}}
                            <div class="p-4 border-b">
                                <h3 class="text-sm font-medium text-gray-900 mb-3">{{ __('linscarbon.ai.helper.quick_actions') }}</h3>
                                <div class="flex flex-wrap gap-2">
                                    <button
                                        type="button"
                                        wire:click="askCategoryHelp"
                                        class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-emerald-700 bg-emerald-50 hover:bg-emerald-100 rounded-full transition-colors"
                                    >
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        {{ __('linscarbon.ai.helper.how_to_fill') }}
                                    </button>
                                    <button
                                        type="button"
                                        wire:click="suggestFactor"
                                        class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-purple-700 bg-purple-50 hover:bg-purple-100 rounded-full transition-colors"
                                        @disabled(!$currentInput)
                                    >
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                                        </svg>
                                        {{ __('linscarbon.ai.helper.suggest_factor') }}
                                    </button>
                                    <button
                                        type="button"
                                        wire:click="suggestCategory"
                                        class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-blue-700 bg-blue-50 hover:bg-blue-100 rounded-full transition-colors"
                                        @disabled(!$currentInput)
                                    >
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                        </svg>
                                        {{ __('linscarbon.ai.helper.suggest_category') }}
                                    </button>
                                </div>
                            </div>

                            {{-- Chat Messages --}}
                            <div class="p-4 space-y-4" style="min-height: 200px;">
                                @forelse($messages as $message)
                                    <div class="flex {{ $message['role'] === 'user' ? 'justify-end' : 'justify-start' }}">
                                        <div class="max-w-[85%] px-4 py-2 rounded-2xl {{ $message['role'] === 'user' ? 'bg-emerald-600 text-white rounded-br-md' : 'bg-gray-100 text-gray-900 rounded-bl-md' }}">
                                            <div class="text-sm whitespace-pre-wrap">{!! nl2br(e($message['content'])) !!}</div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-8 text-gray-400">
                                        <svg class="mx-auto h-8 w-8 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                        </svg>
                                        <p class="text-sm">{{ __('linscarbon.ai.helper.ask_about_category') }}</p>
                                    </div>
                                @endforelse

                                {{-- Loading indicator --}}
                                @if($isLoading)
                                    <div class="flex justify-start">
                                        <div class="bg-gray-100 rounded-2xl rounded-bl-md px-4 py-3">
                                            <div class="flex items-center gap-1">
                                                <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0ms"></div>
                                                <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 150ms"></div>
                                                <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 300ms"></div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            {{-- Quick prompts --}}
                            @if(count($messages) === 0)
                                <div class="px-4 pb-4">
                                    <p class="text-xs text-gray-500 mb-2">{{ __('linscarbon.ai.helper.frequent_questions') }}</p>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($this->quickPrompts as $prompt)
                                            <button
                                                type="button"
                                                wire:click="quickAsk('{{ $prompt }}')"
                                                class="text-xs px-3 py-1.5 bg-gray-50 hover:bg-gray-100 text-gray-700 rounded-full border border-gray-200 transition-colors"
                                            >
                                                {{ $prompt }}
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        @endif
                    </div>

                    {{-- Input Footer --}}
                    @if($aiAvailable)
                        <div class="border-t p-4 bg-gray-50">
                            {{-- Auto-completions dropdown --}}
                            @if(count($autoCompletions) > 0)
                                <div class="mb-2 bg-white border rounded-lg shadow-sm max-h-32 overflow-y-auto">
                                    @foreach($autoCompletions as $completion)
                                        <button
                                            type="button"
                                            wire:click="applyAutoComplete('{{ addslashes($completion) }}')"
                                            class="w-full text-left px-3 py-2 text-sm hover:bg-emerald-50 transition-colors"
                                        >
                                            {{ $completion }}
                                        </button>
                                    @endforeach
                                </div>
                            @endif

                            <form wire:submit.prevent="askQuestion" class="flex gap-2">
                                <input
                                    type="text"
                                    wire:model.live.debounce.300ms="userQuestion"
                                    placeholder="{{ __('linscarbon.ai.ask_question') }}"
                                    class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                    @disabled($isLoading)
                                >
                                <button
                                    type="submit"
                                    class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                                    @disabled($isLoading || !$userQuestion)
                                >
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
