{{-- 5 Pillars Navigation Sidebar --}}
<nav class="flex-1 px-3 py-4 space-y-2">
    {{-- 1. MEASURE (Mesurer) --}}
    <div class="nav-group">
        <div class="flex items-center px-3 py-2 text-xs font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
            {{ __('linscarbon.pillars.measure') }}
        </div>
        <div class="ml-6 space-y-1">
            <a href="{{ route('dashboard') }}" class="@if(request()->routeIs('dashboard')) bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300 @else text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700 @endif flex items-center px-3 py-2 text-sm rounded-lg">
                {{ __('linscarbon.nav.dashboard') }}
            </a>
            <a href="{{ route('emissions') }}" class="@if(request()->routeIs('emissions*')) bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300 @else text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700 @endif flex items-center px-3 py-2 text-sm rounded-lg">
                {{ __('linscarbon.nav.scopes') }}
            </a>
            <a href="{{ route('banking') }}" class="@if(request()->routeIs('banking*')) bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300 @else text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700 @endif flex items-center px-3 py-2 text-sm rounded-lg">
                {{ __('linscarbon.nav.banking') }}
            </a>
            <a href="{{ route('sites.comparison') }}" class="@if(request()->routeIs('sites.*')) bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300 @else text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700 @endif flex items-center px-3 py-2 text-sm rounded-lg">
                {{ __('linscarbon.pillars.sites') }}
            </a>
        </div>
    </div>

    {{-- 2. PLAN (Planifier) --}}
    <div class="nav-group mt-4">
        <div class="flex items-center px-3 py-2 text-xs font-semibold text-blue-600 dark:text-blue-400 uppercase tracking-wider">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
            </svg>
            {{ __('linscarbon.pillars.plan') }}
        </div>
        <div class="ml-6 space-y-1">
            <a href="{{ route('transition-plan') }}" class="@if(request()->routeIs('transition-plan*')) bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300 @else text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700 @endif flex items-center px-3 py-2 text-sm rounded-lg">
                {{ __('linscarbon.pillars.transition') }}
            </a>
            <a href="{{ route('trajectory') }}" class="@if(request()->routeIs('trajectory')) bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300 @else text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700 @endif flex items-center px-3 py-2 text-sm rounded-lg">
                {{ __('linscarbon.pillars.trajectory') }}
            </a>
            <a href="{{ route('assessments') }}" class="@if(request()->routeIs('assessments*')) bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300 @else text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700 @endif flex items-center px-3 py-2 text-sm rounded-lg">
                {{ __('linscarbon.pillars.assessments') }}
            </a>
        </div>
    </div>

    {{-- 3. ENGAGE (Engager) --}}
    <div class="nav-group mt-4">
        <div class="flex items-center px-3 py-2 text-xs font-semibold text-amber-600 dark:text-amber-400 uppercase tracking-wider">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            {{ __('linscarbon.pillars.engage') }}
        </div>
        <div class="ml-6 space-y-1">
            <a href="{{ route('suppliers') }}" class="@if(request()->routeIs('suppliers*')) bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300 @else text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700 @endif flex items-center px-3 py-2 text-sm rounded-lg">
                {{ __('linscarbon.pillars.suppliers') }}
            </a>
            <a href="{{ route('engage.employees') }}" class="@if(request()->routeIs('engage.*')) bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300 @else text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700 @endif flex items-center px-3 py-2 text-sm rounded-lg">
                {{ __('linscarbon.pillars.employees') }}
            </a>
        </div>
    </div>

    {{-- 4. REPORT (Rapporter) --}}
    <div class="nav-group mt-4">
        <div class="flex items-center px-3 py-2 text-xs font-semibold text-purple-600 dark:text-purple-400 uppercase tracking-wider">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            {{ __('linscarbon.pillars.report') }}
        </div>
        <div class="ml-6 space-y-1">
            <a href="{{ route('reports') }}" class="@if(request()->routeIs('reports*')) bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300 @else text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700 @endif flex items-center px-3 py-2 text-sm rounded-lg">
                {{ __('linscarbon.pillars.reports') }}
            </a>
            <a href="{{ route('compliance') }}" class="@if(request()->routeIs('compliance*')) bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300 @else text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700 @endif flex items-center px-3 py-2 text-sm rounded-lg">
                {{ __('linscarbon.pillars.compliance') }}
            </a>
        </div>
    </div>

    {{-- 5. PROMOTE (Promouvoir) --}}
    <div class="nav-group mt-4">
        <div class="flex items-center px-3 py-2 text-xs font-semibold text-pink-600 dark:text-pink-400 uppercase tracking-wider">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
            </svg>
            {{ __('linscarbon.pillars.promote') }}
        </div>
        <div class="ml-6 space-y-1">
            <a href="{{ route('gamification') }}" class="@if(request()->routeIs('gamification')) bg-pink-100 text-pink-800 dark:bg-pink-900/30 dark:text-pink-300 @else text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700 @endif flex items-center px-3 py-2 text-sm rounded-lg">
                {{ __('linscarbon.pillars.badges') }}
            </a>
            <a href="{{ route('promote.badges') }}" class="@if(request()->routeIs('promote.*')) bg-pink-100 text-pink-800 dark:bg-pink-900/30 dark:text-pink-300 @else text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700 @endif flex items-center px-3 py-2 text-sm rounded-lg">
                {{ __('linscarbon.pillars.showcase') }}
            </a>
        </div>
    </div>
</nav>
