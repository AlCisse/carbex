<div>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-semibold text-gray-900">{{ __('carbex.promote.showcase_title') }}</h1>
                <p class="mt-1 text-sm text-gray-500">{{ __('carbex.promote.showcase_desc') }}</p>
            </div>
        </div>
    </x-slot>

    <!-- Organization Summary Card -->
    <div class="mb-8 bg-gradient-to-r from-emerald-600 to-teal-600 rounded-2xl p-8 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold">{{ $organizationInfo['name'] ?? '' }}</h2>
                <p class="mt-1 text-emerald-100">{{ __('carbex.promote.sustainability_journey') }}</p>
            </div>
            <div class="flex items-center gap-8">
                <div class="text-center">
                    <div class="text-3xl font-bold">{{ $organizationInfo['badges_count'] ?? 0 }}</div>
                    <div class="text-sm text-emerald-100">{{ __('carbex.promote.badges_earned') }}</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold">{{ number_format($organizationInfo['total_points'] ?? 0) }}</div>
                    <div class="text-sm text-emerald-100">{{ __('carbex.gamification.points') }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Earned Badges Grid -->
    @if(count($earnedBadges) > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($earnedBadges as $badge)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-lg transition-shadow">
                    <!-- Badge Header -->
                    <div class="p-6 {{ match($badge['color']) {
                        'emerald' => 'bg-emerald-50',
                        'blue' => 'bg-blue-50',
                        'purple' => 'bg-purple-50',
                        'yellow' => 'bg-yellow-50',
                        'orange' => 'bg-orange-50',
                        default => 'bg-gray-50'
                    } }}">
                        <div class="flex items-center gap-4">
                            <div class="w-16 h-16 rounded-full flex items-center justify-center {{ match($badge['color']) {
                                'emerald' => 'bg-emerald-500',
                                'blue' => 'bg-blue-500',
                                'purple' => 'bg-purple-500',
                                'yellow' => 'bg-yellow-500',
                                'orange' => 'bg-orange-500',
                                default => 'bg-gray-500'
                            } }}">
                                @switch($badge['icon'])
                                    @case('trophy')
                                        <svg class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                                        </svg>
                                        @break
                                    @case('chart-down')
                                        <svg class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6" />
                                        </svg>
                                        @break
                                    @case('users')
                                        <svg class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                        @break
                                    @case('academic-cap')
                                        <svg class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0v6m0 0l-3-1.5m3 1.5l3-1.5" />
                                        </svg>
                                        @break
                                    @default
                                        <svg class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                                        </svg>
                                @endswitch
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900">{{ $badge['name'] }}</h3>
                                <p class="text-sm text-gray-500">{{ __('carbex.gamification.earned_on') }} {{ $badge['earned_at'] }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Badge Content -->
                    <div class="p-6">
                        <p class="text-sm text-gray-600 mb-4">{{ $badge['description'] }}</p>

                        <div class="flex items-center justify-between text-sm">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full font-medium {{ $badge['color_class'] }}">
                                +{{ $badge['points'] }} pts
                            </span>
                            <span class="text-gray-400 text-xs uppercase">{{ __('carbex.gamification.categories.' . $badge['category']) }}</span>
                        </div>
                    </div>

                    <!-- Badge Actions -->
                    <div class="px-6 pb-6 flex gap-2">
                        <button
                            wire:click="openShareModal('{{ $badge['id'] }}')"
                            class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors text-sm font-medium"
                        >
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                            </svg>
                            {{ __('carbex.promote.share') }}
                        </button>
                        <button
                            wire:click="openEmbedModal('{{ $badge['id'] }}')"
                            class="inline-flex items-center justify-center p-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
                            title="{{ __('carbex.promote.embed_widget') }}"
                        >
                            <svg class="w-5 h-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
                            </svg>
                        </button>
                        <button
                            wire:click="downloadBadge('{{ $badge['id'] }}', 'png')"
                            class="inline-flex items-center justify-center p-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
                            title="{{ __('carbex.promote.download') }}"
                        >
                            <svg class="w-5 h-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <!-- Empty State -->
        <div class="text-center py-16 bg-white rounded-xl border border-gray-200">
            <div class="w-16 h-16 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                <svg class="w-8 h-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('carbex.gamification.no_badges_yet') }}</h3>
            <p class="text-gray-500 mb-6 max-w-md mx-auto">{{ __('carbex.promote.no_badges_desc') }}</p>
            <a href="{{ route('gamification') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                {{ __('carbex.promote.view_all_badges') }}
            </a>
        </div>
    @endif

    <!-- Share Modal -->
    @if($showShareModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="closeShareModal"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                    <div>
                        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-emerald-100">
                            <svg class="h-6 w-6 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-5">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                {{ __('carbex.promote.share_badge') }}
                            </h3>
                            <div class="mt-4">
                                <!-- Share URL -->
                                <div class="flex items-center gap-2 p-3 bg-gray-50 rounded-lg">
                                    <input type="text" readonly value="{{ $shareUrl }}" class="flex-1 bg-transparent border-0 text-sm text-gray-600 focus:ring-0">
                                    <button onclick="navigator.clipboard.writeText('{{ $shareUrl }}')" class="p-2 hover:bg-gray-200 rounded transition-colors" title="{{ __('carbex.promote.copy_link') }}">
                                        <svg class="w-5 h-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                        </svg>
                                    </button>
                                </div>

                                <!-- Social Buttons -->
                                <div class="mt-4 flex gap-3 justify-center">
                                    @if($selectedBadgeId)
                                        <a href="{{ $this->getLinkedInShareUrl($selectedBadgeId) }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 px-4 py-2 bg-[#0A66C2] text-white rounded-lg hover:bg-[#004182] transition-colors">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                                            </svg>
                                            LinkedIn
                                        </a>
                                        <a href="{{ $this->getTwitterShareUrl($selectedBadgeId) }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 px-4 py-2 bg-black text-white rounded-lg hover:bg-gray-800 transition-colors">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                                            </svg>
                                            X (Twitter)
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-5 sm:mt-6">
                        <button type="button" wire:click="closeShareModal" class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:text-sm">
                            {{ __('carbex.common.close') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Embed Modal -->
    @if($showEmbedModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="closeEmbedModal"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                    <div>
                        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-purple-100">
                            <svg class="h-6 w-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-5">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                {{ __('carbex.promote.embed_widget') }}
                            </h3>
                            <p class="mt-2 text-sm text-gray-500">
                                {{ __('carbex.promote.embed_desc') }}
                            </p>
                            <div class="mt-4">
                                <textarea readonly rows="4" class="w-full p-3 bg-gray-50 rounded-lg border border-gray-200 text-xs font-mono text-gray-600 focus:ring-emerald-500 focus:border-emerald-500">{{ $embedCode }}</textarea>
                                <button onclick="navigator.clipboard.writeText(document.querySelector('textarea').value)" class="mt-2 inline-flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors text-sm">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                    </svg>
                                    {{ __('carbex.promote.copy_code') }}
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="mt-5 sm:mt-6">
                        <button type="button" wire:click="closeEmbedModal" class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:text-sm">
                            {{ __('carbex.common.close') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

@script
<script>
    $wire.on('download-badge', (data) => {
        const link = document.createElement('a');
        link.href = data.url;
        link.download = data.filename;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    });
</script>
@endscript
