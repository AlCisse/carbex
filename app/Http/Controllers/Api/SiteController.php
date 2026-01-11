<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Site;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class SiteController extends Controller
{
    /**
     * Get all sites for the organization.
     */
    public function index(Request $request): JsonResponse
    {
        $sites = Site::query()
            ->when($request->boolean('active_only', true), fn ($q) => $q->active())
            ->orderBy('is_primary', 'desc')
            ->orderBy('name')
            ->get();

        return response()->json([
            'sites' => $sites->map(fn ($site) => $this->formatSite($site)),
            'total' => $sites->count(),
        ]);
    }

    /**
     * Get a specific site.
     */
    public function show(Site $site): JsonResponse
    {
        Gate::authorize('view', $site);

        $site->load(['activities', 'emissionRecords']);

        return response()->json([
            'site' => $this->formatSite($site, true),
        ]);
    }

    /**
     * Create a new site.
     */
    public function store(Request $request): JsonResponse
    {
        Gate::authorize('create', Site::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:sites,code',
            'description' => 'nullable|string|max:1000',
            'type' => 'nullable|string|in:office,warehouse,factory,store,datacenter,other',
            'address_line_1' => 'nullable|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|size:2',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'floor_area_m2' => 'nullable|numeric|min:0',
            'employee_count' => 'nullable|integer|min:0',
            'electricity_provider' => 'nullable|string|max:255',
            'renewable_energy' => 'boolean',
            'renewable_percentage' => 'nullable|numeric|min:0|max:100',
            'is_primary' => 'boolean',
        ]);

        // Auto-generate code if not provided
        if (empty($validated['code'])) {
            $validated['code'] = strtoupper(substr(preg_replace('/[^a-zA-Z0-9]/', '', $validated['name']), 0, 8));
        }

        // Use organization's country if not specified
        if (empty($validated['country'])) {
            $validated['country'] = $request->user()->organization->country;
        }

        $site = Site::create($validated);

        // If this is marked as primary, unset other primary sites
        if ($site->is_primary) {
            Site::where('id', '!=', $site->id)
                ->where('is_primary', true)
                ->update(['is_primary' => false]);
        }

        // Update subscription usage
        $subscription = $request->user()->organization->subscription;
        if ($subscription) {
            $subscription->increment('sites_used');
        }

        return response()->json([
            'message' => __('carbex.sites.created'),
            'site' => $this->formatSite($site),
        ], 201);
    }

    /**
     * Update a site.
     */
    public function update(Request $request, Site $site): JsonResponse
    {
        Gate::authorize('update', $site);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'code' => "sometimes|string|max:50|unique:sites,code,{$site->id}",
            'description' => 'nullable|string|max:1000',
            'type' => 'nullable|string|in:office,warehouse,factory,store,datacenter,other',
            'address_line_1' => 'nullable|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|size:2',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'floor_area_m2' => 'nullable|numeric|min:0',
            'employee_count' => 'nullable|integer|min:0',
            'electricity_provider' => 'nullable|string|max:255',
            'renewable_energy' => 'boolean',
            'renewable_percentage' => 'nullable|numeric|min:0|max:100',
            'is_active' => 'boolean',
        ]);

        $site->update($validated);

        return response()->json([
            'message' => __('carbex.sites.updated'),
            'site' => $this->formatSite($site->fresh()),
        ]);
    }

    /**
     * Delete a site.
     */
    public function destroy(Site $site): JsonResponse
    {
        Gate::authorize('delete', $site);

        // Check if primary
        if ($site->is_primary) {
            return response()->json([
                'message' => __('carbex.sites.cannot_delete_primary'),
            ], 422);
        }

        // Soft delete
        $site->delete();

        // Update subscription usage
        $subscription = request()->user()->organization->subscription;
        if ($subscription && $subscription->sites_used > 0) {
            $subscription->decrement('sites_used');
        }

        return response()->json([
            'message' => __('carbex.sites.deleted'),
        ]);
    }

    /**
     * Set a site as primary.
     */
    public function setPrimary(Site $site): JsonResponse
    {
        Gate::authorize('setPrimary', $site);

        // Unset current primary
        Site::where('is_primary', true)->update(['is_primary' => false]);

        // Set new primary
        $site->update(['is_primary' => true]);

        return response()->json([
            'message' => __('carbex.sites.set_as_primary'),
            'site' => $this->formatSite($site->fresh()),
        ]);
    }

    /**
     * Format site for response.
     */
    private function formatSite(Site $site, bool $detailed = false): array
    {
        $data = [
            'id' => $site->id,
            'name' => $site->name,
            'code' => $site->code,
            'description' => $site->description,
            'type' => $site->type,
            'address' => [
                'line_1' => $site->address_line_1,
                'line_2' => $site->address_line_2,
                'city' => $site->city,
                'postal_code' => $site->postal_code,
                'country' => $site->country,
                'full' => $site->full_address,
            ],
            'coordinates' => $site->latitude && $site->longitude ? [
                'latitude' => $site->latitude,
                'longitude' => $site->longitude,
            ] : null,
            'floor_area_m2' => $site->floor_area_m2,
            'employee_count' => $site->employee_count,
            'is_primary' => $site->is_primary,
            'is_active' => $site->is_active,
            'created_at' => $site->created_at->toIso8601String(),
        ];

        if ($detailed) {
            $data['electricity_provider'] = $site->electricity_provider;
            $data['renewable_energy'] = $site->renewable_energy;
            $data['renewable_percentage'] = $site->renewable_percentage;
            $data['activities_count'] = $site->activities()->count();
            $data['emission_records_count'] = $site->emissionRecords()->count();
        }

        return $data;
    }
}
