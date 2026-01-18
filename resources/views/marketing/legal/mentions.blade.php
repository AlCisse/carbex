@extends('layouts.marketing')

@section('title', __('linscarbon.legal.mentions.title') . ' - LinsCarbon')
@section('description', __('linscarbon.legal.mentions.meta_description'))

@section('content')
<section class="pt-32 pb-20" style="background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);">
    <div class="max-w-4xl mx-auto px-6">
        <div class="mb-12">
            <p class="text-sm font-medium mb-4" style="color: var(--accent);">{{ __('linscarbon.legal.label') }}</p>
            <h1 class="text-4xl font-semibold mb-6" style="color: var(--text-primary); letter-spacing: -0.025em;">
                {{ __('linscarbon.legal.mentions.title') }}
            </h1>
            <p class="text-sm" style="color: var(--text-muted);">{{ __('linscarbon.legal.last_updated') }}</p>
        </div>

        <div class="prose prose-lg max-w-none" style="color: var(--text-secondary);">

            <h2 style="color: var(--text-primary);">{{ __('linscarbon.legal.mentions.section1_title') }}</h2>
            <p>{{ __('linscarbon.legal.mentions.site_edited_by') }}</p>
            <div class="bg-gray-50 p-6 rounded-xl my-6">
                <p class="mb-2"><strong>{{ __('linscarbon.legal.mentions.company_name') }}</strong></p>
                <p class="mb-2">{{ __('linscarbon.legal.mentions.company_type') }}</p>
                <p class="mb-2">{{ __('linscarbon.legal.mentions.address') }}</p>
                <p class="mb-2">{{ __('linscarbon.legal.mentions.register') }}</p>
                <p class="mb-2">{{ __('linscarbon.legal.mentions.tax_id') }}</p>
                <p class="mb-2">{{ __('linscarbon.legal.mentions.vat_id') }}</p>
                <p class="mb-2">{{ __('linscarbon.legal.mentions.director') }}</p>
                <p>{{ __('linscarbon.legal.mentions.email') }} : <a href="mailto:contact@linscarbon.de" style="color: var(--accent);">contact@linscarbon.de</a></p>
            </div>

            <h2 style="color: var(--text-primary);">{{ __('linscarbon.legal.mentions.section2_title') }}</h2>
            <p>{{ __('linscarbon.legal.mentions.hosted_by') }}</p>
            <div class="bg-gray-50 p-6 rounded-xl my-6">
                <p class="mb-2"><strong>{{ __('linscarbon.legal.mentions.hosting_name') }}</strong></p>
                <p class="mb-2">{{ __('linscarbon.legal.mentions.hosting_address') }}</p>
                <p>{{ __('linscarbon.legal.mentions.website') }} : <a href="https://www.hetzner.com" target="_blank" rel="noopener" style="color: var(--accent);">www.hetzner.com</a></p>
            </div>

            <h2 style="color: var(--text-primary);">{{ __('linscarbon.legal.mentions.section3_title') }}</h2>

            <h3 style="color: var(--text-primary);">{{ __('linscarbon.legal.mentions.section3_1_title') }}</h3>
            <p>{{ __('linscarbon.legal.mentions.section3_1_text') }}</p>

            <h3 style="color: var(--text-primary);">{{ __('linscarbon.legal.mentions.section3_2_title') }}</h3>
            <p>{{ __('linscarbon.legal.mentions.section3_2_text') }}</p>
            <ul>
                <li><strong>{{ __('linscarbon.legal.mentions.data_id') }}</strong></li>
                <li><strong>{{ __('linscarbon.legal.mentions.data_company') }}</strong></li>
                <li><strong>{{ __('linscarbon.legal.mentions.data_connection') }}</strong></li>
                <li><strong>{{ __('linscarbon.legal.mentions.data_business') }}</strong></li>
            </ul>

            <h3 style="color: var(--text-primary);">{{ __('linscarbon.legal.mentions.section3_3_title') }}</h3>
            <p>{{ __('linscarbon.legal.mentions.section3_3_text') }}</p>
            <ul>
                <li>{{ __('linscarbon.legal.mentions.purpose_service') }}</li>
                <li>{{ __('linscarbon.legal.mentions.purpose_account') }}</li>
                <li>{{ __('linscarbon.legal.mentions.purpose_communication') }}</li>
                <li>{{ __('linscarbon.legal.mentions.purpose_improve') }}</li>
                <li>{{ __('linscarbon.legal.mentions.purpose_legal') }}</li>
            </ul>

            <h3 style="color: var(--text-primary);">{{ __('linscarbon.legal.mentions.section3_4_title') }}</h3>
            <p>{{ __('linscarbon.legal.mentions.section3_4_text') }}</p>
            <ul>
                <li>{{ __('linscarbon.legal.mentions.legal_contract') }}</li>
                <li>{{ __('linscarbon.legal.mentions.legal_legitimate') }}</li>
                <li>{{ __('linscarbon.legal.mentions.legal_consent') }}</li>
                <li>{{ __('linscarbon.legal.mentions.legal_obligation') }}</li>
            </ul>

            <h3 style="color: var(--text-primary);">{{ __('linscarbon.legal.mentions.section3_5_title') }}</h3>
            <ul>
                <li><strong>{{ __('linscarbon.legal.mentions.account_data') }}</strong></li>
                <li><strong>{{ __('linscarbon.legal.mentions.billing_data') }}</strong></li>
                <li><strong>{{ __('linscarbon.legal.mentions.connection_logs') }}</strong></li>
                <li><strong>{{ __('linscarbon.legal.mentions.business_data') }}</strong></li>
            </ul>

            <h3 style="color: var(--text-primary);">{{ __('linscarbon.legal.mentions.section3_6_title') }}</h3>
            <p>{{ __('linscarbon.legal.mentions.section3_6_text') }}</p>
            <ul>
                <li><strong>{{ __('linscarbon.legal.mentions.recipient_stripe') }}</strong></li>
                <li><strong>{{ __('linscarbon.legal.mentions.recipient_hosting') }}</strong></li>
                <li><strong>{{ __('linscarbon.legal.mentions.recipient_ai') }}</strong></li>
                <li><strong>{{ __('linscarbon.legal.mentions.recipient_email') }}</strong></li>
            </ul>
            <p>{{ __('linscarbon.legal.mentions.no_transfer') }}</p>

            <h3 style="color: var(--text-primary);">{{ __('linscarbon.legal.mentions.section3_7_title') }}</h3>
            <p>{{ __('linscarbon.legal.mentions.section3_7_text') }}</p>
            <ul>
                <li><strong>{{ __('linscarbon.legal.mentions.right_access') }}</strong></li>
                <li><strong>{{ __('linscarbon.legal.mentions.right_rectification') }}</strong></li>
                <li><strong>{{ __('linscarbon.legal.mentions.right_erasure') }}</strong></li>
                <li><strong>{{ __('linscarbon.legal.mentions.right_portability') }}</strong></li>
                <li><strong>{{ __('linscarbon.legal.mentions.right_objection') }}</strong></li>
                <li><strong>{{ __('linscarbon.legal.mentions.right_restriction') }}</strong></li>
            </ul>
            <p>{{ __('linscarbon.legal.mentions.contact_dpo') }} <a href="mailto:dpo@linscarbon.de" style="color: var(--accent);">dpo@linscarbon.de</a></p>
            <p>{{ __('linscarbon.legal.mentions.supervisory_authority') }} <a href="https://www.bfdi.bund.de" target="_blank" rel="noopener" style="color: var(--accent);">www.bfdi.bund.de</a></p>

            <h2 style="color: var(--text-primary);">{{ __('linscarbon.legal.mentions.section4_title') }}</h2>

            <h3 style="color: var(--text-primary);">{{ __('linscarbon.legal.mentions.section4_1_title') }}</h3>
            <p>{{ __('linscarbon.legal.mentions.section4_1_text') }}</p>
            <ul>
                <li><strong>{{ __('linscarbon.legal.mentions.cookie_session') }}</strong></li>
                <li><strong>{{ __('linscarbon.legal.mentions.cookie_csrf') }}</strong></li>
                <li><strong>{{ __('linscarbon.legal.mentions.cookie_preferences') }}</strong></li>
            </ul>

            <h3 style="color: var(--text-primary);">{{ __('linscarbon.legal.mentions.section4_2_title') }}</h3>
            <p>{{ __('linscarbon.legal.mentions.section4_2_text') }}</p>

            <h3 style="color: var(--text-primary);">{{ __('linscarbon.legal.mentions.section4_3_title') }}</h3>
            <p>{{ __('linscarbon.legal.mentions.section4_3_text') }}</p>

            <h2 style="color: var(--text-primary);">{{ __('linscarbon.legal.mentions.section5_title') }}</h2>
            <p>{{ __('linscarbon.legal.mentions.section5_text1') }}</p>
            <p>{{ __('linscarbon.legal.mentions.section5_text2') }}</p>

            <h2 style="color: var(--text-primary);">{{ __('linscarbon.legal.mentions.section6_title') }}</h2>
            <p>{{ __('linscarbon.legal.mentions.section6_text') }}</p>

            <h2 style="color: var(--text-primary);">{{ __('linscarbon.legal.mentions.section7_title') }}</h2>
            <p>{{ __('linscarbon.legal.mentions.section7_text') }}</p>

            <h2 style="color: var(--text-primary);">{{ __('linscarbon.legal.mentions.section8_title') }}</h2>

            <h3 style="color: var(--text-primary);">{{ __('linscarbon.legal.mentions.section8_1_title') }}</h3>
            <p>{{ __('linscarbon.legal.mentions.section8_1_text') }}</p>

            <h3 style="color: var(--text-primary);">{{ __('linscarbon.legal.mentions.section8_2_title') }}</h3>
            <p>{{ __('linscarbon.legal.mentions.section8_2_text') }}</p>
            <div class="bg-gray-50 p-6 rounded-xl my-6">
                <p class="mb-2"><strong>{{ __('linscarbon.legal.mentions.evz_name') }}</strong></p>
                <p class="mb-2">{{ __('linscarbon.legal.mentions.evz_address') }}</p>
                <p class="mb-2">{{ __('linscarbon.legal.mentions.evz_website') }} : <a href="https://www.evz.de" target="_blank" rel="noopener" style="color: var(--accent);">www.evz.de</a></p>
                <p>{{ __('linscarbon.legal.mentions.evz_phone') }}</p>
            </div>

            <h3 style="color: var(--text-primary);">{{ __('linscarbon.legal.mentions.section8_3_title') }}</h3>
            <p class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-r-lg">{{ __('linscarbon.legal.mentions.section8_3_text') }}</p>

            <h2 style="color: var(--text-primary);">{{ __('linscarbon.legal.mentions.section9_title') }}</h2>
            <p>{{ __('linscarbon.legal.mentions.section9_text') }}</p>

            <h2 style="color: var(--text-primary);">{{ __('linscarbon.legal.mentions.section10_title') }}</h2>
            <p>{{ __('linscarbon.legal.mentions.section10_text') }}</p>
            <ul>
                <li>{{ __('linscarbon.legal.mentions.contact_legal') }} : <a href="mailto:legal@linscarbon.de" style="color: var(--accent);">legal@linscarbon.de</a></li>
                <li>{{ __('linscarbon.legal.mentions.contact_dpo_label') }} : <a href="mailto:dpo@linscarbon.de" style="color: var(--accent);">dpo@linscarbon.de</a></li>
                <li>{{ __('linscarbon.legal.mentions.contact_mail') }}</li>
            </ul>

        </div>
    </div>
</section>
@endsection
