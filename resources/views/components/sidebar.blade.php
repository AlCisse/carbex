@props(['currentScope' => null, 'currentCategory' => null])

@php
    $scopes = [
        1 => [
            'name' => 'Scope 1 - Émissions directes',
            'icon' => 'fire',
            'categories' => [
                ['code' => '1.1', 'name' => 'Sources fixes de combustion'],
                ['code' => '1.2', 'name' => 'Sources mobiles de combustion'],
                ['code' => '1.4', 'name' => 'Émissions fugitives'],
                ['code' => '1.5', 'name' => 'Biomasse (sols et forêts)'],
            ],
        ],
        2 => [
            'name' => 'Scope 2 - Émissions indirectes liées à l\'énergie',
            'icon' => 'bolt',
            'categories' => [
                ['code' => '2.1', 'name' => 'Consommation d\'électricité'],
            ],
        ],
        3 => [
            'name' => 'Scope 3 - Autres émissions indirectes',
            'icon' => 'globe',
            'categories' => [
                ['code' => '3.1', 'name' => 'Transport de marchandise amont'],
                ['code' => '3.2', 'name' => 'Transport de marchandise aval'],
                ['code' => '3.3', 'name' => 'Déplacements domicile-travail'],
                ['code' => '3.5', 'name' => 'Déplacements professionnels'],
                ['code' => '4.1', 'name' => 'Achats de biens'],
                ['code' => '4.2', 'name' => 'Immobilisations de biens'],
                ['code' => '4.3', 'name' => 'Gestion des déchets'],
                ['code' => '4.4', 'name' => 'Actifs en leasing amont'],
                ['code' => '4.5', 'name' => 'Achats de services'],
            ],
        ],
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
            Tableau de bord
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
            Analyse
        </a>

        <!-- Documents IA -->
        <a href="{{ route('documents') }}"
           class="flex items-center px-4 py-2.5 text-sm font-medium {{ request()->routeIs('documents*') ? 'bg-slate-700 text-white' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">
            <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <span class="flex items-center">
                Documents
                <span class="ml-2 inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-green-600 text-white">IA</span>
            </span>
        </a>

        <!-- Plan de transition -->
        <a href="{{ route('transition-plan') }}"
           class="flex items-center px-4 py-2.5 text-sm font-medium {{ request()->routeIs('transition-plan*') ? 'bg-slate-700 text-white' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">
            <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
            </svg>
            Plan de transition
        </a>

        <!-- Rapports & exports -->
        <a href="{{ route('reports') }}"
           class="flex items-center px-4 py-2.5 text-sm font-medium {{ request()->routeIs('reports*') ? 'bg-slate-700 text-white' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">
            <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            Rapports & exports
        </a>
    </nav>

    <!-- Plan Badge (Footer) -->
    <x-sidebar-plan-badge />
</aside>
