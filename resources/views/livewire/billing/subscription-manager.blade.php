<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">{{ __('linscarbon.billing.subscription_billing') }}</h1>
        <p class="text-gray-600 mt-1">{{ __('linscarbon.billing.manage_desc') }}</p>
    </div>

    {{-- Flash Messages --}}
    @if (request('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
            <p class="text-green-800">{{ __('linscarbon.billing.subscription_activated') }}</p>
        </div>
    @endif

    @if (request('canceled'))
        <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
            <p class="text-yellow-800">{{ __('linscarbon.billing.checkout_canceled') }}</p>
        </div>
    @endif

    {{-- Trial Banner --}}
    @if ($this->trialInfo['on_trial'])
        <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg flex items-center justify-between">
            <div>
                <p class="text-blue-800 font-medium">
                    {{ __('linscarbon.billing.on_trial') }} - {{ $this->trialInfo['days_remaining'] }} {{ __('linscarbon.billing.days_remaining') }}
                </p>
                <p class="text-blue-600 text-sm">
                    {{ __('linscarbon.billing.trial_ends_on', ['date' => $this->trialInfo['ends_at']]) }}
                </p>
            </div>
            <button wire:click="checkout('professional')" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                {{ __('linscarbon.billing.upgrade_now') }}
            </button>
        </div>
    @endif

    {{-- Cancellation Warning --}}
    @if ($this->subscription?->cancel_at_period_end)
        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg flex items-center justify-between">
            <div>
                <p class="text-red-800 font-medium">{{ __('linscarbon.billing.subscription_ends_on', ['date' => $this->subscription->current_period_end->format('d/m/Y')]) }}</p>
                <p class="text-red-600 text-sm">{{ __('linscarbon.billing.lose_premium') }}</p>
            </div>
            <button wire:click="resumeSubscription" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                {{ __('linscarbon.billing.resume_subscription') }}
            </button>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Left Column: Current Plan & Usage --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Current Subscription --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">{{ __('linscarbon.billing.current_plan') }}</h2>

                @if ($this->subscription && $this->subscription->isActive())
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-2xl font-bold text-gray-900">
                                {{ config("cashier.plans.{$this->subscription->plan}.name") }}
                            </p>
                            <p class="text-gray-600 mt-1">
                                {{ $this->formatPrice(config("cashier.plans.{$this->subscription->plan}.amount.{$this->subscription->billing_cycle}")) }}
                                / {{ $this->subscription->billing_cycle === 'yearly' ? __('linscarbon.billing.year') : __('linscarbon.billing.month') }}
                            </p>
                            @if ($this->subscription->current_period_end)
                                <p class="text-sm text-gray-500 mt-2">
                                    {{ __('linscarbon.billing.next_billing', ['date' => $this->subscription->current_period_end->format('d/m/Y')]) }}
                                </p>
                            @endif
                        </div>
                        <button wire:click="openPortal" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                            {{ __('linscarbon.billing.manage_billing') }}
                        </button>
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="w-16 h-16 mx-auto text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                        <p class="text-gray-600 mt-4">{{ __('linscarbon.billing.no_subscription') }}</p>
                        @if (config('cashier.trial.enabled') && !$this->subscription)
                            <button wire:click="startTrial" class="mt-4 px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                                {{ __('linscarbon.billing.start_trial_days', ['days' => config('cashier.trial.days')]) }}
                            </button>
                        @endif
                    </div>
                @endif
            </div>

            {{-- Usage Metrics --}}
            @if ($this->subscription && $this->usageSummary['has_subscription'])
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">{{ __('linscarbon.billing.usage') }}</h2>

                    <div class="grid grid-cols-2 gap-6">
                        @foreach (['bank_connections' => __('linscarbon.billing.bank_connections'), 'users' => __('linscarbon.billing.users'), 'sites' => __('linscarbon.billing.sites'), 'reports' => __('linscarbon.billing.monthly_reports')] as $key => $label)
                            @php $usage = $this->usageSummary['resources'][$key] @endphp
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm font-medium text-gray-700">{{ $label }}</span>
                                    <span class="text-sm text-gray-500">
                                        @if ($usage['unlimited'])
                                            {{ $usage['used'] }} / {{ __('linscarbon.billing.unlimited') }}
                                        @else
                                            {{ $usage['used'] }} / {{ $usage['limit'] }}
                                        @endif
                                    </span>
                                </div>
                                @unless ($usage['unlimited'])
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="h-2 rounded-full transition-all {{ $usage['percentage'] >= 90 ? 'bg-red-500' : ($usage['percentage'] >= 70 ? 'bg-yellow-500' : 'bg-green-500') }}"
                                             style="width: {{ min($usage['percentage'], 100) }}%"></div>
                                    </div>
                                @endunless
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Plans Comparison --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-lg font-semibold text-gray-900">{{ __('linscarbon.billing.available_plans') }}</h2>

                    {{-- Billing Cycle Toggle --}}
                    <div class="flex items-center bg-gray-100 rounded-lg p-1">
                        <button wire:click="setBillingCycle('monthly')"
                                class="px-4 py-2 text-sm font-medium rounded-md transition {{ $billingCycle === 'monthly' ? 'bg-white shadow text-gray-900' : 'text-gray-600' }}">
                            {{ __('linscarbon.billing.monthly') }}
                        </button>
                        <button wire:click="setBillingCycle('yearly')"
                                class="px-4 py-2 text-sm font-medium rounded-md transition {{ $billingCycle === 'yearly' ? 'bg-white shadow text-gray-900' : 'text-gray-600' }}">
                            {{ __('linscarbon.billing.yearly') }}
                            <span class="ml-1 text-green-600 text-xs">-17%</span>
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach ($this->plans as $plan)
                        <div class="border rounded-xl p-5 {{ $plan['is_current'] ? 'border-green-500 bg-green-50' : 'border-gray-200 hover:border-gray-300' }} transition">
                            <div class="mb-4">
                                <h3 class="text-lg font-bold text-gray-900">{{ $plan['name'] }}</h3>
                                <p class="text-sm text-gray-600 mt-1">{{ $plan['description'] }}</p>
                            </div>

                            <div class="mb-4">
                                <span class="text-3xl font-bold text-gray-900">{{ $this->formatPrice($plan['prices'][$billingCycle]) }}</span>
                                <span class="text-gray-500">/ {{ $billingCycle === 'yearly' ? __('linscarbon.billing.year') : __('linscarbon.billing.month') }}</span>
                            </div>

                            <ul class="space-y-2 mb-6 text-sm">
                                @if ($plan['limits']['bank_connections'])
                                    <li class="flex items-center text-gray-600">
                                        <svg class="w-4 h-4 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                        {{ $plan['limits']['bank_connections'] }} {{ __('linscarbon.billing.bank_connections') }}
                                    </li>
                                @else
                                    <li class="flex items-center text-gray-600">
                                        <svg class="w-4 h-4 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                        {{ __('linscarbon.billing.unlimited_bank_connections') }}
                                    </li>
                                @endif

                                @if ($plan['limits']['users'])
                                    <li class="flex items-center text-gray-600">
                                        <svg class="w-4 h-4 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                        {{ $plan['limits']['users'] }} {{ __('linscarbon.billing.users') }}
                                    </li>
                                @else
                                    <li class="flex items-center text-gray-600">
                                        <svg class="w-4 h-4 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                        {{ __('linscarbon.billing.unlimited_users') }}
                                    </li>
                                @endif

                                @if (in_array('api_access', $plan['features']))
                                    <li class="flex items-center text-gray-600">
                                        <svg class="w-4 h-4 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                        {{ __('linscarbon.billing.api_access') }}
                                    </li>
                                @endif

                                @if (in_array('sso', $plan['features']))
                                    <li class="flex items-center text-gray-600">
                                        <svg class="w-4 h-4 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                        SSO
                                    </li>
                                @endif
                            </ul>

                            @if ($plan['is_current'])
                                <button disabled class="w-full px-4 py-2 bg-green-100 text-green-700 rounded-lg font-medium">
                                    {{ __('linscarbon.billing.current_plan') }}
                                </button>
                            @elseif ($plan['is_upgrade'])
                                <button wire:click="showUpgrade('{{ $plan['key'] }}')"
                                        class="w-full px-4 py-2 bg-green-600 text-white rounded-lg font-medium hover:bg-green-700 transition">
                                    {{ __('linscarbon.billing.upgrade') }}
                                </button>
                            @else
                                <button wire:click="showUpgrade('{{ $plan['key'] }}')"
                                        class="w-full px-4 py-2 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50 transition">
                                    {{ __('linscarbon.billing.downgrade') }}
                                </button>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Right Column: Invoices & Actions --}}
        <div class="space-y-6">
            {{-- Quick Actions --}}
            @if ($this->subscription && $this->subscription->isActive())
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">{{ __('linscarbon.billing.quick_actions') }}</h2>

                    <div class="space-y-3">
                        <button wire:click="openPortal" class="w-full flex items-center justify-between px-4 py-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                            <span class="text-gray-700">{{ __('linscarbon.billing.update_payment_method') }}</span>
                            <svg class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>

                        <button wire:click="openPortal" class="w-full flex items-center justify-between px-4 py-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                            <span class="text-gray-700">{{ __('linscarbon.billing.update_billing_address') }}</span>
                            <svg class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>

                        @unless ($this->subscription->cancel_at_period_end)
                            <button wire:click="$set('showConfirmCancel', true)"
                                    class="w-full flex items-center justify-between px-4 py-3 border border-red-200 rounded-lg hover:bg-red-50 transition text-red-600">
                                <span>{{ __('linscarbon.billing.cancel_subscription') }}</span>
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </button>
                        @endunless
                    </div>
                </div>
            @endif

            {{-- Invoices --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">{{ __('linscarbon.billing.recent_invoices') }}</h2>

                @if ($this->invoices->isEmpty())
                    <p class="text-gray-500 text-center py-4">{{ __('linscarbon.billing.no_invoices_yet') }}</p>
                @else
                    <div class="space-y-3">
                        @foreach ($this->invoices as $invoice)
                            <a href="{{ $invoice->invoice_pdf ?? $invoice->hosted_invoice_url }}"
                               target="_blank"
                               class="flex items-center justify-between px-4 py-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $invoice->number }}</p>
                                    <p class="text-xs text-gray-500">{{ $invoice->created_at->format('d/m/Y') }}</p>
                                </div>
                                <div class="flex items-center">
                                    <span class="text-sm font-medium text-gray-900 mr-2">
                                        {{ number_format($invoice->amount / 100, 2, ',', ' ') }} €
                                    </span>
                                    @if ($invoice->status === 'paid')
                                        <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-700 rounded">{{ __('linscarbon.billing.paid') }}</span>
                                    @else
                                        <span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-700 rounded">{{ __('linscarbon.billing.failed') }}</span>
                                    @endif
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Help --}}
            <div class="bg-gray-50 rounded-xl border border-gray-200 p-6">
                <h3 class="font-medium text-gray-900 mb-2">{{ __('linscarbon.billing.need_help') }}</h3>
                <p class="text-sm text-gray-600 mb-4">{{ __('linscarbon.billing.help_choose_plan') }}</p>
                <a href="mailto:support@linscarbon.io" class="text-sm text-green-600 hover:text-green-700 font-medium">
                    {{ __('linscarbon.billing.contact_support') }} →
                </a>
            </div>
        </div>
    </div>

    {{-- Cancel Confirmation Modal --}}
    @if ($showConfirmCancel)
        <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50" wire:click.self="$set('showConfirmCancel', false)">
            <div class="bg-white rounded-xl shadow-xl max-w-md w-full mx-4 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-2">{{ __('linscarbon.billing.cancel_subscription_title') }}</h3>
                <p class="text-gray-600 mb-4">
                    {{ __('linscarbon.billing.cancel_subscription_desc', ['date' => $this->subscription?->current_period_end?->format('d/m/Y')]) }}
                </p>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('linscarbon.billing.why_canceling') }}</label>
                    <textarea wire:model="cancelFeedback" rows="3" class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500" placeholder="{{ __('linscarbon.billing.feedback_placeholder') }}"></textarea>
                </div>

                <div class="flex gap-3">
                    <button wire:click="$set('showConfirmCancel', false)" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                        {{ __('linscarbon.billing.keep_subscription') }}
                    </button>
                    <button wire:click="cancelSubscription" class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                        {{ __('linscarbon.billing.cancel') }}
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Upgrade Confirmation Modal --}}
    @if ($showUpgradeModal)
        <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50" wire:click.self="$set('showUpgradeModal', false)">
            <div class="bg-white rounded-xl shadow-xl max-w-md w-full mx-4 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-2">
                    {{ __('linscarbon.billing.upgrade_to', ['plan' => config("cashier.plans.{$selectedPlan}.name")]) }}
                </h3>
                <p class="text-gray-600 mb-4">
                    {{ __('linscarbon.billing.redirect_to_payment') }}
                </p>

                <div class="bg-gray-50 rounded-lg p-4 mb-4">
                    <div class="flex justify-between mb-2">
                        <span class="text-gray-600">{{ __('linscarbon.billing.plan') }}</span>
                        <span class="font-medium">{{ config("cashier.plans.{$selectedPlan}.name") }}</span>
                    </div>
                    <div class="flex justify-between mb-2">
                        <span class="text-gray-600">{{ __('linscarbon.billing.billing_period') }}</span>
                        <span class="font-medium">{{ $billingCycle === 'yearly' ? __('linscarbon.billing.yearly') : __('linscarbon.billing.monthly') }}</span>
                    </div>
                    <div class="flex justify-between border-t pt-2 mt-2">
                        <span class="text-gray-600">{{ __('linscarbon.billing.total') }}</span>
                        <span class="font-bold text-lg">
                            {{ $this->formatPrice(config("cashier.plans.{$selectedPlan}.amount.{$billingCycle}")) }}
                        </span>
                    </div>
                </div>

                <div class="flex gap-3">
                    <button wire:click="$set('showUpgradeModal', false)" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                        {{ __('linscarbon.billing.cancel') }}
                    </button>
                    <button wire:click="confirmUpgrade" class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                        {{ __('linscarbon.billing.continue_to_payment') }}
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
