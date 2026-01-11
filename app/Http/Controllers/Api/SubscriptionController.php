<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Organization;
use App\Models\Subscription;
use App\Services\Billing\PlanLimitsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Checkout\Session;
use Stripe\Customer;
use Stripe\Stripe;
use Stripe\BillingPortal\Session as PortalSession;

/**
 * Subscription Controller
 *
 * Manages subscription operations:
 * - View current subscription
 * - Create checkout session
 * - Access billing portal
 * - Change plans
 * - View invoices
 */
class SubscriptionController extends Controller
{
    public function __construct(
        private PlanLimitsService $planLimits
    ) {
        Stripe::setApiKey(config('cashier.secret'));
    }

    /**
     * Get current subscription status.
     *
     * GET /api/subscription
     */
    public function show(): JsonResponse
    {
        $organization = auth()->user()->organization;
        $subscription = $organization->subscription;

        return response()->json([
            'success' => true,
            'data' => [
                'subscription' => $subscription,
                'on_trial' => $organization->onTrial(),
                'trial_days_remaining' => $subscription?->trialDaysRemaining(),
                'has_active_subscription' => $organization->hasActiveSubscription(),
                'plan' => $subscription?->plan,
                'plan_details' => $subscription ? config("cashier.plans.{$subscription->plan}") : null,
                'usage' => $subscription ? [
                    'bank_connections' => [
                        'used' => $subscription->bank_connections_used,
                        'limit' => $subscription->bank_connections_limit,
                    ],
                    'users' => [
                        'used' => $subscription->users_used,
                        'limit' => $subscription->users_limit,
                    ],
                    'sites' => [
                        'used' => $subscription->sites_used,
                        'limit' => $subscription->sites_limit,
                    ],
                    'reports' => [
                        'used' => $subscription->reports_monthly_used,
                        'limit' => $subscription->reports_monthly_limit,
                    ],
                ] : null,
            ],
        ]);
    }

    /**
     * Get available plans.
     *
     * GET /api/subscription/plans
     */
    public function plans(): JsonResponse
    {
        $plans = config('cashier.plans');
        $currentPlan = auth()->user()->organization->subscription?->plan;

        $formatted = collect($plans)->map(function ($plan, $key) use ($currentPlan) {
            return [
                'key' => $key,
                'name' => $plan['name'],
                'description' => $plan['description'],
                'prices' => $plan['amount'],
                'limits' => $plan['limits'],
                'features' => $plan['features'],
                'is_current' => $key === $currentPlan,
            ];
        })->values();

        return response()->json([
            'success' => true,
            'data' => $formatted,
        ]);
    }

    /**
     * Create a Stripe Checkout session for subscription.
     *
     * POST /api/subscription/checkout
     */
    public function checkout(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'plan' => 'required|in:starter,professional,enterprise',
            'billing_cycle' => 'required|in:monthly,yearly',
        ]);

        $organization = auth()->user()->organization;
        $user = auth()->user();

        // Check if already has active subscription
        if ($organization->hasActiveSubscription() && !$organization->onTrial()) {
            return response()->json([
                'success' => false,
                'message' => __('You already have an active subscription. Use the billing portal to change plans.'),
            ], 400);
        }

        $plan = config("cashier.plans.{$validated['plan']}");
        $priceId = $plan['prices'][$validated['billing_cycle']];

        // Get or create Stripe customer
        $customerId = $this->getOrCreateStripeCustomer($organization, $user);

        try {
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
                        'organization_id' => $organization->id,
                        'plan' => $validated['plan'],
                    ],
                ],
                'billing_address_collection' => 'required',
                'customer_update' => [
                    'address' => 'auto',
                    'name' => 'auto',
                ],
                'locale' => $organization->locale ?? 'fr',
                'allow_promotion_codes' => true,
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'checkout_url' => $session->url,
                    'session_id' => $session->id,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Stripe checkout creation failed', [
                'organization' => $organization->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => __('Unable to create checkout session. Please try again.'),
            ], 500);
        }
    }

    /**
     * Create a billing portal session.
     *
     * POST /api/subscription/portal
     */
    public function portal(): JsonResponse
    {
        $organization = auth()->user()->organization;
        $subscription = $organization->subscription;

        if (!$subscription?->stripe_customer_id) {
            return response()->json([
                'success' => false,
                'message' => __('No active subscription found.'),
            ], 400);
        }

        try {
            $session = PortalSession::create([
                'customer' => $subscription->stripe_customer_id,
                'return_url' => url(config('cashier.portal.return_url')),
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'portal_url' => $session->url,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Stripe portal creation failed', [
                'organization' => $organization->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => __('Unable to access billing portal. Please try again.'),
            ], 500);
        }
    }

    /**
     * Change subscription plan.
     *
     * POST /api/subscription/change-plan
     */
    public function changePlan(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'plan' => 'required|in:starter,professional,enterprise',
            'billing_cycle' => 'required|in:monthly,yearly',
        ]);

        $organization = auth()->user()->organization;
        $subscription = $organization->subscription;

        if (!$subscription || !$subscription->stripe_subscription_id) {
            return response()->json([
                'success' => false,
                'message' => __('No active subscription to change.'),
            ], 400);
        }

        $plan = config("cashier.plans.{$validated['plan']}");
        $newPriceId = $plan['prices'][$validated['billing_cycle']];

        try {
            $stripe = new \Stripe\StripeClient(config('cashier.secret'));

            // Get current subscription
            $stripeSubscription = $stripe->subscriptions->retrieve($subscription->stripe_subscription_id);

            // Update subscription with new price
            $stripe->subscriptions->update($subscription->stripe_subscription_id, [
                'items' => [[
                    'id' => $stripeSubscription->items->data[0]->id,
                    'price' => $newPriceId,
                ]],
                'proration_behavior' => 'create_prorations',
                'metadata' => [
                    'plan' => $validated['plan'],
                ],
            ]);

            // Update local subscription
            $subscription->update([
                'plan' => $validated['plan'],
                'stripe_price_id' => $newPriceId,
                'billing_cycle' => $validated['billing_cycle'],
                ...$this->planLimits->getLimitsForPlan($validated['plan']),
            ]);

            return response()->json([
                'success' => true,
                'message' => __('Your subscription has been updated successfully.'),
                'data' => $subscription->fresh(),
            ]);
        } catch (\Exception $e) {
            Log::error('Subscription change failed', [
                'organization' => $organization->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => __('Unable to change subscription. Please try again or contact support.'),
            ], 500);
        }
    }

    /**
     * Cancel subscription.
     *
     * POST /api/subscription/cancel
     */
    public function cancel(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'immediately' => 'nullable|boolean',
            'feedback' => 'nullable|string|max:1000',
        ]);

        $organization = auth()->user()->organization;
        $subscription = $organization->subscription;

        if (!$subscription || !$subscription->stripe_subscription_id) {
            return response()->json([
                'success' => false,
                'message' => __('No active subscription to cancel.'),
            ], 400);
        }

        try {
            $stripe = new \Stripe\StripeClient(config('cashier.secret'));

            if ($validated['immediately'] ?? false) {
                // Cancel immediately
                $stripe->subscriptions->cancel($subscription->stripe_subscription_id);

                $subscription->update([
                    'status' => 'canceled',
                    'canceled_at' => now(),
                ]);
            } else {
                // Cancel at period end
                $stripe->subscriptions->update($subscription->stripe_subscription_id, [
                    'cancel_at_period_end' => true,
                ]);

                $subscription->update([
                    'cancel_at_period_end' => true,
                    'canceled_at' => now(),
                ]);
            }

            // Log feedback
            if ($validated['feedback'] ?? null) {
                Log::info('Subscription cancellation feedback', [
                    'organization' => $organization->id,
                    'feedback' => $validated['feedback'],
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => $validated['immediately'] ?? false
                    ? __('Your subscription has been canceled.')
                    : __('Your subscription will be canceled at the end of the current billing period.'),
            ]);
        } catch (\Exception $e) {
            Log::error('Subscription cancellation failed', [
                'organization' => $organization->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => __('Unable to cancel subscription. Please try again.'),
            ], 500);
        }
    }

    /**
     * Resume canceled subscription.
     *
     * POST /api/subscription/resume
     */
    public function resume(): JsonResponse
    {
        $organization = auth()->user()->organization;
        $subscription = $organization->subscription;

        if (!$subscription || !$subscription->cancel_at_period_end) {
            return response()->json([
                'success' => false,
                'message' => __('No canceled subscription to resume.'),
            ], 400);
        }

        try {
            $stripe = new \Stripe\StripeClient(config('cashier.secret'));

            $stripe->subscriptions->update($subscription->stripe_subscription_id, [
                'cancel_at_period_end' => false,
            ]);

            $subscription->update([
                'cancel_at_period_end' => false,
                'canceled_at' => null,
            ]);

            return response()->json([
                'success' => true,
                'message' => __('Your subscription has been resumed.'),
            ]);
        } catch (\Exception $e) {
            Log::error('Subscription resume failed', [
                'organization' => $organization->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => __('Unable to resume subscription. Please try again.'),
            ], 500);
        }
    }

    /**
     * Get invoices.
     *
     * GET /api/subscription/invoices
     */
    public function invoices(Request $request): JsonResponse
    {
        $organization = auth()->user()->organization;

        $invoices = Invoice::where('organization_id', $organization->id)
            ->orderByDesc('created_at')
            ->paginate($request->integer('per_page', 10));

        return response()->json([
            'success' => true,
            'data' => $invoices,
        ]);
    }

    /**
     * Download invoice PDF.
     *
     * GET /api/subscription/invoices/{invoice}/download
     */
    public function downloadInvoice(Invoice $invoice)
    {
        $organization = auth()->user()->organization;

        // Ensure invoice belongs to organization
        if ($invoice->organization_id !== $organization->id) {
            abort(403);
        }

        if (!$invoice->stripe_invoice_id) {
            abort(404, 'Invoice not available');
        }

        try {
            $stripe = new \Stripe\StripeClient(config('cashier.secret'));
            $stripeInvoice = $stripe->invoices->retrieve($invoice->stripe_invoice_id);

            // Redirect to Stripe hosted invoice PDF
            return redirect($stripeInvoice->invoice_pdf);
        } catch (\Exception $e) {
            Log::error('Invoice download failed', [
                'invoice' => $invoice->id,
                'error' => $e->getMessage(),
            ]);

            abort(404, 'Invoice not available');
        }
    }

    /**
     * Start a free trial.
     *
     * POST /api/subscription/trial
     */
    public function startTrial(): JsonResponse
    {
        $organization = auth()->user()->organization;

        // Check if already has subscription
        if ($organization->subscription) {
            return response()->json([
                'success' => false,
                'message' => __('You already have a subscription.'),
            ], 400);
        }

        // Check trial config
        if (!config('cashier.trial.enabled')) {
            return response()->json([
                'success' => false,
                'message' => __('Free trial is not available.'),
            ], 400);
        }

        $trialPlan = config('cashier.trial.plan');
        $trialDays = config('cashier.trial.days');

        // Create trial subscription
        $subscription = Subscription::create([
            'organization_id' => $organization->id,
            'plan' => $trialPlan,
            'status' => 'trialing',
            'billing_cycle' => 'monthly',
            'trial_ends_at' => now()->addDays($trialDays),
            'current_period_start' => now(),
            'current_period_end' => now()->addDays($trialDays),
            ...$this->planLimits->getLimitsForPlan($trialPlan),
            'features' => config("cashier.plans.{$trialPlan}.features"),
        ]);

        return response()->json([
            'success' => true,
            'message' => __('Your :days-day free trial has started!', ['days' => $trialDays]),
            'data' => $subscription,
        ], 201);
    }

    /**
     * Get or create Stripe customer.
     */
    private function getOrCreateStripeCustomer(Organization $organization, $user): string
    {
        $subscription = $organization->subscription;

        if ($subscription?->stripe_customer_id) {
            return $subscription->stripe_customer_id;
        }

        $customer = Customer::create([
            'email' => $user->email,
            'name' => $organization->name,
            'metadata' => [
                'organization_id' => $organization->id,
            ],
            'address' => [
                'line1' => $organization->address_line_1,
                'line2' => $organization->address_line_2,
                'city' => $organization->city,
                'postal_code' => $organization->postal_code,
                'country' => strtoupper($organization->country),
            ],
            'preferred_locales' => [$organization->locale ?? 'fr'],
        ]);

        return $customer->id;
    }
}
