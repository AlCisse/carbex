<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\SyncBankTransactions;
use App\Models\BankConnection;
use App\Services\Banking\BankingProviderInterface;
use App\Services\Banking\BridgeService;
use App\Services\Banking\FinapiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class BankConnectionController extends Controller
{
    public function __construct(
        private BridgeService $bridgeService,
        private FinapiService $finapiService,
    ) {}

    /**
     * List all bank connections for the organization.
     */
    public function index(Request $request): JsonResponse
    {
        $connections = BankConnection::where('organization_id', $request->user()->organization_id)
            ->with('accounts')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'data' => $connections->map(fn ($conn) => [
                'id' => $conn->id,
                'bank_name' => $conn->bank_name,
                'provider' => $conn->provider,
                'status' => $conn->status,
                'accounts_count' => $conn->accounts->count(),
                'last_sync_at' => $conn->last_sync_at?->toIso8601String(),
                'consent_expires_at' => $conn->consent_expires_at?->toIso8601String(),
                'created_at' => $conn->created_at->toIso8601String(),
            ]),
        ]);
    }

    /**
     * Get available banks for a country.
     */
    public function banks(Request $request): JsonResponse
    {
        $country = strtoupper($request->input('country', 'FR'));
        $provider = $this->getProviderForCountry($country);

        if (! $provider) {
            return response()->json([
                'error' => 'Unsupported country',
                'message' => "No banking provider available for country: {$country}",
            ], 400);
        }

        $banks = $provider->getBanks($country);

        return response()->json([
            'data' => $banks->values(),
            'provider' => $provider->getProvider(),
            'country' => $country,
        ]);
    }

    /**
     * Initiate a new bank connection.
     */
    public function initiate(Request $request): JsonResponse
    {
        $request->validate([
            'bank_id' => 'required|string',
            'country' => 'required|string|size:2',
        ]);

        $country = strtoupper($request->input('country'));
        $bankId = $request->input('bank_id');
        $organizationId = $request->user()->organization_id;

        $provider = $this->getProviderForCountry($country);

        if (! $provider) {
            return response()->json([
                'error' => 'Unsupported country',
                'message' => "No banking provider available for country: {$country}",
            ], 400);
        }

        // Generate callback URL
        $redirectUrl = route('api.bank-connections.callback', [
            'provider' => $provider->getProvider(),
        ]);

        try {
            $result = $provider->initiateConnection(
                $organizationId,
                $bankId,
                $redirectUrl
            );

            return response()->json([
                'redirect_url' => $result['redirect_url'],
                'state' => $result['state'],
                'expires_at' => $result['expires_at']->toIso8601String(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Connection failed',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Handle OAuth callback from banking provider.
     */
    public function callback(Request $request, string $provider): JsonResponse
    {
        $request->validate([
            'code' => 'required|string',
            'state' => 'required|string',
        ]);

        $bankingProvider = $this->getProvider($provider);

        if (! $bankingProvider) {
            return response()->json([
                'error' => 'Unknown provider',
            ], 400);
        }

        $organizationId = $request->user()->organization_id;

        try {
            $connection = $bankingProvider->handleCallback(
                $request->input('code'),
                $request->input('state'),
                $organizationId
            );

            // Queue initial sync
            SyncBankTransactions::dispatch($connection);

            return response()->json([
                'message' => 'Bank connected successfully',
                'connection' => [
                    'id' => $connection->id,
                    'bank_name' => $connection->bank_name,
                    'status' => $connection->status,
                    'accounts_count' => $connection->accounts()->count(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Callback processing failed',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get connection details.
     */
    public function show(BankConnection $connection): JsonResponse
    {
        Gate::authorize('view', $connection);

        $provider = $this->getProvider($connection->provider);
        $status = $provider?->getConnectionStatus($connection) ?? [
            'status' => $connection->status,
            'last_sync' => $connection->last_sync_at,
            'error' => $connection->error_message,
        ];

        return response()->json([
            'data' => [
                'id' => $connection->id,
                'bank_name' => $connection->bank_name,
                'provider' => $connection->provider,
                'status' => $status['status'],
                'last_sync_at' => $connection->last_sync_at?->toIso8601String(),
                'consent_expires_at' => $connection->consent_expires_at?->toIso8601String(),
                'error' => $status['error'],
                'accounts' => $connection->accounts->map(fn ($account) => [
                    'id' => $account->id,
                    'name' => $account->name,
                    'iban' => $account->iban ? $this->maskIban($account->iban) : null,
                    'balance' => $account->balance,
                    'currency' => $account->currency,
                    'type' => $account->type,
                    'transactions_count' => $account->transactions()->count(),
                ]),
            ],
        ]);
    }

    /**
     * Manually trigger sync for a connection.
     */
    public function sync(BankConnection $connection): JsonResponse
    {
        Gate::authorize('update', $connection);

        if (! in_array($connection->status, ['active', 'syncing'])) {
            return response()->json([
                'error' => 'Cannot sync',
                'message' => 'Connection is not active',
            ], 400);
        }

        SyncBankTransactions::dispatch($connection);

        return response()->json([
            'message' => 'Sync queued',
            'connection_id' => $connection->id,
        ]);
    }

    /**
     * Refresh connection (renew consent if needed).
     */
    public function refresh(Request $request, BankConnection $connection): JsonResponse
    {
        Gate::authorize('update', $connection);

        $provider = $this->getProvider($connection->provider);

        if (! $provider) {
            return response()->json([
                'error' => 'Unknown provider',
            ], 400);
        }

        try {
            $provider->refreshToken($connection);

            return response()->json([
                'message' => 'Connection refreshed',
                'connection' => [
                    'id' => $connection->id,
                    'status' => $connection->fresh()->status,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Refresh failed',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Disconnect and remove bank connection.
     */
    public function destroy(BankConnection $connection): JsonResponse
    {
        Gate::authorize('delete', $connection);

        $provider = $this->getProvider($connection->provider);

        if ($provider) {
            $provider->disconnect($connection);
        } else {
            $connection->update(['status' => 'disconnected']);
        }

        // Soft delete connection
        $connection->delete();

        return response()->json([
            'message' => 'Bank disconnected successfully',
        ]);
    }

    /**
     * Get provider instance for a country.
     */
    private function getProviderForCountry(string $country): ?BankingProviderInterface
    {
        if ($this->bridgeService->supportsCountry($country)) {
            return $this->bridgeService;
        }

        if ($this->finapiService->supportsCountry($country)) {
            return $this->finapiService;
        }

        return null;
    }

    /**
     * Get provider instance by name.
     */
    private function getProvider(string $provider): ?BankingProviderInterface
    {
        return match ($provider) {
            'bridge' => $this->bridgeService,
            'finapi' => $this->finapiService,
            default => null,
        };
    }

    /**
     * Mask IBAN for display.
     */
    private function maskIban(string $iban): string
    {
        $length = strlen($iban);

        if ($length <= 8) {
            return $iban;
        }

        return substr($iban, 0, 4) . str_repeat('*', $length - 8) . substr($iban, -4);
    }
}
