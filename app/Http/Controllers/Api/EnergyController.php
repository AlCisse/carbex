<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\SyncEnergyData;
use App\Models\EnergyConnection;
use App\Models\EnergyConsumption;
use App\Services\Energy\EnedisService;
use App\Services\Energy\GrdfService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

/**
 * Energy Controller
 *
 * Manages energy connections and consumption data:
 * - Connect to energy providers (Enedis, GRDF)
 * - View consumption data
 * - Trigger sync
 * - Get emissions from energy
 */
class EnergyController extends Controller
{
    public function __construct(
        private EnedisService $enedisService,
        private GrdfService $grdfService
    ) {}

    /**
     * List energy connections.
     *
     * GET /api/energy/connections
     */
    public function connections(): JsonResponse
    {
        $connections = EnergyConnection::where('organization_id', auth()->user()->organization_id)
            ->with('site')
            ->orderBy('provider')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $connections,
        ]);
    }

    /**
     * Initiate connection to energy provider.
     *
     * POST /api/energy/connect
     */
    public function initiate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'provider' => 'required|in:enedis,grdf',
            'site_id' => 'nullable|uuid|exists:sites,id',
        ]);

        $provider = $validated['provider'];
        $service = $this->getService($provider);

        // Generate state for OAuth
        $state = Str::random(40);

        // Store state in cache
        Cache::put(
            "energy_oauth_state:{$state}",
            [
                'organization_id' => auth()->user()->organization_id,
                'site_id' => $validated['site_id'] ?? null,
                'provider' => $provider,
            ],
            now()->addMinutes(30)
        );

        $authUrl = $service->getAuthorizationUrl($state);

        return response()->json([
            'success' => true,
            'data' => [
                'authorization_url' => $authUrl,
                'state' => $state,
            ],
        ]);
    }

    /**
     * Handle OAuth callback.
     *
     * POST /api/energy/callback
     */
    public function callback(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'code' => 'required|string',
            'state' => 'required|string',
        ]);

        // Verify state
        $stateData = Cache::pull("energy_oauth_state:{$validated['state']}");

        if (!$stateData) {
            return response()->json([
                'success' => false,
                'message' => __('Invalid or expired authorization state.'),
            ], 400);
        }

        $provider = $stateData['provider'];
        $service = $this->getService($provider);

        try {
            // Exchange code for tokens
            $tokens = $service->exchangeCode($validated['code']);

            // Create connection
            $connection = EnergyConnection::create([
                'organization_id' => $stateData['organization_id'],
                'site_id' => $stateData['site_id'],
                'provider' => $provider,
                'provider_customer_id' => $tokens['usage_points_id'] ?? $tokens['pce'] ?? null,
                'access_token' => encrypt($tokens['access_token']),
                'refresh_token' => $tokens['refresh_token'] ? encrypt($tokens['refresh_token']) : null,
                'token_expires_at' => now()->addSeconds($tokens['expires_in'] ?? 3600),
                'status' => 'active',
                'connected_at' => now(),
                'consent_expires_at' => now()->addMonths(config('energy.consent.duration_months', 12)),
                'next_sync_at' => now(),
            ]);

            // Trigger initial sync
            SyncEnergyData::dispatch($connection, null, null, true);

            return response()->json([
                'success' => true,
                'message' => __('Energy connection established successfully.'),
                'data' => $connection,
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Failed to establish connection: ') . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update connection settings.
     *
     * PUT /api/energy/connections/{connection}
     */
    public function updateConnection(Request $request, EnergyConnection $connection): JsonResponse
    {
        $this->authorize('update', $connection);

        $validated = $request->validate([
            'label' => 'nullable|string|max:255',
            'site_id' => 'nullable|uuid|exists:sites,id',
        ]);

        $connection->update($validated);

        return response()->json([
            'success' => true,
            'data' => $connection->fresh(),
        ]);
    }

    /**
     * Disconnect energy provider.
     *
     * DELETE /api/energy/connections/{connection}
     */
    public function disconnect(EnergyConnection $connection): JsonResponse
    {
        $this->authorize('delete', $connection);

        $service = $this->getService($connection->provider);

        // Revoke access at provider
        $service->revokeAccess($connection);

        // Soft delete connection
        $connection->update(['status' => 'revoked']);
        $connection->delete();

        return response()->json([
            'success' => true,
            'message' => __('Energy connection removed successfully.'),
        ]);
    }

    /**
     * Trigger manual sync.
     *
     * POST /api/energy/connections/{connection}/sync
     */
    public function sync(Request $request, EnergyConnection $connection): JsonResponse
    {
        $this->authorize('update', $connection);

        $validated = $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'full_sync' => 'nullable|boolean',
        ]);

        $startDate = isset($validated['start_date']) ? Carbon::parse($validated['start_date']) : null;
        $endDate = isset($validated['end_date']) ? Carbon::parse($validated['end_date']) : null;

        SyncEnergyData::dispatch(
            $connection,
            $startDate,
            $endDate,
            $validated['full_sync'] ?? false
        );

        return response()->json([
            'success' => true,
            'message' => __('Energy sync started. You will be notified when complete.'),
        ], 202);
    }

    /**
     * Get consumption data.
     *
     * GET /api/energy/consumption
     */
    public function consumption(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'energy_type' => 'nullable|in:electricity,gas',
            'granularity' => 'nullable|in:hourly,daily,monthly',
            'site_id' => 'nullable|uuid|exists:sites,id',
            'connection_id' => 'nullable|uuid|exists:energy_connections,id',
        ]);

        $query = EnergyConsumption::where('organization_id', auth()->user()->organization_id)
            ->whereBetween('date', [$validated['start_date'], $validated['end_date']]);

        if (isset($validated['energy_type'])) {
            $query->where('energy_type', $validated['energy_type']);
        }

        if (isset($validated['granularity'])) {
            $query->where('granularity', $validated['granularity']);
        }

        if (isset($validated['site_id'])) {
            $query->where('site_id', $validated['site_id']);
        }

        if (isset($validated['connection_id'])) {
            $query->where('energy_connection_id', $validated['connection_id']);
        }

        $data = $query->orderBy('date')->get();

        // Calculate totals
        $totals = [
            'consumption' => $data->sum('consumption'),
            'emissions_kg' => $data->sum('emissions_kg'),
            'by_type' => $data->groupBy('energy_type')->map(fn ($items) => [
                'consumption' => $items->sum('consumption'),
                'emissions_kg' => $items->sum('emissions_kg'),
            ]),
        ];

        return response()->json([
            'success' => true,
            'data' => $data,
            'totals' => $totals,
        ]);
    }

    /**
     * Get energy summary for dashboard.
     *
     * GET /api/energy/summary
     */
    public function summary(Request $request): JsonResponse
    {
        $organizationId = auth()->user()->organization_id;

        $validated = $request->validate([
            'period' => 'nullable|in:month,quarter,year',
            'site_id' => 'nullable|uuid|exists:sites,id',
        ]);

        $period = $validated['period'] ?? 'month';

        $startDate = match ($period) {
            'quarter' => now()->subMonths(3)->startOfMonth(),
            'year' => now()->subYear()->startOfMonth(),
            default => now()->subMonth()->startOfMonth(),
        };

        $query = EnergyConsumption::where('organization_id', $organizationId)
            ->where('date', '>=', $startDate)
            ->where('granularity', 'daily');

        if (isset($validated['site_id'])) {
            $query->where('site_id', $validated['site_id']);
        }

        $data = $query->get();

        // Group by type
        $byType = $data->groupBy('energy_type')->map(function ($items, $type) {
            return [
                'type' => $type,
                'total_consumption' => $items->sum('consumption'),
                'total_emissions_kg' => $items->sum('emissions_kg'),
                'unit' => 'kWh',
                'avg_daily' => $items->count() > 0 ? $items->sum('consumption') / $items->count() : 0,
            ];
        });

        // Get connections status
        $connections = EnergyConnection::where('organization_id', $organizationId)
            ->select('id', 'provider', 'status', 'last_sync_at', 'next_sync_at')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'period' => [
                    'start' => $startDate->toDateString(),
                    'end' => now()->toDateString(),
                ],
                'by_type' => $byType,
                'total_emissions_kg' => $data->sum('emissions_kg'),
                'connections' => $connections,
            ],
        ]);
    }

    /**
     * Get provider service by name.
     */
    private function getService(string $provider)
    {
        return match ($provider) {
            'enedis' => $this->enedisService,
            'grdf' => $this->grdfService,
            default => throw new \InvalidArgumentException("Unknown provider: {$provider}"),
        };
    }
}
