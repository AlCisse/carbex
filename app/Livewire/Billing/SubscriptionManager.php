<?php

namespace App\Livewire\Billing;

use App\Models\Invoice;
use App\Services\Billing\PlanLimitsService;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Stripe\BillingPortal\Session as PortalSession;

/**
 * Subscription Manager Component
 *
 * Full billing management interface:
 * - Current subscription status
 * - Plan comparison and upgrade
 * - Billing portal access
 * - Invoice history
 * - Usage metrics
 */
#[Layout('layouts.app')]
class SubscriptionManager extends Component
{
    public string $selectedPlan = '';
    public string $billingCycle = 'yearly';
    public bool $showConfirmCancel = false;
    public bool $showUpgradeModal = false;
    public string $cancelFeedback = '';

    public function mount(): void
    {
        $subscription = auth()->user()->organization->subscription;
        $this->selectedPlan = $subscription?->plan ?? '';
    }

    #[Computed]
    public function organization()
    {
        return auth()->user()->organization;
    }

    #[Computed]
    public function subscription()
    {
        return $this->organization->subscription;
    }

    #[Computed]
    public function plans(): array
    {
        $plans = config('cashier.plans');
        $currentPlan = $this->subscription?->plan;

        return collect($plans)->map(function ($plan, $key) use ($currentPlan) {
            return [
                'key' => $key,
                'name' => $plan['name'],
                'description' => $plan['description'],
                'prices' => $plan['amount'],
                'limits' => $plan['limits'],
                'features' => $plan['features'],
                'is_current' => $key === $currentPlan,
                'is_upgrade' => $this->isUpgrade($currentPlan, $key),
            ];
        })->values()->toArray();
    }

    #[Computed]
    public function usageSummary(): array
    {
        return app(PlanLimitsService::class)->getUsageSummary($this->organization);
    }

    #[Computed]
    public function invoices()
    {
        return Invoice::where('organization_id', $this->organization->id)
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();
    }

    #[Computed]
    public function trialInfo(): array
    {
        $subscription = $this->subscription;

        if (!$subscription?->onTrial()) {
            return ['on_trial' => false];
        }

        return [
            'on_trial' => true,
            'days_remaining' => $subscription->trialDaysRemaining(),
            'ends_at' => $subscription->trial_ends_at->format('d/m/Y'),
        ];
    }

    /**
     * Start free trial.
     */
    public function startTrial(): void
    {
        if ($this->subscription) {
            $this->dispatch('toast', message: __('You already have a subscription.'), type: 'error');
            return;
        }

        try {
            $trialPlan = config('cashier.trial.plan');
            $trialDays = config('cashier.trial.days');

            $this->organization->subscription()->create([
                'plan' => $trialPlan,
                'status' => 'trialing',
                'billing_cycle' => 'monthly',
                'trial_ends_at' => now()->addDays($trialDays),
                'current_period_start' => now(),
                'current_period_end' => now()->addDays($trialDays),
                ...app(PlanLimitsService::class)->getLimitsForPlan($trialPlan),
                'features' => config("cashier.plans.{$trialPlan}.features"),
            ]);

            $this->dispatch('toast', message: __('Your :days-day free trial has started!', ['days' => $trialDays]), type: 'success');
            $this->dispatch('$refresh');
        } catch (\Exception $e) {
            $this->dispatch('toast', message: __('Unable to start trial. Please try again.'), type: 'error');
        }
    }

    /**
     * Create Stripe checkout session.
     */
    public function checkout(string $plan): void
    {
        Stripe::setApiKey(config('cashier.secret'));

        $planConfig = config("cashier.plans.{$plan}");
        $priceId = $planConfig['prices'][$this->billingCycle];

        try {
            // Get or create Stripe customer
            $customerId = $this->getOrCreateStripeCustomer();

            $session = Session::create([
                'customer' => $customerId,
                'payment_method_types' => ['card', 'sepa_debit'],
                'line_items' => [[
                    'price' => $priceId,
                    'quantity' => 1,
                ]],
                'mode' => 'subscription',
                'success_url' => url('/settings/billing?success=1'),
                'cancel_url' => url('/settings/billing?canceled=1'),
                'subscription_data' => [
                    'metadata' => [
                        'organization_id' => $this->organization->id,
                        'plan' => $plan,
                    ],
                ],
                'billing_address_collection' => 'required',
                'locale' => $this->organization->locale ?? 'fr',
                'allow_promotion_codes' => true,
            ]);

            $this->redirect($session->url);
        } catch (\Exception $e) {
            $this->dispatch('toast', message: __('Unable to create checkout. Please try again.'), type: 'error');
        }
    }

    /**
     * Open Stripe billing portal.
     */
    public function openPortal(): void
    {
        if (!$this->subscription?->stripe_customer_id) {
            $this->dispatch('toast', message: __('No billing information found.'), type: 'error');
            return;
        }

        Stripe::setApiKey(config('cashier.secret'));

        try {
            $session = PortalSession::create([
                'customer' => $this->subscription->stripe_customer_id,
                'return_url' => url('/settings/billing'),
            ]);

            $this->redirect($session->url);
        } catch (\Exception $e) {
            $this->dispatch('toast', message: __('Unable to access billing portal. Please try again.'), type: 'error');
        }
    }

    /**
     * Change billing cycle.
     */
    public function setBillingCycle(string $cycle): void
    {
        $this->billingCycle = $cycle;
    }

    /**
     * Show upgrade modal.
     */
    public function showUpgrade(string $plan): void
    {
        $this->selectedPlan = $plan;
        $this->showUpgradeModal = true;
    }

    /**
     * Confirm plan upgrade.
     */
    public function confirmUpgrade(): void
    {
        $this->showUpgradeModal = false;
        $this->checkout($this->selectedPlan);
    }

    /**
     * Cancel subscription.
     */
    public function cancelSubscription(): void
    {
        if (!$this->subscription?->stripe_subscription_id) {
            // Cancel local trial
            $this->subscription->update([
                'status' => 'canceled',
                'canceled_at' => now(),
            ]);

            $this->showConfirmCancel = false;
            $this->dispatch('toast', message: __('Your trial has been canceled.'), type: 'success');
            $this->dispatch('$refresh');
            return;
        }

        try {
            $stripe = new \Stripe\StripeClient(config('cashier.secret'));

            $stripe->subscriptions->update($this->subscription->stripe_subscription_id, [
                'cancel_at_period_end' => true,
            ]);

            $this->subscription->update([
                'cancel_at_period_end' => true,
                'canceled_at' => now(),
            ]);

            $this->showConfirmCancel = false;
            $this->cancelFeedback = '';
            $this->dispatch('toast', message: __('Your subscription will be canceled at the end of the billing period.'), type: 'success');
            $this->dispatch('$refresh');
        } catch (\Exception $e) {
            $this->dispatch('toast', message: __('Unable to cancel subscription. Please try again.'), type: 'error');
        }
    }

    /**
     * Resume canceled subscription.
     */
    public function resumeSubscription(): void
    {
        if (!$this->subscription?->cancel_at_period_end) {
            return;
        }

        try {
            $stripe = new \Stripe\StripeClient(config('cashier.secret'));

            $stripe->subscriptions->update($this->subscription->stripe_subscription_id, [
                'cancel_at_period_end' => false,
            ]);

            $this->subscription->update([
                'cancel_at_period_end' => false,
                'canceled_at' => null,
            ]);

            $this->dispatch('toast', message: __('Your subscription has been resumed.'), type: 'success');
            $this->dispatch('$refresh');
        } catch (\Exception $e) {
            $this->dispatch('toast', message: __('Unable to resume subscription. Please try again.'), type: 'error');
        }
    }

    /**
     * Format price for display.
     */
    public function formatPrice(int $cents): string
    {
        return number_format($cents / 100, 0, ',', ' ') . ' €';
    }

    /**
     * Get or create Stripe customer.
     */
    private function getOrCreateStripeCustomer(): string
    {
        if ($this->subscription?->stripe_customer_id) {
            return $this->subscription->stripe_customer_id;
        }

        $user = auth()->user();

        $customer = \Stripe\Customer::create([
            'email' => $user->email,
            'name' => $this->organization->name,
            'metadata' => [
                'organization_id' => $this->organization->id,
            ],
            'preferred_locales' => [$this->organization->locale ?? 'fr'],
        ]);

        return $customer->id;
    }

    /**
     * Check if target plan is an upgrade.
     */
    private function isUpgrade(?string $currentPlan, string $targetPlan): bool
    {
        if (!$currentPlan) {
            return true;
        }

        $planOrder = ['starter' => 1, 'professional' => 2, 'enterprise' => 3];

        return ($planOrder[$targetPlan] ?? 0) > ($planOrder[$currentPlan] ?? 0);
    }

    public function render()
    {
        return view('livewire.billing.subscription-manager');
    }
}
