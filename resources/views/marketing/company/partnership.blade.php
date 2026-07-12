@extends('layouts.marketing')

@section('title', __('linscarbon.company.partnership.title') . ' - LinsCarbon')
@section('description', __('linscarbon.company.partnership.meta_description'))

@section('content')
<section class="pt-32 pb-20" style="background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);">
    <div class="max-w-4xl mx-auto px-6">
        <div class="mb-12 text-center">
            <p class="text-sm font-medium mb-4" style="color: var(--accent);">{{ __('linscarbon.company.label') }}</p>
            <h1 class="text-4xl font-semibold mb-6" style="color: var(--text-primary); letter-spacing: -0.025em;">
                {{ __('linscarbon.company.partnership.title') }}
            </h1>
            <p class="text-lg" style="color: var(--text-secondary);">
                {{ __('linscarbon.company.partnership.subtitle') }}
            </p>
        </div>

        <div class="grid md:grid-cols-2 gap-8 mb-16">
            {{-- Partner Type 1: Consultants --}}
            <div class="p-8 rounded-2xl" style="background-color: var(--bg-card); border: 1px solid var(--border);">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center mb-6" style="background-color: var(--accent-light);">
                    <svg class="w-6 h-6" style="color: var(--accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold mb-3" style="color: var(--text-primary);">{{ __('linscarbon.company.partnership.consultants_title') }}</h3>
                <p class="mb-4" style="color: var(--text-secondary);">{{ __('linscarbon.company.partnership.consultants_desc') }}</p>
                <ul class="space-y-2 text-sm" style="color: var(--text-secondary);">
                    <li class="flex items-center gap-2">
                        <svg class="w-4 h-4 flex-shrink-0" style="color: var(--accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        {{ __('linscarbon.company.partnership.consultants_benefit1') }}
                    </li>
                    <li class="flex items-center gap-2">
                        <svg class="w-4 h-4 flex-shrink-0" style="color: var(--accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        {{ __('linscarbon.company.partnership.consultants_benefit2') }}
                    </li>
                    <li class="flex items-center gap-2">
                        <svg class="w-4 h-4 flex-shrink-0" style="color: var(--accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        {{ __('linscarbon.company.partnership.consultants_benefit3') }}
                    </li>
                </ul>
            </div>

            {{-- Partner Type 2: Software --}}
            <div class="p-8 rounded-2xl" style="background-color: var(--bg-card); border: 1px solid var(--border);">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center mb-6" style="background-color: var(--accent-light);">
                    <svg class="w-6 h-6" style="color: var(--accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold mb-3" style="color: var(--text-primary);">{{ __('linscarbon.company.partnership.software_title') }}</h3>
                <p class="mb-4" style="color: var(--text-secondary);">{{ __('linscarbon.company.partnership.software_desc') }}</p>
                <ul class="space-y-2 text-sm" style="color: var(--text-secondary);">
                    <li class="flex items-center gap-2">
                        <svg class="w-4 h-4 flex-shrink-0" style="color: var(--accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        {{ __('linscarbon.company.partnership.software_benefit1') }}
                    </li>
                    <li class="flex items-center gap-2">
                        <svg class="w-4 h-4 flex-shrink-0" style="color: var(--accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        {{ __('linscarbon.company.partnership.software_benefit2') }}
                    </li>
                    <li class="flex items-center gap-2">
                        <svg class="w-4 h-4 flex-shrink-0" style="color: var(--accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        {{ __('linscarbon.company.partnership.software_benefit3') }}
                    </li>
                </ul>
            </div>

            {{-- Partner Type 3: Resellers --}}
            <div class="p-8 rounded-2xl" style="background-color: var(--bg-card); border: 1px solid var(--border);">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center mb-6" style="background-color: var(--accent-light);">
                    <svg class="w-6 h-6" style="color: var(--accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold mb-3" style="color: var(--text-primary);">{{ __('linscarbon.company.partnership.resellers_title') }}</h3>
                <p class="mb-4" style="color: var(--text-secondary);">{{ __('linscarbon.company.partnership.resellers_desc') }}</p>
                <ul class="space-y-2 text-sm" style="color: var(--text-secondary);">
                    <li class="flex items-center gap-2">
                        <svg class="w-4 h-4 flex-shrink-0" style="color: var(--accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        {{ __('linscarbon.company.partnership.resellers_benefit1') }}
                    </li>
                    <li class="flex items-center gap-2">
                        <svg class="w-4 h-4 flex-shrink-0" style="color: var(--accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        {{ __('linscarbon.company.partnership.resellers_benefit2') }}
                    </li>
                    <li class="flex items-center gap-2">
                        <svg class="w-4 h-4 flex-shrink-0" style="color: var(--accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        {{ __('linscarbon.company.partnership.resellers_benefit3') }}
                    </li>
                </ul>
            </div>

            {{-- Partner Type 4: Accountants --}}
            <div class="p-8 rounded-2xl" style="background-color: var(--bg-card); border: 1px solid var(--border);">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center mb-6" style="background-color: var(--accent-light);">
                    <svg class="w-6 h-6" style="color: var(--accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold mb-3" style="color: var(--text-primary);">{{ __('linscarbon.company.partnership.accountants_title') }}</h3>
                <p class="mb-4" style="color: var(--text-secondary);">{{ __('linscarbon.company.partnership.accountants_desc') }}</p>
                <ul class="space-y-2 text-sm" style="color: var(--text-secondary);">
                    <li class="flex items-center gap-2">
                        <svg class="w-4 h-4 flex-shrink-0" style="color: var(--accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        {{ __('linscarbon.company.partnership.accountants_benefit1') }}
                    </li>
                    <li class="flex items-center gap-2">
                        <svg class="w-4 h-4 flex-shrink-0" style="color: var(--accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        {{ __('linscarbon.company.partnership.accountants_benefit2') }}
                    </li>
                    <li class="flex items-center gap-2">
                        <svg class="w-4 h-4 flex-shrink-0" style="color: var(--accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        {{ __('linscarbon.company.partnership.accountants_benefit3') }}
                    </li>
                </ul>
            </div>
        </div>

        {{-- CTA --}}
        <div class="text-center p-12 rounded-2xl" style="background-color: var(--accent-light);">
            <h2 class="text-2xl font-semibold mb-4" style="color: var(--text-primary);">{{ __('linscarbon.company.partnership.cta_title') }}</h2>
            <p class="mb-6" style="color: var(--text-secondary);">{{ __('linscarbon.company.partnership.cta_desc') }}</p>
            <a href="{{ route('contact') }}?subject=partnership" class="inline-flex items-center gap-2 px-6 py-3 rounded-lg font-medium text-white" style="background-color: var(--accent);">
                {{ __('linscarbon.company.partnership.cta_button') }}
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
            </a>
        </div>
    </div>
</section>
@endsection
