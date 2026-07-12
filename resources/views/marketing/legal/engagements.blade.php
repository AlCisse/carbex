@extends('layouts.marketing')

@section('title', __('linscarbon.legal.engagements.title') . ' - LinsCarbon')
@section('description', __('linscarbon.legal.engagements.meta_description'))

@section('content')
<section class="pt-32 pb-20" style="background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);">
    <div class="max-w-4xl mx-auto px-6">
        <div class="text-center mb-16">
            <p class="text-sm font-medium mb-4" style="color: var(--accent);">{{ __('linscarbon.legal.engagements.label') }}</p>
            <h1 class="text-4xl font-semibold mb-6" style="color: var(--text-primary); letter-spacing: -0.025em;">
                {{ __('linscarbon.legal.engagements.title') }}
            </h1>
            <p class="text-lg max-w-2xl mx-auto" style="color: var(--text-secondary);">
                {{ __('linscarbon.legal.engagements.hero_subtitle') }}
            </p>
        </div>

        <!-- Engagement Cards -->
        <div class="grid md:grid-cols-2 gap-8 mb-16">
            <!-- Card 1 - Security -->
            <div class="bg-white rounded-2xl p-8 border" style="border-color: var(--border);">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center mb-6" style="background-color: var(--accent-light);">
                    <svg class="w-6 h-6" style="color: var(--accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
                <h3 class="text-xl font-semibold mb-4" style="color: var(--text-primary);">{{ __('linscarbon.legal.engagements.security_title') }}</h3>
                <p class="mb-4" style="color: var(--text-secondary);">
                    {{ __('linscarbon.legal.engagements.security_text') }}
                </p>
                <ul class="space-y-2 text-sm" style="color: var(--text-secondary);">
                    <li class="flex items-center gap-2">
                        <svg class="w-4 h-4 flex-shrink-0" style="color: var(--accent);" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        {{ __('linscarbon.legal.engagements.security_hosting') }}
                    </li>
                    <li class="flex items-center gap-2">
                        <svg class="w-4 h-4 flex-shrink-0" style="color: var(--accent);" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        {{ __('linscarbon.legal.engagements.security_gdpr') }}
                    </li>
                    <li class="flex items-center gap-2">
                        <svg class="w-4 h-4 flex-shrink-0" style="color: var(--accent);" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        {{ __('linscarbon.legal.engagements.security_backup') }}
                    </li>
                </ul>
            </div>

            <!-- Card 2 - Transparency -->
            <div class="bg-white rounded-2xl p-8 border" style="border-color: var(--border);">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center mb-6" style="background-color: var(--accent-light);">
                    <svg class="w-6 h-6" style="color: var(--accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                </div>
                <h3 class="text-xl font-semibold mb-4" style="color: var(--text-primary);">{{ __('linscarbon.legal.engagements.transparency_title') }}</h3>
                <p class="mb-4" style="color: var(--text-secondary);">
                    {{ __('linscarbon.legal.engagements.transparency_text') }}
                </p>
                <ul class="space-y-2 text-sm" style="color: var(--text-secondary);">
                    <li class="flex items-center gap-2">
                        <svg class="w-4 h-4 flex-shrink-0" style="color: var(--accent);" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        {{ __('linscarbon.legal.engagements.transparency_factors') }}
                    </li>
                    <li class="flex items-center gap-2">
                        <svg class="w-4 h-4 flex-shrink-0" style="color: var(--accent);" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        {{ __('linscarbon.legal.engagements.transparency_ghg') }}
                    </li>
                    <li class="flex items-center gap-2">
                        <svg class="w-4 h-4 flex-shrink-0" style="color: var(--accent);" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        {{ __('linscarbon.legal.engagements.transparency_trace') }}
                    </li>
                </ul>
            </div>

            <!-- Card 3 - Carbon Neutrality -->
            <div class="bg-white rounded-2xl p-8 border" style="border-color: var(--border);">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center mb-6" style="background-color: var(--accent-light);">
                    <svg class="w-6 h-6" style="color: var(--accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="text-xl font-semibold mb-4" style="color: var(--text-primary);">{{ __('linscarbon.legal.engagements.neutrality_title') }}</h3>
                <p class="mb-4" style="color: var(--text-secondary);">
                    {{ __('linscarbon.legal.engagements.neutrality_text') }}
                </p>
                <ul class="space-y-2 text-sm" style="color: var(--text-secondary);">
                    <li class="flex items-center gap-2">
                        <svg class="w-4 h-4 flex-shrink-0" style="color: var(--accent);" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        {{ __('linscarbon.legal.engagements.neutrality_annual') }}
                    </li>
                    <li class="flex items-center gap-2">
                        <svg class="w-4 h-4 flex-shrink-0" style="color: var(--accent);" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        {{ __('linscarbon.legal.engagements.neutrality_hosting') }}
                    </li>
                    <li class="flex items-center gap-2">
                        <svg class="w-4 h-4 flex-shrink-0" style="color: var(--accent);" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        {{ __('linscarbon.legal.engagements.neutrality_offset') }}
                    </li>
                </ul>
            </div>

            <!-- Card 4 - Accessibility -->
            <div class="bg-white rounded-2xl p-8 border" style="border-color: var(--border);">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center mb-6" style="background-color: var(--accent-light);">
                    <svg class="w-6 h-6" style="color: var(--accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <h3 class="text-xl font-semibold mb-4" style="color: var(--text-primary);">{{ __('linscarbon.legal.engagements.accessibility_title') }}</h3>
                <p class="mb-4" style="color: var(--text-secondary);">
                    {{ __('linscarbon.legal.engagements.accessibility_text') }}
                </p>
                <ul class="space-y-2 text-sm" style="color: var(--text-secondary);">
                    <li class="flex items-center gap-2">
                        <svg class="w-4 h-4 flex-shrink-0" style="color: var(--accent);" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        {{ __('linscarbon.legal.engagements.accessibility_trial') }}
                    </li>
                    <li class="flex items-center gap-2">
                        <svg class="w-4 h-4 flex-shrink-0" style="color: var(--accent);" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        {{ __('linscarbon.legal.engagements.accessibility_pricing') }}
                    </li>
                    <li class="flex items-center gap-2">
                        <svg class="w-4 h-4 flex-shrink-0" style="color: var(--accent);" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        {{ __('linscarbon.legal.engagements.accessibility_support') }}
                    </li>
                </ul>
            </div>
        </div>

        <!-- Mission Statement -->
        <div class="text-center p-12 rounded-2xl" style="background-color: var(--accent-light);">
            <svg class="w-12 h-12 mx-auto mb-6" style="color: var(--accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
            </svg>
            <h2 class="text-2xl font-semibold mb-4" style="color: var(--text-primary);">
                {{ __('linscarbon.legal.engagements.mission_title') }}
            </h2>
            <p class="text-lg max-w-2xl mx-auto" style="color: var(--text-secondary);">
                {{ __('linscarbon.legal.engagements.mission_text') }}
            </p>
        </div>

        <!-- Standards & Certifications -->
        <div class="mt-16">
            <h2 class="text-2xl font-semibold text-center mb-8" style="color: var(--text-primary);">
                {{ __('linscarbon.legal.engagements.standards_title') }}
            </h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                <div class="text-center p-6 bg-white rounded-xl border" style="border-color: var(--border);">
                    <div class="text-2xl font-bold mb-2" style="color: var(--accent);">UBA</div>
                    <p class="text-sm" style="color: var(--text-muted);">{{ __('linscarbon.legal.engagements.standard_uba') }}</p>
                </div>
                <div class="text-center p-6 bg-white rounded-xl border" style="border-color: var(--border);">
                    <div class="text-2xl font-bold mb-2" style="color: var(--accent);">GHG</div>
                    <p class="text-sm" style="color: var(--text-muted);">{{ __('linscarbon.legal.engagements.standard_ghg') }}</p>
                </div>
                <div class="text-center p-6 bg-white rounded-xl border" style="border-color: var(--border);">
                    <div class="text-2xl font-bold mb-2" style="color: var(--accent);">ISO</div>
                    <p class="text-sm" style="color: var(--text-muted);">{{ __('linscarbon.legal.engagements.standard_iso') }}</p>
                </div>
                <div class="text-center p-6 bg-white rounded-xl border" style="border-color: var(--border);">
                    <div class="text-2xl font-bold mb-2" style="color: var(--accent);">CSRD</div>
                    <p class="text-sm" style="color: var(--text-muted);">{{ __('linscarbon.legal.engagements.standard_csrd') }}</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="py-20" style="background-color: var(--accent);">
    <div class="max-w-3xl mx-auto px-6 text-center">
        <h2 class="text-2xl font-semibold text-white mb-4">
            {{ __('linscarbon.legal.engagements.cta_title') }}
        </h2>
        <p class="text-white/80 mb-8">
            {{ __('linscarbon.legal.engagements.cta_subtitle') }}
        </p>
        <a href="{{ route('register') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-white rounded-lg text-sm font-medium transition-colors hover:bg-gray-100" style="color: var(--accent);">
            {{ __('linscarbon.legal.engagements.cta_button') }}
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
            </svg>
        </a>
    </div>
</section>
@endsection
