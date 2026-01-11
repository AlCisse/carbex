@props(['currentPillar' => null, 'currentSection' => null])

@php
    $pillars = [
        'measure' => [
            'name' => __('carbex.pillars.measure.name'),
            'icon' => 'chart-bar',
            'color' => 'emerald',
            'sections' => [
                ['key' => 'scope1', 'name' => __('carbex.pillars.measure.scope1'), 'route' => 'emissions.category', 'params' => ['scope' => 1, 'category' => '1.1']],
                ['key' => 'scope2', 'name' => __('carbex.pillars.measure.scope2'), 'route' => 'emissions.category', 'params' => ['scope' => 2, 'category' => '2.1']],
                ['key' => 'scope3', 'name' => __('carbex.pillars.measure.scope3'), 'route' => 'emissions.category', 'params' => ['scope' => 3, 'category' => '3.1']],
                ['key' => 'documents', 'name' => __('carbex.pillars.measure.documents'), 'route' => 'documents'],
                ['key' => 'analysis', 'name' => __('carbex.pillars.measure.analysis'), 'route' => 'emissions'],
            ],
        ],
        'plan' => [
            'name' => __('carbex.pillars.plan.name'),
            'icon' => 'clipboard-list',
            'color' => 'blue',
            'sections' => [
                ['key' => 'objectives', 'name' => __('carbex.pillars.plan.objectives'), 'route' => 'transition-plan.trajectory'],
                ['key' => 'actions', 'name' => __('carbex.pillars.plan.actions'), 'route' => 'transition-plan'],
                ['key' => 'ai-recommendations', 'name' => __('carbex.pillars.plan.ai_recommendations'), 'route' => 'ai.analysis'],
            ],
        ],
        'engage' => [
            'name' => __('carbex.pillars.engage.name'),
            'icon' => 'user-group',
            'color' => 'purple',
            'sections' => [
                ['key' => 'suppliers', 'name' => __('carbex.pillars.engage.suppliers'), 'route' => 'suppliers'],
                ['key' => 'employees', 'name' => __('carbex.pillars.engage.employees'), 'route' => 'engage.employees'],
                ['key' => 'sites', 'name' => __('carbex.pillars.engage.sites'), 'route' => 'settings.sites'],
            ],
        ],
        'report' => [
            'name' => __('carbex.pillars.report.name'),
            'icon' => 'document-text',
            'color' => 'amber',
            'sections' => [
                ['key' => 'reports', 'name' => __('carbex.pillars.report.reports'), 'route' => 'reports'],
                ['key' => 'compliance', 'name' => __('carbex.pillars.report.compliance'), 'route' => 'reports.compliance'],
                ['key' => 'history', 'name' => __('carbex.pillars.report.history'), 'route' => 'assessments'],
            ],
        ],
        'promote' => [
            'name' => __('carbex.pillars.promote.name'),
            'icon' => 'megaphone',
            'color' => 'rose',
            'sections' => [
                ['key' => 'badges', 'name' => __('carbex.pillars.promote.badges'), 'route' => 'gamification'],
                ['key' => 'showcase', 'name' => __('carbex.pillars.promote.showcase'), 'route' => 'promote.showcase'],
                ['key' => 'share', 'name' => __('carbex.pillars.promote.share'), 'route' => 'promote.share'],
            ],
        ],
    ];

    $colorClasses = [
        'emerald' => 'text-emerald-400 bg-emerald-500/10',
        'blue' => 'text-blue-400 bg-blue-500/10',
        'purple' => 'text-purple-400 bg-purple-500/10',
        'amber' => 'text-amber-400 bg-amber-500/10',
        'rose' => 'text-rose-400 bg-rose-500/10',
    ];
@endphp

<aside class="fixed inset-y-0 left-0 z-50 w-64 bg-slate-800 text-white flex flex-col">
    <!-- Logo -->
    <div class="flex items-center h-16 px-4 bg-slate-900">
        <a href="{{ route('dashboard') }}">
            <x-logo :dark="true" />
        </a>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 overflow-y-auto py-4">
        <!-- Dashboard -->
        <a href="{{ route('dashboard') }}"
           class="flex items-center px-4 py-2.5 text-sm font-medium {{ request()->routeIs('dashboard') ? 'bg-slate-700 text-white' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">
            <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            {{ __('carbex.navigation.dashboard') }}
        </a>

        <!-- 5 Pillars -->
        @foreach($pillars as $pillarKey => $pillar)
            <div x-data="{ open: {{ $currentPillar == $pillarKey ? 'true' : 'false' }} }" class="mt-1">
                <button @click="open = !open"
                        class="w-full flex items-center justify-between px-4 py-2.5 text-sm font-medium text-slate-300 hover:bg-slate-700 hover:text-white transition-colors">
                    <div class="flex items-center">
                        <span class="mr-3 h-8 w-8 flex items-center justify-center rounded-lg {{ $colorClasses[$pillar['color']] }}">
                            @if($pillar['icon'] === 'chart-bar')
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                            @elseif($pillar['icon'] === 'clipboard-list')
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                </svg>
                            @elseif($pillar['icon'] === 'user-group')
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            @elseif($pillar['icon'] === 'document-text')
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            @elseif($pillar['icon'] === 'megaphone')
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                                </svg>
                            @endif
                        </span>
                        <span class="truncate">{{ $pillar['name'] }}</span>
                    </div>
                    <svg :class="{ 'rotate-90': open }" class="h-4 w-4 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>

                <div x-show="open" x-collapse class="bg-slate-900/50">
                    @foreach($pillar['sections'] as $section)
                        @php
                            $routeExists = \Route::has($section['route']);
                            $isActive = $routeExists && request()->routeIs($section['route'] . '*');
                        @endphp
                        @if($routeExists)
                            <a href="{{ isset($section['params']) ? route($section['route'], $section['params']) : route($section['route']) }}"
                               class="flex items-center pl-14 pr-4 py-2 text-sm {{ $isActive ? 'bg-slate-700 text-white' : 'text-slate-400 hover:bg-slate-700 hover:text-white' }}">
                                {{ $section['name'] }}
                            </a>
                        @else
                            <span class="flex items-center pl-14 pr-4 py-2 text-sm text-slate-500 cursor-not-allowed">
                                {{ $section['name'] }}
                                <span class="ml-2 text-xs bg-slate-600 px-1.5 py-0.5 rounded">{{ __('carbex.common.coming_soon') }}</span>
                            </span>
                        @endif
                    @endforeach
                </div>
            </div>
        @endforeach
    </nav>

    <!-- Plan Badge (Footer) -->
    <x-sidebar-plan-badge />
</aside>
