<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Organization;
use App\Models\Subscription;
use App\Notifications\PaymentFailed;
use App\Notifications\SubscriptionCanceled;
use App\Notifications\TrialEnding;
use App\Services\Billing\PlanLimitsService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Stripe\Event;
use Stripe\Webhook;

/**
 * Stripe Webhook Controller
 *
 * Handles all Stripe webhook events:
 * - Subscription lifecycle (created, updated, deleted)
 * - Invoice events (paid, failed)
 * - Customer events
 * - Trial ending notifications
 */
class StripeWebhookController extends Controller
{
    public function __construct(
        private PlanLimitsService $planLimits
    ) {}

    /**
     * Handle Stripe webhook.
     *
     * POST /webhooks/stripe
     */
    public function handle(Request $request): Response
    {
        $payload = $request->getContent();
        $signature = $request->header('Stripe-Signature');
        $secret = config('cashier.webhook.secret');

        try {
            $event = Webhook::constructEvent($payload, $signature, $secret);
        } catch (\UnexpectedValueException $e) {
            Log::warning('Stripe webhook invalid payload', ['error' => $e->getMessage()]);
            return response('Invalid payload', 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            Log::warning('Stripe webhook invalid signature', ['error' => $e->getMessage()]);
            return response('Invalid signature', 400);
        }

        Log::info('Stripe webhook received', [
            'type' => $event->type,
            'id' => $event->id,
        ]);

        $method = $this->getHandlerMethod($event->type);

        if (method_exists($this, $method)) {
            try {
                $this->$method($event);
            } catch (\Exception $e) {
                Log::error('Stripe webhook handler failed', [
                    'type' => $event->type,
                    'error' => $e->getMessage(),
                ]);
                return response('Handler error', 500);
            }
        }

        return response('OK', 200);
    }

    /**
     * Convert Stripe event type to handler method name.
     */
    private function getHandlerMethod(string $type): string
    {
        return 'handle' . str_replace(' ', '', ucwords(str_replace(['.', '_'], ' ', $type)));
    }

    /**
     * Handle subscription created.
     */
    protected function handleCustomerSubscriptionCreated(Event $event): void
    {
        $stripeSubscription = $event->data->object;
        $organizationId = $stripeSubscription->metadata->organization_id ?? null;

        if (!$organizationId) {
            Log::warning('Stripe subscription created without organization_id', [
                'subscription_id' => $stripeSubscription->id,
            ]);
            return;
        }

        $organization = Organization::find($organizationId);
        if (!$organization) {
            return;
        }

        $plan = $stripeSubscription->metadata->plan ?? 'starter';
        $priceId = $stripeSubscription->items->data[0]->price->id ?? null;
        $billingCycle = $this->determineBillingCycle($priceId, $plan);

        // Create or update subscription
        $subscription = $organization->subscription ?? new Subscription();

        $subscription->fill([
            'organization_id' => $organizationId,
            'plan' => $plan,
            'status' => $stripeSubscription->status,
            'stripe_subscription_id' => $stripeSubscription->id,
            'stripe_customer_id' => $stripeSubscription->customer,
            'stripe_price_id' => $priceId,
            'billing_cycle' => $billingCycle,
            'current_period_start' => \Carbon\Carbon::createFromTimestamp($stripeSubscription->current_period_start),
            'current_period_end' => \Carbon\Carbon::createFromTimestamp($stripeSubscription->current_period_end),
            'trial_ends_at' => $stripeSubscription->trial_end
                ? \Carbon\Carbon::createFromTimestamp($stripeSubscription->trial_end)
                : null,
            ...$this->planLimits->getLimitsForPlan($plan),
            'features' => config("cashier.plans.{$plan}.features"),
        ]);

        $subscription->save();

        Log::info('Subscription created from webhook', [
            'organization' => $organizationId,
            'plan' => $plan,
        ]);
    }

    /**
     * Handle subscription updated.
     */
    protected function handleCustomerSubscriptionUpdated(Event $event): void
    {
        $stripeSubscription = $event->data->object;

        $subscription = Subscription::where('stripe_subscription_id', $stripeSubscription->id)->first();
        if (!$subscription) {
            return;
        }

        $plan = $stripeSubscription->metadata->plan ?? $subscription->plan;
        $priceId = $stripeSubscription->items->data[0]->price->id ?? null;

        $subscription->update([
            'status' => $stripeSubscription->status,
            'plan' => $plan,
            'stripe_price_id' => $priceId,
            'current_period_start' => \Carbon\Carbon::createFromTimestamp($stripeSubscription->current_period_start),
            'current_period_end' => \Carbon\Carbon::createFromTimestamp($stripeSubscription->current_period_end),
            'trial_ends_at' => $stripeSubscription->trial_end
                ? \Carbon\Carbon::createFromTimestamp($stripeSubscription->trial_end)
                : null,
            'cancel_at_period_end' => $stripeSubscription->cancel_at_period_end,
            ...$this->planLimits->getLimitsForPlan($plan),
            'features' => config("cashier.plans.{$plan}.features"),
        ]);

        Log::info('Subscription updated from webhook', [
            'subscription' => $subscription->id,
            'status' => $stripeSubscription->status,
        ]);
    }

    /**
     * Handle subscription deleted.
     */
    protected function handleCustomerSubscriptionDeleted(Event $event): void
    {
        $stripeSubscription = $event->data->object;

        $subscription = Subscription::where('stripe_subscription_id', $stripeSubscription->id)->first();
        if (!$subscription) {
            return;
        }

        $subscription->update([
            'status' => 'canceled',
            'canceled_at' => now(),
        ]);

        // Notify organization owner
        $organization = $subscription->organization;
        $owner = $organization->owner;

        if ($owner) {
            $owner->notify(new SubscriptionCanceled($subscription));
        }

        Log::info('Subscription canceled from webhook', [
            'subscription' => $subscription->id,
        ]);
    }

    /**
     * Handle trial will end (3 days before).
     */
    protected function handleCustomerSubscriptionTrialWillEnd(Event $event): void
    {
        $stripeSubscription = $event->data->object;

        $subscription = Subscription::where('stripe_subscription_id', $stripeSubscription->id)->first();
        if (!$subscription) {
            return;
        }

        // Notify organization owner
        $organization = $subscription->organization;
        $owner = $organization->owner;

        if ($owner) {
            $owner->notify(new TrialEnding($subscription));
        }

        Log::info('Trial ending notification sent', [
            'subscription' => $subscription->id,
        ]);
    }

    /**
     * Handle invoice paid.
     */
    protected function handleInvoicePaid(Event $event): void
    {
        $stripeInvoice = $event->data->object;

        $subscription = Subscription::where('stripe_customer_id', $stripeInvoice->customer)->first();
        if (!$subscription) {
            return;
        }

        // Create invoice record
        Invoice::updateOrCreate(
            ['stripe_invoice_id' => $stripeInvoice->id],
            [
                'organization_id' => $subscription->organization_id,
                'subscription_id' => $subscription->id,
                'stripe_customer_id' => $stripeInvoice->customer,
                'number' => $stripeInvoice->number,
                'amount' => $stripeInvoice->amount_paid,
                'currency' => $stripeInvoice->currency,
                'status' => 'paid',
                'paid_at' => now(),
                'period_start' => \Carbon\Carbon::createFromTimestamp($stripeInvoice->period_start),
                'period_end' => \Carbon\Carbon::createFromTimestamp($stripeInvoice->period_end),
                'hosted_invoice_url' => $stripeInvoice->hosted_invoice_url,
                'invoice_pdf' => $stripeInvoice->invoice_pdf,
            ]
        );

        // Reset monthly report counter if needed
        if ($subscription->reports_reset_at && $subscription->reports_reset_at->isPast()) {
            $subscription->update([
                'reports_monthly_used' => 0,
                'reports_reset_at' => now()->addMonth(),
            ]);
        }

        Log::info('Invoice paid', [
            'invoice' => $stripeInvoice->id,
            'organization' => $subscription->organization_id,
        ]);
    }

    /**
     * Handle invoice payment failed.
     */
    protected function handleInvoicePaymentFailed(Event $event): void
    {
        $stripeInvoice = $event->data->object;

        $subscription = Subscription::where('stripe_customer_id', $stripeInvoice->customer)->first();
        if (!$subscription) {
            return;
        }

        // Update subscription status
        $subscription->update([
            'status' => 'past_due',
        ]);

        // Create/update invoice record
        Invoice::updateOrCreate(
            ['stripe_invoice_id' => $stripeInvoice->id],
            [
                'organization_id' => $subscription->organization_id,
                'subscription_id' => $subscription->id,
                'stripe_customer_id' => $stripeInvoice->customer,
                'number' => $stripeInvoice->number,
                'amount' => $stripeInvoice->amount_due,
                'currency' => $stripeInvoice->currency,
                'status' => 'failed',
                'period_start' => \Carbon\Carbon::createFromTimestamp($stripeInvoice->period_start),
                'period_end' => \Carbon\Carbon::createFromTimestamp($stripeInvoice->period_end),
                'hosted_invoice_url' => $stripeInvoice->hosted_invoice_url,
            ]
        );

        // Notify organization owner
        $organization = $subscription->organization;
        $owner = $organization->owner;

        if ($owner) {
            $owner->notify(new PaymentFailed($subscription, $stripeInvoice));
        }

        Log::warning('Invoice payment failed', [
            'invoice' => $stripeInvoice->id,
            'organization' => $subscription->organization_id,
        ]);
    }

    /**
     * Handle invoice payment action required.
     */
    protected function handleInvoicePaymentActionRequired(Event $event): void
    {
        // Same as payment failed - requires action
        $this->handleInvoicePaymentFailed($event);
    }

    /**
     * Determine billing cycle from price ID.
     */
    private function determineBillingCycle(?string $priceId, string $plan): string
    {
        if (!$priceId) {
            return 'monthly';
        }

        $planConfig = config("cashier.plans.{$plan}");

        if ($priceId === ($planConfig['prices']['yearly'] ?? null)) {
            return 'yearly';
        }

        return 'monthly';
    }
}
