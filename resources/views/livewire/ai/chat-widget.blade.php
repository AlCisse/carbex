<div>
    {{-- Floating Button --}}
    <button
        wire:click="toggle"
        class="fixed bottom-6 right-6 z-50 w-14 h-14 rounded-full shadow-lg flex items-center justify-center transition-all duration-300 hover:scale-110"
        style="background-color: #0d9488;"
        aria-label="{{ __('carbex.ai.chat.assistant_name') }}"
    >
        @if($isOpen)
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        @else
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
            </svg>
        @endif
    </button>

    {{-- Chat Panel --}}
    <div
        x-data="{ show: @entangle('isOpen') }"
        x-show="show"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-4"
        class="fixed bottom-24 right-6 z-50 w-96 max-w-[calc(100vw-3rem)] bg-white rounded-2xl shadow-2xl border overflow-hidden"
        style="border-color: #e2e8f0; height: 500px; max-height: calc(100vh - 8rem);"
    >
        {{-- Header --}}
        <div class="flex items-center justify-between px-4 py-3 border-b" style="border-color: #e2e8f0; background: linear-gradient(135deg, #0d9488 0%, #0f766e 100%);">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-white">{{ __('carbex.ai.chat.assistant_name') }}</h3>
                    <p class="text-xs text-white/70">{{ __('carbex.ai.chat.subtitle') }}</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button wire:click="startNewConversation" class="p-1.5 rounded-lg hover:bg-white/10 transition-colors" title="{{ __('carbex.ai.chat.new_conversation') }}">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                </button>
                <button wire:click="close" class="p-1.5 rounded-lg hover:bg-white/10 transition-colors">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        {{-- Messages --}}
        <div
            class="flex-1 overflow-y-auto p-4 space-y-4"
            style="height: calc(100% - 130px);"
            x-ref="messagesContainer"
            x-init="$watch('$wire.messages', () => { setTimeout(() => { $refs.messagesContainer.scrollTop = $refs.messagesContainer.scrollHeight }, 100) })"
        >
            @if(empty($messages))
                {{-- Welcome Message --}}
                <div class="text-center py-6">
                    <div class="w-16 h-16 rounded-full mx-auto mb-4 flex items-center justify-center" style="background-color: #ccfbf1;">
                        <svg class="w-8 h-8" style="color: #0d9488;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                        </svg>
                    </div>
                    <h4 class="font-semibold mb-2" style="color: #0f172a;">{{ __('carbex.ai.chat.welcome') }}</h4>
                    <p class="text-sm mb-4" style="color: #64748b;">{{ __('carbex.ai.chat.welcome_description') }}</p>

                    {{-- Suggested Prompts --}}
                    <div class="space-y-2">
                        @foreach($suggestedPrompts as $prompt)
                            <button
                                wire:click="useSuggestedPrompt('{{ addslashes($prompt) }}')"
                                class="block w-full text-left px-3 py-2 text-sm rounded-lg border transition-colors hover:border-teal-300 hover:bg-teal-50"
                                style="border-color: #e2e8f0; color: #0f172a;"
                            >
                                {{ $prompt }}
                            </button>
                        @endforeach
                    </div>
                </div>
            @else
                {{-- Conversation Messages --}}
                @foreach($messages as $msg)
                    <div class="flex {{ $msg['role'] === 'user' ? 'justify-end' : 'justify-start' }}">
                        <div class="max-w-[80%] {{ $msg['role'] === 'user' ? 'order-2' : 'order-1' }}">
                            @if($msg['role'] === 'assistant')
                                <div class="flex items-start gap-2">
                                    <div class="w-6 h-6 rounded-full flex-shrink-0 flex items-center justify-center" style="background-color: #0d9488;">
                                        <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                                        </svg>
                                    </div>
                                    <div class="px-4 py-2 rounded-2xl rounded-tl-none text-sm" style="background-color: #f1f5f9; color: #0f172a;">
                                        {!! nl2br(e($msg['content'])) !!}
                                    </div>
                                </div>
                            @else
                                <div class="px-4 py-2 rounded-2xl rounded-tr-none text-sm text-white" style="background-color: #0d9488;">
                                    {{ $msg['content'] }}
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach

                {{-- Loading indicator --}}
                @if($isLoading)
                    <div class="flex justify-start">
                        <div class="flex items-start gap-2">
                            <div class="w-6 h-6 rounded-full flex-shrink-0 flex items-center justify-center" style="background-color: #0d9488;">
                                <svg class="w-3 h-3 text-white animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                                </svg>
                            </div>
                            <div class="px-4 py-2 rounded-2xl rounded-tl-none" style="background-color: #f1f5f9;">
                                <div class="flex items-center gap-1">
                                    <div class="w-2 h-2 rounded-full bg-gray-400 animate-bounce" style="animation-delay: 0ms;"></div>
                                    <div class="w-2 h-2 rounded-full bg-gray-400 animate-bounce" style="animation-delay: 150ms;"></div>
                                    <div class="w-2 h-2 rounded-full bg-gray-400 animate-bounce" style="animation-delay: 300ms;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endif

            {{-- Error Message --}}
            @if($error)
                <div class="px-4 py-2 rounded-lg text-sm text-red-700 bg-red-50 border border-red-200">
                    {{ $error }}
                </div>
            @endif
        </div>

        {{-- Input --}}
        <div class="absolute bottom-0 left-0 right-0 p-4 border-t bg-white" style="border-color: #e2e8f0;">
            @if($quota['enabled'] ?? false)
                <form wire:submit="sendMessage" class="flex items-center gap-2">
                    <input
                        type="text"
                        wire:model="message"
                        placeholder="{{ __('carbex.ai.ask_question') }}"
                        class="flex-1 px-4 py-2.5 rounded-xl border text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                        style="border-color: #e2e8f0;"
                        @disabled($isLoading)
                    >
                    <button
                        type="submit"
                        class="p-2.5 rounded-xl text-white transition-colors disabled:opacity-50"
                        style="background-color: #0d9488;"
                        @disabled($isLoading || empty(trim($message)))
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                        </svg>
                    </button>
                </form>
                {{-- Quota Display --}}
                <div class="flex items-center justify-between mt-2 text-xs" style="color: #94a3b8;">
                    <span>{{ __('carbex.ai.chat.powered_by') }}</span>
                    @if($quota['unlimited'] ?? false)
                        <span class="flex items-center gap-1">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
                            {{ __('carbex.ai.chat.unlimited') }}
                        </span>
                    @else
                        <span class="flex items-center gap-1" title="{{ __('carbex.ai.chat.quota_daily') }}: {{ $quota['daily_used'] }}/{{ $quota['daily_limit'] }} | {{ __('carbex.ai.chat.quota_monthly') }}: {{ $quota['monthly_used'] }}/{{ $quota['monthly_limit'] }}">
                            @php
                                $remaining = min($quota['daily_remaining'] ?? PHP_INT_MAX, $quota['monthly_remaining'] ?? PHP_INT_MAX);
                                $isLow = $remaining <= 5;
                            @endphp
                            <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded {{ $isLow ? 'bg-orange-100 text-orange-600' : 'bg-teal-50 text-teal-600' }}">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                                {{ $remaining }} {{ $remaining > 1 ? __('carbex.ai.chat.remaining_plural') : __('carbex.ai.chat.remaining') }}
                            </span>
                        </span>
                    @endif
                </div>
            @else
                {{-- AI Not Available --}}
                <div class="text-center py-3">
                    <p class="text-sm font-medium" style="color: #64748b;">{{ __('carbex.ai.chat.ai_not_available') }}</p>
                    <a href="{{ url('/settings/billing') }}" class="inline-flex items-center gap-1 mt-2 text-xs font-medium" style="color: #0d9488;">
                        {{ __('carbex.ai.chat.upgrade_premium') }}
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
