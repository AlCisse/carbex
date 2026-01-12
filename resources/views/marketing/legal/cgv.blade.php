@extends('layouts.marketing')

@section('title', __('carbex.legal.cgv.title') . ' - Carbex')
@section('description', __('carbex.legal.cgv.meta_description'))

@section('content')
<section class="pt-32 pb-20" style="background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);">
    <div class="max-w-4xl mx-auto px-6">
        <div class="mb-12">
            <p class="text-sm font-medium mb-4" style="color: var(--accent);">{{ __('carbex.legal.label') }}</p>
            <h1 class="text-4xl font-semibold mb-6" style="color: var(--text-primary); letter-spacing: -0.025em;">
                {{ __('carbex.legal.cgv.title') }}
            </h1>
            <p class="text-sm" style="color: var(--text-muted);">{{ __('carbex.legal.last_updated') }}</p>
        </div>

        <div class="prose prose-lg max-w-none" style="color: var(--text-secondary);">

            <h2 style="color: var(--text-primary);">{{ __('carbex.legal.cgv.article1_title') }}</h2>
            <p>{{ __('carbex.legal.cgv.article1_text1') }}</p>
            <p>{{ __('carbex.legal.cgv.article1_text2') }}</p>

            <h2 style="color: var(--text-primary);">{{ __('carbex.legal.cgv.article2_title') }}</h2>
            <p>{{ __('carbex.legal.cgv.article2_text') }}</p>
            <ul>
                <li>{{ __('carbex.legal.cgv.service_assessment') }}</li>
                <li>{{ __('carbex.legal.cgv.service_factors') }}</li>
                <li>{{ __('carbex.legal.cgv.service_dashboard') }}</li>
                <li>{{ __('carbex.legal.cgv.service_plan') }}</li>
                <li>{{ __('carbex.legal.cgv.service_report') }}</li>
                <li>{{ __('carbex.legal.cgv.service_ai') }}</li>
            </ul>

            <h2 style="color: var(--text-primary);">{{ __('carbex.legal.cgv.article3_title') }}</h2>
            <h3 style="color: var(--text-primary);">{{ __('carbex.legal.cgv.article3_1_title') }}</h3>
            <p>{{ __('carbex.legal.cgv.article3_1_text') }}</p>
            <ul>
                <li><strong>{{ __('carbex.legal.cgv.price_trial') }}</strong></li>
                <li><strong>{{ __('carbex.legal.cgv.price_premium') }}</strong></li>
                <li><strong>{{ __('carbex.legal.cgv.price_advanced') }}</strong></li>
                <li><strong>{{ __('carbex.legal.cgv.price_enterprise') }}</strong></li>
            </ul>

            <h3 style="color: var(--text-primary);">{{ __('carbex.legal.cgv.article3_2_title') }}</h3>
            <p>{{ __('carbex.legal.cgv.article3_2_text') }}</p>
            <p>{{ __('carbex.legal.cgv.article3_2_vat') }}</p>

            <h2 style="color: var(--text-primary);">{{ __('carbex.legal.cgv.article4_title') }}</h2>
            <h3 style="color: var(--text-primary);">{{ __('carbex.legal.cgv.article4_1_title') }}</h3>
            <p>{{ __('carbex.legal.cgv.article4_1_text') }}</p>

            <h3 style="color: var(--text-primary);">{{ __('carbex.legal.cgv.article4_2_title') }}</h3>
            <p>{{ __('carbex.legal.cgv.article4_2_text') }}</p>

            <h2 style="color: var(--text-primary);">{{ __('carbex.legal.cgv.article5_title') }}</h2>
            <p>{{ __('carbex.legal.cgv.article5_text') }}</p>
            <ul>
                <li>{{ __('carbex.legal.cgv.obligation_access') }}</li>
                <li>{{ __('carbex.legal.cgv.obligation_security') }}</li>
                <li>{{ __('carbex.legal.cgv.obligation_support') }}</li>
                <li>{{ __('carbex.legal.cgv.obligation_update') }}</li>
            </ul>

            <h2 style="color: var(--text-primary);">{{ __('carbex.legal.cgv.article6_title') }}</h2>
            <p>{{ __('carbex.legal.cgv.article6_text') }}</p>
            <ul>
                <li>{{ __('carbex.legal.cgv.client_accurate') }}</li>
                <li>{{ __('carbex.legal.cgv.client_credentials') }}</li>
                <li>{{ __('carbex.legal.cgv.client_terms') }}</li>
                <li>{{ __('carbex.legal.cgv.client_payment') }}</li>
            </ul>

            <h2 style="color: var(--text-primary);">{{ __('carbex.legal.cgv.article7_title') }}</h2>
            <p>{{ __('carbex.legal.cgv.article7_text') }}</p>

            <h2 style="color: var(--text-primary);">{{ __('carbex.legal.cgv.article8_title') }}</h2>
            <p>{{ __('carbex.legal.cgv.article8_text1') }}</p>
            <p>{{ __('carbex.legal.cgv.article8_text2') }}</p>

            <h2 style="color: var(--text-primary);">{{ __('carbex.legal.cgv.article9_title') }}</h2>
            <p>
                {{ __('carbex.legal.cgv.article9_text') }}
                <a href="{{ route('mentions-legales') }}" style="color: var(--accent);">{{ __('carbex.legal.cgv.privacy_policy') }}</a>.
            </p>

            <h2 style="color: var(--text-primary);">{{ __('carbex.legal.cgv.article10_title') }}</h2>
            <p>{{ __('carbex.legal.cgv.article10_text') }}</p>

            <h2 style="color: var(--text-primary);">{{ __('carbex.legal.cgv.article11_title') }}</h2>
            <p>{{ __('carbex.legal.cgv.article11_text') }}</p>

        </div>
    </div>
</section>
@endsection
