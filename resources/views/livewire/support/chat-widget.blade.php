<div class="fixed bottom-4 right-4 z-50" x-data="{ animateButton: true }">
    <!-- Chat Panel -->
    @if($isOpen)
        <div class="absolute bottom-16 right-0 w-80 sm:w-96 bg-white rounded-xl shadow-2xl border border-gray-200 overflow-hidden flex flex-col"
             style="height: 480px;"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 transform translate-y-4"
             x-transition:enter-end="opacity-100 transform translate-y-0">

            <!-- Header -->
            <div class="bg-gradient-to-r from-green-600 to-green-700 text-white p-4 flex items-center justify-between">
                <div class="flex items-center">
                    <div class="relative">
                        <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                        </div>
                        <span class="absolute bottom-0 right-0 block h-3 w-3 rounded-full ring-2 ring-white {{ $this->isOnline ? 'bg-green-400' : 'bg-gray-400' }}"></span>
                    </div>
                    <div class="ml-3">
                        <h3 class="font-semibold text-sm">{{ __('carbex.support.title') }}</h3>
                        <p class="text-xs text-green-200">{{ $this->statusLabel }}</p>
                    </div>
                </div>
                <div class="flex items-center space-x-1">
                    <button wire:click="clearChat" class="p-1.5 hover:bg-white/10 rounded-lg transition" title="{{ __('carbex.support.clear_chat') }}">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                    <button wire:click="close" class="p-1.5 hover:bg-white/10 rounded-lg transition">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Messages -->
            <div class="flex-1 overflow-y-auto p-4 space-y-4 bg-gray-50" id="chat-messages">
                @forelse($messages as $msg)
                    <div class="flex {{ $msg['type'] === 'user' ? 'justify-end' : 'justify-start' }}">
                        <div class="max-w-[80%] {{ $msg['type'] === 'user' ? 'bg-green-600 text-white' : 'bg-white border border-gray-200' }} rounded-2xl px-4 py-2 shadow-sm">
                            <p class="text-sm {{ $msg['type'] === 'user' ? 'text-white' : 'text-gray-800' }}">{{ $msg['content'] }}</p>
                            <p class="text-xs {{ $msg['type'] === 'user' ? 'text-green-200' : 'text-gray-400' }} mt-1">{{ $msg['timestamp'] }}</p>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-gray-500 text-sm py-8">
                        {{ __('carbex.support.start_conversation') }}
                    </div>
                @endforelse

                <!-- Contact Form -->
                @if($showContactForm)
                    <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm">
                        <h4 class="font-medium text-gray-900 mb-3">{{ __('carbex.support.contact_form_title') }}</h4>
                        <div class="space-y-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">{{ __('carbex.support.your_name') }}</label>
                                <input type="text"
                                       wire:model="userName"
                                       class="w-full text-sm border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500"
                                       placeholder="{{ __('carbex.support.name_placeholder') }}">
                                @error('userName') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">{{ __('carbex.support.your_email') }}</label>
                                <input type="email"
                                       wire:model="userEmail"
                                       class="w-full text-sm border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500"
                                       placeholder="{{ __('carbex.support.email_placeholder') }}">
                                @error('userEmail') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                            </div>
                            <button wire:click="submitContactForm"
                                    class="w-full bg-green-600 text-white text-sm font-medium py-2 rounded-lg hover:bg-green-700 transition">
                                {{ __('carbex.support.submit_request') }}
                            </button>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Quick Responses (show when no messages or few messages) -->
            @if(count($messages) <= 1)
                <div class="px-4 pb-2 bg-gray-50">
                    <p class="text-xs text-gray-500 mb-2">{{ __('carbex.support.quick_help') }}</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach($quickResponses as $key => $label)
                            <button wire:click="quickResponse('{{ $key }}')"
                                    class="text-xs px-3 py-1.5 bg-white border border-gray-200 rounded-full text-gray-600 hover:bg-gray-50 hover:border-gray-300 transition">
                                {{ $label }}
                            </button>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Input -->
            <div class="p-3 bg-white border-t border-gray-200">
                <form wire:submit="sendMessage" class="flex items-center space-x-2">
                    <input type="text"
                           wire:model="message"
                           class="flex-1 text-sm border-gray-300 rounded-full px-4 py-2 focus:ring-green-500 focus:border-green-500"
                           placeholder="{{ __('carbex.support.message_placeholder') }}"
                           autocomplete="off">
                    <button type="submit"
                            class="p-2 bg-green-600 text-white rounded-full hover:bg-green-700 transition flex-shrink-0 disabled:opacity-50"
                            :disabled="!$wire.message">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    @endif

    <!-- Toggle Button -->
    <button wire:click="toggle"
            class="group flex items-center px-4 py-3 rounded-full shadow-lg transition-all duration-300 {{ $isOpen ? 'bg-gray-700 hover:bg-gray-800' : 'bg-green-600 hover:bg-green-700' }}"
            x-init="setTimeout(() => animateButton = false, 3000)"
            :class="{ 'animate-bounce': animateButton && !$wire.isOpen }">

        @if($isOpen)
            <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        @else
            <span class="relative flex h-2.5 w-2.5 mr-2">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full {{ $this->isOnline ? 'bg-green-300' : 'bg-gray-300' }} opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2.5 w-2.5 {{ $this->isOnline ? 'bg-green-200' : 'bg-gray-200' }}"></span>
            </span>
            <span class="text-white text-sm font-medium">{{ $this->statusLabel }}</span>

            <!-- Notification Badge -->
            @if(count($messages) > 0 && !$isOpen)
                <span class="absolute -top-1 -right-1 flex h-5 w-5 items-center justify-center rounded-full bg-red-500 text-xs text-white font-bold">
                    {{ min(count($messages), 9) }}{{ count($messages) > 9 ? '+' : '' }}
                </span>
            @endif
        @endif
    </button>
</div>

@script
<script>
    // Auto-scroll to bottom when new messages arrive
    $wire.on('message-sent', () => {
        const container = document.getElementById('chat-messages');
        if (container) {
            setTimeout(() => {
                container.scrollTop = container.scrollHeight;
            }, 100);
        }
    });

    // Scroll to bottom on load
    document.addEventListener('livewire:navigated', () => {
        const container = document.getElementById('chat-messages');
        if (container) {
            container.scrollTop = container.scrollHeight;
        }
    });
</script>
@endscript
