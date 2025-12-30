<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Carbex - Plateforme de Bilan Carbone pour PME')</title>
    <meta name="description" content="@yield('description', 'Mesurez, comprenez et reduisez l\'empreinte carbone de votre entreprise avec Carbex. Outil SaaS conforme GHG Protocol, ISO 14064 et ADEME.')">
    <meta name="keywords" content="bilan carbone, empreinte carbone, PME, GHG Protocol, ADEME, CSRD, emissions CO2, RSE, decarbonation">
    <meta name="author" content="Carbex SAS">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ url()->current() }}">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="@yield('og_title', '@yield('title', 'Carbex - Bilan Carbone PME')')">
    <meta property="og:description" content="@yield('og_description', '@yield('description', 'Plateforme de bilan carbone pour PME francaises, augmentee par l\'IA.')')">
    <meta property="og:image" content="@yield('og_image', asset('images/og-default.png'))">
    <meta property="og:locale" content="fr_FR">
    <meta property="og:site_name" content="Carbex">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:site" content="@carbex_fr">
    <meta name="twitter:title" content="@yield('title', 'Carbex - Bilan Carbone PME')">
    <meta name="twitter:description" content="@yield('description', 'Plateforme de bilan carbone pour PME')">
    <meta name="twitter:image" content="@yield('og_image', asset('images/og-default.png'))">

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">

    <!-- JSON-LD Structured Data -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "SoftwareApplication",
        "name": "Carbex",
        "applicationCategory": "BusinessApplication",
        "operatingSystem": "Web",
        "description": "Plateforme SaaS de bilan carbone pour PME, conforme GHG Protocol et ADEME",
        "url": "{{ config('app.url') }}",
        "author": {
            "@type": "Organization",
            "name": "Carbex SAS",
            "url": "{{ config('app.url') }}"
        },
        "offers": {
            "@type": "Offer",
            "price": "0",
            "priceCurrency": "EUR",
            "description": "Essai gratuit 15 jours"
        }
    }
    </script>
    @yield('structured_data')

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --bg-primary: #fafafa;
            --bg-card: #ffffff;
            --text-primary: #0f172a;
            --text-secondary: #64748b;
            --text-muted: #94a3b8;
            --accent: #0d9488;
            --accent-light: #ccfbf1;
            --border: #e2e8f0;
        }
        * { font-family: 'Inter', -apple-system, BlinkMacSystemFont, system-ui, sans-serif; }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in-up { animation: fadeInUp 0.6s ease-out forwards; }
        .delay-100 { animation-delay: 100ms; }
        .delay-200 { animation-delay: 200ms; }
        .delay-300 { animation-delay: 300ms; }

        .hover-lift { transition: transform 0.2s ease, box-shadow 0.2s ease; }
        .hover-lift:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px -4px rgb(0 0 0 / 0.08);
        }

        .btn-primary { transition: all 0.2s ease; }
        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px -2px rgb(13 148 136 / 0.25);
        }

    </style>
    @stack('styles')
</head>
<body class="antialiased" style="background-color: var(--bg-primary);">

    <!-- Header -->
    <header class="fixed top-0 left-0 right-0 z-50 bg-white/90 backdrop-blur-md border-b" style="border-color: var(--border);">
        <nav class="max-w-6xl mx-auto px-6">
            <div class="flex items-center justify-between h-16">
                <a href="/" class="flex items-center space-x-2">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background-color: var(--accent);">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <span class="text-lg font-semibold" style="color: var(--text-primary);">Carbex</span>
                </a>

                <div class="hidden md:flex items-center space-x-8">
                    <a href="/#features" class="text-sm font-medium hover:opacity-70 transition-opacity" style="color: var(--text-secondary);">Fonctionnalites</a>
                    <a href="{{ route('pricing') }}" class="text-sm font-medium hover:opacity-70 transition-opacity {{ request()->routeIs('pricing') ? 'opacity-100' : '' }}" style="color: {{ request()->routeIs('pricing') ? 'var(--accent)' : 'var(--text-secondary)' }};">Tarifs</a>
                    <a href="/pour-qui" class="text-sm font-medium hover:opacity-70 transition-opacity" style="color: var(--text-secondary);">Pour qui ?</a>
                    <a href="/blog" class="text-sm font-medium hover:opacity-70 transition-opacity" style="color: var(--text-secondary);">Blog</a>
                    <a href="/contact" class="text-sm font-medium hover:opacity-70 transition-opacity" style="color: var(--text-secondary);">Contact</a>
                </div>

                <div class="flex items-center space-x-4">
                    @auth
                        <a href="{{ route('dashboard') }}" class="btn-primary px-4 py-2 text-sm font-medium text-white rounded-lg" style="background-color: var(--accent);">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-medium hover:opacity-70 transition-opacity" style="color: var(--text-secondary);">Connexion</a>
                        <a href="{{ route('register') }}" class="btn-primary px-4 py-2 text-sm font-medium text-white rounded-lg" style="background-color: var(--accent);">Essai gratuit</a>
                    @endauth
                </div>

                <!-- Mobile Menu Button -->
                <button type="button" class="md:hidden p-2" x-data x-on:click="$dispatch('toggle-mobile-menu')">
                    <svg class="w-6 h-6" style="color: var(--text-secondary);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </nav>
    </header>

    <main>
        @yield('content')
    </main>

    {{-- Footer Component --}}
    <x-marketing-footer />

    @stack('scripts')
</body>
</html>
