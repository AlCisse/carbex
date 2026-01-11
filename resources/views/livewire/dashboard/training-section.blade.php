<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
    <!-- Header -->
    <button wire:click="toggleExpanded" class="w-full flex items-center justify-between p-4 hover:bg-gray-50 transition-colors">
        <div class="flex items-center">
            <svg class="w-6 h-6 text-green-600 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
            </svg>
            <div class="text-left">
                <h3 class="text-lg font-semibold text-gray-900">{{ __('carbex.training.title') }}</h3>
                <p class="text-sm text-gray-500">{{ __('carbex.training.subtitle') }}</p>
            </div>
        </div>
        <svg class="w-5 h-5 text-gray-400 transition-transform {{ $expanded ? 'rotate-180' : '' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
    </button>

    <!-- Content -->
    @if($expanded)
    <div class="border-t border-gray-200 p-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @foreach($videos as $video)
                <div class="bg-gray-50 rounded-lg overflow-hidden">
                    <!-- Video Thumbnail -->
                    <div class="relative aspect-video bg-gray-200 group cursor-pointer" x-data="{ playing: false }">
                        @if(!$video['youtube_id'])
                            <div class="absolute inset-0 flex items-center justify-center">
                                <span class="text-gray-400">{{ __('carbex.training.coming_soon') }}</span>
                            </div>
                        @else
                            <template x-if="!playing">
                                <div @click="playing = true" class="absolute inset-0">
                                    <img src="https://img.youtube.com/vi/{{ $video['youtube_id'] }}/mqdefault.jpg" alt="{{ $video['title'] }}" class="w-full h-full object-cover">
                                    <div class="absolute inset-0 flex items-center justify-center bg-black/30 group-hover:bg-black/40 transition-colors">
                                        <svg class="w-16 h-16 text-white opacity-90 group-hover:opacity-100 group-hover:scale-110 transition-all" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <span class="absolute bottom-2 right-2 bg-black/70 text-white text-xs px-2 py-1 rounded">{{ $video['duration'] }}</span>
                                </div>
                            </template>
                            <template x-if="playing">
                                <iframe
                                    class="w-full h-full"
                                    src="https://www.youtube.com/embed/{{ $video['youtube_id'] }}?autoplay=1&rel=0"
                                    frameborder="0"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                    allowfullscreen>
                                </iframe>
                            </template>
                        @endif
                    </div>

                    <!-- Video Info -->
                    <div class="p-3">
                        <h4 class="font-medium text-gray-900 text-sm">{{ $video['title'] }}</h4>
                        <p class="text-xs text-gray-500 mt-1">{{ $video['description'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Additional Resources -->
        <div class="mt-4 pt-4 border-t border-gray-200">
            <div class="flex items-center justify-between">
                <p class="text-sm text-gray-600">
                    <svg class="w-4 h-4 inline mr-1 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ __('carbex.training.need_help') }}
                </p>
                <a href="mailto:support@carbex.fr" class="text-sm text-green-600 hover:text-green-700 font-medium">
                    {{ __('carbex.training.contact_support') }}
                </a>
            </div>
        </div>
    </div>
    @endif
</div>
