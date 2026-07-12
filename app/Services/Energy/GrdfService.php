<?php

namespace App\Services\Energy;

use App\Models\EnergyConnection;
use Carbon\Carbon;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * GRDF ADICT Service
 *
 * Integration with GRDF ADICT API for gas consumption data.
 *
 * @see https://api.grdf.fr/
 */
class GrdfService implements EnergyProviderInterface
{
    private array $config;
    private bool $isSandbox;
    private bool $isMock;

    public function __construct()
    {
        $this->config = config('energy.providers.grdf');
        $this->isSandbox = $this->config['sandbox'] ?? true;
        $this->isMock = $this->config['mock'] ?? true;
    }

    public function getName(): string
    {
        return 'GRDF';
    }

    public function getEnergyType(): string
    {
        return 'gas';
    }

    /**
     * Get OAuth2 authorization URL.
     *
     * User will be redirected here to consent to data access.
     */
    public function getAuthorizationUrl(string $state, array $scopes = []): string
    {
        if ($this->isMock) {
            return url('/energy/grdf/mock-auth?state=' . $state);
        }

        $baseUrl = $this->isSandbox
            ? $this->config['sandbox_auth_url']
            : $this->config['auth_url'];

        $defaultScopes = [
            'openid',
            'donnees_consos_gaz',
            'donnees_contractuelles',
            'donnees_techniques',
        ];

        $params = [
            'client_id' => $this->config['client_id'],
            'response_type' => 'code',
            'redirect_uri' => url($this->config['redirect_uri']),
            'scope' => implode(' ', $scopes ?: $defaultScopes),
            'state' => $state,
            'acr_values' => 'urn:grdf:acr:fga', // FranceConnect or GRDF account
        ];

        return $baseUrl . '/oauth2/authorize?' . http_build_query($params);
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

        $response = Http::asForm()->post($baseUrl . '/oauth2/token', [
            'grant_type' => 'authorization_code',
            'client_id' => $this->config['client_id'],
            'client_secret' => $this->config['client_secret'],
            'code' => $code,
            'redirect_uri' => url($this->config['redirect_uri']),
        ]);

        if (!$response->successful()) {
            Log::error('GRDF token exchange failed', [
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
            'pce' => $data['pce'] ?? null, // Point de Comptage et d'Estimation
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

        $response = Http::asForm()->post($baseUrl . '/oauth2/token', [
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
     * Get customer information (PCE details).
     */
    public function getCustomerInfo(EnergyConnection $connection): array
    {
        if ($this->isMock) {
            return $this->getMockCustomerInfo($connection);
        }

        $client = $this->getAuthenticatedClient($connection);
        $pce = $connection->provider_customer_id;

        $response = $client->get("/pce/{$pce}/donnees-techniques");

        if (!$response->successful()) {
            throw new \Exception('Failed to get customer info: ' . $response->body());
        }

        $data = $response->json();

        return [
            'pce' => $data['pce'] ?? $pce,
            'meter_type' => $data['type_compteur'] ?? null,
            'contract_type' => $data['type_offre'] ?? null,
            'address' => [
                'street' => $data['adresse']['voie'] ?? null,
                'postal_code' => $data['adresse']['code_postal'] ?? null,
                'city' => $data['adresse']['commune'] ?? null,
            ],
            'coefficient_conversion' => $data['coefficient_conversion'] ?? 11.2, // kWh/m³
        ];
    }

    /**
     * Get gas consumption data.
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
        $pce = $connection->provider_customer_id;

        // ADICT API endpoint
        $endpoint = "/pce/{$pce}/consommations";

        $response = $client->get($endpoint, [
            'date_debut' => $startDate->format('Y-m-d'),
            'date_fin' => $endDate->format('Y-m-d'),
            'frequence' => $this->mapGranularity($granularity),
        ]);

        if (!$response->successful()) {
            Log::error('GRDF consumption fetch failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            throw new \Exception('Failed to get consumption data: ' . $response->body());
        }

        return $response->json();
    }

    /**
     * Get consumption in energy units (kWh).
     */
    public function getConsumptionEnergy(
        EnergyConnection $connection,
        Carbon $startDate,
        Carbon $endDate
    ): array {
        if ($this->isMock) {
            return [];
        }

        $client = $this->getAuthenticatedClient($connection);
        $pce = $connection->provider_customer_id;

        $response = $client->get("/pce/{$pce}/consommations-energie", [
            'date_debut' => $startDate->format('Y-m-d'),
            'date_fin' => $endDate->format('Y-m-d'),
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
            $this->getCustomerInfo($connection);
            return true;
        } catch (\Exception $e) {
            Log::warning('GRDF connection validation failed', [
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

            Http::asForm()->post($baseUrl . '/oauth2/revoke', [
                'client_id' => $this->config['client_id'],
                'client_secret' => $this->config['client_secret'],
                'token' => decrypt($connection->access_token),
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('GRDF revoke failed', ['error' => $e->getMessage()]);
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
     * Map granularity to GRDF API format.
     */
    private function mapGranularity(string $granularity): string
    {
        return match ($granularity) {
            'hourly' => 'horaire',
            'daily' => 'journalier',
            'monthly' => 'mensuel',
            default => 'journalier',
        };
    }

    /**
     * Mock tokens for development.
     */
    private function getMockTokens(): array
    {
        return [
            'access_token' => 'mock_grdf_access_token_' . uniqid(),
            'refresh_token' => 'mock_grdf_refresh_token_' . uniqid(),
            'expires_in' => 3600,
            'pce' => 'GI' . str_pad(mt_rand(1, 999999999999), 14, '0', STR_PAD_LEFT), // Mock PCE
        ];
    }

    /**
     * Mock customer info for development.
     */
    private function getMockCustomerInfo(EnergyConnection $connection): array
    {
        return [
            'pce' => $connection->provider_customer_id ?? 'GI12345678901234',
            'meter_type' => 'Gazpar',
            'contract_type' => 'T1',
            'address' => [
                'street' => '1 rue de la Paix',
                'postal_code' => '75001',
                'city' => 'Paris',
            ],
            'coefficient_conversion' => 11.2,
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

        // Coefficient de conversion moyen (PCS/PCI)
        $conversionCoef = 11.2; // kWh/m³

        while ($current <= $endDate) {
            // Daily gas consumption (realistic for French household)
            $baseDaily = 3.5; // m³/day average
            // Higher consumption in winter
            $seasonMultiplier = match (true) {
                $current->month >= 11 || $current->month <= 2 => 3.5, // Winter heating
                $current->month >= 3 && $current->month <= 4 => 2.0, // Spring
                $current->month >= 9 && $current->month <= 10 => 2.0, // Fall
                default => 0.5, // Summer (hot water only)
            };
            $randomVariation = (mt_rand(70, 130) / 100);

            $volumeM3 = round($baseDaily * $seasonMultiplier * $randomVariation, 3);
            $energyKwh = round($volumeM3 * $conversionCoef, 3);

            $data[] = [
                'date_debut' => $current->format('Y-m-d'),
                'date_fin' => $current->format('Y-m-d'),
                'consommation_m3' => $volumeM3,
                'consommation_kwh' => $energyKwh,
                'coefficient_conversion' => $conversionCoef,
                'qualite' => 'mesure',
            ];

            $current->addDay();
        }

        return [
            'pce' => $connection->provider_customer_id,
            'consommations' => $data,
        ];
    }
}
