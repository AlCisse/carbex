<div class="space-y-6">
    {{-- Score et niveau --}}
    <div class="bg-gradient-to-r from-emerald-500 to-teal-600 rounded-xl shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold">{{ $score['level_name'] ?? __('carbex.gamification.level.starter') }}</h2>
                <p class="text-emerald-100 mt-1">{{ __('carbex.gamification.total_points', ['points' => $score['total_points'] ?? 0]) }}</p>
            </div>
            <div class="text-right">
                <div class="text-5xl font-bold">{{ $score['badge_count'] ?? 0 }}</div>
                <div class="text-emerald-100 text-sm">{{ __('carbex.gamification.badges_earned') }}</div>
            </div>
        </div>

        {{-- Barre de progression vers le niveau suivant --}}
        @if(($score['level'] ?? 0) < 5)
            <div class="mt-4">
                <div class="flex justify-between text-sm text-emerald-100 mb-1">
                    <span>{{ __('carbex.gamification.next_level') }}</span>
                    <span>{{ $score['next_level_points'] ?? 0 }} {{ __('carbex.gamification.points') }}</span>
                </div>
                <div class="w-full bg-emerald-700 rounded-full h-2">
                    <div class="bg-white rounded-full h-2 transition-all duration-500"
                         style="width: {{ $score['progress_to_next'] ?? 0 }}%"></div>
                </div>
            </div>
        @else
            <div class="mt-4 text-emerald-100 text-center">
                {{ __('carbex.gamification.max_level_reached') }}
            </div>
        @endif

        {{-- Actions --}}
        <div class="mt-4 flex gap-3">
            <button wire:click="checkNewBadges"
                    class="px-4 py-2 bg-white/20 hover:bg-white/30 rounded-lg text-sm font-medium transition-colors">
                <svg class="w-4 h-4 inline-block mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                {{ __('carbex.gamification.check_badges') }}
            </button>
            <button wire:click="loadLeaderboard"
                    class="px-4 py-2 bg-white/20 hover:bg-white/30 rounded-lg text-sm font-medium transition-colors">
                <svg class="w-4 h-4 inline-block mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                {{ __('carbex.gamification.leaderboard') }}
            </button>
        </div>
    </div>

    {{-- Grille des badges --}}
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-lg p-6">
        <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">
            {{ __('carbex.gamification.your_badges') }}
        </h3>

        {{-- Filtres par catégorie --}}
        <div class="flex flex-wrap gap-2 mb-6">
            @php
                $categories = [
                    'all' => __('carbex.gamification.category.all'),
                    'assessment' => __('carbex.gamification.category.assessment'),
                    'reduction' => __('carbex.gamification.category.reduction'),
                    'engagement' => __('carbex.gamification.category.engagement'),
                    'expert' => __('carbex.gamification.category.expert'),
                ];
            @endphp
            @foreach($categories as $key => $label)
                <button x-data="{ active: '{{ $key }}' === 'all' }"
                        @click="$dispatch('filter-badges', { category: '{{ $key }}' })"
                        class="px-3 py-1.5 text-sm font-medium rounded-full transition-colors"
                        :class="active ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400' : 'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-600'">
                    {{ $label }}
                </button>
            @endforeach
        </div>

        {{-- Grille --}}
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            @forelse($badges as $badge)
                <div wire:click="selectBadge('{{ $badge['id'] }}')"
                     class="relative group cursor-pointer rounded-xl border-2 p-4 transition-all duration-200 hover:shadow-lg
                            {{ $badge['earned']
                                ? 'border-emerald-200 dark:border-emerald-700 bg-emerald-50 dark:bg-emerald-900/20'
                                : 'border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 opacity-60' }}">

                    {{-- Icône --}}
                    <div class="flex justify-center mb-3">
                        <div class="w-16 h-16 rounded-full flex items-center justify-center
                                    {{ $badge['earned'] ? $badge['color_class'] : 'bg-slate-200 dark:bg-slate-700 text-slate-400' }}">
                            @switch($badge['icon'])
                                @case('trophy')
                                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                                    </svg>
                                    @break
                                @case('academic-cap')
                                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0v6m0 0l-3-1.5m3 1.5l3-1.5" />
                                    </svg>
                                    @break
                                @case('arrow-trending-down')
                                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6" />
                                    </svg>
                                    @break
                                @case('globe-alt')
                                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                                    </svg>
                                    @break
                                @case('building-office-2')
                                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                    @break
                                @case('chart-bar')
                                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                    </svg>
                                    @break
                                @case('beaker')
                                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                                    </svg>
                                    @break
                                @default
                                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                                    </svg>
                            @endswitch
                        </div>
                    </div>

                    {{-- Nom et points --}}
                    <h4 class="text-center font-semibold text-slate-900 dark:text-white text-sm">
                        {{ $badge['name'] }}
                    </h4>
                    <p class="text-center text-xs text-slate-500 dark:text-slate-400 mt-1">
                        {{ $badge['points'] }} {{ __('carbex.gamification.points') }}
                    </p>

                    {{-- Barre de progression (si non obtenu) --}}
                    @if(!$badge['earned'])
                        <div class="mt-3">
                            <div class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-1.5">
                                <div class="bg-emerald-500 rounded-full h-1.5 transition-all duration-500"
                                     style="width: {{ $badge['progress'] }}%"></div>
                            </div>
                            <p class="text-center text-xs text-slate-400 mt-1">{{ $badge['progress'] }}%</p>
                        </div>
                    @else
                        <p class="text-center text-xs text-emerald-600 dark:text-emerald-400 mt-2">
                            {{ __('carbex.gamification.earned_on') }} {{ $badge['earned_at'] }}
                        </p>
                    @endif

                    {{-- Bouton partage (si obtenu) --}}
                    @if($badge['earned'])
                        <button wire:click.stop="shareBadge('{{ $badge['id'] }}')"
                                class="absolute top-2 right-2 p-1.5 text-slate-400 hover:text-emerald-600 dark:hover:text-emerald-400 opacity-0 group-hover:opacity-100 transition-opacity">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                            </svg>
                        </button>
                    @endif
                </div>
            @empty
                <div class="col-span-full text-center py-8 text-slate-500 dark:text-slate-400">
                    {{ __('carbex.gamification.no_badges_available') }}
                </div>
            @endforelse
        </div>
    </div>

    {{-- Modal Leaderboard --}}
    @if($showLeaderboard)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-slate-500 bg-opacity-75 transition-opacity" wire:click="hideLeaderboard"></div>

                <div class="inline-block align-bottom bg-white dark:bg-slate-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white dark:bg-slate-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">
                            {{ __('carbex.gamification.leaderboard') }}
                        </h3>

                        <div class="space-y-3">
                            @foreach($leaderboard as $entry)
                                <div class="flex items-center gap-4 p-3 rounded-lg {{ $entry['rank'] <= 3 ? 'bg-emerald-50 dark:bg-emerald-900/20' : 'bg-slate-50 dark:bg-slate-700/50' }}">
                                    <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center font-bold
                                                {{ $entry['rank'] === 1 ? 'bg-yellow-400 text-yellow-900' :
                                                   ($entry['rank'] === 2 ? 'bg-slate-300 text-slate-700' :
                                                   ($entry['rank'] === 3 ? 'bg-orange-400 text-orange-900' : 'bg-slate-200 dark:bg-slate-600 text-slate-600 dark:text-slate-300')) }}">
                                        {{ $entry['rank'] }}
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="font-medium text-slate-900 dark:text-white truncate">
                                            {{ $entry['organization_name'] }}
                                        </p>
                                        <p class="text-sm text-slate-500 dark:text-slate-400">
                                            {{ $entry['badge_count'] }} {{ __('carbex.gamification.badges') }}
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-bold text-emerald-600 dark:text-emerald-400">
                                            {{ $entry['total_points'] }}
                                        </p>
                                        <p class="text-xs text-slate-500 dark:text-slate-400">
                                            {{ __('carbex.gamification.points') }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="bg-slate-50 dark:bg-slate-700/50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button wire:click="hideLeaderboard"
                                class="w-full inline-flex justify-center rounded-md border border-slate-300 dark:border-slate-600 shadow-sm px-4 py-2 bg-white dark:bg-slate-800 text-base font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 sm:w-auto sm:text-sm">
                            {{ __('carbex.common.close') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal Partage --}}
    @if($showShareModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-slate-500 bg-opacity-75 transition-opacity" wire:click="closeShareModal"></div>

                <div class="inline-block align-bottom bg-white dark:bg-slate-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full">
                    <div class="bg-white dark:bg-slate-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">
                            {{ __('carbex.gamification.share_badge') }}
                        </h3>

                        @if($shareUrl)
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                                        {{ __('carbex.gamification.share_link') }}
                                    </label>
                                    <div class="flex gap-2">
                                        <input type="text" readonly value="{{ $shareUrl }}"
                                               class="flex-1 rounded-md border-slate-300 dark:border-slate-600 dark:bg-slate-700 text-sm">
                                        <button onclick="navigator.clipboard.writeText('{{ $shareUrl }}')"
                                                class="px-3 py-2 bg-emerald-600 text-white rounded-md hover:bg-emerald-700 text-sm">
                                            {{ __('carbex.common.copy') }}
                                        </button>
                                    </div>
                                </div>

                                <div class="flex gap-3">
                                    <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode($shareUrl) }}"
                                       target="_blank"
                                       class="flex-1 py-2 px-4 bg-[#0077b5] text-white rounded-md text-center text-sm hover:bg-[#006097]">
                                        LinkedIn
                                    </a>
                                    <a href="https://twitter.com/intent/tweet?url={{ urlencode($shareUrl) }}&text={{ urlencode(__('carbex.gamification.share_text')) }}"
                                       target="_blank"
                                       class="flex-1 py-2 px-4 bg-[#1da1f2] text-white rounded-md text-center text-sm hover:bg-[#0c85d0]">
                                        Twitter
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="bg-slate-50 dark:bg-slate-700/50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button wire:click="closeShareModal"
                                class="w-full inline-flex justify-center rounded-md border border-slate-300 dark:border-slate-600 shadow-sm px-4 py-2 bg-white dark:bg-slate-800 text-base font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 sm:w-auto sm:text-sm">
                            {{ __('carbex.common.close') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
