@extends('layouts.marketing')

@section('title', __('linscarbon.features.page_title') . ' - LinsCarbon')
@section('description', __('linscarbon.features.meta_description'))

@section('content')
<section class="pt-32 pb-20" style="background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);">
    <div class="max-w-6xl mx-auto px-6">
        <div class="mb-16 text-center">
            <p class="text-sm font-medium mb-4" style="color: var(--accent);">{{ __('linscarbon.features.label') }}</p>
            <h1 class="text-4xl md:text-5xl font-semibold mb-6" style="color: var(--text-primary); letter-spacing: -0.025em;">
                {{ __('linscarbon.features.title') }}
            </h1>
            <p class="text-lg max-w-2xl mx-auto" style="color: var(--text-secondary);">
                {{ __('linscarbon.features.subtitle') }}
            </p>
        </div>

        {{-- Feature 1: Automatic Measurement --}}
        <div class="grid md:grid-cols-2 gap-12 items-center mb-24">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-sm font-medium mb-4" style="background-color: var(--accent-light); color: var(--accent);">
                    <span class="w-2 h-2 rounded-full" style="background-color: var(--accent);"></span>
                    {{ __('linscarbon.features.measure.badge') }}
                </div>
                <h2 class="text-3xl font-semibold mb-4" style="color: var(--text-primary);">{{ __('linscarbon.features.measure.title') }}</h2>
                <p class="mb-6" style="color: var(--text-secondary);">{{ __('linscarbon.features.measure.desc') }}</p>
                <ul class="space-y-3">
                    @foreach(['import', 'factors', 'scopes', 'banking'] as $item)
                    <li class="flex items-center gap-3">
                        <svg class="w-5 h-5 flex-shrink-0" style="color: var(--accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        <span style="color: var(--text-secondary);">{{ __('linscarbon.features.measure.' . $item) }}</span>
                    </li>
                    @endforeach
                </ul>
            </div>
            <div class="p-8 rounded-2xl" style="background-color: var(--bg-card); border: 1px solid var(--border);">
                <div class="aspect-video rounded-xl flex items-center justify-center" style="background: linear-gradient(135deg, var(--accent-light) 0%, #e0f2fe 100%);">
                    <svg class="w-24 h-24" style="color: var(--accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Feature 2: AI Analysis --}}
        <div class="grid md:grid-cols-2 gap-12 items-center mb-24">
            <div class="order-2 md:order-1 p-8 rounded-2xl" style="background-color: var(--bg-card); border: 1px solid var(--border);">
                <div class="aspect-video rounded-xl flex items-center justify-center" style="background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);">
                    <svg class="w-24 h-24" style="color: #d97706;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                    </svg>
                </div>
            </div>
            <div class="order-1 md:order-2">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-sm font-medium mb-4" style="background-color: #fef3c7; color: #d97706;">
                    <span class="w-2 h-2 rounded-full" style="background-color: #d97706;"></span>
                    {{ __('linscarbon.features.ai.badge') }}
                </div>
                <h2 class="text-3xl font-semibold mb-4" style="color: var(--text-primary);">{{ __('linscarbon.features.ai.title') }}</h2>
                <p class="mb-6" style="color: var(--text-secondary);">{{ __('linscarbon.features.ai.desc') }}</p>
                <ul class="space-y-3">
                    @foreach(['categorization', 'recommendations', 'questions', 'providers'] as $item)
                    <li class="flex items-center gap-3">
                        <svg class="w-5 h-5 flex-shrink-0" style="color: #d97706;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        <span style="color: var(--text-secondary);">{{ __('linscarbon.features.ai.' . $item) }}</span>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>

        {{-- Feature 3: Action Plan --}}
        <div class="grid md:grid-cols-2 gap-12 items-center mb-24">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-sm font-medium mb-4" style="background-color: #dcfce7; color: #16a34a;">
                    <span class="w-2 h-2 rounded-full" style="background-color: #16a34a;"></span>
                    {{ __('linscarbon.features.action.badge') }}
                </div>
                <h2 class="text-3xl font-semibold mb-4" style="color: var(--text-primary);">{{ __('linscarbon.features.action.title') }}</h2>
                <p class="mb-6" style="color: var(--text-secondary);">{{ __('linscarbon.features.action.desc') }}</p>
                <ul class="space-y-3">
                    @foreach(['trajectory', 'roi', 'priorities', 'tracking'] as $item)
                    <li class="flex items-center gap-3">
                        <svg class="w-5 h-5 flex-shrink-0" style="color: #16a34a;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        <span style="color: var(--text-secondary);">{{ __('linscarbon.features.action.' . $item) }}</span>
                    </li>
                    @endforeach
                </ul>
            </div>
            <div class="p-8 rounded-2xl" style="background-color: var(--bg-card); border: 1px solid var(--border);">
                <div class="aspect-video rounded-xl flex items-center justify-center" style="background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%);">
                    <svg class="w-24 h-24" style="color: #16a34a;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Feature 4: Compliance & Reports --}}
        <div class="grid md:grid-cols-2 gap-12 items-center mb-24">
            <div class="order-2 md:order-1 p-8 rounded-2xl" style="background-color: var(--bg-card); border: 1px solid var(--border);">
                <div class="aspect-video rounded-xl flex items-center justify-center" style="background: linear-gradient(135deg, #ede9fe 0%, #ddd6fe 100%);">
                    <svg class="w-24 h-24" style="color: #7c3aed;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
            </div>
            <div class="order-1 md:order-2">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-sm font-medium mb-4" style="background-color: #ede9fe; color: #7c3aed;">
                    <span class="w-2 h-2 rounded-full" style="background-color: #7c3aed;"></span>
                    {{ __('linscarbon.features.compliance.badge') }}
                </div>
                <h2 class="text-3xl font-semibold mb-4" style="color: var(--text-primary);">{{ __('linscarbon.features.compliance.title') }}</h2>
                <p class="mb-6" style="color: var(--text-secondary);">{{ __('linscarbon.features.compliance.desc') }}</p>
                <ul class="space-y-3">
                    @foreach(['csrd', 'ghg', 'export', 'audit'] as $item)
                    <li class="flex items-center gap-3">
                        <svg class="w-5 h-5 flex-shrink-0" style="color: #7c3aed;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        <span style="color: var(--text-secondary);">{{ __('linscarbon.features.compliance.' . $item) }}</span>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>

        {{-- CTA --}}
        <div class="text-center p-12 rounded-2xl" style="background: linear-gradient(135deg, var(--accent) 0%, #059669 100%);">
            <h2 class="text-2xl font-semibold mb-4 text-white">{{ __('linscarbon.features.cta_title') }}</h2>
            <p class="mb-6 text-white/90">{{ __('linscarbon.features.cta_desc') }}</p>
            <a href="{{ route('register') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-lg font-medium" style="background-color: white; color: var(--accent);">
                {{ __('linscarbon.features.cta_button') }}
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
            </a>
        </div>
    </div>
</section>
@endsection
