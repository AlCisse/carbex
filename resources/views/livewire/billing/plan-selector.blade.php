<div>
    <!-- Session Messages -->
    @if (session()->has('message'))
        <div class="mb-6 rounded-md bg-green-50 p-4">
            <div class="flex">
                <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                </svg>
                <p class="ml-3 text-sm font-medium text-green-800">{{ session('message') }}</p>
            </div>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-6 rounded-md bg-red-50 p-4">
            <div class="flex">
                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
                </svg>
                <p class="ml-3 text-sm font-medium text-red-800">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    <!-- Current Plan Badge -->
    @if($this->currentPlan)
        <div class="mb-6 rounded-lg bg-green-50 border border-green-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-medium text-green-800">{{ __('linscarbon.billing.current_plan') }}</h3>
                    <p class="text-lg font-semibold text-green-900">
                        {{ $plans[$this->currentPlan['plan']]['name'] ?? 'N/A' }}
                        @if($this->currentPlan['is_trial'])
                            <span class="ml-2 inline-flex items-center rounded-full bg-yellow-100 px-2.5 py-0.5 text-xs font-medium text-yellow-800">
                                {{ __('linscarbon.billing.trial') }}
                            </span>
                        @endif
                    </p>
                </div>
                @if($this->currentPlan['is_trial'] && $this->currentPlan['trial_ends_at'])
                    <div class="text-right">
                        <p class="text-sm text-green-600">{{ __('linscarbon.billing.trial_ends') }}</p>
                        <p class="text-sm font-medium text-green-800">{{ $this->currentPlan['trial_ends_at']->format('d/m/Y') }}</p>
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- Billing Period Toggle -->
    <div class="flex justify-center mb-8">
        <div class="bg-gray-100 p-1 rounded-lg inline-flex">
            <button wire:click="setBillingPeriod('monthly')"
                    class="px-4 py-2 text-sm font-medium rounded-md transition-all {{ $billingPeriod === 'monthly' ? 'bg-white shadow-sm text-gray-900' : 'text-gray-500 hover:text-gray-700' }}">
                {{ __('linscarbon.billing.monthly') }}
            </button>
            <button wire:click="setBillingPeriod('annual')"
                    class="px-4 py-2 text-sm font-medium rounded-md transition-all {{ $billingPeriod === 'annual' ? 'bg-white shadow-sm text-gray-900' : 'text-gray-500 hover:text-gray-700' }}">
                {{ __('linscarbon.billing.annual') }} <span class="text-green-600 text-xs ml-1">-17%</span>
            </button>
        </div>
    </div>

    <!-- Plans Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @foreach($plans as $key => $plan)
            <div class="bg-white rounded-lg shadow-sm border {{ $selectedPlan === $key ? 'border-green-500 ring-2 ring-green-500' : 'border-gray-200' }} p-6 relative flex flex-col">
                @if(isset($plan['popular']) && $plan['popular'])
                    <div class="absolute -top-3 left-1/2 transform -translate-x-1/2">
                        <span class="bg-green-500 text-white text-xs font-semibold px-3 py-1 rounded-full">{{ __('linscarbon.billing.popular') }}</span>
                    </div>
                @endif

                <div class="text-center flex-1">
                    <h3 class="text-lg font-semibold text-gray-900">{{ $plan['name'] }}</h3>
                    <p class="mt-1 text-sm text-gray-500">{{ $plan['description'] }}</p>

                    <div class="mt-4">
                        @if($key === 'free')
                            <span class="text-4xl font-bold text-gray-900">0{{ __('linscarbon.billing.currency') }}</span>
                            <span class="text-gray-500">/{{ $plan['duration'] }}</span>
                        @else
                            <span class="text-4xl font-bold text-gray-900">
                                {{ $billingPeriod === 'annual' ? $plan['price_annual'] : $plan['price_monthly'] }}{{ __('linscarbon.billing.currency') }}
                            </span>
                            <span class="text-gray-500">/{{ $billingPeriod === 'annual' ? __('linscarbon.billing.per_year') : __('linscarbon.billing.per_month') }}</span>
                        @endif
                    </div>

                    @if($billingPeriod === 'annual' && $key !== 'free')
                        <p class="mt-1 text-xs text-green-600">
                            {{ __('linscarbon.billing.save') }} {{ ($plan['price_monthly'] * 12) - $plan['price_annual'] }}{{ __('linscarbon.billing.currency') }}/{{ __('linscarbon.billing.year') }}
                        </p>
                    @endif

                    <!-- Limits -->
                    <div class="mt-4 pt-4 border-t border-gray-100">
                        <div class="flex justify-center gap-4 text-xs text-gray-500">
                            <span>
                                <svg class="inline h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                {{ $plan['limits']['users'] ?? '∞' }} {{ __('linscarbon.billing.users') }}
                            </span>
                            <span>
                                <svg class="inline h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                                {{ $plan['limits']['sites'] ?? '∞' }} {{ __('linscarbon.billing.sites') }}
                            </span>
                        </div>
                    </div>

                    <ul class="mt-6 space-y-3 text-left">
                        @foreach($plan['features'] as $feature)
                            <li class="flex items-start">
                                <svg class="h-5 w-5 text-green-500 mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span class="text-sm text-gray-600">{{ $feature }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <button wire:click="openPaymentModal('{{ $key }}')"
                        class="mt-6 w-full py-2 px-4 border rounded-md text-sm font-medium transition-all {{ $selectedPlan === $key ? 'bg-green-600 text-white border-transparent hover:bg-green-700' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50' }}">
                    @if($key === 'free')
                        {{ __('linscarbon.billing.start_trial') }}
                    @else
                        {{ __('linscarbon.billing.choose') }}
                    @endif
                </button>
            </div>
        @endforeach
    </div>

    <!-- Payment Modal -->
    @if($showPaymentModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <!-- Background overlay -->
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closePaymentModal"></div>

                <!-- Modal panel -->
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left flex-1">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                    {{ __('linscarbon.billing.checkout_title') }}
                                </h3>
                                <p class="mt-1 text-sm text-gray-500">
                                    Plan {{ $plans[$selectedPlan]['name'] }}
                                </p>
                            </div>
                        </div>

                        <div class="mt-6 space-y-4">
                            <!-- Billing Period Selection in Modal -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('linscarbon.billing.billing_period') }}</label>
                                <div class="grid grid-cols-2 gap-3">
                                    <button wire:click="setBillingPeriod('monthly')"
                                            type="button"
                                            class="relative flex items-center justify-center px-4 py-3 border rounded-lg cursor-pointer focus:outline-none {{ $billingPeriod === 'monthly' ? 'border-green-500 ring-2 ring-green-500 bg-green-50' : 'border-gray-200 hover:border-gray-300' }}">
                                        <span class="text-sm font-medium {{ $billingPeriod === 'monthly' ? 'text-green-900' : 'text-gray-900' }}">
                                            {{ __('linscarbon.billing.monthly') }}
                                        </span>
                                    </button>
                                    <button wire:click="setBillingPeriod('annual')"
                                            type="button"
                                            class="relative flex flex-col items-center justify-center px-4 py-3 border rounded-lg cursor-pointer focus:outline-none {{ $billingPeriod === 'annual' ? 'border-green-500 ring-2 ring-green-500 bg-green-50' : 'border-gray-200 hover:border-gray-300' }}">
                                        <span class="text-sm font-medium {{ $billingPeriod === 'annual' ? 'text-green-900' : 'text-gray-900' }}">
                                            {{ __('linscarbon.billing.annual') }}
                                        </span>
                                        <span class="text-xs text-green-600 mt-0.5">{{ __('linscarbon.billing.save') }} 17%</span>
                                    </button>
                                </div>
                            </div>

                            <!-- Promo Code -->
                            <div>
                                <label for="promo-code" class="block text-sm font-medium text-gray-700">{{ __('linscarbon.billing.promo_code') }}</label>
                                <div class="mt-1 flex rounded-md shadow-sm">
                                    <input type="text"
                                           id="promo-code"
                                           wire:model="promoCode"
                                           class="flex-1 rounded-l-md border-gray-300 focus:border-green-500 focus:ring-green-500 uppercase"
                                           placeholder="{{ __('linscarbon.billing.promo_placeholder') }}">
                                    <button type="button"
                                            wire:click="applyPromoCode"
                                            class="inline-flex items-center px-4 py-2 border border-l-0 border-gray-300 rounded-r-md bg-gray-50 text-sm font-medium text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-green-500">
                                        {{ __('linscarbon.billing.apply') }}
                                    </button>
                                </div>
                                @if($promoError)
                                    <p class="mt-1 text-sm text-red-600">{{ $promoError }}</p>
                                @endif
                                @if(session()->has('promo_success'))
                                    <p class="mt-1 text-sm text-green-600">{{ session('promo_success') }}</p>
                                @endif
                            </div>

                            <!-- Price Breakdown -->
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="space-y-2">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">{{ $plans[$selectedPlan]['name'] }} ({{ $billingPeriod === 'annual' ? __('linscarbon.billing.annual') : __('linscarbon.billing.monthly') }})</span>
                                        <span class="text-gray-900">{{ $this->calculatedPrice['base'] }}{{ __('linscarbon.billing.currency') }}</span>
                                    </div>

                                    @if($this->calculatedPrice['discount'] > 0)
                                        <div class="flex justify-between text-sm">
                                            <span class="text-green-600">{{ __('linscarbon.billing.discount') }} (-{{ $this->calculatedPrice['discount_percent'] }}%)</span>
                                            <span class="text-green-600">-{{ number_format($this->calculatedPrice['discount'], 2) }}{{ __('linscarbon.billing.currency') }}</span>
                                        </div>
                                    @endif

                                    @if($this->calculatedPrice['annual_savings'] > 0)
                                        <div class="flex justify-between text-sm">
                                            <span class="text-green-600">{{ __('linscarbon.billing.annual_savings') }}</span>
                                            <span class="text-green-600">{{ $this->calculatedPrice['annual_savings'] }}{{ __('linscarbon.billing.currency') }}</span>
                                        </div>
                                    @endif

                                    <div class="pt-2 border-t border-gray-200">
                                        <div class="flex justify-between">
                                            <span class="text-base font-medium text-gray-900">{{ __('linscarbon.billing.total') }}</span>
                                            <span class="text-xl font-bold text-gray-900">{{ number_format($this->calculatedPrice['final'], 2) }}{{ __('linscarbon.billing.currency') }} <span class="text-sm font-normal text-gray-500">{{ $this->calculatedPrice['period_label'] }} HT</span></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Security Note -->
                            <div class="flex items-center text-xs text-gray-500">
                                <svg class="h-4 w-4 mr-1.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                                {{ __('linscarbon.billing.secure_payment') }}
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-3">
                        <button wire:click="checkout"
                                wire:loading.attr="disabled"
                                wire:loading.class="opacity-50 cursor-not-allowed"
                                type="button"
                                class="w-full inline-flex justify-center items-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50">
                            <span wire:loading.remove wire:target="checkout">
                                {{ __('linscarbon.billing.checkout_button') }}
                            </span>
                            <span wire:loading wire:target="checkout" class="flex items-center">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                {{ __('linscarbon.billing.processing') }}
                            </span>
                        </button>
                        <button wire:click="closePaymentModal"
                                type="button"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:mt-0 sm:w-auto sm:text-sm">
                            {{ __('linscarbon.billing.cancel') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
