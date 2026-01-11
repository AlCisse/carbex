<?php

namespace App\Services\Banking;

use App\Models\BankAccount;
use App\Models\BankConnection;
use Illuminate\Support\Collection;

/**
 * Interface for Open Banking providers.
 *
 * Implementations:
 * - BridgeService: France (via Bridge.io)
 * - FinapiService: Germany (via Finapi)
 */
interface BankingProviderInterface
{
    /**
     * Get the provider identifier.
     */
    public function getProvider(): string;

    /**
     * Get supported countries.
     *
     * @return array<string>
     */
    public function getSupportedCountries(): array;

    /**
     * Check if provider supports a country.
     */
    public function supportsCountry(string $countryCode): bool;

    /**
     * Get list of available banks.
     *
     * @return Collection<int, array{id: string, name: string, logo_url: ?string, country: string}>
     */
    public function getBanks(?string $country = null): Collection;

    /**
     * Initiate OAuth connection flow.
     *
     * @return array{redirect_url: string, state: string, expires_at: \DateTimeInterface}
     */
    public function initiateConnection(
        string $organizationId,
        string $bankId,
        string $redirectUrl
    ): array;

    /**
     * Complete OAuth callback and create connection.
     */
    public function handleCallback(
        string $code,
        string $state,
        string $organizationId
    ): BankConnection;

    /**
     * Refresh access token if expired.
     */
    public function refreshToken(BankConnection $connection): BankConnection;

    /**
     * Check if connection is valid and active.
     */
    public function isConnectionValid(BankConnection $connection): bool;

    /**
     * Get accounts for a connection.
     *
     * @return Collection<int, array{
     *     id: string,
     *     name: string,
     *     iban: ?string,
     *     currency: string,
     *     balance: float,
     *     type: string
     * }>
     */
    public function getAccounts(BankConnection $connection): Collection;

    /**
     * Sync accounts from provider.
     *
     * @return Collection<int, BankAccount>
     */
    public function syncAccounts(BankConnection $connection): Collection;

    /**
     * Get transactions for an account.
     *
     * @return Collection<int, array{
     *     id: string,
     *     date: string,
     *     amount: float,
     *     currency: string,
     *     description: string,
     *     counterparty_name: ?string,
     *     counterparty_iban: ?string,
     *     mcc_code: ?string,
     *     category: ?string,
     *     type: string,
     *     status: string,
     *     raw_data: array
     * }>
     */
    public function getTransactions(
        BankAccount $account,
        ?\DateTimeInterface $from = null,
        ?\DateTimeInterface $to = null
    ): Collection;

    /**
     * Disconnect and revoke access.
     */
    public function disconnect(BankConnection $connection): bool;

    /**
     * Get connection status details.
     *
     * @return array{status: string, last_sync: ?\DateTimeInterface, error: ?string}
     */
    public function getConnectionStatus(BankConnection $connection): array;
}
