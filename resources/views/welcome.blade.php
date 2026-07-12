<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('linscarbon.home.title') }}</title>
    <meta name="description" content="{{ __('linscarbon.home.meta_description') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --bg-primary: #fafafa;
            --bg-card: #ffffff;
            --bg-dashboard: #f8fafc;
            --text-primary: #0f172a;
            --text-secondary: #64748b;
            --text-muted: #94a3b8;
            --accent: #0d9488;
            --accent-hover: #0f766e;
            --accent-light: #ccfbf1;
            --accent-glow: rgba(13, 148, 136, 0.15);
            --border: #e2e8f0;
            --border-subtle: rgba(255, 255, 255, 0.1);
        }
        * { font-family: 'Inter', -apple-system, BlinkMacSystemFont, system-ui, sans-serif; }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes scaleIn {
            from { opacity: 0; transform: scale(0.92); }
            to { opacity: 1; transform: scale(1); }
        }
        @keyframes growBar {
            from { transform: scaleY(0); }
            to { transform: scaleY(1); }
        }
        @keyframes countUp {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes pulse-subtle {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-8px); }
        }

        .animate-fade-in-up { animation: fadeInUp 0.7s ease-out forwards; }
        .animate-scale-in { animation: scaleIn 0.6s ease-out forwards; }
        .animate-count { animation: countUp 0.5s ease-out forwards; }
        .animate-float { animation: float 6s ease-in-out infinite; }
        .delay-100 { animation-delay: 100ms; }
        .delay-200 { animation-delay: 200ms; }
        .delay-300 { animation-delay: 300ms; }
        .delay-400 { animation-delay: 400ms; }
        .delay-500 { animation-delay: 500ms; }

        .bar-animate {
            transform-origin: bottom;
            animation: growBar 0.8s ease-out forwards;
        }

        .hover-lift {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .hover-lift:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 40px -8px rgb(0 0 0 / 0.12);
        }

        .btn-primary {
            transition: all 0.25s ease;
            position: relative;
            overflow: hidden;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px -4px rgb(13 148 136 / 0.4);
            background-color: var(--accent-hover) !important;
        }
        .btn-primary::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0.2) 0%, transparent 50%);
            opacity: 0;
            transition: opacity 0.25s ease;
        }
        .btn-primary:hover::after {
            opacity: 1;
        }

        .btn-secondary {
            transition: all 0.25s ease;
        }
        .btn-secondary:hover {
            transform: translateY(-1px);
            border-color: var(--accent) !important;
            color: var(--accent) !important;
        }

        /* Premium gradient highlight */
        .text-gradient {
            background: linear-gradient(135deg, var(--accent) 0%, #14b8a6 50%, #2dd4bf 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Dashboard card premium styling */
        .dashboard-card {
            background: linear-gradient(145deg, #ffffff 0%, #f8fafc 100%);
            box-shadow:
                0 4px 6px -1px rgba(0, 0, 0, 0.05),
                0 20px 50px -12px rgba(0, 0, 0, 0.15),
                0 0 0 1px rgba(255, 255, 255, 0.8) inset;
            border: 1px solid rgba(226, 232, 240, 0.5);
        }

        /* Counter animation for stats */
        .stat-number {
            display: inline-block;
            animation: countUp 0.8s ease-out forwards;
        }

        /* Feature step number */
        .step-number {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 14px;
            background: linear-gradient(135deg, var(--accent) 0%, #14b8a6 100%);
            color: white;
            box-shadow: 0 4px 12px -2px var(--accent-glow);
        }

        /* Section spacing */
        main > section { margin-top: 0 !important; }

        /* Premium card glow on hover */
        .card-glow {
            position: relative;
        }
        .card-glow::before {
            content: '';
            position: absolute;
            inset: -1px;
            background: linear-gradient(135deg, var(--accent-glow), transparent 50%);
            border-radius: inherit;
            opacity: 0;
            transition: opacity 0.3s ease;
            z-index: -1;
        }
        .card-glow:hover::before {
            opacity: 1;
        }

        /* Indicator arrow animation */
        .indicator-arrow {
            transition: transform 0.2s ease;
        }
        .indicator-arrow:hover {
            transform: translateY(-2px);
        }
    </style>
</head>
<body class="antialiased" style="background-color: var(--bg-primary);">

    <!-- Header -->
    <header class="fixed top-0 left-0 right-0 z-50 bg-white/95 backdrop-blur-lg border-b" style="border-color: var(--border);">
        <nav class="max-w-6xl mx-auto px-6">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center gap-6">
                    <a href="/">
                        <x-logo variant="premium" />
                    </a>
                    <span class="hidden lg:block text-xs font-medium px-2 py-1 rounded-full" style="background-color: var(--accent-light); color: var(--accent);">
                        {{ __('linscarbon.home.badge') }}
                    </span>
                </div>

                <div class="hidden md:flex items-center space-x-8">
                    <a href="#features" class="text-sm font-medium hover:text-teal-600 transition-colors" style="color: var(--text-secondary);">{{ __('linscarbon.home.nav.features') }}</a>
                    <a href="#pricing" class="text-sm font-medium hover:text-teal-600 transition-colors" style="color: var(--text-secondary);">{{ __('linscarbon.home.nav.pricing') }}</a>
                    <a href="#" class="text-sm font-medium hover:text-teal-600 transition-colors" style="color: var(--text-secondary);">{{ __('linscarbon.home.nav.resources') }}</a>
                </div>

                <div class="flex items-center space-x-4">
                    <x-language-selector />
                    @auth
                        <a href="{{ route('dashboard') }}" class="btn-primary px-4 py-2 text-sm font-medium text-white rounded-lg" style="background-color: var(--accent);">{{ __('linscarbon.nav.dashboard') }}</a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-medium hover:text-teal-600 transition-colors" style="color: var(--text-secondary);">{{ __('linscarbon.home.nav.login') }}</a>
                        <a href="{{ route('register') }}" class="btn-primary px-4 py-2 text-sm font-semibold text-white rounded-lg" style="background-color: var(--accent);">{{ __('linscarbon.home.nav.start') }}</a>
                    @endauth
                </div>
            </div>
        </nav>
    </header>

    <main>
        <!-- Hero Section -->
        <section style="background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%); padding: 120px 0 80px;">
            <div class="max-w-6xl mx-auto px-6">
                <div class="grid lg:grid-cols-2 items-center" style="gap: 60px;">
                    <!-- Left Content -->
                    <div>
                        <div class="inline-flex items-center rounded-full text-xs font-semibold" style="background-color: var(--accent-light); color: var(--accent); padding: 6px 12px; margin-bottom: 24px;">
                            <span class="w-2 h-2 rounded-full" style="background-color: var(--accent); margin-right: 8px;"></span>
                            {{ __('linscarbon.home.csrd_badge') }}
                        </div>

                        <h1 class="text-4xl sm:text-5xl lg:text-[3.25rem] font-bold leading-[1.1]" style="color: var(--text-primary); letter-spacing: -0.02em; margin-bottom: 24px;">
                            {{ __('linscarbon.home.hero.title_line1') }}<br>{{ __('linscarbon.home.hero.title_line2') }}
                        </h1>

                        <p class="text-lg leading-relaxed" style="color: var(--text-secondary); margin-bottom: 32px; max-width: 420px;">
                            {{ __('linscarbon.home.hero.subtitle') }}
                        </p>

                        <div class="flex flex-wrap items-center" style="gap: 16px; margin-bottom: 16px;">
                            <a href="{{ route('register') }}" class="inline-flex items-center text-sm font-semibold text-white rounded-xl" style="background-color: var(--accent); padding: 14px 24px;">
                                {{ __('linscarbon.home.hero.cta_primary') }}
                                <svg class="w-4 h-4" style="margin-left: 8px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                </svg>
                            </a>
                            <a href="#features" class="inline-flex items-center text-sm font-semibold rounded-xl border-2" style="color: var(--text-secondary); border-color: var(--border); padding: 12px 20px;">
                                {{ __('linscarbon.home.hero.cta_secondary') }}
                            </a>
                        </div>

                        <p class="text-xs" style="color: var(--text-muted); margin-bottom: 32px;">
                            {{ __('linscarbon.home.hero.no_commitment') }}
                        </p>

                        <div class="flex flex-wrap items-center text-xs" style="color: var(--text-muted); gap: 20px;">
                            {{ __('linscarbon.home.hero.badges') }}
                        </div>
                    </div>

                    <!-- Right: Dashboard Preview -->
                    <div class="animate-scale-in delay-200 animate-float">
                        <div class="rounded-2xl p-1.5" style="background: linear-gradient(135deg, rgba(13, 148, 136, 0.2) 0%, rgba(226, 232, 240, 0.8) 50%, rgba(13, 148, 136, 0.1) 100%);">
                            <div class="dashboard-card rounded-xl p-8">
                                <!-- Header -->
                                <div class="flex items-center justify-between mb-10">
                                    <div>
                                        <p class="text-xs font-medium uppercase tracking-wider mb-1" style="color: var(--text-muted);">{{ __('linscarbon.home.preview.total_footprint') }}</p>
                                        <p class="text-4xl font-bold animate-count" style="color: var(--text-primary);">2,847 <span class="text-base font-medium" style="color: var(--text-muted);">tCO₂e</span></p>
                                    </div>
                                    <div class="indicator-arrow flex flex-col items-end gap-1 px-3 py-2 rounded-lg cursor-default" style="background-color: #dcfce7;">
                                        <div class="flex items-center gap-1.5 text-sm font-bold" style="color: #16a34a;">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6" />
                                            </svg>
                                            -12%
                                        </div>
                                        <span class="text-[10px] font-medium" style="color: #15803d;">{{ __('linscarbon.home.preview.vs_previous_year') }}</span>
                                    </div>
                                </div>

                                <!-- Scopes -->
                                <div class="grid grid-cols-3 gap-4 mb-10">
                                    <div class="p-4 rounded-xl transition-all hover:shadow-md" style="background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);">
                                        <div class="flex items-center gap-2 mb-2">
                                            <span class="w-6 h-6 rounded-md flex items-center justify-center text-xs font-bold" style="background-color: #f59e0b; color: white;">S1</span>
                                            <p class="text-[10px] font-medium uppercase tracking-wide" style="color: var(--text-muted);">Scope 1</p>
                                        </div>
                                        <p class="text-2xl font-bold mb-2" style="color: var(--text-primary);">342</p>
                                        <div class="w-full h-1.5 rounded-full" style="background-color: rgba(245, 158, 11, 0.2);">
                                            <div class="h-1.5 rounded-full transition-all" style="width: 12%; background: linear-gradient(90deg, #f59e0b, #fbbf24);"></div>
                                        </div>
                                    </div>
                                    <div class="p-4 rounded-xl transition-all hover:shadow-md" style="background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);">
                                        <div class="flex items-center gap-2 mb-2">
                                            <span class="w-6 h-6 rounded-md flex items-center justify-center text-xs font-bold" style="background-color: #3b82f6; color: white;">S2</span>
                                            <p class="text-[10px] font-medium uppercase tracking-wide" style="color: var(--text-muted);">Scope 2</p>
                                        </div>
                                        <p class="text-2xl font-bold mb-2" style="color: var(--text-primary);">584</p>
                                        <div class="w-full h-1.5 rounded-full" style="background-color: rgba(59, 130, 246, 0.2);">
                                            <div class="h-1.5 rounded-full transition-all" style="width: 20%; background: linear-gradient(90deg, #3b82f6, #60a5fa);"></div>
                                        </div>
                                    </div>
                                    <div class="p-4 rounded-xl transition-all hover:shadow-md" style="background: linear-gradient(135deg, #f0fdfa 0%, #ccfbf1 100%);">
                                        <div class="flex items-center gap-2 mb-2">
                                            <span class="w-6 h-6 rounded-md flex items-center justify-center text-xs font-bold" style="background-color: var(--accent); color: white;">S3</span>
                                            <p class="text-[10px] font-medium uppercase tracking-wide" style="color: var(--text-muted);">Scope 3</p>
                                        </div>
                                        <p class="text-2xl font-bold mb-2" style="color: var(--text-primary);">1,921</p>
                                        <div class="w-full h-1.5 rounded-full" style="background-color: rgba(13, 148, 136, 0.2);">
                                            <div class="h-1.5 rounded-full transition-all" style="width: 68%; background: linear-gradient(90deg, var(--accent), #14b8a6);"></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Chart -->
                                <div class="p-5 rounded-xl" style="background-color: #f8fafc;">
                                    <div class="flex items-center justify-between mb-4">
                                        <p class="text-xs font-semibold" style="color: var(--text-secondary);">{{ __('linscarbon.home.preview.monthly_evolution') }}</p>
                                        <div class="flex items-center gap-4 text-xs" style="color: var(--text-muted);">
                                            <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full" style="background: linear-gradient(135deg, var(--accent), #14b8a6);"></span> 2024</span>
                                            <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full" style="background-color: #cbd5e1;"></span> 2023</span>
                                        </div>
                                    </div>
                                    <div class="h-24 flex items-end justify-between gap-3">
                                        <div class="flex-1 flex flex-col items-center gap-1.5">
                                            <div class="w-full flex gap-1 items-end justify-center h-16">
                                                <div class="w-4 rounded-t bar-animate" style="height: 80%; background-color: #cbd5e1;"></div>
                                                <div class="w-4 rounded-t bar-animate delay-100" style="height: 65%; background: linear-gradient(180deg, var(--accent), #14b8a6);"></div>
                                            </div>
                                            <span class="text-[10px] font-medium" style="color: var(--text-muted);">{{ __('linscarbon.home.preview.months.jan') }}</span>
                                        </div>
                                        <div class="flex-1 flex flex-col items-center gap-1.5">
                                            <div class="w-full flex gap-1 items-end justify-center h-16">
                                                <div class="w-4 rounded-t bar-animate delay-100" style="height: 85%; background-color: #cbd5e1;"></div>
                                                <div class="w-4 rounded-t bar-animate delay-200" style="height: 60%; background: linear-gradient(180deg, var(--accent), #14b8a6);"></div>
                                            </div>
                                            <span class="text-[10px] font-medium" style="color: var(--text-muted);">{{ __('linscarbon.home.preview.months.feb') }}</span>
                                        </div>
                                        <div class="flex-1 flex flex-col items-center gap-1.5">
                                            <div class="w-full flex gap-1 items-end justify-center h-16">
                                                <div class="w-4 rounded-t bar-animate delay-200" style="height: 75%; background-color: #cbd5e1;"></div>
                                                <div class="w-4 rounded-t bar-animate delay-300" style="height: 55%; background: linear-gradient(180deg, var(--accent), #14b8a6);"></div>
                                            </div>
                                            <span class="text-[10px] font-medium" style="color: var(--text-muted);">{{ __('linscarbon.home.preview.months.mar') }}</span>
                                        </div>
                                        <div class="flex-1 flex flex-col items-center gap-1.5">
                                            <div class="w-full flex gap-1 items-end justify-center h-16">
                                                <div class="w-4 rounded-t bar-animate delay-300" style="height: 90%; background-color: #cbd5e1;"></div>
                                                <div class="w-4 rounded-t bar-animate delay-400" style="height: 50%; background: linear-gradient(180deg, var(--accent), #14b8a6);"></div>
                                            </div>
                                            <span class="text-[10px] font-medium" style="color: var(--text-muted);">{{ __('linscarbon.home.preview.months.apr') }}</span>
                                        </div>
                                        <div class="flex-1 flex flex-col items-center gap-1.5">
                                            <div class="w-full flex gap-1 items-end justify-center h-16">
                                                <div class="w-4 rounded-t bar-animate delay-400" style="height: 70%; background-color: #cbd5e1;"></div>
                                                <div class="w-4 rounded-t bar-animate delay-500" style="height: 45%; background: linear-gradient(180deg, var(--accent), #14b8a6);"></div>
                                            </div>
                                            <span class="text-[10px] font-medium" style="color: var(--text-muted);">{{ __('linscarbon.home.preview.months.may') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>


        <!-- Features Section -->
        <section id="features" class="py-32 lg:py-48" style="background-color: var(--bg-primary);">
            <div class="max-w-6xl mx-auto px-6">
                <div class="text-center" style="margin-bottom: 80px;">
                    <h2 class="text-3xl lg:text-4xl font-bold" style="color: var(--text-primary); letter-spacing: -0.02em; margin-bottom: 20px;">
                        {{ __('linscarbon.home.features.title') }}
                    </h2>
                    <p class="text-lg" style="color: var(--text-secondary);">
                        {{ __('linscarbon.home.features.subtitle') }}
                    </p>
                </div>

                <!-- Step 1 -->
                <div class="grid lg:grid-cols-2 items-center" style="gap: 60px; margin-bottom: 100px;">
                    <div class="order-2 lg:order-1">
                        <div class="flex items-start" style="gap: 24px;">
                            <div class="step-number flex-shrink-0">1</div>
                            <div>
                                <h3 class="text-2xl font-bold" style="color: var(--text-primary); margin-bottom: 16px;">{{ __('linscarbon.home.features.step1.title') }}</h3>
                                <p class="text-base leading-relaxed" style="color: var(--text-secondary); margin-bottom: 24px;">
                                    {{ __('linscarbon.home.features.step1.description') }}
                                </p>
                                <ul style="display: flex; flex-direction: column; gap: 16px;">
                                    <li class="flex items-center text-sm" style="color: var(--text-secondary); gap: 12px;">
                                        <svg class="w-5 h-5 flex-shrink-0" style="color: var(--accent);" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                                        {{ __('linscarbon.home.features.step1.item1') }}
                                    </li>
                                    <li class="flex items-center text-sm" style="color: var(--text-secondary); gap: 12px;">
                                        <svg class="w-5 h-5 flex-shrink-0" style="color: var(--accent);" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                                        {{ __('linscarbon.home.features.step1.item2') }}
                                    </li>
                                    <li class="flex items-center text-sm" style="color: var(--text-secondary); gap: 12px;">
                                        <svg class="w-5 h-5 flex-shrink-0" style="color: var(--accent);" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                                        {{ __('linscarbon.home.features.step1.item3') }}
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="order-1 lg:order-2">
                        <div class="bg-white rounded-2xl border" style="border-color: var(--border); padding: 32px;">
                            <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="background: linear-gradient(135deg, var(--accent-light), #a7f3d0); margin-bottom: 24px;">
                                <svg class="w-6 h-6" style="color: var(--accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                </svg>
                            </div>
                            <div style="display: flex; flex-direction: column; gap: 12px;">
                                <div class="flex items-center rounded-lg" style="background-color: var(--bg-primary); padding: 16px; gap: 16px;">
                                    <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background-color: #fee2e2;">
                                        <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd" /></svg>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm font-medium" style="color: var(--text-primary);">{{ __('linscarbon.home.features.upload.file1_name') }}</p>
                                        <p class="text-xs" style="color: var(--text-muted); margin-top: 4px;">{{ __('linscarbon.home.features.upload.file1_category') }}</p>
                                    </div>
                                    <span class="text-xs font-medium rounded" style="background-color: #dcfce7; color: #16a34a; padding: 6px 10px;">{{ __('linscarbon.home.features.upload.file1_status') }}</span>
                                </div>
                                <div class="flex items-center rounded-lg" style="background-color: var(--bg-primary); padding: 16px; gap: 16px;">
                                    <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background-color: #dbeafe;">
                                        <svg class="w-5 h-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd" /></svg>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm font-medium" style="color: var(--text-primary);">{{ __('linscarbon.home.features.upload.file2_name') }}</p>
                                        <p class="text-xs" style="color: var(--text-muted); margin-top: 4px;">{{ __('linscarbon.home.features.upload.file2_category') }}</p>
                                    </div>
                                    <span class="text-xs font-medium rounded" style="background-color: #fef3c7; color: #d97706; padding: 6px 10px;">{{ __('linscarbon.home.features.upload.file2_status') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 2 -->
                <div class="grid lg:grid-cols-2 items-center" style="gap: 60px; margin-bottom: 100px;">
                    <div>
                        <div class="bg-white rounded-2xl border" style="border-color: var(--border); padding: 32px;">
                            <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="background: linear-gradient(135deg, #dbeafe, #bfdbfe); margin-bottom: 24px;">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                                </svg>
                            </div>
                            <div class="rounded-xl" style="background-color: var(--bg-primary); padding: 20px;">
                                <p class="text-sm" style="color: var(--text-secondary); margin-bottom: 16px;">{{ __('linscarbon.home.features.step2.ai_question') }}</p>
                                <div class="rounded-lg" style="background-color: white; padding: 16px;">
                                    <p class="text-sm leading-relaxed" style="color: var(--text-primary);">
                                        {{ __('linscarbon.home.features.step2.ai_answer_title') }}<br><br>
                                        <strong>1.</strong> {{ __('linscarbon.home.features.step2.ai_answer1') }}<br>
                                        <strong>2.</strong> {{ __('linscarbon.home.features.step2.ai_answer2') }}<br>
                                        <strong>3.</strong> {{ __('linscarbon.home.features.step2.ai_answer3') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div>
                        <div class="flex items-start" style="gap: 24px;">
                            <div class="step-number flex-shrink-0">2</div>
                            <div>
                                <h3 class="text-2xl font-bold" style="color: var(--text-primary); margin-bottom: 16px;">{{ __('linscarbon.home.features.step2.title') }}</h3>
                                <p class="text-base leading-relaxed" style="color: var(--text-secondary); margin-bottom: 24px;">
                                    {{ __('linscarbon.home.features.step2.description') }}
                                </p>
                                <ul style="display: flex; flex-direction: column; gap: 16px;">
                                    <li class="flex items-center text-sm" style="color: var(--text-secondary); gap: 12px;">
                                        <svg class="w-5 h-5 flex-shrink-0" style="color: var(--accent);" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                                        {{ __('linscarbon.home.features.step2.item1') }}
                                    </li>
                                    <li class="flex items-center text-sm" style="color: var(--text-secondary); gap: 12px;">
                                        <svg class="w-5 h-5 flex-shrink-0" style="color: var(--accent);" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                                        {{ __('linscarbon.home.features.step2.item2') }}
                                    </li>
                                    <li class="flex items-center text-sm" style="color: var(--text-secondary); gap: 12px;">
                                        <svg class="w-5 h-5 flex-shrink-0" style="color: var(--accent);" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                                        {{ __('linscarbon.home.features.step2.item3') }}
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 3 -->
                <div class="grid lg:grid-cols-2 items-center" style="gap: 60px;">
                    <div class="order-2 lg:order-1">
                        <div class="flex items-start" style="gap: 24px;">
                            <div class="step-number flex-shrink-0">3</div>
                            <div>
                                <h3 class="text-2xl font-bold" style="color: var(--text-primary); margin-bottom: 16px;">{{ __('linscarbon.home.features.step3.title') }}</h3>
                                <p class="text-base leading-relaxed" style="color: var(--text-secondary); margin-bottom: 24px;">
                                    {{ __('linscarbon.home.features.step3.description') }}
                                </p>
                                <ul style="display: flex; flex-direction: column; gap: 16px;">
                                    <li class="flex items-center text-sm" style="color: var(--text-secondary); gap: 12px;">
                                        <svg class="w-5 h-5 flex-shrink-0" style="color: var(--accent);" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                                        {{ __('linscarbon.home.features.step3.item1') }}
                                    </li>
                                    <li class="flex items-center text-sm" style="color: var(--text-secondary); gap: 12px;">
                                        <svg class="w-5 h-5 flex-shrink-0" style="color: var(--accent);" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                                        {{ __('linscarbon.home.features.step3.item2') }}
                                    </li>
                                    <li class="flex items-center text-sm" style="color: var(--text-secondary); gap: 12px;">
                                        <svg class="w-5 h-5 flex-shrink-0" style="color: var(--accent);" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                                        {{ __('linscarbon.home.features.step3.item3') }}
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="order-1 lg:order-2">
                        <div class="bg-white rounded-2xl border" style="border-color: var(--border); padding: 32px;">
                            <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="background: linear-gradient(135deg, #fef3c7, #fde68a); margin-bottom: 24px;">
                                <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                </svg>
                            </div>
                            <div style="display: flex; flex-direction: column; gap: 16px;">
                                <div class="rounded-xl" style="background-color: var(--bg-primary); padding: 20px; border-left: 4px solid var(--accent);">
                                    <div class="flex items-center justify-between" style="margin-bottom: 12px;">
                                        <p class="text-sm font-semibold" style="color: var(--text-primary);">{{ __('linscarbon.home.features.step3.action1_title') }}</p>
                                        <span class="text-xs font-bold rounded" style="background-color: #dcfce7; color: #16a34a; padding: 6px 10px;">{{ __('linscarbon.home.features.step3.action1_impact') }}</span>
                                    </div>
                                    <p class="text-xs" style="color: var(--text-muted);">{{ __('linscarbon.home.features.step3.action1_details') }}</p>
                                </div>
                                <div class="rounded-xl" style="background-color: var(--bg-primary); padding: 20px;">
                                    <div class="flex items-center justify-between" style="margin-bottom: 12px;">
                                        <p class="text-sm font-semibold" style="color: var(--text-primary);">{{ __('linscarbon.home.features.step3.action2_title') }}</p>
                                        <span class="text-xs font-bold rounded" style="background-color: #dbeafe; color: #2563eb; padding: 6px 10px;">{{ __('linscarbon.home.features.step3.action2_impact') }}</span>
                                    </div>
                                    <p class="text-xs" style="color: var(--text-muted);">{{ __('linscarbon.home.features.step3.action2_details') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Stats Section -->
        <section style="background-color: var(--bg-card); padding: 100px 0;">
            <div class="max-w-6xl mx-auto px-6">
                <div class="text-center" style="margin-bottom: 60px;">
                    <h2 class="text-3xl lg:text-4xl font-bold" style="color: var(--text-primary); margin-bottom: 16px;">
                        {{ __('linscarbon.home.stats.title') }}
                    </h2>
                    <p class="text-lg" style="color: var(--text-secondary);">
                        {{ __('linscarbon.home.stats.subtitle') }}
                    </p>
                </div>

                <div class="grid md:grid-cols-3" style="gap: 32px;">
                    <div class="text-center rounded-2xl" style="background: linear-gradient(135deg, #f0fdfa 0%, #ccfbf1 100%); padding: 48px 32px;">
                        <p class="text-5xl lg:text-6xl font-bold" style="color: var(--text-primary); margin-bottom: 16px;">{{ __('linscarbon.home.stats.stat1_value') }}</p>
                        <p class="text-sm" style="color: var(--text-secondary);">{{ __('linscarbon.home.stats.stat1_label') }}</p>
                    </div>
                    <div class="text-center rounded-2xl" style="background: linear-gradient(135deg, #f0fdfa 0%, #ccfbf1 100%); padding: 48px 32px;">
                        <p class="text-5xl lg:text-6xl font-bold" style="color: var(--text-primary); margin-bottom: 16px;">{{ __('linscarbon.home.stats.stat2_value') }}</p>
                        <p class="text-sm" style="color: var(--text-secondary);">{{ __('linscarbon.home.stats.stat2_label') }}</p>
                    </div>
                    <div class="text-center rounded-2xl" style="background: linear-gradient(135deg, #f0fdfa 0%, #ccfbf1 100%); padding: 48px 32px;">
                        <p class="text-5xl lg:text-6xl font-bold" style="color: var(--text-primary); margin-bottom: 16px;">{{ __('linscarbon.home.stats.stat3_value') }}</p>
                        <p class="text-sm" style="color: var(--text-secondary);">{{ __('linscarbon.home.stats.stat3_label') }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Pricing Section -->
        <section id="pricing" style="background-color: var(--bg-primary); padding: 100px 0;">
            <div class="max-w-5xl mx-auto px-6">
                <div class="text-center" style="margin-bottom: 60px;">
                    <h2 class="text-3xl lg:text-4xl font-bold" style="color: var(--text-primary); margin-bottom: 16px;">
                        {{ __('linscarbon.home.pricing.title') }}
                    </h2>
                    <p class="text-lg" style="color: var(--text-secondary);">
                        {{ __('linscarbon.home.pricing.subtitle') }}
                    </p>
                </div>

                <div class="grid md:grid-cols-2 lg:grid-cols-4" style="gap: 20px;">
                    <!-- Gratuit -->
                    <div class="bg-white rounded-2xl border" style="border-color: var(--border); padding: 28px;">
                        <p class="text-sm font-semibold uppercase" style="color: var(--text-muted); margin-bottom: 8px;">{{ __('linscarbon.home.pricing.free.name') }}</p>
                        <p class="text-4xl font-bold" style="color: var(--text-primary);">{{ __('linscarbon.home.pricing.free.price') }}</p>
                        <p class="text-sm" style="color: var(--text-muted); margin-bottom: 20px;">{{ __('linscarbon.home.pricing.free.period') }}</p>
                        <ul style="display: flex; flex-direction: column; gap: 10px; margin-bottom: 20px;">
                            <li class="flex items-center text-sm" style="color: var(--text-secondary); gap: 10px;">
                                <svg class="w-4 h-4 flex-shrink-0" style="color: var(--accent);" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                                {{ __('linscarbon.home.pricing.free.feature1') }}
                            </li>
                            <li class="flex items-center text-sm" style="color: var(--text-secondary); gap: 10px;">
                                <svg class="w-4 h-4 flex-shrink-0" style="color: var(--accent);" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                                {{ __('linscarbon.home.pricing.free.feature2') }}
                            </li>
                            <li class="flex items-center text-sm" style="color: var(--text-muted); gap: 10px;">
                                <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                {{ __('linscarbon.home.pricing.free.feature3') }}
                            </li>
                        </ul>
                        <a href="{{ route('register') }}" class="block w-full text-center text-sm font-semibold rounded-xl border-2" style="color: var(--text-secondary); border-color: var(--border); padding: 12px;">
                            {{ __('linscarbon.home.pricing.free.cta') }}
                        </a>
                    </div>

                    <!-- Premium Mensuel -->
                    <div class="bg-white rounded-2xl border" style="border-color: var(--border); padding: 28px;">
                        <p class="text-sm font-semibold uppercase" style="color: var(--text-muted); margin-bottom: 8px;">{{ __('linscarbon.home.pricing.premium_monthly.name') }}</p>
                        <p class="text-4xl font-bold" style="color: var(--text-primary);">{{ __('linscarbon.home.pricing.premium_monthly.price') }}</p>
                        <p class="text-sm" style="color: var(--text-muted); margin-bottom: 20px;">{{ __('linscarbon.home.pricing.premium_monthly.period') }}</p>
                        <ul style="display: flex; flex-direction: column; gap: 10px; margin-bottom: 20px;">
                            <li class="flex items-center text-sm" style="color: var(--text-secondary); gap: 10px;">
                                <svg class="w-4 h-4 flex-shrink-0" style="color: var(--accent);" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                                {{ __('linscarbon.home.pricing.premium_monthly.feature1') }}
                            </li>
                            <li class="flex items-center text-sm" style="color: var(--text-secondary); gap: 10px;">
                                <svg class="w-4 h-4 flex-shrink-0" style="color: var(--accent);" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                                {{ __('linscarbon.home.pricing.premium_monthly.feature2') }}
                            </li>
                            <li class="flex items-center text-sm" style="color: var(--text-secondary); gap: 10px;">
                                <svg class="w-4 h-4 flex-shrink-0" style="color: var(--accent);" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                                {{ __('linscarbon.home.pricing.premium_monthly.feature3') }}
                            </li>
                        </ul>
                        <a href="{{ route('register') }}" class="block w-full text-center text-sm font-semibold text-white rounded-xl" style="background-color: var(--accent); padding: 12px;">
                            {{ __('linscarbon.home.pricing.premium_monthly.cta') }}
                        </a>
                    </div>

                    <!-- Premium Annuel -->
                    <div class="bg-white rounded-2xl border-2 relative" style="border-color: var(--accent); padding: 28px;">
                        <div class="absolute -top-3 left-1/2 -translate-x-1/2">
                            <span class="text-xs font-bold text-white rounded-full" style="background: var(--accent); padding: 5px 12px;">{{ __('linscarbon.home.pricing.premium_annual.discount') }}</span>
                        </div>
                        <p class="text-sm font-semibold uppercase" style="color: var(--text-muted); margin-bottom: 8px;">{{ __('linscarbon.home.pricing.premium_annual.name') }}</p>
                        <p class="text-4xl font-bold" style="color: var(--text-primary);">{{ __('linscarbon.home.pricing.premium_annual.price') }}</p>
                        <p class="text-sm" style="color: var(--text-muted); margin-bottom: 20px;">{{ __('linscarbon.home.pricing.premium_annual.period') }}</p>
                        <ul style="display: flex; flex-direction: column; gap: 10px; margin-bottom: 20px;">
                            <li class="flex items-center text-sm" style="color: var(--text-secondary); gap: 10px;">
                                <svg class="w-4 h-4 flex-shrink-0" style="color: var(--accent);" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                                {{ __('linscarbon.home.pricing.premium_annual.feature1') }}
                            </li>
                            <li class="flex items-center text-sm" style="color: var(--text-secondary); gap: 10px;">
                                <svg class="w-4 h-4 flex-shrink-0" style="color: var(--accent);" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                                {{ __('linscarbon.home.pricing.premium_annual.feature2') }}
                            </li>
                            <li class="flex items-center text-sm" style="color: var(--text-secondary); gap: 10px;">
                                <svg class="w-4 h-4 flex-shrink-0" style="color: var(--accent);" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                                {{ __('linscarbon.home.pricing.premium_annual.feature3') }}
                            </li>
                        </ul>
                        <a href="{{ route('register') }}" class="block w-full text-center text-sm font-semibold text-white rounded-xl" style="background-color: var(--accent); padding: 12px;">
                            {{ __('linscarbon.home.pricing.premium_annual.cta') }}
                        </a>
                    </div>

                    <!-- Entreprise -->
                    <div class="bg-white rounded-2xl border relative" style="border-color: var(--border); padding: 28px;">
                        <div class="absolute -top-3 left-1/2 -translate-x-1/2">
                            <span class="text-xs font-bold text-white rounded-full" style="background: #f59e0b; padding: 5px 12px;">{{ __('linscarbon.home.pricing.enterprise.discount') }}</span>
                        </div>
                        <p class="text-sm font-semibold uppercase" style="color: var(--text-muted); margin-bottom: 8px;">{{ __('linscarbon.home.pricing.enterprise.name') }}</p>
                        <p class="text-4xl font-bold" style="color: var(--text-primary);">{{ __('linscarbon.home.pricing.enterprise.price') }}</p>
                        <p class="text-sm" style="color: var(--text-muted); margin-bottom: 20px;">{{ __('linscarbon.home.pricing.enterprise.period') }}</p>
                        <ul style="display: flex; flex-direction: column; gap: 10px; margin-bottom: 20px;">
                            <li class="flex items-center text-sm" style="color: var(--text-secondary); gap: 10px;">
                                <svg class="w-4 h-4 flex-shrink-0" style="color: var(--accent);" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                                {{ __('linscarbon.home.pricing.enterprise.feature1') }}
                            </li>
                            <li class="flex items-center text-sm" style="color: var(--text-secondary); gap: 10px;">
                                <svg class="w-4 h-4 flex-shrink-0" style="color: var(--accent);" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                                {{ __('linscarbon.home.pricing.enterprise.feature2') }}
                            </li>
                            <li class="flex items-center text-sm" style="color: var(--text-secondary); gap: 10px;">
                                <svg class="w-4 h-4 flex-shrink-0" style="color: var(--accent);" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                                {{ __('linscarbon.home.pricing.enterprise.feature3') }}
                            </li>
                        </ul>
                        <a href="{{ route('register') }}" class="block w-full text-center text-sm font-semibold rounded-xl border-2" style="color: var(--text-secondary); border-color: var(--border); padding: 12px;">
                            {{ __('linscarbon.home.pricing.enterprise.cta') }}
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section style="background: linear-gradient(135deg, #f0fdfa 0%, #ccfbf1 100%); padding: 100px 0;">
            <div class="max-w-2xl mx-auto px-6 text-center">
                <h2 class="text-3xl lg:text-4xl font-bold" style="color: var(--text-primary); margin-bottom: 16px;">
                    {{ __('linscarbon.home.cta.title') }}
                </h2>
                <p class="text-lg" style="color: var(--text-secondary); margin-bottom: 32px;">
                    {{ __('linscarbon.home.cta.subtitle') }}
                </p>
                <a href="{{ route('register') }}" class="inline-flex items-center text-base font-semibold text-white rounded-xl" style="background-color: var(--accent); padding: 16px 32px; margin-bottom: 20px;">
                    {{ __('linscarbon.home.cta.button') }}
                    <svg class="w-5 h-5" style="margin-left: 10px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                </a>
                <p class="text-sm" style="color: var(--text-muted);">
                    {{ __('linscarbon.home.cta.note') }}
                </p>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="border-t" style="border-color: var(--border); background-color: var(--bg-card); padding: 60px 0;">
        <div class="max-w-6xl mx-auto px-6">
            <div class="grid md:grid-cols-4" style="gap: 48px; margin-bottom: 48px;">
                <div>
                    <a href="/" class="block" style="margin-bottom: 16px;">
                        <x-logo variant="premium" />
                    </a>
                    <p class="text-sm" style="color: var(--text-muted);">{{ __('linscarbon.home.footer.tagline') }}</p>
                </div>
                <div>
                    <p class="text-sm font-semibold" style="color: var(--text-primary); margin-bottom: 16px;">{{ __('linscarbon.home.footer.product') }}</p>
                    <ul style="display: flex; flex-direction: column; gap: 12px;" class="text-sm">
                        <li><a href="#features" style="color: var(--text-secondary);">{{ __('linscarbon.home.nav.features') }}</a></li>
                        <li><a href="#pricing" style="color: var(--text-secondary);">{{ __('linscarbon.home.nav.pricing') }}</a></li>
                    </ul>
                </div>
                <div>
                    <p class="text-sm font-semibold" style="color: var(--text-primary); margin-bottom: 16px;">{{ __('linscarbon.home.footer.resources') }}</p>
                    <ul style="display: flex; flex-direction: column; gap: 12px;" class="text-sm">
                        <li><a href="#" style="color: var(--text-secondary);">{{ __('linscarbon.home.footer.documentation') }}</a></li>
                        <li><a href="#" style="color: var(--text-secondary);">{{ __('linscarbon.home.footer.csrd_guide') }}</a></li>
                    </ul>
                </div>
                <div>
                    <p class="text-sm font-semibold" style="color: var(--text-primary); margin-bottom: 16px;">{{ __('linscarbon.home.footer.legal') }}</p>
                    <ul style="display: flex; flex-direction: column; gap: 12px;" class="text-sm">
                        <li><a href="{{ route('confidentialite') }}" style="color: var(--text-secondary);">{{ __('linscarbon.home.footer.privacy') }}</a></li>
                        <li><a href="{{ route('cgu') }}" style="color: var(--text-secondary);">{{ __('linscarbon.home.footer.terms') }}</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t flex flex-col md:flex-row items-center justify-between" style="border-color: var(--border); padding-top: 24px; gap: 16px;">
                <p class="text-sm" style="color: var(--text-muted);">© {{ date('Y') }} LinsCarbon</p>
                <div class="flex items-center" style="gap: 16px;">
                    <span class="text-xs" style="color: var(--text-muted);">{{ __('linscarbon.home.footer.compliance') }}</span>
                </div>
            </div>
        </div>
    </footer>

</body>
</html>
