<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $badgeName }} - {{ $organization->name }} | LinsCarbon</title>

    <!-- SEO Meta -->
    <meta name="description" content="{{ __('linscarbon.promote.seo_description', ['organization' => $organization->name, 'badge' => $badgeName]) }}">
    <meta property="og:title" content="{{ $badgeName }} - {{ $organization->name }}">
    <meta property="og:description" content="{{ $badgeDescription }}">
    <meta property="og:type" content="website">
    <meta property="og:image" content="{{ asset('images/badge-og-' . $badge->code . '.png') }}">
    <meta name="twitter:card" content="summary_large_image">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gradient-to-br from-emerald-50 to-teal-100 dark:from-gray-900 dark:to-gray-800">
    <div class="min-h-screen flex flex-col">
        <!-- Header -->
        <header class="py-4 px-6">
            <div class="max-w-4xl mx-auto flex items-center justify-between">
                <a href="{{ url('/') }}" class="flex items-center space-x-2">
                    <div class="w-8 h-8 bg-emerald-500 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <span class="text-xl font-bold text-gray-900 dark:text-white">LinsCarbon</span>
                </a>
                <a href="{{ url('/') }}" class="text-sm text-emerald-600 hover:text-emerald-700 font-medium">
                    {{ __('linscarbon.promote.learn_more') }}
                </a>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1 flex items-center justify-center px-6 py-12">
            <div class="max-w-lg w-full">
                <!-- Badge Card -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden">
                    <!-- Badge Header -->
                    <div class="bg-gradient-to-r from-emerald-500 to-teal-500 px-6 py-8 text-center">
                        <div class="inline-flex items-center justify-center w-24 h-24 bg-white/90 rounded-full mb-4 shadow-lg">
                            @if($badge->icon)
                                <span class="text-5xl">{{ $badge->icon }}</span>
                            @else
                                <svg class="w-14 h-14 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            @endif
                        </div>
                        <h1 class="text-2xl font-bold text-white">{{ $badgeName }}</h1>
                    </div>

                    <!-- Badge Body -->
                    <div class="px-6 py-8">
                        <!-- Organization -->
                        <div class="text-center mb-6">
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">
                                {{ __('linscarbon.promote.awarded_to') }}
                            </p>
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
                                {{ $organization->name }}
                            </h2>
                            @if($organization->country)
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                    {{ $organization->country }}
                                </p>
                            @endif
                        </div>

                        <!-- Description -->
                        <p class="text-gray-600 dark:text-gray-400 text-center mb-6">
                            {{ $badgeDescription }}
                        </p>

                        <!-- Date & Verification -->
                        <div class="flex items-center justify-center space-x-4 text-sm">
                            <span class="text-gray-500 dark:text-gray-400">
                                {{ __('linscarbon.promote.earned_on') }} {{ $earned_at->format('d/m/Y') }}
                            </span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full bg-emerald-100 dark:bg-emerald-900/30 text-emerald-800 dark:text-emerald-300 text-xs font-medium">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                {{ __('linscarbon.promote.verified') }}
                            </span>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/50 border-t border-gray-200 dark:border-gray-600">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-2 text-sm text-gray-500 dark:text-gray-400">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span>{{ __('linscarbon.promote.verified_by_linscarbon') }}</span>
                            </div>
                            <a href="{{ url('/') }}" class="text-emerald-600 hover:text-emerald-700 text-sm font-medium">
                                linscarbon.io
                            </a>
                        </div>
                    </div>
                </div>

                <!-- CTA -->
                <div class="mt-8 text-center">
                    <p class="text-gray-600 dark:text-gray-400 mb-4">
                        {{ __('linscarbon.promote.cta_text') }}
                    </p>
                    <a href="{{ url('/') }}" class="inline-flex items-center px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-medium shadow-lg hover:shadow-xl transition-all">
                        {{ __('linscarbon.promote.start_free') }}
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                        </svg>
                    </a>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="py-4 px-6 text-center text-sm text-gray-500 dark:text-gray-400">
            &copy; {{ date('Y') }} LinsCarbon. {{ __('linscarbon.promote.all_rights_reserved') }}
        </footer>
    </div>
</body>
</html>
