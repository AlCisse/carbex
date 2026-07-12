<?php

namespace App\Services\Energy;

use App\Models\EnergyConnection;
use Carbon\Carbon;

/**
 * Energy Provider Interface
 *
 * Common interface for energy data providers (Enedis, GRDF, etc.)
 */
interface EnergyProviderInterface
{
    /**
     * Get the provider name.
     */
    public function getName(): string;

    /**
     * Get the energy type (electricity, gas).
     */
    public function getEnergyType(): string;

    /**
     * Get the authorization URL for OAuth flow.
     */
    public function getAuthorizationUrl(string $state, array $scopes = []): string;

    /**
     * Exchange authorization code for tokens.
     */
    public function exchangeCode(string $code): array;

    /**
     * Refresh access token.
     */
    public function refreshToken(EnergyConnection $connection): array;

    /**
     * Get customer/meter information.
     */
    public function getCustomerInfo(EnergyConnection $connection): array;

    /**
     * Get consumption data for a date range.
     */
    public function getConsumption(
        EnergyConnection $connection,
        Carbon $startDate,
        Carbon $endDate,
        string $granularity = 'daily'
    ): array;

    /**
     * Get the maximum historical data range.
     */
    public function getMaxHistoryMonths(): int;

    /**
     * Check if the connection is valid.
     */
    public function validateConnection(EnergyConnection $connection): bool;

    /**
     * Revoke access.
     */
    public function revokeAccess(EnergyConnection $connection): bool;
}
