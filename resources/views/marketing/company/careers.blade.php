@extends('layouts.marketing')

@section('title', __('linscarbon.company.careers.title') . ' - LinsCarbon')
@section('description', __('linscarbon.company.careers.meta_description'))

@section('content')
<section class="pt-32 pb-20" style="background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);">
    <div class="max-w-4xl mx-auto px-6">
        <div class="mb-12 text-center">
            <p class="text-sm font-medium mb-4" style="color: var(--accent);">{{ __('linscarbon.company.label') }}</p>
            <h1 class="text-4xl font-semibold mb-6" style="color: var(--text-primary); letter-spacing: -0.025em;">
                {{ __('linscarbon.company.careers.title') }}
            </h1>
            <p class="text-lg" style="color: var(--text-secondary);">
                {{ __('linscarbon.company.careers.subtitle') }}
            </p>
        </div>

        {{-- Why Join Us --}}
        <div class="mb-16">
            <h2 class="text-2xl font-semibold mb-8 text-center" style="color: var(--text-primary);">{{ __('linscarbon.company.careers.why_join') }}</h2>
            <div class="grid md:grid-cols-3 gap-6">
                <div class="p-6 rounded-xl text-center" style="background-color: var(--bg-card); border: 1px solid var(--border);">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center mx-auto mb-4" style="background-color: var(--accent-light);">
                        <svg class="w-6 h-6" style="color: var(--accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="font-semibold mb-2" style="color: var(--text-primary);">{{ __('linscarbon.company.careers.value1_title') }}</h3>
                    <p class="text-sm" style="color: var(--text-secondary);">{{ __('linscarbon.company.careers.value1_desc') }}</p>
                </div>
                <div class="p-6 rounded-xl text-center" style="background-color: var(--bg-card); border: 1px solid var(--border);">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center mx-auto mb-4" style="background-color: var(--accent-light);">
                        <svg class="w-6 h-6" style="color: var(--accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <h3 class="font-semibold mb-2" style="color: var(--text-primary);">{{ __('linscarbon.company.careers.value2_title') }}</h3>
                    <p class="text-sm" style="color: var(--text-secondary);">{{ __('linscarbon.company.careers.value2_desc') }}</p>
                </div>
                <div class="p-6 rounded-xl text-center" style="background-color: var(--bg-card); border: 1px solid var(--border);">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center mx-auto mb-4" style="background-color: var(--accent-light);">
                        <svg class="w-6 h-6" style="color: var(--accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="font-semibold mb-2" style="color: var(--text-primary);">{{ __('linscarbon.company.careers.value3_title') }}</h3>
                    <p class="text-sm" style="color: var(--text-secondary);">{{ __('linscarbon.company.careers.value3_desc') }}</p>
                </div>
            </div>
        </div>

        {{-- Benefits --}}
        <div class="mb-16 p-8 rounded-2xl" style="background-color: var(--bg-card); border: 1px solid var(--border);">
            <h2 class="text-2xl font-semibold mb-6" style="color: var(--text-primary);">{{ __('linscarbon.company.careers.benefits_title') }}</h2>
            <div class="grid md:grid-cols-2 gap-4">
                @foreach(['remote', 'equity', 'learning', 'hardware', 'vacation', 'team'] as $benefit)
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 flex-shrink-0" style="color: var(--accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span style="color: var(--text-secondary);">{{ __('linscarbon.company.careers.benefit_' . $benefit) }}</span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Open Positions --}}
        <div class="mb-16">
            <h2 class="text-2xl font-semibold mb-8" style="color: var(--text-primary);">{{ __('linscarbon.company.careers.open_positions') }}</h2>

            <div class="space-y-4">
                {{-- Position 1 --}}
                <div class="p-6 rounded-xl" style="background-color: var(--bg-card); border: 1px solid var(--border);">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div>
                            <h3 class="font-semibold mb-1" style="color: var(--text-primary);">{{ __('linscarbon.company.careers.job1_title') }}</h3>
                            <div class="flex flex-wrap items-center gap-3 text-sm" style="color: var(--text-muted);">
                                <span class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    {{ __('linscarbon.company.careers.location_remote') }}
                                </span>
                                <span class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    {{ __('linscarbon.company.careers.full_time') }}
                                </span>
                            </div>
                        </div>
                        <a href="{{ route('contact') }}?subject=careers&position=fullstack" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg font-medium text-sm" style="background-color: var(--accent-light); color: var(--accent);">
                            {{ __('linscarbon.company.careers.apply') }}
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                        </a>
                    </div>
                </div>

                {{-- Position 2 --}}
                <div class="p-6 rounded-xl" style="background-color: var(--bg-card); border: 1px solid var(--border);">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div>
                            <h3 class="font-semibold mb-1" style="color: var(--text-primary);">{{ __('linscarbon.company.careers.job2_title') }}</h3>
                            <div class="flex flex-wrap items-center gap-3 text-sm" style="color: var(--text-muted);">
                                <span class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    {{ __('linscarbon.company.careers.location_remote') }}
                                </span>
                                <span class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    {{ __('linscarbon.company.careers.full_time') }}
                                </span>
                            </div>
                        </div>
                        <a href="{{ route('contact') }}?subject=careers&position=carbon" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg font-medium text-sm" style="background-color: var(--accent-light); color: var(--accent);">
                            {{ __('linscarbon.company.careers.apply') }}
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                        </a>
                    </div>
                </div>

                {{-- Position 3 --}}
                <div class="p-6 rounded-xl" style="background-color: var(--bg-card); border: 1px solid var(--border);">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div>
                            <h3 class="font-semibold mb-1" style="color: var(--text-primary);">{{ __('linscarbon.company.careers.job3_title') }}</h3>
                            <div class="flex flex-wrap items-center gap-3 text-sm" style="color: var(--text-muted);">
                                <span class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    {{ __('linscarbon.company.careers.location_remote') }}
                                </span>
                                <span class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    {{ __('linscarbon.company.careers.full_time') }}
                                </span>
                            </div>
                        </div>
                        <a href="{{ route('contact') }}?subject=careers&position=sales" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg font-medium text-sm" style="background-color: var(--accent-light); color: var(--accent);">
                            {{ __('linscarbon.company.careers.apply') }}
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Spontaneous Application --}}
        <div class="text-center p-12 rounded-2xl" style="background-color: var(--accent-light);">
            <h2 class="text-2xl font-semibold mb-4" style="color: var(--text-primary);">{{ __('linscarbon.company.careers.spontaneous_title') }}</h2>
            <p class="mb-6" style="color: var(--text-secondary);">{{ __('linscarbon.company.careers.spontaneous_desc') }}</p>
            <a href="{{ route('contact') }}?subject=careers" class="inline-flex items-center gap-2 px-6 py-3 rounded-lg font-medium text-white" style="background-color: var(--accent);">
                {{ __('linscarbon.company.careers.spontaneous_button') }}
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
            </a>
        </div>
    </div>
</section>
@endsection
