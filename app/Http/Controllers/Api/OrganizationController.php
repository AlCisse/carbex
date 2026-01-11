<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Organization\CountryConfigurationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class OrganizationController extends Controller
{
    public function __construct(
        private CountryConfigurationService $countryConfig
    ) {}

    /**
     * Get the current user's organization.
     */
    public function current(Request $request): JsonResponse
    {
        $organization = $request->user()->organization;

        $organization->load(['sites', 'users', 'subscription']);

        return response()->json([
            'organization' => $this->formatOrganization($organization),
            'sites' => $organization->sites->map(fn ($site) => $this->formatSite($site)),
            'team_count' => $organization->users->count(),
            'subscription' => $organization->subscription ? [
                'plan' => $organization->subscription->plan,
                'status' => $organization->subscription->status,
                'trial_ends_at' => $organization->subscription->trial_ends_at?->toIso8601String(),
            ] : null,
        ]);
    }

    /**
     * Update the organization.
     */
    public function update(Request $request): JsonResponse
    {
        $organization = $request->user()->organization;

        Gate::authorize('update', $organization);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'legal_name' => 'nullable|string|max:255',
            'registration_number' => 'nullable|string|max:100',
            'vat_number' => 'nullable|string|max:50',
            'sector' => 'nullable|string|max:255',
            'size' => 'nullable|string|in:1-10,11-50,51-250,251-500,500+',
            'website' => 'nullable|url|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'address_line_1' => 'nullable|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:20',
            'fiscal_year_start_month' => 'sometimes|integer|min:1|max:12',
            'default_currency' => 'sometimes|string|size:3',
            'logo' => 'nullable|image|max:2048',
        ]);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store("organizations/{$organization->id}", 's3');
            $validated['logo_url'] = $path;
        }

        $organization->update($validated);

        return response()->json([
            'message' => __('carbex.organization.updated'),
            'organization' => $this->formatOrganization($organization->fresh()),
        ]);
    }

    /**
     * Get organization statistics.
     */
    public function stats(Request $request): JsonResponse
    {
        $organization = $request->user()->organization;

        Gate::authorize('viewStats', $organization);

        // Load counts
        $stats = [
            'sites_count' => $organization->sites()->count(),
            'users_count' => $organization->users()->count(),
            'bank_connections_count' => $organization->bankConnections()->active()->count(),
            'bank_accounts_count' => $organization->bankAccounts()->active()->count(),
            'transactions_count' => $organization->transactions()->count(),
            'pending_review_count' => $organization->transactions()->needsReview()->count(),
        ];

        // Emissions summary
        $currentYear = now()->year;
        $emissionsSummary = $organization->emissionRecords()
            ->whereYear('date', $currentYear)
            ->selectRaw('scope, SUM(co2e_kg) as total_kg')
            ->groupBy('scope')
            ->pluck('total_kg', 'scope');

        $stats['emissions'] = [
            'year' => $currentYear,
            'scope_1_kg' => $emissionsSummary[1] ?? 0,
            'scope_2_kg' => $emissionsSummary[2] ?? 0,
            'scope_3_kg' => $emissionsSummary[3] ?? 0,
            'total_kg' => array_sum($emissionsSummary->toArray()),
            'total_tonnes' => round(array_sum($emissionsSummary->toArray()) / 1000, 2),
        ];

        // Subscription usage
        if ($organization->subscription) {
            $stats['subscription_usage'] = [
                'bank_connections' => [
                    'used' => $organization->subscription->bank_connections_used,
                    'limit' => $organization->subscription->bank_connections_limit,
                ],
                'users' => [
                    'used' => $organization->subscription->users_used,
                    'limit' => $organization->subscription->users_limit,
                ],
                'sites' => [
                    'used' => $organization->subscription->sites_used,
                    'limit' => $organization->subscription->sites_limit,
                ],
            ];
        }

        return response()->json($stats);
    }

    /**
     * Update organization settings (onboarding, etc.).
     */
    public function updateSettings(Request $request): JsonResponse
    {
        $organization = $request->user()->organization;

        Gate::authorize('manageSettings', $organization);

        $validated = $request->validate([
            'settings' => 'required|array',
            'settings.onboarding_completed' => 'sometimes|boolean',
            'settings.setup_step' => 'sometimes|integer|min:1',
            'settings.emission_methodology' => 'sometimes|string|in:ghg_protocol,beges,iso14064',
            'settings.auto_categorization' => 'sometimes|boolean',
            'settings.default_emission_source' => 'sometimes|string|in:ademe,uba,ecoinvent',
        ]);

        $organization->update([
            'settings' => array_merge($organization->settings ?? [], $validated['settings']),
        ]);

        return response()->json([
            'message' => __('carbex.organization.settings_updated'),
            'settings' => $organization->fresh()->settings,
        ]);
    }

    /**
     * Format organization for response.
     */
    private function formatOrganization($organization): array
    {
        return [
            'id' => $organization->id,
            'name' => $organization->name,
            'legal_name' => $organization->legal_name,
            'country' => $organization->country,
            'country_config' => $this->countryConfig->getConfig($organization->country),
            'sector' => $organization->sector,
            'size' => $organization->size,
            'registration_number' => $organization->registration_number,
            'vat_number' => $organization->vat_number,
            'website' => $organization->website,
            'phone' => $organization->phone,
            'email' => $organization->email,
            'address' => [
                'line_1' => $organization->address_line_1,
                'line_2' => $organization->address_line_2,
                'city' => $organization->city,
                'postal_code' => $organization->postal_code,
                'country' => $organization->country,
            ],
            'fiscal_year_start_month' => $organization->fiscal_year_start_month,
            'default_currency' => $organization->default_currency,
            'timezone' => $organization->timezone,
            'logo_url' => $organization->logo_url,
            'settings' => $organization->settings,
            'is_active' => $organization->is_active,
            'created_at' => $organization->created_at->toIso8601String(),
        ];
    }

    /**
     * Format site for response.
     */
    private function formatSite($site): array
    {
        return [
            'id' => $site->id,
            'name' => $site->name,
            'code' => $site->code,
            'type' => $site->type,
            'city' => $site->city,
            'country' => $site->country,
            'is_primary' => $site->is_primary,
            'is_active' => $site->is_active,
        ];
    }
}
