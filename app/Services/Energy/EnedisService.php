<?php

namespace App\Services\Energy;

use App\Models\EnergyConnection;
use Carbon\Carbon;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Enedis DataConnect Service
 *
 * Integration with Enedis DataConnect API for electricity consumption data.
 *
 * @see https://datahub-enedis.fr/data-connect/
 */
class EnedisService implements EnergyProviderInterface
{
    private array $config;
    private bool $isSandbox;
    private bool $isMock;

    public function __construct()
    {
        $this->config = config('energy.providers.enedis');
        $this->isSandbox = $this->config['sandbox'] ?? true;
        $this->isMock = $this->config['mock'] ?? true;
    }

    public function getName(): string
    {
        return 'Enedis';
    }

    public function getEnergyType(): string
    {
        return 'electricity';
    }

    /**
     * Get OAuth2 authorization URL.
     *
     * User will be redirected here to consent to data access.
     */
    public function getAuthorizationUrl(string $state, array $scopes = []): string
    {
        if ($this->isMock) {
            return url('/energy/enedis/mock-auth?state=' . $state);
        }

        $baseUrl = $this->isSandbox
            ? $this->config['sandbox_auth_url']
            : $this->config['auth_url'];

        $defaultScopes = [
            'consumption_load_curve',
            'consumption_max_power',
            'customers_identity',
            'customers_contact',
        ];

        $params = [
            'client_id' => $this->config['client_id'],
            'response_type' => 'code',
            'redirect_uri' => url($this->config['redirect_uri']),
            'scope' => implode(' ', $scopes ?: $defaultScopes),
            'state' => $state,
            'duration' => 'P1Y', // 1 year consent
        ];

        return $baseUrl . '/oauth2/v3/authorize?' . http_build_query($params);
    }

    /**
     * Exchange authorization code for access tokens.
     */
    public function exchangeCode(string $code): array
    {
        if ($this->isMock) {
            return $this->getMockTokens();
        }

        $baseUrl = $this->isSandbox
            ? $this->config['sandbox_auth_url']
            : $this->config['auth_url'];

        $response = Http::asForm()->post($baseUrl . '/oauth2/v3/token', [
            'grant_type' => 'authorization_code',
            'client_id' => $this->config['client_id'],
            'client_secret' => $this->config['client_secret'],
            'code' => $code,
            'redirect_uri' => url($this->config['redirect_uri']),
        ]);

        if (!$response->successful()) {
            Log::error('Enedis token exchange failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            throw new \Exception('Failed to exchange authorization code: ' . $response->body());
        }

        $data = $response->json();

        return [
            'access_token' => $data['access_token'],
            'refresh_token' => $data['refresh_token'] ?? null,
            'expires_in' => $data['expires_in'] ?? 3600,
            'usage_points_id' => $data['usage_points_id'] ?? null, // PRM
        ];
    }

    /**
     * Refresh access token.
     */
    public function refreshToken(EnergyConnection $connection): array
    {
        if ($this->isMock) {
            return $this->getMockTokens();
        }

        $baseUrl = $this->isSandbox
            ? $this->config['sandbox_auth_url']
            : $this->config['auth_url'];

        $response = Http::asForm()->post($baseUrl . '/oauth2/v3/token', [
            'grant_type' => 'refresh_token',
            'client_id' => $this->config['client_id'],
            'client_secret' => $this->config['client_secret'],
            'refresh_token' => decrypt($connection->refresh_token),
        ]);

        if (!$response->successful()) {
            throw new \Exception('Failed to refresh token: ' . $response->body());
        }

        $data = $response->json();

        return [
            'access_token' => $data['access_token'],
            'refresh_token' => $data['refresh_token'] ?? null,
            'expires_in' => $data['expires_in'] ?? 3600,
        ];
    }

    /**
     * Get customer information (PRM details).
     */
    public function getCustomerInfo(EnergyConnection $connection): array
    {
        if ($this->isMock) {
            return $this->getMockCustomerInfo($connection);
        }

        $client = $this->getAuthenticatedClient($connection);
        $usagePointId = $connection->provider_customer_id;

        $response = $client->get("/metering_data_dc/v5/usage_points/{$usagePointId}");

        if (!$response->successful()) {
            throw new \Exception('Failed to get customer info: ' . $response->body());
        }

        $data = $response->json();

        return [
            'usage_point_id' => $data['usage_point']['usage_point_id'] ?? $usagePointId,
            'meter_type' => $data['usage_point']['meter_type'] ?? null,
            'contract_type' => $data['usage_point']['usage_point_status'] ?? null,
            'address' => $data['usage_point']['usage_point_addresses']['usage_point_address'] ?? null,
        ];
    }

    /**
     * Get electricity consumption data.
     */
    public function getConsumption(
        EnergyConnection $connection,
        Carbon $startDate,
        Carbon $endDate,
        string $granularity = 'daily'
    ): array {
        if ($this->isMock) {
            return $this->getMockConsumption($connection, $startDate, $endDate, $granularity);
        }

        $client = $this->getAuthenticatedClient($connection);
        $usagePointId = $connection->provider_customer_id;

        // Choose endpoint based on granularity
        $endpoint = match ($granularity) {
            'hourly' => "/metering_data_clc/v5/consumption_load_curve",
            'daily' => "/metering_data_dcr/v5/daily_consumption",
            'monthly' => "/metering_data_dcr/v5/daily_consumption", // Will aggregate
            default => "/metering_data_dcr/v5/daily_consumption",
        };

        $response = $client->get($endpoint, [
            'usage_point_id' => $usagePointId,
            'start' => $startDate->format('Y-m-d'),
            'end' => $endDate->format('Y-m-d'),
        ]);

        if (!$response->successful()) {
            Log::error('Enedis consumption fetch failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            throw new \Exception('Failed to get consumption data: ' . $response->body());
        }

        return $response->json();
    }

    /**
     * Get maximum power data.
     */
    public function getMaxPower(
        EnergyConnection $connection,
        Carbon $startDate,
        Carbon $endDate
    ): array {
        if ($this->isMock) {
            return [];
        }

        $client = $this->getAuthenticatedClient($connection);
        $usagePointId = $connection->provider_customer_id;

        $response = $client->get("/metering_data_dpm/v5/daily_production_max_power", [
            'usage_point_id' => $usagePointId,
            'start' => $startDate->format('Y-m-d'),
            'end' => $endDate->format('Y-m-d'),
        ]);

        if (!$response->successful()) {
            return [];
        }

        return $response->json();
    }

    public function getMaxHistoryMonths(): int
    {
        return $this->config['history_months'] ?? 36;
    }

    public function validateConnection(EnergyConnection $connection): bool
    {
        if ($this->isMock) {
            return true;
        }

        try {
            // Try to fetch customer info to validate connection
            $this->getCustomerInfo($connection);
            return true;
        } catch (\Exception $e) {
            Log::warning('Enedis connection validation failed', [
                'connection_id' => $connection->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    public function revokeAccess(EnergyConnection $connection): bool
    {
        if ($this->isMock) {
            return true;
        }

        try {
            $baseUrl = $this->isSandbox
                ? $this->config['sandbox_auth_url']
                : $this->config['auth_url'];

            Http::asForm()->post($baseUrl . '/oauth2/v3/revoke', [
                'client_id' => $this->config['client_id'],
                'client_secret' => $this->config['client_secret'],
                'token' => decrypt($connection->access_token),
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Enedis revoke failed', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Get authenticated HTTP client.
     */
    private function getAuthenticatedClient(EnergyConnection $connection): PendingRequest
    {
        // Refresh token if needed
        if ($connection->isTokenExpired()) {
            $tokens = $this->refreshToken($connection);

            $connection->update([
                'access_token' => encrypt($tokens['access_token']),
                'refresh_token' => $tokens['refresh_token'] ? encrypt($tokens['refresh_token']) : $connection->refresh_token,
                'token_expires_at' => now()->addSeconds($tokens['expires_in'] - 60),
            ]);
        }

        $baseUrl = $this->isSandbox
            ? $this->config['sandbox_api_url']
            : $this->config['api_url'];

        return Http::baseUrl($baseUrl)
            ->withToken(decrypt($connection->access_token))
            ->acceptJson()
            ->timeout(30);
    }

    /**
     * Mock tokens for development.
     */
    private function getMockTokens(): array
    {
        return [
            'access_token' => 'mock_access_token_' . uniqid(),
            'refresh_token' => 'mock_refresh_token_' . uniqid(),
            'expires_in' => 3600,
            'usage_points_id' => '12345678901234', // Mock PRM
        ];
    }

    /**
     * Mock customer info for development.
     */
    private function getMockCustomerInfo(EnergyConnection $connection): array
    {
        return [
            'usage_point_id' => $connection->provider_customer_id ?? '12345678901234',
            'meter_type' => 'Linky',
            'contract_type' => 'BTINF',
            'address' => [
                'street' => '1 rue de la Paix',
                'postal_code' => '75001',
                'city' => 'Paris',
            ],
        ];
    }

    /**
     * Mock consumption data for development.
     */
    private function getMockConsumption(
        EnergyConnection $connection,
        Carbon $startDate,
        Carbon $endDate,
        string $granularity
    ): array {
        $data = [];
        $current = $startDate->copy();

        while ($current <= $endDate) {
            if ($granularity === 'hourly') {
                for ($hour = 0; $hour < 24; $hour++) {
                    // Simulate realistic consumption pattern
                    $baseConsumption = 0.5; // kWh base
                    $peakMultiplier = ($hour >= 7 && $hour <= 9) || ($hour >= 18 && $hour <= 22) ? 2.5 : 1;
                    $randomVariation = (mt_rand(80, 120) / 100);

                    $data[] = [
                        'date' => $current->format('Y-m-d'),
                        'time_start' => sprintf('%02d:00', $hour),
                        'time_end' => sprintf('%02d:00', ($hour + 1) % 24),
                        'value' => round($baseConsumption * $peakMultiplier * $randomVariation, 3),
                        'unit' => 'kWh',
                        'quality' => 'measured',
                    ];
                }
            } else {
                // Daily consumption (realistic average for French household)
                $baseDaily = 12; // kWh/day average
                $seasonMultiplier = $current->month >= 11 || $current->month <= 3 ? 1.4 : 0.8;
                $randomVariation = (mt_rand(70, 130) / 100);

                $data[] = [
                    'date' => $current->format('Y-m-d'),
                    'value' => round($baseDaily * $seasonMultiplier * $randomVariation, 3),
                    'unit' => 'kWh',
                    'quality' => 'measured',
                ];
            }

            $current->addDay();
        }

        return [
            'usage_point' => [
                'usage_point_id' => $connection->provider_customer_id,
            ],
            'meter_reading' => [
                'interval_reading' => $data,
            ],
        ];
    }
}
