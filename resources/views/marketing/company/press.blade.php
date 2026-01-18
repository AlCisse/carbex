@extends('layouts.marketing')

@section('title', __('linscarbon.company.press.title') . ' - LinsCarbon')
@section('description', __('linscarbon.company.press.meta_description'))

@section('content')
<section class="pt-32 pb-20" style="background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);">
    <div class="max-w-4xl mx-auto px-6">
        <div class="mb-12 text-center">
            <p class="text-sm font-medium mb-4" style="color: var(--accent);">{{ __('linscarbon.company.label') }}</p>
            <h1 class="text-4xl font-semibold mb-6" style="color: var(--text-primary); letter-spacing: -0.025em;">
                {{ __('linscarbon.company.press.title') }}
            </h1>
            <p class="text-lg" style="color: var(--text-secondary);">
                {{ __('linscarbon.company.press.subtitle') }}
            </p>
        </div>

        {{-- Press Contact --}}
        <div class="mb-16 p-8 rounded-2xl" style="background-color: var(--bg-card); border: 1px solid var(--border);">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
                <div>
                    <h2 class="text-xl font-semibold mb-2" style="color: var(--text-primary);">{{ __('linscarbon.company.press.contact_title') }}</h2>
                    <p style="color: var(--text-secondary);">{{ __('linscarbon.company.press.contact_desc') }}</p>
                </div>
                <a href="mailto:presse@linscarbon.eu" class="inline-flex items-center gap-2 px-6 py-3 rounded-lg font-medium text-white whitespace-nowrap" style="background-color: var(--accent);">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    presse@linscarbon.eu
                </a>
            </div>
        </div>

        {{-- Company Info --}}
        <div class="mb-16">
            <h2 class="text-2xl font-semibold mb-8" style="color: var(--text-primary);">{{ __('linscarbon.company.press.about_title') }}</h2>
            <div class="prose prose-lg max-w-none" style="color: var(--text-secondary);">
                <p>{{ __('linscarbon.company.press.about_p1') }}</p>
                <p>{{ __('linscarbon.company.press.about_p2') }}</p>
            </div>

            <div class="grid md:grid-cols-3 gap-6 mt-8">
                <div class="p-6 rounded-xl text-center" style="background-color: var(--accent-light);">
                    <p class="text-3xl font-bold mb-1" style="color: var(--accent);">2024</p>
                    <p class="text-sm" style="color: var(--text-secondary);">{{ __('linscarbon.company.press.stat_founded') }}</p>
                </div>
                <div class="p-6 rounded-xl text-center" style="background-color: var(--accent-light);">
                    <p class="text-3xl font-bold mb-1" style="color: var(--accent);">8</p>
                    <p class="text-sm" style="color: var(--text-secondary);">{{ __('linscarbon.company.press.stat_countries') }}</p>
                </div>
                <div class="p-6 rounded-xl text-center" style="background-color: var(--accent-light);">
                    <p class="text-3xl font-bold mb-1" style="color: var(--accent);">100%</p>
                    <p class="text-sm" style="color: var(--text-secondary);">{{ __('linscarbon.company.press.stat_compliant') }}</p>
                </div>
            </div>
        </div>

        {{-- Press Kit --}}
        <div class="mb-16">
            <h2 class="text-2xl font-semibold mb-8" style="color: var(--text-primary);">{{ __('linscarbon.company.press.kit_title') }}</h2>
            <div class="grid md:grid-cols-2 gap-6">
                <div class="p-6 rounded-xl" style="background-color: var(--bg-card); border: 1px solid var(--border);">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0" style="background-color: var(--accent-light);">
                            <svg class="w-6 h-6" style="color: var(--accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold mb-1" style="color: var(--text-primary);">{{ __('linscarbon.company.press.logos_title') }}</h3>
                            <p class="text-sm mb-3" style="color: var(--text-secondary);">{{ __('linscarbon.company.press.logos_desc') }}</p>
                            <a href="#" class="text-sm font-medium" style="color: var(--accent);">{{ __('linscarbon.company.press.download') }} →</a>
                        </div>
                    </div>
                </div>
                <div class="p-6 rounded-xl" style="background-color: var(--bg-card); border: 1px solid var(--border);">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0" style="background-color: var(--accent-light);">
                            <svg class="w-6 h-6" style="color: var(--accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold mb-1" style="color: var(--text-primary);">{{ __('linscarbon.company.press.factsheet_title') }}</h3>
                            <p class="text-sm mb-3" style="color: var(--text-secondary);">{{ __('linscarbon.company.press.factsheet_desc') }}</p>
                            <a href="#" class="text-sm font-medium" style="color: var(--accent);">{{ __('linscarbon.company.press.download') }} →</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Recent News --}}
        <div class="mb-16">
            <h2 class="text-2xl font-semibold mb-8" style="color: var(--text-primary);">{{ __('linscarbon.company.press.news_title') }}</h2>
            <div class="space-y-4">
                <div class="p-6 rounded-xl" style="background-color: var(--bg-card); border: 1px solid var(--border);">
                    <p class="text-sm mb-2" style="color: var(--text-muted);">{{ __('linscarbon.company.press.news1_date') }}</p>
                    <h3 class="font-semibold mb-2" style="color: var(--text-primary);">{{ __('linscarbon.company.press.news1_title') }}</h3>
                    <p class="text-sm" style="color: var(--text-secondary);">{{ __('linscarbon.company.press.news1_excerpt') }}</p>
                </div>
                <div class="p-6 rounded-xl" style="background-color: var(--bg-card); border: 1px solid var(--border);">
                    <p class="text-sm mb-2" style="color: var(--text-muted);">{{ __('linscarbon.company.press.news2_date') }}</p>
                    <h3 class="font-semibold mb-2" style="color: var(--text-primary);">{{ __('linscarbon.company.press.news2_title') }}</h3>
                    <p class="text-sm" style="color: var(--text-secondary);">{{ __('linscarbon.company.press.news2_excerpt') }}</p>
                </div>
            </div>
        </div>

        {{-- CTA --}}
        <div class="text-center p-12 rounded-2xl" style="background-color: var(--accent-light);">
            <h2 class="text-2xl font-semibold mb-4" style="color: var(--text-primary);">{{ __('linscarbon.company.press.interview_title') }}</h2>
            <p class="mb-6" style="color: var(--text-secondary);">{{ __('linscarbon.company.press.interview_desc') }}</p>
            <a href="mailto:presse@linscarbon.eu" class="inline-flex items-center gap-2 px-6 py-3 rounded-lg font-medium text-white" style="background-color: var(--accent);">
                {{ __('linscarbon.company.press.interview_button') }}
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
            </a>
        </div>
    </div>
</section>
@endsection
