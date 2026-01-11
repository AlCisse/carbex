<?php

namespace App\Services\Billing;

use App\Models\Organization;
use App\Models\Subscription;

/**
 * Plan Limits Service
 *
 * Enforces subscription plan limits:
 * - Bank connections
 * - Users
 * - Sites
 * - Monthly reports
 * - Feature access
 */
class PlanLimitsService
{
    /**
     * Get limits array for a given plan.
     */
    public function getLimitsForPlan(string $plan): array
    {
        $config = config("cashier.plans.{$plan}.limits", []);

        return [
            'bank_connections_limit' => $config['bank_connections'] ?? null,
            'bank_connections_used' => 0,
            'users_limit' => $config['users'] ?? null,
            'users_used' => 0,
            'sites_limit' => $config['sites'] ?? null,
            'sites_used' => 0,
            'reports_monthly_limit' => $config['reports_monthly'] ?? null,
            'reports_monthly_used' => 0,
            'reports_reset_at' => now()->addMonth(),
        ];
    }

    /**
     * Check if organization can perform an action based on limits.
     */
    public function canPerformAction(Organization $organization, string $action): bool
    {
        $subscription = $organization->subscription;

        // No subscription = no access (except during grace period for existing orgs)
        if (!$subscription) {
            return $this->isInGracePeriod($organization);
        }

        // Check subscription is active
        if (!$subscription->isActive()) {
            return $this->isInGracePeriod($organization);
        }

        return match ($action) {
            'add_bank_connection' => $subscription->canAddBankConnection(),
            'add_user' => $subscription->canAddUser(),
            'add_site' => $subscription->canAddSite(),
            'generate_report' => $subscription->canGenerateReport(),
            default => true,
        };
    }

    /**
     * Check if organization has access to a feature.
     */
    public function hasFeature(Organization $organization, string $feature): bool
    {
        $subscription = $organization->subscription;

        if (!$subscription || !$subscription->isActive()) {
            // Basic features during trial or grace period
            return in_array($feature, ['basic_dashboard', 'manual_entry']);
        }

        return $subscription->hasFeature($feature);
    }

    /**
     * Get all features for an organization.
     */
    public function getFeatures(Organization $organization): array
    {
        $subscription = $organization->subscription;

        if (!$subscription) {
            return [];
        }

        return $subscription->features ?? [];
    }

    /**
     * Increment usage counter.
     */
    public function incrementUsage(Organization $organization, string $resource): void
    {
        $subscription = $organization->subscription;

        if (!$subscription) {
            return;
        }

        $field = match ($resource) {
            'bank_connection' => 'bank_connections_used',
            'user' => 'users_used',
            'site' => 'sites_used',
            'report' => 'reports_monthly_used',
            default => null,
        };

        if ($field) {
            $subscription->increment($field);
        }
    }

    /**
     * Decrement usage counter.
     */
    public function decrementUsage(Organization $organization, string $resource): void
    {
        $subscription = $organization->subscription;

        if (!$subscription) {
            return;
        }

        $field = match ($resource) {
            'bank_connection' => 'bank_connections_used',
            'user' => 'users_used',
            'site' => 'sites_used',
            default => null,
        };

        if ($field && $subscription->$field > 0) {
            $subscription->decrement($field);
        }
    }

    /**
     * Sync actual usage counts with database.
     */
    public function syncUsage(Organization $organization): void
    {
        $subscription = $organization->subscription;

        if (!$subscription) {
            return;
        }

        $subscription->update([
            'bank_connections_used' => $organization->bankConnections()->active()->count(),
            'users_used' => $organization->users()->count(),
            'sites_used' => $organization->sites()->count(),
        ]);
    }

    /**
     * Get usage summary for organization.
     */
    public function getUsageSummary(Organization $organization): array
    {
        $subscription = $organization->subscription;

        if (!$subscription) {
            return [
                'has_subscription' => false,
                'resources' => [],
            ];
        }

        return [
            'has_subscription' => true,
            'plan' => $subscription->plan,
            'status' => $subscription->status,
            'resources' => [
                'bank_connections' => [
                    'used' => $subscription->bank_connections_used,
                    'limit' => $subscription->bank_connections_limit,
                    'unlimited' => $subscription->bank_connections_limit === null,
                    'percentage' => $subscription->bank_connections_limit
                        ? round(($subscription->bank_connections_used / $subscription->bank_connections_limit) * 100)
                        : null,
                    'can_add' => $subscription->canAddBankConnection(),
                ],
                'users' => [
                    'used' => $subscription->users_used,
                    'limit' => $subscription->users_limit,
                    'unlimited' => $subscription->users_limit === null,
                    'percentage' => $subscription->users_limit
                        ? round(($subscription->users_used / $subscription->users_limit) * 100)
                        : null,
                    'can_add' => $subscription->canAddUser(),
                ],
                'sites' => [
                    'used' => $subscription->sites_used,
                    'limit' => $subscription->sites_limit,
                    'unlimited' => $subscription->sites_limit === null,
                    'percentage' => $subscription->sites_limit
                        ? round(($subscription->sites_used / $subscription->sites_limit) * 100)
                        : null,
                    'can_add' => $subscription->canAddSite(),
                ],
                'reports' => [
                    'used' => $subscription->reports_monthly_used,
                    'limit' => $subscription->reports_monthly_limit,
                    'unlimited' => $subscription->reports_monthly_limit === null,
                    'percentage' => $subscription->reports_monthly_limit
                        ? round(($subscription->reports_monthly_used / $subscription->reports_monthly_limit) * 100)
                        : null,
                    'can_generate' => $subscription->canGenerateReport(),
                    'resets_at' => $subscription->reports_reset_at?->toIso8601String(),
                ],
            ],
        ];
    }

    /**
     * Check if a plan upgrade is required for a feature.
     */
    public function requiresUpgrade(Organization $organization, string $feature): bool
    {
        if ($this->hasFeature($organization, $feature)) {
            return false;
        }

        // Check which plan provides this feature
        foreach (config('cashier.plans') as $planKey => $plan) {
            if (in_array($feature, $plan['features'] ?? [])) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get recommended plan for required features.
     */
    public function getRecommendedPlan(array $requiredFeatures): string
    {
        $plans = config('cashier.plans');

        foreach ($plans as $planKey => $plan) {
            $planFeatures = $plan['features'] ?? [];

            if (count(array_intersect($requiredFeatures, $planFeatures)) === count($requiredFeatures)) {
                return $planKey;
            }
        }

        return 'enterprise';
    }

    /**
     * Check if organization is in grace period.
     */
    private function isInGracePeriod(Organization $organization): bool
    {
        $subscription = $organization->subscription;

        if (!$subscription) {
            return false;
        }

        if ($subscription->status === 'canceled' && $subscription->current_period_end) {
            $graceEnd = $subscription->current_period_end->addDays(
                config('cashier.grace_period_days', 3)
            );

            return $graceEnd->isFuture();
        }

        return false;
    }

    /**
     * Get limit reached message.
     */
    public function getLimitMessage(string $resource): string
    {
        return match ($resource) {
            'bank_connection' => __('You have reached the maximum number of bank connections for your plan. Upgrade to add more.'),
            'user' => __('You have reached the maximum number of users for your plan. Upgrade to add more team members.'),
            'site' => __('You have reached the maximum number of sites for your plan. Upgrade to add more locations.'),
            'report' => __('You have reached the monthly report limit for your plan. Upgrade for more reports or wait until next month.'),
            default => __('You have reached a limit for your current plan. Please upgrade to continue.'),
        };
    }
}
