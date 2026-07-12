@extends('layouts.marketing')

@section('title', __('linscarbon.legal.cgu.title') . ' - LinsCarbon')
@section('description', __('linscarbon.legal.cgu.meta_description'))

@section('content')
<section class="pt-32 pb-20" style="background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);">
    <div class="max-w-4xl mx-auto px-6">
        <div class="mb-12">
            <p class="text-sm font-medium mb-4" style="color: var(--accent);">{{ __('linscarbon.legal.label') }}</p>
            <h1 class="text-4xl font-semibold mb-6" style="color: var(--text-primary); letter-spacing: -0.025em;">
                {{ __('linscarbon.legal.cgu.title') }}
            </h1>
            <p class="text-sm" style="color: var(--text-muted);">{{ __('linscarbon.legal.last_updated') }}</p>
        </div>

        <div class="prose prose-lg max-w-none" style="color: var(--text-secondary);">

            <h2 style="color: var(--text-primary);">{{ __('linscarbon.legal.cgu.article1_title') }}</h2>
            <ul>
                <li><strong>{{ __('linscarbon.legal.cgu.def_platform') }}</strong></li>
                <li><strong>{{ __('linscarbon.legal.cgu.def_user') }}</strong></li>
                <li><strong>{{ __('linscarbon.legal.cgu.def_account') }}</strong></li>
                <li><strong>{{ __('linscarbon.legal.cgu.def_organization') }}</strong></li>
                <li><strong>{{ __('linscarbon.legal.cgu.def_assessment') }}</strong></li>
            </ul>

            <h2 style="color: var(--text-primary);">{{ __('linscarbon.legal.cgu.article2_title') }}</h2>
            <p>{{ __('linscarbon.legal.cgu.article2_text') }}</p>

            <h2 style="color: var(--text-primary);">{{ __('linscarbon.legal.cgu.article3_title') }}</h2>
            <h3 style="color: var(--text-primary);">{{ __('linscarbon.legal.cgu.article3_1_title') }}</h3>
            <p>{{ __('linscarbon.legal.cgu.article3_1_text') }}</p>

            <h3 style="color: var(--text-primary);">{{ __('linscarbon.legal.cgu.article3_2_title') }}</h3>
            <p>{{ __('linscarbon.legal.cgu.article3_2_text') }}</p>

            <h3 style="color: var(--text-primary);">{{ __('linscarbon.legal.cgu.article3_3_title') }}</h3>
            <p>{{ __('linscarbon.legal.cgu.article3_3_text') }}</p>
            <ul>
                <li><strong>{{ __('linscarbon.legal.cgu.role_owner') }}</strong></li>
                <li><strong>{{ __('linscarbon.legal.cgu.role_admin') }}</strong></li>
                <li><strong>{{ __('linscarbon.legal.cgu.role_member') }}</strong></li>
                <li><strong>{{ __('linscarbon.legal.cgu.role_reader') }}</strong></li>
            </ul>

            <h2 style="color: var(--text-primary);">{{ __('linscarbon.legal.cgu.article4_title') }}</h2>
            <h3 style="color: var(--text-primary);">{{ __('linscarbon.legal.cgu.article4_1_title') }}</h3>
            <p>{{ __('linscarbon.legal.cgu.article4_1_text') }}</p>
            <ul>
                <li>{{ __('linscarbon.legal.cgu.use_ghg') }}</li>
                <li>{{ __('linscarbon.legal.cgu.use_track') }}</li>
                <li>{{ __('linscarbon.legal.cgu.use_plan') }}</li>
                <li>{{ __('linscarbon.legal.cgu.use_report') }}</li>
            </ul>

            <h3 style="color: var(--text-primary);">{{ __('linscarbon.legal.cgu.article4_2_title') }}</h3>
            <p>{{ __('linscarbon.legal.cgu.article4_2_text') }}</p>
            <ul>
                <li>{{ __('linscarbon.legal.cgu.forbidden_illegal') }}</li>
                <li>{{ __('linscarbon.legal.cgu.forbidden_security') }}</li>
                <li>{{ __('linscarbon.legal.cgu.forbidden_scraping') }}</li>
                <li>{{ __('linscarbon.legal.cgu.forbidden_share') }}</li>
                <li>{{ __('linscarbon.legal.cgu.forbidden_false') }}</li>
                <li>{{ __('linscarbon.legal.cgu.forbidden_resell') }}</li>
            </ul>

            <h2 style="color: var(--text-primary);">{{ __('linscarbon.legal.cgu.article5_title') }}</h2>
            <h3 style="color: var(--text-primary);">{{ __('linscarbon.legal.cgu.article5_1_title') }}</h3>
            <p>{{ __('linscarbon.legal.cgu.article5_1_text') }}</p>

            <h3 style="color: var(--text-primary);">{{ __('linscarbon.legal.cgu.article5_2_title') }}</h3>
            <p>{{ __('linscarbon.legal.cgu.article5_2_text') }}</p>

            <h3 style="color: var(--text-primary);">{{ __('linscarbon.legal.cgu.article5_3_title') }}</h3>
            <p>{{ __('linscarbon.legal.cgu.article5_3_text') }}</p>

            <h2 style="color: var(--text-primary);">{{ __('linscarbon.legal.cgu.article6_title') }}</h2>
            <p>{{ __('linscarbon.legal.cgu.article6_text') }}</p>

            <h2 style="color: var(--text-primary);">{{ __('linscarbon.legal.cgu.article7_title') }}</h2>
            <p>{{ __('linscarbon.legal.cgu.article7_text') }}</p>

            <h2 style="color: var(--text-primary);">{{ __('linscarbon.legal.cgu.article8_title') }}</h2>
            <p>{{ __('linscarbon.legal.cgu.article8_text') }}</p>

            <h2 style="color: var(--text-primary);">{{ __('linscarbon.legal.cgu.article9_title') }}</h2>
            <p>{{ __('linscarbon.legal.cgu.article9_text') }}</p>

            <h2 style="color: var(--text-primary);">{{ __('linscarbon.legal.cgu.article10_title') }}</h2>
            <p>{{ __('linscarbon.legal.cgu.article10_text') }}</p>

            <h2 style="color: var(--text-primary);">{{ __('linscarbon.legal.cgu.article11_title') }}</h2>
            <p>{{ __('linscarbon.legal.cgu.article11_text') }}</p>

            <h2 style="color: var(--text-primary);">{{ __('linscarbon.legal.cgu.article12_title') }}</h2>
            <p>{{ __('linscarbon.legal.cgu.article12_text') }} <a href="mailto:legal@linscarbon.de" style="color: var(--accent);">legal@linscarbon.de</a></p>

        </div>
    </div>
</section>
@endsection
