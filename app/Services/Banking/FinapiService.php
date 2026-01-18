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
 * Finapi Open Banking Service for Germany.
 *
 * Finapi provides PSD2-compliant access to German banks.
 *
 * @see https://docs.finapi.io/
 */
class FinapiService implements BankingProviderInterface
{
    private const API_BASE_URL = 'https://live.finapi.io/api/v2';
    private const SANDBOX_URL = 'https://sandbox.finapi.io/api/v2';

    private string $clientId;
    private string $clientSecret;
    private string $dataDecryptionKey;
    private bool $sandbox;

    public function __construct()
    {
        $this->clientId = config('services.finapi.client_id', '');
        $this->clientSecret = config('services.finapi.client_secret', '');
        $this->dataDecryptionKey = config('services.finapi.data_decryption_key', '');
        $this->sandbox = config('services.finapi.sandbox', true);
    }

    public function getProvider(): string
    {
        return 'finapi';
    }

    public function getSupportedCountries(): array
    {
        return ['DE', 'AT'];
    }

    public function supportsCountry(string $countryCode): bool
    {
        return in_array(strtoupper($countryCode), $this->getSupportedCountries());
    }

    private function getBaseUrl(): string
    {
        return $this->sandbox ? self::SANDBOX_URL : self::API_BASE_URL;
    }

    public function getBanks(?string $country = null): Collection
    {
        $cacheKey = 'finapi_banks_' . ($country ?? 'all');

        return Cache::remember($cacheKey, 3600, function () use ($country) {
            try {
                $clientToken = $this->getClientToken();

                $params = [
                    'isSupported' => true,
                    'paging' => ['page' => 1, 'perPage' => 500],
                ];

                if ($country) {
                    $params['country'] = strtoupper($country);
                }

                $response = $this->request('GET', '/banks', $params, $clientToken);

                return collect($response['banks'] ?? [])->map(fn ($bank) => [
                    'id' => (string) $bank['id'],
                    'name' => $bank['name'],
                    'logo_url' => $bank['iconUrl'] ?? null,
                    'country' => $bank['location'] ?? 'DE',
                    'bic' => $bank['bic'] ?? null,
                    'capabilities' => [
                        'ais' => $bank['isSupported'] ?? false,
                        'pis' => $bank['supportedPaymentTypes'] ?? [],
                    ],
                ]);
            } catch (\Exception $e) {
                Log::error('Finapi: Failed to fetch banks', ['error' => $e->getMessage()]);

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
            "finapi_state_{$state}",
            [
                'organization_id' => $organizationId,
                'bank_id' => $bankId,
                'redirect_url' => $redirectUrl,
            ],
            now()->addHour()
        );

        // Get or create user token
        $userToken = $this->getOrCreateUserToken($organizationId);

        // Create bank connection
        $response = $this->request('POST', '/bankConnections/import', [
            'bankId' => (int) $bankId,
            'skipPositionsDownload' => false,
            'loadOwnerData' => true,
            'storeSecrets' => false,
            'interface' => 'XS2A',
            'redirectUrl' => $redirectUrl,
            'callbacks' => [
                'finalised' => $redirectUrl . '?state=' . $state,
            ],
        ], $userToken);

        return [
            'redirect_url' => $response['url'] ?? ($response['webFormUrl'] ?? ''),
            'state' => $state,
            'expires_at' => now()->addHour(),
            'task_id' => $response['taskId'] ?? null,
        ];
    }

    public function handleCallback(
        string $code,
        string $state,
        string $organizationId
    ): BankConnection {
        // Verify state
        $sessionData = Cache::pull("finapi_state_{$state}");

        if (! $sessionData || $sessionData['organization_id'] !== $organizationId) {
            throw new \InvalidArgumentException('Invalid or expired state');
        }

        $userToken = $this->getOrCreateUserToken($organizationId);

        // Get bank connection details
        $connections = $this->request('GET', '/bankConnections', [
            'ids' => [$code],
        ], $userToken);

        $connectionData = $connections['connections'][0] ?? null;

        if (! $connectionData) {
            throw new \RuntimeException('Bank connection not found');
        }

        // Create or update connection
        $connection = BankConnection::updateOrCreate(
            [
                'organization_id' => $organizationId,
                'provider' => 'finapi',
                'provider_item_id' => (string) $connectionData['id'],
            ],
            [
                'id' => Str::uuid()->toString(),
                'provider_bank_id' => (string) $connectionData['bankId'],
                'bank_name' => $connectionData['bank']['name'] ?? 'Unknown Bank',
                'status' => $this->mapFinapiStatus($connectionData['status']),
                'access_token' => $userToken,
                'refresh_token' => null,
                'token_expires_at' => now()->addDays(90),
                'consent_expires_at' => isset($connectionData['consentExpiryDate'])
                    ? \Carbon\Carbon::parse($connectionData['consentExpiryDate'])
                    : now()->addDays(90),
                'last_sync_at' => null,
                'metadata' => [
                    'connection_id' => $connectionData['id'],
                    'interface' => $connectionData['interface'] ?? 'XS2A',
                    'iban' => $connectionData['iban'] ?? null,
                ],
            ]
        );

        // Sync accounts immediately
        $this->syncAccounts($connection);

        return $connection;
    }

    public function refreshToken(BankConnection $connection): BankConnection
    {
        // Finapi uses user-level tokens, refresh by getting new user token
        $userToken = $this->getOrCreateUserToken($connection->organization_id, true);

        $connection->update([
            'access_token' => $userToken,
            'token_expires_at' => now()->addDays(90),
        ]);

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

        return true;
    }

    public function getAccounts(BankConnection $connection): Collection
    {
        $userToken = $connection->access_token;

        $response = $this->request('GET', '/accounts', [
            'bankConnectionIds' => [$connection->provider_item_id],
        ], $userToken);

        return collect($response['accounts'] ?? [])->map(fn ($account) => [
            'id' => (string) $account['id'],
            'name' => $account['accountName'] ?? $account['accountHolderName'] ?? 'Account',
            'iban' => $account['iban'] ?? null,
            'currency' => $account['currency'] ?? 'EUR',
            'balance' => $account['balance'] ?? 0,
            'type' => $this->mapAccountType($account['accountType'] ?? 'Checking'),
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
        $userToken = $connection->access_token;

        $params = [
            'accountIds' => [$account->provider_account_id],
            'view' => 'userView',
            'order' => 'desc',
            'perPage' => 500,
        ];

        if ($from) {
            $params['minBankBookingDate'] = $from->format('Y-m-d');
        }

        if ($to) {
            $params['maxBankBookingDate'] = $to->format('Y-m-d');
        }

        $allTransactions = collect();
        $page = 1;

        do {
            $params['page'] = $page;

            $response = $this->request('GET', '/transactions', $params, $userToken);

            $transactions = collect($response['transactions'] ?? [])->map(fn ($tx) => [
                'id' => (string) $tx['id'],
                'date' => $tx['bankBookingDate'] ?? $tx['valueDate'],
                'amount' => $tx['amount'],
                'currency' => $tx['currency'] ?? 'EUR',
                'description' => $tx['purpose'] ?? '',
                'clean_description' => $this->cleanDescription($tx['purpose'] ?? ''),
                'counterparty_name' => $tx['counterpartName'] ?? null,
                'counterparty_iban' => $tx['counterpartIban'] ?? null,
                'mcc_code' => $tx['mcc'] ?? null,
                'category' => $tx['category']['name'] ?? null,
                'type' => $tx['amount'] >= 0 ? 'credit' : 'debit',
                'status' => 'booked',
                'raw_data' => $tx,
            ]);

            $allTransactions = $allTransactions->merge($transactions);

            $totalPages = $response['paging']['pageCount'] ?? 1;
            $page++;
        } while ($page <= $totalPages);

        return $allTransactions;
    }

    public function disconnect(BankConnection $connection): bool
    {
        try {
            $userToken = $connection->access_token;

            $this->request(
                'DELETE',
                '/bankConnections/' . $connection->provider_item_id,
                [],
                $userToken
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
            Log::error('Finapi: Disconnect failed', [
                'connection_id' => $connection->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    public function getConnectionStatus(BankConnection $connection): array
    {
        try {
            $userToken = $connection->access_token;

            $response = $this->request(
                'GET',
                '/bankConnections/' . $connection->provider_item_id,
                [],
                $userToken
            );

            return [
                'status' => $this->mapFinapiStatus($response['status'] ?? 'UNKNOWN'),
                'last_sync' => $connection->last_sync_at,
                'error' => $response['statusMessage'] ?? null,
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
     * Get client-level access token.
     */
    private function getClientToken(): string
    {
        $cacheKey = 'finapi_client_token';

        return Cache::remember($cacheKey, 3500, function () {
            $response = Http::asForm()->post($this->getBaseUrl() . '/../oauth/token', [
                'grant_type' => 'client_credentials',
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
            ]);

            if (! $response->successful()) {
                throw new \RuntimeException('Failed to get Finapi client token');
            }

            return $response->json('access_token');
        });
    }

    /**
     * Get or create user-level token.
     */
    private function getOrCreateUserToken(string $organizationId, bool $refresh = false): string
    {
        $cacheKey = "finapi_user_token_{$organizationId}";

        if ($refresh) {
            Cache::forget($cacheKey);
        }

        return Cache::remember($cacheKey, 3500, function () use ($organizationId) {
            $clientToken = $this->getClientToken();
            $userId = 'linscarbon_' . $organizationId;
            $password = hash('sha256', $organizationId . config('app.key'));

            // Try to get token for existing user
            try {
                $response = Http::asForm()->post($this->getBaseUrl() . '/../oauth/token', [
                    'grant_type' => 'password',
                    'client_id' => $this->clientId,
                    'client_secret' => $this->clientSecret,
                    'username' => $userId,
                    'password' => $password,
                ]);

                if ($response->successful()) {
                    return $response->json('access_token');
                }
            } catch (\Exception $e) {
                // User doesn't exist, create it
            }

            // Create new user
            $this->request('POST', '/users', [
                'id' => $userId,
                'password' => $password,
                'email' => $userId . '@linscarbon.local',
                'phone' => null,
                'isAutoUpdateEnabled' => true,
            ], $clientToken);

            // Get token for new user
            $response = Http::asForm()->post($this->getBaseUrl() . '/../oauth/token', [
                'grant_type' => 'password',
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'username' => $userId,
                'password' => $password,
            ]);

            if (! $response->successful()) {
                throw new \RuntimeException('Failed to get Finapi user token');
            }

            return $response->json('access_token');
        });
    }

    /**
     * Make API request.
     */
    private function request(string $method, string $endpoint, array $data = [], ?string $token = null): array
    {
        $url = $this->getBaseUrl() . $endpoint;

        $request = Http::withHeaders([
            'Accept' => 'application/json',
        ]);

        if ($token) {
            $request = $request->withToken($token);
        }

        $response = match ($method) {
            'GET' => $request->get($url, $data),
            'POST' => $request->post($url, $data),
            'PUT' => $request->put($url, $data),
            'PATCH' => $request->patch($url, $data),
            'DELETE' => $request->delete($url, $data),
            default => throw new \InvalidArgumentException("Unsupported method: {$method}"),
        };

        if (! $response->successful()) {
            Log::error('Finapi API error', [
                'endpoint' => $endpoint,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            throw new \RuntimeException(
                'Finapi API error: ' . ($response->json('errors.0.message') ?? $response->body())
            );
        }

        return $response->json() ?? [];
    }

    /**
     * Map Finapi account type to internal type.
     */
    private function mapAccountType(string $type): string
    {
        return match (strtolower($type)) {
            'checking', 'bausparen' => 'checking',
            'savings' => 'savings',
            'creditcard', 'credit_card' => 'card',
            'loan', 'mortgage' => 'loan',
            'securities', 'depot' => 'investment',
            default => 'other',
        };
    }

    /**
     * Map Finapi status to internal status.
     */
    private function mapFinapiStatus(string $status): string
    {
        return match (strtoupper($status)) {
            'COMPLETED', 'UPDATED' => 'active',
            'DOWNLOAD_IN_PROGRESS', 'UPDATE_IN_PROGRESS' => 'syncing',
            'DISABLED' => 'inactive',
            default => 'error',
        };
    }

    /**
     * Clean transaction description.
     */
    private function cleanDescription(string $description): string
    {
        // Remove common prefixes and clean up
        $description = preg_replace('/^(SEPA-|SVWZ\+|EREF\+|KREF\+|MREF\+)/', '', $description);
        $description = preg_replace('/\s+/', ' ', $description);

        return trim($description);
    }
}
