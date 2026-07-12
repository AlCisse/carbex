@extends('layouts.marketing')

@section('title', __('linscarbon.legal.privacy.title') . ' - LinsCarbon')
@section('description', __('linscarbon.legal.privacy.meta_description'))

@section('content')
<section class="pt-32 pb-20" style="background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);">
    <div class="max-w-4xl mx-auto px-6">
        <div class="mb-12">
            <p class="text-sm font-medium mb-4" style="color: var(--accent);">{{ __('linscarbon.legal.label') }}</p>
            <h1 class="text-4xl font-semibold mb-6" style="color: var(--text-primary); letter-spacing: -0.025em;">
                {{ __('linscarbon.legal.privacy.title') }}
            </h1>
            <p class="text-sm" style="color: var(--text-muted);">{{ __('linscarbon.legal.last_updated') }}</p>
        </div>

        <div class="prose prose-lg max-w-none" style="color: var(--text-secondary);">

            {{-- Introduction --}}
            <p>{{ __('linscarbon.legal.privacy.intro') }}</p>

            {{-- Article 1: Responsable du traitement --}}
            <h2 style="color: var(--text-primary);">{{ __('linscarbon.legal.privacy.article1_title') }}</h2>
            <p>{{ __('linscarbon.legal.privacy.article1_text') }}</p>
            <ul>
                <li><strong>{{ __('linscarbon.legal.privacy.company_name') }}</strong></li>
                <li><strong>{{ __('linscarbon.legal.privacy.company_address') }}</strong></li>
                <li><strong>{{ __('linscarbon.legal.privacy.company_email') }}</strong></li>
                <li><strong>{{ __('linscarbon.legal.privacy.dpo_contact') }}</strong></li>
            </ul>

            {{-- Article 2: Données collectées --}}
            <h2 style="color: var(--text-primary);">{{ __('linscarbon.legal.privacy.article2_title') }}</h2>

            <h3 style="color: var(--text-primary);">{{ __('linscarbon.legal.privacy.article2_1_title') }}</h3>
            <p>{{ __('linscarbon.legal.privacy.article2_1_text') }}</p>
            <ul>
                <li>{{ __('linscarbon.legal.privacy.data_identity') }}</li>
                <li>{{ __('linscarbon.legal.privacy.data_contact') }}</li>
                <li>{{ __('linscarbon.legal.privacy.data_professional') }}</li>
                <li>{{ __('linscarbon.legal.privacy.data_connection') }}</li>
            </ul>

            <h3 style="color: var(--text-primary);">{{ __('linscarbon.legal.privacy.article2_2_title') }}</h3>
            <p>{{ __('linscarbon.legal.privacy.article2_2_text') }}</p>
            <ul>
                <li>{{ __('linscarbon.legal.privacy.data_org_info') }}</li>
                <li>{{ __('linscarbon.legal.privacy.data_financial') }}</li>
                <li>{{ __('linscarbon.legal.privacy.data_energy') }}</li>
                <li>{{ __('linscarbon.legal.privacy.data_emissions') }}</li>
            </ul>

            <h3 style="color: var(--text-primary);">{{ __('linscarbon.legal.privacy.article2_3_title') }}</h3>
            <p>{{ __('linscarbon.legal.privacy.article2_3_text') }}</p>
            <ul>
                <li>{{ __('linscarbon.legal.privacy.data_bank_transactions') }}</li>
                <li>{{ __('linscarbon.legal.privacy.data_bank_note') }}</li>
            </ul>

            {{-- Article 3: Finalités du traitement --}}
            <h2 style="color: var(--text-primary);">{{ __('linscarbon.legal.privacy.article3_title') }}</h2>
            <p>{{ __('linscarbon.legal.privacy.article3_text') }}</p>
            <ul>
                <li>{{ __('linscarbon.legal.privacy.purpose_account') }}</li>
                <li>{{ __('linscarbon.legal.privacy.purpose_carbon') }}</li>
                <li>{{ __('linscarbon.legal.privacy.purpose_reports') }}</li>
                <li>{{ __('linscarbon.legal.privacy.purpose_ai') }}</li>
                <li>{{ __('linscarbon.legal.privacy.purpose_billing') }}</li>
                <li>{{ __('linscarbon.legal.privacy.purpose_support') }}</li>
                <li>{{ __('linscarbon.legal.privacy.purpose_improve') }}</li>
                <li>{{ __('linscarbon.legal.privacy.purpose_legal') }}</li>
            </ul>

            {{-- Article 4: Bases légales --}}
            <h2 style="color: var(--text-primary);">{{ __('linscarbon.legal.privacy.article4_title') }}</h2>
            <p>{{ __('linscarbon.legal.privacy.article4_text') }}</p>
            <ul>
                <li><strong>{{ __('linscarbon.legal.privacy.legal_contract') }}</strong></li>
                <li><strong>{{ __('linscarbon.legal.privacy.legal_consent') }}</strong></li>
                <li><strong>{{ __('linscarbon.legal.privacy.legal_interest') }}</strong></li>
                <li><strong>{{ __('linscarbon.legal.privacy.legal_obligation') }}</strong></li>
            </ul>

            {{-- Article 5: Destinataires --}}
            <h2 style="color: var(--text-primary);">{{ __('linscarbon.legal.privacy.article5_title') }}</h2>
            <p>{{ __('linscarbon.legal.privacy.article5_text') }}</p>
            <ul>
                <li>{{ __('linscarbon.legal.privacy.recipient_team') }}</li>
                <li>{{ __('linscarbon.legal.privacy.recipient_hosting') }}</li>
                <li>{{ __('linscarbon.legal.privacy.recipient_payment') }}</li>
                <li>{{ __('linscarbon.legal.privacy.recipient_banking') }}</li>
                <li>{{ __('linscarbon.legal.privacy.recipient_ai') }}</li>
                <li>{{ __('linscarbon.legal.privacy.recipient_analytics') }}</li>
            </ul>
            <p>{{ __('linscarbon.legal.privacy.recipient_note') }}</p>

            {{-- Article 6: Transferts internationaux --}}
            <h2 style="color: var(--text-primary);">{{ __('linscarbon.legal.privacy.article6_title') }}</h2>
            <p>{{ __('linscarbon.legal.privacy.article6_text') }}</p>

            {{-- Article 7: Durée de conservation --}}
            <h2 style="color: var(--text-primary);">{{ __('linscarbon.legal.privacy.article7_title') }}</h2>
            <p>{{ __('linscarbon.legal.privacy.article7_text') }}</p>
            <ul>
                <li>{{ __('linscarbon.legal.privacy.retention_account') }}</li>
                <li>{{ __('linscarbon.legal.privacy.retention_carbon') }}</li>
                <li>{{ __('linscarbon.legal.privacy.retention_billing') }}</li>
                <li>{{ __('linscarbon.legal.privacy.retention_logs') }}</li>
                <li>{{ __('linscarbon.legal.privacy.retention_cookies') }}</li>
            </ul>

            {{-- Article 8: Vos droits --}}
            <h2 style="color: var(--text-primary);">{{ __('linscarbon.legal.privacy.article8_title') }}</h2>
            <p>{{ __('linscarbon.legal.privacy.article8_text') }}</p>
            <ul>
                <li><strong>{{ __('linscarbon.legal.privacy.right_access') }}</strong></li>
                <li><strong>{{ __('linscarbon.legal.privacy.right_rectification') }}</strong></li>
                <li><strong>{{ __('linscarbon.legal.privacy.right_erasure') }}</strong></li>
                <li><strong>{{ __('linscarbon.legal.privacy.right_portability') }}</strong></li>
                <li><strong>{{ __('linscarbon.legal.privacy.right_restriction') }}</strong></li>
                <li><strong>{{ __('linscarbon.legal.privacy.right_objection') }}</strong></li>
                <li><strong>{{ __('linscarbon.legal.privacy.right_withdraw') }}</strong></li>
            </ul>
            <p>{{ __('linscarbon.legal.privacy.rights_exercise') }} <a href="mailto:privacy@linscarbon.app" style="color: var(--accent);">privacy@linscarbon.app</a></p>
            <p>{{ __('linscarbon.legal.privacy.rights_authority') }}</p>

            {{-- Article 9: Cookies --}}
            <h2 style="color: var(--text-primary);">{{ __('linscarbon.legal.privacy.article9_title') }}</h2>
            <p>{{ __('linscarbon.legal.privacy.article9_text') }}</p>

            <h3 style="color: var(--text-primary);">{{ __('linscarbon.legal.privacy.cookies_essential_title') }}</h3>
            <p>{{ __('linscarbon.legal.privacy.cookies_essential_text') }}</p>

            <h3 style="color: var(--text-primary);">{{ __('linscarbon.legal.privacy.cookies_analytics_title') }}</h3>
            <p>{{ __('linscarbon.legal.privacy.cookies_analytics_text') }}</p>

            <h3 style="color: var(--text-primary);">{{ __('linscarbon.legal.privacy.cookies_preferences_title') }}</h3>
            <p>{{ __('linscarbon.legal.privacy.cookies_preferences_text') }}</p>

            {{-- Article 10: Sécurité --}}
            <h2 style="color: var(--text-primary);">{{ __('linscarbon.legal.privacy.article10_title') }}</h2>
            <p>{{ __('linscarbon.legal.privacy.article10_text') }}</p>
            <ul>
                <li>{{ __('linscarbon.legal.privacy.security_encryption') }}</li>
                <li>{{ __('linscarbon.legal.privacy.security_access') }}</li>
                <li>{{ __('linscarbon.legal.privacy.security_audit') }}</li>
                <li>{{ __('linscarbon.legal.privacy.security_backup') }}</li>
                <li>{{ __('linscarbon.legal.privacy.security_hosting') }}</li>
            </ul>

            {{-- Article 11: Modifications --}}
            <h2 style="color: var(--text-primary);">{{ __('linscarbon.legal.privacy.article11_title') }}</h2>
            <p>{{ __('linscarbon.legal.privacy.article11_text') }}</p>

            {{-- Article 12: Contact --}}
            <h2 style="color: var(--text-primary);">{{ __('linscarbon.legal.privacy.article12_title') }}</h2>
            <p>{{ __('linscarbon.legal.privacy.article12_text') }} <a href="mailto:privacy@linscarbon.app" style="color: var(--accent);">privacy@linscarbon.app</a></p>

        </div>
    </div>
</section>
@endsection
