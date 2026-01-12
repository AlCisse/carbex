<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $badge->name }} - {{ $organization->name }} | Carbex</title>

    {{-- Open Graph --}}
    <meta property="og:title" content="{{ __('carbex.gamification.badge_og_title', ['organization' => $organization->name, 'badge' => $badge->name]) }}">
    <meta property="og:description" content="{{ $badge->description }}">
    <meta property="og:type" content="website">
    <meta property="og:image" content="{{ asset('images/badges/' . $badge->code . '.png') }}">

    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ __('carbex.gamification.badge_og_title', ['organization' => $organization->name, 'badge' => $badge->name]) }}">
    <meta name="twitter:description" content="{{ $badge->description }}">

    @vite(['resources/css/app.css'])
</head>
<body class="bg-gradient-to-br from-slate-50 to-slate-100 dark:from-slate-900 dark:to-slate-800 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full">
        {{-- Card --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl overflow-hidden">
            {{-- Header avec gradient --}}
            <div class="bg-gradient-to-r from-emerald-500 to-teal-600 p-8 text-center">
                <div class="w-24 h-24 mx-auto mb-4 bg-white/20 rounded-full flex items-center justify-center">
                    @switch($badge->icon)
                        @case('trophy')
                            <svg class="w-12 h-12 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                            </svg>
                            @break
                        @case('academic-cap')
                            <svg class="w-12 h-12 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0v6m0 0l-3-1.5m3 1.5l3-1.5" />
                            </svg>
                            @break
                        @default
                            <svg class="w-12 h-12 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                            </svg>
                    @endswitch
                </div>
                <h1 class="text-2xl font-bold text-white">{{ $badge->name }}</h1>
                <p class="text-emerald-100 mt-2">{{ $badge->description }}</p>
            </div>

            {{-- Content --}}
            <div class="p-8 text-center">
                <p class="text-slate-500 dark:text-slate-400 text-sm mb-2">{{ __('carbex.gamification.badge_earned_by') }}</p>
                <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-4">{{ $organization->name }}</h2>

                <div class="flex items-center justify-center gap-2 text-sm text-slate-500 dark:text-slate-400">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span>{{ __('carbex.gamification.earned_on') }} {{ \Carbon\Carbon::parse($earned_at)->format('d/m/Y') }}</span>
                </div>

                <div class="mt-6 pt-6 border-t border-slate-200 dark:border-slate-700">
                    <div class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-100 dark:bg-emerald-900/30 rounded-full">
                        <span class="text-emerald-800 dark:text-emerald-300 font-medium">+{{ $badge->points }}</span>
                        <span class="text-emerald-600 dark:text-emerald-400 text-sm">{{ __('carbex.gamification.points') }}</span>
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <div class="px-8 pb-8">
                <a href="{{ route('home') }}"
                   class="block w-full py-3 px-4 bg-emerald-600 hover:bg-emerald-700 text-white text-center rounded-lg font-medium transition-colors">
                    {{ __('carbex.gamification.discover_carbex') }}
                </a>
            </div>
        </div>

        {{-- Logo Carbex --}}
        <div class="mt-6 text-center">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-slate-400 hover:text-emerald-600 transition-colors">
                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/>
                </svg>
                <span class="font-semibold">Carbex</span>
            </a>
        </div>
    </div>
</body>
</html>
