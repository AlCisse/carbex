<?php

namespace App\Services\Banking;

use App\Models\BankAccount;
use App\Models\BankConnection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Bridge.io Open Banking Service for France.
 *
 * Bridge.io provides PSD2-compliant access to French banks via their API.
 *
 * @see https://docs.bridgeapi.io/
 */
class BridgeService implements BankingProviderInterface
{
    private const API_BASE_URL = 'https://api.bridgeapi.io/v2';
    private const SANDBOX_URL = 'https://api.bridgeapi.io/v2';

    private string $clientId;
    private string $clientSecret;
    private string $webhookSecret;
    private bool $sandbox;

    public function __construct()
    {
        $this->clientId = config('services.bridge.client_id', '');
        $this->clientSecret = config('services.bridge.client_secret', '');
        $this->webhookSecret = config('services.bridge.webhook_secret', '');
        $this->sandbox = config('services.bridge.sandbox', true);
    }

    public function getProvider(): string
    {
        return 'bridge';
    }

    public function getSupportedCountries(): array
    {
        return ['FR'];
    }

    public function supportsCountry(string $countryCode): bool
    {
        return in_array(strtoupper($countryCode), $this->getSupportedCountries());
    }

    public function getBanks(?string $country = null): Collection
    {
        $cacheKey = 'bridge_banks_' . ($country ?? 'all');

        return Cache::remember($cacheKey, 3600, function () {
            try {
                $response = $this->request('GET', '/banks');

                return collect($response['resources'] ?? [])->map(fn ($bank) => [
                    'id' => (string) $bank['id'],
                    'name' => $bank['name'],
                    'logo_url' => $bank['logo_url'] ?? null,
                    'country' => $bank['country_code'] ?? 'FR',
                    'capabilities' => $bank['capabilities'] ?? [],
                ]);
            } catch (\Exception $e) {
                Log::error('Bridge: Failed to fetch banks', ['error' => $e->getMessage()]);

                return collect();
            }
        });
    }

    public function initiateConnection(
        string $organizationId,
        string $bankId,
        string $redirectUrl
    ): array {
        $state = Str::random(40);

        // Store state for verification
        Cache::put(
            "bridge_state_{$state}",
            [
                'organization_id' => $organizationId,
                'bank_id' => $bankId,
                'redirect_url' => $redirectUrl,
            ],
            now()->addHour()
        );

        // Create connect session
        $response = $this->request('POST', '/connect/items/add', [
            'country' => 'fr',
            'bank_id' => (int) $bankId,
            'redirect_url' => $redirectUrl,
            'context' => $state,
        ]);

        return [
            'redirect_url' => $response['redirect_url'],
            'state' => $state,
            'expires_at' => now()->addHour(),
        ];
    }

    public function handleCallback(
        string $code,
        string $state,
        string $organizationId
    ): BankConnection {
        // Verify state
        $sessionData = Cache::pull("bridge_state_{$state}");

        if (! $sessionData || $sessionData['organization_id'] !== $organizationId) {
            throw new \InvalidArgumentException('Invalid or expired state');
        }

        // Exchange code for tokens
        $tokenResponse = $this->request('POST', '/connect/items/add/complete', [
            'code' => $code,
        ]);

        // Create or update connection
        $connection = BankConnection::updateOrCreate(
            [
                'organization_id' => $organizationId,
                'provider' => 'bridge',
                'provider_item_id' => (string) $tokenResponse['item_id'],
            ],
            [
                'id' => Str::uuid()->toString(),
                'provider_bank_id' => $sessionData['bank_id'],
                'bank_name' => $tokenResponse['bank_name'] ?? 'Unknown Bank',
                'status' => 'active',
                'access_token' => $tokenResponse['access_token'] ?? null,
                'refresh_token' => $tokenResponse['refresh_token'] ?? null,
                'token_expires_at' => isset($tokenResponse['expires_in'])
                    ? now()->addSeconds($tokenResponse['expires_in'])
                    : now()->addDays(90),
                'consent_expires_at' => now()->addDays(90),
                'last_sync_at' => null,
                'metadata' => [
                    'item_id' => $tokenResponse['item_id'],
                    'status' => $tokenResponse['status'] ?? null,
                ],
            ]
        );

        // Sync accounts immediately
        $this->syncAccounts($connection);

        return $connection;
    }

    public function refreshToken(BankConnection $connection): BankConnection
    {
        if (! $connection->refresh_token) {
            throw new \RuntimeException('No refresh token available');
        }

        try {
            $response = $this->request('POST', '/connect/items/refresh', [
                'refresh_token' => $connection->refresh_token,
            ]);

            $connection->update([
                'access_token' => $response['access_token'],
                'refresh_token' => $response['refresh_token'] ?? $connection->refresh_token,
                'token_expires_at' => isset($response['expires_in'])
                    ? now()->addSeconds($response['expires_in'])
                    : now()->addDays(90),
            ]);
        } catch (\Exception $e) {
            Log::error('Bridge: Token refresh failed', [
                'connection_id' => $connection->id,
                'error' => $e->getMessage(),
            ]);

            $connection->update([
                'status' => 'error',
                'error_message' => 'Token refresh failed: ' . $e->getMessage(),
            ]);

            throw $e;
        }

        return $connection->fresh();
    }

    public function isConnectionValid(BankConnection $connection): bool
    {
        if ($connection->status !== 'active') {
            return false;
        }

        if ($connection->consent_expires_at && $connection->consent_expires_at->isPast()) {
            return false;
        }

        // Check token expiry
        if ($connection->token_expires_at && $connection->token_expires_at->isPast()) {
            try {
                $this->refreshToken($connection);
            } catch (\Exception $e) {
                return false;
            }
        }

        return true;
    }

    public function getAccounts(BankConnection $connection): Collection
    {
        $this->ensureValidToken($connection);

        $response = $this->requestWithAuth(
            'GET',
            '/accounts',
            [],
            $connection->access_token
        );

        return collect($response['resources'] ?? [])->map(fn ($account) => [
            'id' => (string) $account['id'],
            'name' => $account['name'],
            'iban' => $account['iban'] ?? null,
            'currency' => $account['currency_code'] ?? 'EUR',
            'balance' => $account['balance'] ?? 0,
            'type' => $this->mapAccountType($account['type'] ?? 'checking'),
            'raw_data' => $account,
        ]);
    }

    public function syncAccounts(BankConnection $connection): Collection
    {
        $accounts = $this->getAccounts($connection);
        $syncedAccounts = collect();

        foreach ($accounts as $accountData) {
            $account = BankAccount::updateOrCreate(
                [
                    'bank_connection_id' => $connection->id,
                    'provider_account_id' => $accountData['id'],
                ],
                [
                    'id' => Str::uuid()->toString(),
                    'organization_id' => $connection->organization_id,
                    'name' => $accountData['name'],
                    'iban' => $accountData['iban'],
                    'currency' => $accountData['currency'],
                    'balance' => $accountData['balance'],
                    'type' => $accountData['type'],
                    'is_active' => true,
                    'last_sync_at' => now(),
                    'metadata' => $accountData['raw_data'],
                ]
            );

            $syncedAccounts->push($account);
        }

        $connection->update(['last_sync_at' => now()]);

        return $syncedAccounts;
    }

    public function getTransactions(
        BankAccount $account,
        ?\DateTimeInterface $from = null,
        ?\DateTimeInterface $to = null
    ): Collection {
        $connection = $account->bankConnection;
        $this->ensureValidToken($connection);

        $params = [
            'account_id' => $account->provider_account_id,
        ];

        if ($from) {
            $params['since'] = $from->format('Y-m-d');
        }

        if ($to) {
            $params['until'] = $to->format('Y-m-d');
        }

        $allTransactions = collect();
        $cursor = null;

        do {
            if ($cursor) {
                $params['after'] = $cursor;
            }

            $response = $this->requestWithAuth(
                'GET',
                '/transactions',
                $params,
                $connection->access_token
            );

            $transactions = collect($response['resources'] ?? [])->map(fn ($tx) => [
                'id' => (string) $tx['id'],
                'date' => $tx['date'],
                'amount' => $tx['amount'],
                'currency' => $tx['currency_code'] ?? 'EUR',
                'description' => $tx['description'] ?? $tx['clean_description'] ?? '',
                'clean_description' => $tx['clean_description'] ?? null,
                'counterparty_name' => $tx['counterparty_name'] ?? null,
                'counterparty_iban' => $tx['counterparty_iban'] ?? null,
                'mcc_code' => $tx['category_id'] ? $this->mapBridgeCategory($tx['category_id']) : null,
                'category' => $tx['category_id'] ?? null,
                'type' => $tx['amount'] >= 0 ? 'credit' : 'debit',
                'status' => $tx['is_future'] ? 'pending' : 'booked',
                'raw_data' => $tx,
            ]);

            $allTransactions = $allTransactions->merge($transactions);

            $cursor = $response['pagination']['next_cursor'] ?? null;
        } while ($cursor);

        return $allTransactions;
    }

    public function disconnect(BankConnection $connection): bool
    {
        try {
            $this->ensureValidToken($connection);

            $this->requestWithAuth(
                'DELETE',
                '/items/' . $connection->provider_item_id,
                [],
                $connection->access_token
            );

            $connection->update([
                'status' => 'disconnected',
                'access_token' => null,
                'refresh_token' => null,
            ]);

            // Deactivate accounts
            $connection->accounts()->update(['is_active' => false]);

            return true;
        } catch (\Exception $e) {
            Log::error('Bridge: Disconnect failed', [
                'connection_id' => $connection->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    public function getConnectionStatus(BankConnection $connection): array
    {
        try {
            $this->ensureValidToken($connection);

            $response = $this->requestWithAuth(
                'GET',
                '/items/' . $connection->provider_item_id,
                [],
                $connection->access_token
            );

            return [
                'status' => $response['status'] ?? 'unknown',
                'last_sync' => $connection->last_sync_at,
                'error' => $response['status_code_info'] ?? null,
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'last_sync' => $connection->last_sync_at,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Verify webhook signature.
     */
    public function verifyWebhookSignature(string $payload, string $signature): bool
    {
        $expected = hash_hmac('sha256', $payload, $this->webhookSecret);

        return hash_equals($expected, $signature);
    }

    /**
     * Make API request.
     */
    private function request(string $method, string $endpoint, array $data = []): array
    {
        $url = self::API_BASE_URL . $endpoint;

        $response = Http::withHeaders([
            'Bridge-Version' => '2021-06-01',
            'Client-Id' => $this->clientId,
            'Client-Secret' => $this->clientSecret,
        ])->$method($url, $data);

        if (! $response->successful()) {
            Log::error('Bridge API error', [
                'endpoint' => $endpoint,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            throw new \RuntimeException(
                'Bridge API error: ' . ($response->json('message') ?? $response->body())
            );
        }

        return $response->json();
    }

    /**
     * Make authenticated API request.
     */
    private function requestWithAuth(
        string $method,
        string $endpoint,
        array $data = [],
        string $accessToken = ''
    ): array {
        $url = self::API_BASE_URL . $endpoint;

        $request = Http::withHeaders([
            'Bridge-Version' => '2021-06-01',
            'Client-Id' => $this->clientId,
            'Client-Secret' => $this->clientSecret,
            'Authorization' => 'Bearer ' . $accessToken,
        ]);

        $response = $method === 'GET'
            ? $request->get($url, $data)
            : $request->$method($url, $data);

        if (! $response->successful()) {
            throw new \RuntimeException(
                'Bridge API error: ' . ($response->json('message') ?? $response->body())
            );
        }

        return $response->json();
    }

    /**
     * Ensure connection has valid token.
     */
    private function ensureValidToken(BankConnection $connection): void
    {
        if ($connection->token_expires_at && $connection->token_expires_at->isPast()) {
            $this->refreshToken($connection);
        }
    }

    /**
     * Map Bridge account type to internal type.
     */
    private function mapAccountType(string $type): string
    {
        return match ($type) {
            'checking' => 'checking',
            'savings' => 'savings',
            'card' => 'card',
            'loan' => 'loan',
            'brokerage' => 'investment',
            default => 'other',
        };
    }

    /**
     * Map Bridge category ID to MCC code.
     */
    private function mapBridgeCategory(int $categoryId): ?string
    {
        // Bridge uses their own category system
        // Map to MCC codes for consistency
        $mapping = [
            1 => '5411',   // Groceries
            2 => '5812',   // Restaurants
            3 => '4111',   // Transport
            4 => '7011',   // Hotels
            5 => '5311',   // Department stores
            // Add more mappings as needed
        ];

        return $mapping[$categoryId] ?? null;
    }
}
