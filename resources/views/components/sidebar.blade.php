@props(['currentScope' => null, 'currentCategory' => null])

{{-- Mobile menu button (visible on small screens) --}}
<div class="lg:hidden fixed top-4 left-4 z-50">
    <button
        x-data
        @click="$dispatch('toggle-mobile-menu')"
        class="p-2 rounded-md bg-slate-800 text-white shadow-lg"
        dusk="mobile-menu-button"
    >
        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
    </button>
</div>

@php
    $scopes = [
        1 => [
            'name' => __('carbex.sidebar.scope1_name'),
            'icon' => 'fire',
            'categories' => [
                ['code' => '1.1', 'name' => __('carbex.sidebar.cat_1_1')],
                ['code' => '1.2', 'name' => __('carbex.sidebar.cat_1_2')],
                ['code' => '1.4', 'name' => __('carbex.sidebar.cat_1_4')],
                ['code' => '1.5', 'name' => __('carbex.sidebar.cat_1_5')],
            ],
        ],
        2 => [
            'name' => __('carbex.sidebar.scope2_name'),
            'icon' => 'bolt',
            'categories' => [
                ['code' => '2.1', 'name' => __('carbex.sidebar.cat_2_1')],
            ],
        ],
        3 => [
            'name' => __('carbex.sidebar.scope3_name'),
            'icon' => 'globe',
            'categories' => [
                ['code' => '3.1', 'name' => __('carbex.sidebar.cat_3_1')],
                ['code' => '3.2', 'name' => __('carbex.sidebar.cat_3_2')],
                ['code' => '3.3', 'name' => __('carbex.sidebar.cat_3_3')],
                ['code' => '3.5', 'name' => __('carbex.sidebar.cat_3_5')],
                ['code' => '4.1', 'name' => __('carbex.sidebar.cat_4_1')],
                ['code' => '4.2', 'name' => __('carbex.sidebar.cat_4_2')],
                ['code' => '4.3', 'name' => __('carbex.sidebar.cat_4_3')],
                ['code' => '4.4', 'name' => __('carbex.sidebar.cat_4_4')],
                ['code' => '4.5', 'name' => __('carbex.sidebar.cat_4_5')],
            ],
        ],
    ];
@endphp

<aside
    x-data="{ mobileOpen: false }"
    @toggle-mobile-menu.window="mobileOpen = !mobileOpen"
    :class="{ '-translate-x-full lg:translate-x-0': !mobileOpen, 'translate-x-0': mobileOpen }"
    class="fixed inset-y-0 left-0 z-50 w-64 bg-slate-800 text-white flex flex-col transition-transform duration-300"
    dusk="mobile-menu"
>
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
            {{ __('carbex.sidebar.dashboard') }}
        </a>

        <!-- Scopes -->
        @foreach($scopes as $scopeNumber => $scope)
            <div x-data="{ open: {{ $currentScope == $scopeNumber ? 'true' : 'false' }} }" class="mt-1">
                <button @click="open = !open"
                        class="w-full flex items-center justify-between px-4 py-2.5 text-sm font-medium text-slate-300 hover:bg-slate-700 hover:text-white transition-colors">
                    <div class="flex items-center">
                        @if($scopeNumber == 1)
                            <svg class="mr-3 h-5 w-5 text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z" />
                            </svg>
                        @elseif($scopeNumber == 2)
                            <svg class="mr-3 h-5 w-5 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        @else
                            <svg class="mr-3 h-5 w-5 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        @endif
                        <span class="truncate">{{ $scope['name'] }}</span>
                    </div>
                    <div class="flex items-center">
                        <span class="mr-2 text-xs bg-slate-600 px-1.5 py-0.5 rounded">0%</span>
                        <svg :class="{ 'rotate-90': open }" class="h-4 w-4 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </div>
                </button>

                <div x-show="open" x-collapse class="bg-slate-900/50">
                    @foreach($scope['categories'] as $category)
                        <a href="{{ route('emissions.category', ['scope' => $scopeNumber, 'category' => $category['code']]) }}"
                           class="flex items-center pl-12 pr-4 py-2 text-sm {{ $currentCategory == $category['code'] ? 'bg-slate-700 text-white' : 'text-slate-400 hover:bg-slate-700 hover:text-white' }}">
                            <span class="mr-2 text-xs text-slate-500">{{ $category['code'] }}</span>
                            {{ $category['name'] }}
                        </a>
                    @endforeach
                </div>
            </div>
        @endforeach

        <!-- Divider -->
        <div class="my-4 border-t border-slate-700"></div>

        <!-- Analyse -->
        <a href="{{ route('emissions') }}"
           class="flex items-center px-4 py-2.5 text-sm font-medium {{ request()->routeIs('emissions.index') ? 'bg-slate-700 text-white' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">
            <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
            {{ __('carbex.sidebar.analysis') }}
        </a>

        <!-- Documents IA -->
        <a href="{{ route('documents') }}"
           class="flex items-center px-4 py-2.5 text-sm font-medium {{ request()->routeIs('documents*') ? 'bg-slate-700 text-white' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">
            <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <span class="flex items-center">
                {{ __('carbex.sidebar.documents') }}
                <span class="ml-2 inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-green-600 text-white">{{ __('carbex.common.ai') }}</span>
            </span>
        </a>

        <!-- Analyse IA (Recommandations) -->
        <a href="{{ route('ai.analysis') }}"
           class="flex items-center px-4 py-2.5 text-sm font-medium {{ request()->routeIs('ai.analysis*') ? 'bg-slate-700 text-white' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">
            <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
            </svg>
            <span class="flex items-center">
                {{ __('carbex.sidebar.ai_analysis') }}
                <span class="ml-2 inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-emerald-600 text-white">{{ __('carbex.sidebar.new') }}</span>
            </span>
        </a>

        <!-- Fournisseurs (Scope 3) -->
        <a href="{{ route('suppliers') }}"
           class="flex items-center px-4 py-2.5 text-sm font-medium {{ request()->routeIs('suppliers*') ? 'bg-slate-700 text-white' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">
            <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
            {{ __('carbex.sidebar.suppliers') }}
        </a>

        <!-- Plan de transition -->
        <a href="{{ route('transition-plan') }}"
           class="flex items-center px-4 py-2.5 text-sm font-medium {{ request()->routeIs('transition-plan*') ? 'bg-slate-700 text-white' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">
            <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
            </svg>
            {{ __('carbex.sidebar.transition_plan') }}
        </a>

        <!-- CSRD Compliance -->
        <a href="{{ route('csrd.dashboard') }}"
           class="flex items-center px-4 py-2.5 text-sm font-medium {{ request()->routeIs('csrd.*') ? 'bg-slate-700 text-white' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">
            <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
            </svg>
            <span class="flex items-center">
                CSRD
                <span class="ml-2 inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-blue-600 text-white">EU</span>
            </span>
        </a>

        <!-- Rapports & exports -->
        <a href="{{ route('reports') }}"
           class="flex items-center px-4 py-2.5 text-sm font-medium {{ request()->routeIs('reports*') ? 'bg-slate-700 text-white' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">
            <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            {{ __('carbex.sidebar.reports') }}
        </a>

        <!-- Badges & Engagement -->
        <a href="{{ route('gamification') }}"
           class="flex items-center px-4 py-2.5 text-sm font-medium {{ request()->routeIs('gamification*') ? 'bg-slate-700 text-white' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">
            <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
            </svg>
            <span class="flex items-center">
                {{ __('carbex.sidebar.badges') }}
                <span class="ml-2 inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-purple-600 text-white">{{ __('carbex.sidebar.new') }}</span>
            </span>
        </a>
    </nav>

    <!-- Plan Badge (Footer) -->
    <x-sidebar-plan-badge />
</aside>
