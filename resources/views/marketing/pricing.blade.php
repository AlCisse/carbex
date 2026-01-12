@extends('layouts.marketing')

@section('title', __('carbex.marketing.pricing.title') . ' - Carbex')
@section('description', __('carbex.marketing.pricing.description'))

@section('content')
<div x-data="{ billingPeriod: 'annual' }">
    <!-- Hero -->
    <section class="pt-32 pb-16" style="background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);">
        <div class="max-w-4xl mx-auto px-6 text-center">
            <h1 class="text-4xl lg:text-5xl font-semibold mb-6" style="color: var(--text-primary); letter-spacing: -0.025em;">
                {{ __('carbex.marketing.pricing.hero_title') }}
            </h1>
            <p class="text-lg mb-10" style="color: var(--text-secondary);">
                {{ __('carbex.marketing.pricing.hero_subtitle') }}
            </p>

            <!-- Billing Toggle -->
            <div class="inline-flex items-center p-1.5 rounded-xl" style="background-color: #f1f5f9;">
                <button
                    @click="billingPeriod = 'monthly'"
                    :class="billingPeriod === 'monthly' ? 'bg-white shadow-sm' : ''"
                    class="px-6 py-2.5 text-sm font-medium rounded-lg transition-all"
                    :style="billingPeriod === 'monthly' ? 'color: var(--text-primary);' : 'color: var(--text-secondary);'"
                >
                    {{ __('carbex.marketing.pricing.monthly') }}
                </button>
                <button
                    @click="billingPeriod = 'annual'"
                    :class="billingPeriod === 'annual' ? 'bg-white shadow-sm' : ''"
                    class="px-6 py-2.5 text-sm font-medium rounded-lg transition-all flex items-center gap-2"
                    :style="billingPeriod === 'annual' ? 'color: var(--text-primary);' : 'color: var(--text-secondary);'"
                >
                    {{ __('carbex.marketing.pricing.annual') }}
                    <span class="px-2 py-0.5 text-xs font-semibold rounded-full" style="background-color: var(--accent-light); color: var(--accent);">-17%</span>
                </button>
            </div>
        </div>
    </section>

    <!-- Pricing Cards -->
    <section class="py-16" style="background-color: var(--bg-primary);">
        <div class="max-w-6xl mx-auto px-6">
            <div class="grid md:grid-cols-3 gap-8">
                <!-- Free Plan -->
                <div class="hover-lift bg-white rounded-2xl p-8 border flex flex-col" style="border-color: var(--border);">
                    <div class="flex-1">
                        <p class="text-sm font-medium mb-2" style="color: var(--text-muted);">{{ __('carbex.marketing.pricing.free_trial') }}</p>
                        <div class="flex items-baseline gap-1 mb-2">
                            <span class="text-5xl font-bold" style="color: var(--text-primary);">0</span>
                            <span class="text-2xl font-semibold" style="color: var(--text-primary);">EUR</span>
                        </div>
                        <p class="text-sm mb-6" style="color: var(--text-muted);">{{ __('carbex.marketing.pricing.trial_duration') }}</p>

                        <p class="text-sm font-medium mb-4" style="color: var(--text-primary);">{{ __('carbex.marketing.pricing.includes') }}</p>
                        <ul class="space-y-3 mb-8">
                            <li class="flex items-start gap-2.5 text-sm" style="color: var(--text-secondary);">
                                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" style="color: var(--accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                {{ __('carbex.marketing.pricing.features.one_user') }}
                            </li>
                            <li class="flex items-start gap-2.5 text-sm" style="color: var(--text-secondary);">
                                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" style="color: var(--accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                {{ __('carbex.marketing.pricing.features.one_site') }}
                            </li>
                            <li class="flex items-start gap-2.5 text-sm" style="color: var(--text-secondary);">
                                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" style="color: var(--accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                {{ __('carbex.marketing.pricing.features.full_access') }}
                            </li>
                            <li class="flex items-start gap-2.5 text-sm" style="color: var(--text-secondary);">
                                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" style="color: var(--accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                {{ __('carbex.marketing.pricing.features.one_report') }}
                            </li>
                            <li class="flex items-start gap-2.5 text-sm" style="color: var(--text-secondary);">
                                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" style="color: var(--accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                {{ __('carbex.marketing.pricing.features.email_support') }}
                            </li>
                        </ul>
                    </div>
                    <a href="{{ route('register') }}" class="block w-full py-3.5 text-center text-sm font-medium rounded-xl border hover:bg-gray-50 transition-colors" style="color: var(--text-primary); border-color: var(--border);">
                        {{ __('carbex.marketing.pricing.start_trial') }}
                    </a>
                </div>

                <!-- Premium Plan -->
                <div class="hover-lift bg-white rounded-2xl p-8 border-2 relative flex flex-col" style="border-color: var(--accent);">
                    <div class="absolute -top-3.5 left-1/2 -translate-x-1/2">
                        <span class="px-4 py-1.5 text-xs font-semibold text-white rounded-full" style="background-color: var(--accent);">{{ __('carbex.marketing.pricing.most_popular') }}</span>
                    </div>

                    <div class="flex-1">
                        <p class="text-sm font-medium mb-2" style="color: var(--text-muted);">Premium</p>
                        <div class="flex items-baseline gap-1 mb-1">
                            <span class="text-5xl font-bold" style="color: var(--text-primary);" x-text="billingPeriod === 'annual' ? '400' : '40'"></span>
                            <span class="text-2xl font-semibold" style="color: var(--text-primary);">EUR</span>
                        </div>
                        <p class="text-sm mb-2" style="color: var(--text-muted);" x-text="billingPeriod === 'annual' ? '{{ __('carbex.marketing.pricing.per_year') }}' : '{{ __('carbex.marketing.pricing.per_month') }}'"></p>
                        <p class="text-xs mb-6" style="color: var(--accent);" x-show="billingPeriod === 'annual'">
                            {{ __('carbex.marketing.pricing.premium_savings') }}
                        </p>
                        <p class="text-xs mb-6" style="color: var(--text-muted);" x-show="billingPeriod === 'monthly'">
                            {{ __('carbex.marketing.pricing.no_commitment') }}
                        </p>

                        <p class="text-sm font-medium mb-4" style="color: var(--text-primary);">{{ __('carbex.marketing.pricing.all_from_trial') }}</p>
                        <ul class="space-y-3 mb-8">
                            <li class="flex items-start gap-2.5 text-sm" style="color: var(--text-secondary);">
                                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" style="color: var(--accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                {{ __('carbex.marketing.pricing.features.five_users') }}
                            </li>
                            <li class="flex items-start gap-2.5 text-sm" style="color: var(--text-secondary);">
                                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" style="color: var(--accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                {{ __('carbex.marketing.pricing.features.three_sites') }}
                            </li>
                            <li class="flex items-start gap-2.5 text-sm" style="color: var(--text-secondary);">
                                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" style="color: var(--accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                {{ __('carbex.marketing.pricing.features.bank_import') }}
                            </li>
                            <li class="flex items-start gap-2.5 text-sm" style="color: var(--text-secondary);">
                                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" style="color: var(--accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                {{ __('carbex.marketing.pricing.features.unlimited_reports') }}
                            </li>
                            <li class="flex items-start gap-2.5 text-sm" style="color: var(--text-secondary);">
                                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" style="color: var(--accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                {{ __('carbex.marketing.pricing.features.ademe_ghg') }}
                            </li>
                            <li class="flex items-start gap-2.5 text-sm" style="color: var(--text-secondary);">
                                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" style="color: var(--accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                {{ __('carbex.marketing.pricing.features.priority_support') }}
                            </li>
                        </ul>
                    </div>
                    <a href="{{ route('register') }}?plan=premium" class="btn-primary block w-full py-3.5 text-center text-sm font-medium text-white rounded-xl" style="background-color: var(--accent);">
                        {{ __('carbex.marketing.pricing.choose_premium') }}
                    </a>
                </div>

                <!-- Advanced Plan -->
                <div class="hover-lift bg-white rounded-2xl p-8 border flex flex-col" style="border-color: var(--border);">
                    <div class="flex-1">
                        <p class="text-sm font-medium mb-2" style="color: var(--text-muted);">{{ __('carbex.marketing.pricing.advanced') }}</p>
                        <div class="flex items-baseline gap-1 mb-1">
                            <span class="text-5xl font-bold" style="color: var(--text-primary);" x-text="billingPeriod === 'annual' ? '1200' : '120'"></span>
                            <span class="text-2xl font-semibold" style="color: var(--text-primary);">EUR</span>
                        </div>
                        <p class="text-sm mb-2" style="color: var(--text-muted);" x-text="billingPeriod === 'annual' ? '{{ __('carbex.marketing.pricing.per_year') }}' : '{{ __('carbex.marketing.pricing.per_month') }}'"></p>
                        <p class="text-xs mb-6" style="color: var(--accent);" x-show="billingPeriod === 'annual'">
                            {{ __('carbex.marketing.pricing.advanced_savings') }}
                        </p>
                        <p class="text-xs mb-6" style="color: var(--text-muted);" x-show="billingPeriod === 'monthly'">
                            {{ __('carbex.marketing.pricing.no_commitment') }}
                        </p>

                        <p class="text-sm font-medium mb-4" style="color: var(--text-primary);">{{ __('carbex.marketing.pricing.all_from_premium') }}</p>
                        <ul class="space-y-3 mb-8">
                            <li class="flex items-start gap-2.5 text-sm" style="color: var(--text-secondary);">
                                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" style="color: var(--accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                {{ __('carbex.marketing.pricing.features.unlimited_users') }}
                            </li>
                            <li class="flex items-start gap-2.5 text-sm" style="color: var(--text-secondary);">
                                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" style="color: var(--accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                {{ __('carbex.marketing.pricing.features.unlimited_sites') }}
                            </li>
                            <li class="flex items-start gap-2.5 text-sm" style="color: var(--text-secondary);">
                                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" style="color: var(--accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                {{ __('carbex.marketing.pricing.features.full_api') }}
                            </li>
                            <li class="flex items-start gap-2.5 text-sm" style="color: var(--text-secondary);">
                                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" style="color: var(--accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                {{ __('carbex.marketing.pricing.features.scope3_suppliers') }}
                            </li>
                            <li class="flex items-start gap-2.5 text-sm" style="color: var(--text-secondary);">
                                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" style="color: var(--accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                {{ __('carbex.marketing.pricing.features.dedicated_support') }}
                            </li>
                            <li class="flex items-start gap-2.5 text-sm" style="color: var(--text-secondary);">
                                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" style="color: var(--accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                {{ __('carbex.marketing.pricing.features.custom_training') }}
                            </li>
                        </ul>
                    </div>
                    <a href="{{ route('register') }}?plan=advanced" class="block w-full py-3.5 text-center text-sm font-medium rounded-xl border hover:bg-gray-50 transition-colors" style="color: var(--text-primary); border-color: var(--border);">
                        {{ __('carbex.marketing.pricing.choose_advanced') }}
                    </a>
                </div>
            </div>

            <!-- Enterprise CTA -->
            <div class="mt-12 p-8 rounded-2xl text-center" style="background: linear-gradient(135deg, #f0fdfa 0%, #ccfbf1 100%);">
                <h3 class="text-xl font-semibold mb-2" style="color: var(--text-primary);">{{ __('carbex.marketing.pricing.enterprise_title') }}</h3>
                <p class="text-sm mb-6" style="color: var(--text-secondary);">{{ __('carbex.marketing.pricing.enterprise_subtitle') }}</p>
                <a href="/contact" class="inline-flex items-center px-6 py-3 text-sm font-medium rounded-xl border-2" style="color: var(--accent); border-color: var(--accent);">
                    {{ __('carbex.marketing.pricing.contact_team') }}
                    <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                </a>
            </div>
        </div>
    </section>

    <!-- Features Comparison -->
    <section class="py-20" style="background-color: var(--bg-card);">
        <div class="max-w-4xl mx-auto px-6">
            <h2 class="text-2xl font-semibold text-center mb-12" style="color: var(--text-primary);">{{ __('carbex.marketing.pricing.comparison_title') }}</h2>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr style="border-bottom: 1px solid var(--border);">
                            <th class="text-left py-4 font-medium" style="color: var(--text-primary);"></th>
                            <th class="text-center py-4 font-medium" style="color: var(--text-primary);">{{ __('carbex.marketing.pricing.trial') }}</th>
                            <th class="text-center py-4 font-medium" style="color: var(--accent);">Premium</th>
                            <th class="text-center py-4 font-medium" style="color: var(--text-primary);">{{ __('carbex.marketing.pricing.advanced') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr style="border-bottom: 1px solid var(--border);">
                            <td class="py-4" style="color: var(--text-secondary);">{{ __('carbex.marketing.pricing.table.users') }}</td>
                            <td class="text-center py-4" style="color: var(--text-primary);">1</td>
                            <td class="text-center py-4" style="color: var(--text-primary);">5</td>
                            <td class="text-center py-4" style="color: var(--text-primary);">{{ __('carbex.marketing.pricing.unlimited') }}</td>
                        </tr>
                        <tr style="border-bottom: 1px solid var(--border);">
                            <td class="py-4" style="color: var(--text-secondary);">{{ __('carbex.marketing.pricing.table.sites') }}</td>
                            <td class="text-center py-4" style="color: var(--text-primary);">1</td>
                            <td class="text-center py-4" style="color: var(--text-primary);">3</td>
                            <td class="text-center py-4" style="color: var(--text-primary);">{{ __('carbex.marketing.pricing.unlimited') }}</td>
                        </tr>
                        <tr style="border-bottom: 1px solid var(--border);">
                            <td class="py-4" style="color: var(--text-secondary);">{{ __('carbex.marketing.pricing.table.reports') }}</td>
                            <td class="text-center py-4" style="color: var(--text-primary);">1</td>
                            <td class="text-center py-4" style="color: var(--text-primary);">{{ __('carbex.marketing.pricing.unlimited') }}</td>
                            <td class="text-center py-4" style="color: var(--text-primary);">{{ __('carbex.marketing.pricing.unlimited') }}</td>
                        </tr>
                        <tr style="border-bottom: 1px solid var(--border);">
                            <td class="py-4" style="color: var(--text-secondary);">{{ __('carbex.marketing.pricing.table.bank_import') }}</td>
                            <td class="text-center py-4">-</td>
                            <td class="text-center py-4"><svg class="w-5 h-5 mx-auto" style="color: var(--accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg></td>
                            <td class="text-center py-4"><svg class="w-5 h-5 mx-auto" style="color: var(--accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg></td>
                        </tr>
                        <tr style="border-bottom: 1px solid var(--border);">
                            <td class="py-4" style="color: var(--text-secondary);">{{ __('carbex.marketing.pricing.table.ademe_ghg') }}</td>
                            <td class="text-center py-4">-</td>
                            <td class="text-center py-4"><svg class="w-5 h-5 mx-auto" style="color: var(--accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg></td>
                            <td class="text-center py-4"><svg class="w-5 h-5 mx-auto" style="color: var(--accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg></td>
                        </tr>
                        <tr style="border-bottom: 1px solid var(--border);">
                            <td class="py-4" style="color: var(--text-secondary);">{{ __('carbex.marketing.pricing.table.api_access') }}</td>
                            <td class="text-center py-4">-</td>
                            <td class="text-center py-4">-</td>
                            <td class="text-center py-4"><svg class="w-5 h-5 mx-auto" style="color: var(--accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg></td>
                        </tr>
                        <tr style="border-bottom: 1px solid var(--border);">
                            <td class="py-4" style="color: var(--text-secondary);">{{ __('carbex.marketing.pricing.table.suppliers_module') }}</td>
                            <td class="text-center py-4">-</td>
                            <td class="text-center py-4">-</td>
                            <td class="text-center py-4"><svg class="w-5 h-5 mx-auto" style="color: var(--accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg></td>
                        </tr>
                        <tr>
                            <td class="py-4" style="color: var(--text-secondary);">{{ __('carbex.marketing.pricing.table.support') }}</td>
                            <td class="text-center py-4" style="color: var(--text-primary);">{{ __('carbex.marketing.pricing.support_email') }}</td>
                            <td class="text-center py-4" style="color: var(--text-primary);">{{ __('carbex.marketing.pricing.support_priority') }}</td>
                            <td class="text-center py-4" style="color: var(--text-primary);">{{ __('carbex.marketing.pricing.support_dedicated') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <!-- FAQ -->
    <section class="py-20" style="background-color: var(--bg-primary);">
        <div class="max-w-3xl mx-auto px-6">
            <h2 class="text-2xl font-semibold text-center mb-12" style="color: var(--text-primary);">{{ __('carbex.marketing.pricing.faq_title') }}</h2>

            <div class="space-y-4">
                <div class="bg-white rounded-xl p-6 border" style="border-color: var(--border);" x-data="{ open: false }">
                    <button @click="open = !open" class="flex items-center justify-between w-full text-left">
                        <span class="font-medium" style="color: var(--text-primary);">{{ __('carbex.marketing.pricing.faq.change_plan_q') }}</span>
                        <svg class="w-5 h-5 transition-transform" :class="{ 'rotate-180': open }" style="color: var(--text-muted);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open" x-collapse class="pt-4 text-sm" style="color: var(--text-secondary);">
                        {{ __('carbex.marketing.pricing.faq.change_plan_a') }}
                    </div>
                </div>

                <div class="bg-white rounded-xl p-6 border" style="border-color: var(--border);" x-data="{ open: false }">
                    <button @click="open = !open" class="flex items-center justify-between w-full text-left">
                        <span class="font-medium" style="color: var(--text-primary);">{{ __('carbex.marketing.pricing.faq.trial_duration_q') }}</span>
                        <svg class="w-5 h-5 transition-transform" :class="{ 'rotate-180': open }" style="color: var(--text-muted);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open" x-collapse class="pt-4 text-sm" style="color: var(--text-secondary);">
                        {{ __('carbex.marketing.pricing.faq.trial_duration_a') }}
                    </div>
                </div>

                <div class="bg-white rounded-xl p-6 border" style="border-color: var(--border);" x-data="{ open: false }">
                    <button @click="open = !open" class="flex items-center justify-between w-full text-left">
                        <span class="font-medium" style="color: var(--text-primary);">{{ __('carbex.marketing.pricing.faq.prices_vat_q') }}</span>
                        <svg class="w-5 h-5 transition-transform" :class="{ 'rotate-180': open }" style="color: var(--text-muted);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open" x-collapse class="pt-4 text-sm" style="color: var(--text-secondary);">
                        {{ __('carbex.marketing.pricing.faq.prices_vat_a') }}
                    </div>
                </div>

                <div class="bg-white rounded-xl p-6 border" style="border-color: var(--border);" x-data="{ open: false }">
                    <button @click="open = !open" class="flex items-center justify-between w-full text-left">
                        <span class="font-medium" style="color: var(--text-primary);">{{ __('carbex.marketing.pricing.faq.monthly_payment_q') }}</span>
                        <svg class="w-5 h-5 transition-transform" :class="{ 'rotate-180': open }" style="color: var(--text-muted);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open" x-collapse class="pt-4 text-sm" style="color: var(--text-secondary);">
                        {{ __('carbex.marketing.pricing.faq.monthly_payment_a') }}
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="py-20" style="background-color: var(--bg-card);">
        <div class="max-w-3xl mx-auto px-6 text-center">
            <h2 class="text-3xl font-semibold mb-5" style="color: var(--text-primary);">
                {{ __('carbex.marketing.pricing.cta_title') }}
            </h2>
            <p class="text-lg mb-10" style="color: var(--text-secondary);">
                {{ __('carbex.marketing.pricing.cta_subtitle') }}
            </p>
            <a href="{{ route('register') }}" class="btn-primary inline-flex items-center px-6 py-3.5 text-sm font-medium text-white rounded-xl" style="background-color: var(--accent);">
                {{ __('carbex.marketing.pricing.start_free') }}
                <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                </svg>
            </a>
            <p class="text-sm mt-4" style="color: var(--text-muted);">{{ __('carbex.marketing.pricing.trial_no_card') }}</p>
        </div>
    </section>
</div>
@endsection
