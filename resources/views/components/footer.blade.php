<!-- Chat Support Widget -->
@auth
    <livewire:support.chat-widget />
@else
    <!-- Simple button for guests -->
    <div class="fixed bottom-4 right-4 z-50">
        <a href="{{ route('login') }}"
           class="flex items-center px-4 py-3 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-full shadow-lg transition-colors"
           title="Support en ligne">
            <span class="relative flex h-2.5 w-2.5 mr-2">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-300 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-green-200"></span>
            </span>
            En ligne
        </a>
    </div>
@endauth
