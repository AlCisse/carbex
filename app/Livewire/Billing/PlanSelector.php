<?php

namespace App\Livewire\Billing;

use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;

/**
 * PlanSelector - Plan selection and Stripe checkout integration
 *
 * Constitution LinsCarbon v3.0 - Section 6.3, T079-T081
 */
class PlanSelector extends Component
{
    public string $selectedPlan = 'premium';

    public string $billingPeriod = 'annual';

    public string $promoCode = '';

    public bool $showPaymentModal = false;

    public bool $processing = false;

    public ?string $promoError = null;

    public ?float $discountPercent = null;

    /**
     * Available plans configuration (prices only, texts come from translations).
     */
    public array $plansConfig = [
        'free' => [
            'price_monthly' => 0,
            'price_annual' => 0,
            'stripe_price_monthly' => null,
            'stripe_price_annual' => null,
            'limits' => [
                'users' => 1,
                'sites' => 1,
                'reports' => 1,
            ],
        ],
        'premium' => [
            'price_monthly' => 40,
            'price_annual' => 400,
            'stripe_price_monthly' => 'price_premium_monthly',
            'stripe_price_annual' => 'price_premium_annual',
            'limits' => [
                'users' => 5,
                'sites' => 3,
                'reports' => null,
            ],
            'popular' => true,
        ],
        'advanced' => [
            'price_monthly' => 120,
            'price_annual' => 1200,
            'stripe_price_monthly' => 'price_advanced_monthly',
            'stripe_price_annual' => 'price_advanced_annual',
            'limits' => [
                'users' => null,
                'sites' => null,
                'reports' => null,
            ],
        ],
    ];

    /**
     * Get plans with translated texts.
     */
    #[Computed]
    public function plans(): array
    {
        $plans = [];
        foreach ($this->plansConfig as $key => $config) {
            $plans[$key] = array_merge($config, [
                'name' => __("linscarbon.billing.plans.{$key}.name"),
                'description' => __("linscarbon.billing.plans.{$key}.description"),
                'duration' => $key === 'free' ? __('linscarbon.billing.plans.free.duration') : null,
                'features' => __("linscarbon.billing.plans.{$key}.features"),
            ]);
        }
        return $plans;
    }

    /**
     * Valid promo codes (in production, these would come from database/Stripe).
     */
    private array $promoCodes = [
        'CARBEX20' => ['discount' => 20, 'description' => '20% de réduction'],
        'LAUNCH50' => ['discount' => 50, 'description' => '50% de réduction - Offre lancement'],
        'PME10' => ['discount' => 10, 'description' => '10% de réduction PME'],
    ];

    public function selectPlan(string $plan): void
    {
        $this->selectedPlan = $plan;
    }

    public function setBillingPeriod(string $period): void
    {
        $this->billingPeriod = in_array($period, ['monthly', 'annual']) ? $period : 'annual';
    }

    /**
     * Open payment modal for a plan.
     */
    public function openPaymentModal(string $plan): void
    {
        if ($plan === 'free') {
            $this->startFreeTrial();

            return;
        }

        $this->selectedPlan = $plan;
        $this->promoCode = '';
        $this->promoError = null;
        $this->discountPercent = null;
        $this->showPaymentModal = true;
    }

    /**
     * Close payment modal.
     */
    public function closePaymentModal(): void
    {
        $this->showPaymentModal = false;
        $this->promoCode = '';
        $this->promoError = null;
        $this->discountPercent = null;
    }

    /**
     * Apply promo code.
     */
    public function applyPromoCode(): void
    {
        $this->promoError = null;
        $this->discountPercent = null;

        $code = strtoupper(trim($this->promoCode));

        if (empty($code)) {
            return;
        }

        if (isset($this->promoCodes[$code])) {
            $this->discountPercent = $this->promoCodes[$code]['discount'];
            session()->flash('promo_success', $this->promoCodes[$code]['description']);
        } else {
            $this->promoError = __('linscarbon.billing.invalid_promo_code');
        }
    }

    /**
     * Calculate price with discount.
     */
    #[Computed]
    public function calculatedPrice(): array
    {
        $plan = $this->plans[$this->selectedPlan] ?? $this->plans['premium'];
        $basePrice = $this->billingPeriod === 'annual'
            ? $plan['price_annual']
            : $plan['price_monthly'];

        $discount = 0;
        if ($this->discountPercent) {
            $discount = $basePrice * ($this->discountPercent / 100);
        }

        $finalPrice = $basePrice - $discount;

        // Annual savings calculation
        $annualSavings = 0;
        if ($this->billingPeriod === 'annual') {
            $monthlyTotal = $plan['price_monthly'] * 12;
            $annualSavings = $monthlyTotal - $plan['price_annual'];
        }

        return [
            'base' => $basePrice,
            'discount' => $discount,
            'discount_percent' => $this->discountPercent,
            'final' => $finalPrice,
            'annual_savings' => $annualSavings,
            'period_label' => '/' . ($this->billingPeriod === 'annual' ? __('linscarbon.billing.year') : __('linscarbon.billing.month')),
        ];
    }

    /**
     * Start free trial.
     */
    public function startFreeTrial(): void
    {
        $organization = auth()->user()->organization;

        // Create trial subscription
        $organization->subscription()->create([
            'plan' => 'free',
            'status' => 'trialing',
            'trial_ends_at' => now()->addDays(15),
            'users_limit' => 1,
            'sites_limit' => 1,
            'reports_monthly_limit' => 1,
        ]);

        session()->flash('message', __('linscarbon.billing.trial_started'));

        $this->redirect(route('dashboard'));
    }

    /**
     * Proceed to Stripe Checkout.
     */
    public function checkout(): void
    {
        $this->processing = true;

        try {
            $organization = auth()->user()->organization;
            $plan = $this->plans[$this->selectedPlan];

            $priceId = $this->billingPeriod === 'annual'
                ? config("cashier.prices.{$this->selectedPlan}_annual")
                : config("cashier.prices.{$this->selectedPlan}_monthly");

            if (! $priceId) {
                // Fallback: use environment variable pattern
                $priceId = env(strtoupper("STRIPE_PRICE_{$this->selectedPlan}_{$this->billingPeriod}"));
            }

            // Build checkout session
            $checkoutBuilder = $organization->newSubscription('default', $priceId);

            // Apply coupon if promo code is valid
            if ($this->discountPercent && $this->promoCode) {
                $couponId = $this->getStripeCouponId($this->promoCode);
                if ($couponId) {
                    $checkoutBuilder->withCoupon($couponId);
                }
            }

            // Create Stripe Checkout session
            $checkout = $checkoutBuilder->checkout([
                'success_url' => route('billing.success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('billing'),
                'customer_email' => auth()->user()->email,
                'billing_address_collection' => 'required',
                'tax_id_collection' => ['enabled' => true],
                'metadata' => [
                    'organization_id' => $organization->id,
                    'plan' => $this->selectedPlan,
                    'billing_period' => $this->billingPeriod,
                ],
            ]);

            // Redirect to Stripe Checkout
            $this->redirect($checkout->url);
        } catch (\Exception $e) {
            $this->processing = false;
            session()->flash('error', __('linscarbon.billing.checkout_error'));

            report($e);
        }
    }

    /**
     * Get Stripe coupon ID for promo code.
     */
    private function getStripeCouponId(string $promoCode): ?string
    {
        // In production, map promo codes to Stripe coupon IDs
        $couponMapping = [
            'CARBEX20' => env('STRIPE_COUPON_CARBEX20'),
            'LAUNCH50' => env('STRIPE_COUPON_LAUNCH50'),
            'PME10' => env('STRIPE_COUPON_PME10'),
        ];

        return $couponMapping[strtoupper($promoCode)] ?? null;
    }

    /**
     * Get current organization subscription status.
     */
    #[Computed]
    public function currentPlan(): ?array
    {
        $subscription = auth()->user()->organization->subscription ?? null;

        if (! $subscription) {
            return null;
        }

        return [
            'plan' => $subscription->plan,
            'status' => $subscription->status,
            'is_trial' => $subscription->onTrial(),
            'trial_ends_at' => $subscription->trial_ends_at,
            'current_period_end' => $subscription->current_period_end,
        ];
    }

    public function render(): View
    {
        return view('livewire.billing.plan-selector', [
            'plans' => $this->plans,
        ]);
    }
}
