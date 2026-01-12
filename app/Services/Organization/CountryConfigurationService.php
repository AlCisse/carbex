<?php

namespace App\Services\Organization;

use Illuminate\Support\Facades\Cache;

class CountryConfigurationService
{
    /**
     * Get configuration for a specific country.
     */
    public function getConfig(?string $countryCode): array
    {
        if (empty($countryCode)) {
            return $this->getDefaultConfig('');
        }

        return Cache::remember("country_config_{$countryCode}", now()->addHour(), function () use ($countryCode) {
            $config = config("countries.{$countryCode}", []);

            if (empty($config)) {
                // Return default config if country not found
                return $this->getDefaultConfig($countryCode);
            }

            return $this->formatConfig($config);
        });
    }

    /**
     * Get all supported countries.
     */
    public function getSupportedCountries(): array
    {
        return Cache::remember('supported_countries', now()->addDay(), function () {
            $countries = config('countries', []);

            return collect($countries)
                ->filter(fn ($config) => $config['supported'] ?? false)
                ->map(fn ($config, $code) => [
                    'code' => $code,
                    'name' => $config['name'],
                    'currency' => $config['currency'],
                    'locale' => $config['locale'],
                ])
                ->values()
                ->toArray();
        });
    }

    /**
     * Get banking provider for a country.
     */
    public function getBankingProvider(string $countryCode): ?array
    {
        $config = $this->getConfig($countryCode);

        return $config['banking'] ?? null;
    }

    /**
     * Get emission factor source for a country.
     */
    public function getEmissionSource(string $countryCode): string
    {
        $sources = [
            'FR' => 'ademe',
            'DE' => 'uba',
        ];

        return $sources[$countryCode] ?? 'ecoinvent';
    }

    /**
     * Get VAT rate for a country.
     */
    public function getVatRate(string $countryCode): float
    {
        $config = $this->getConfig($countryCode);

        return $config['vat_standard'] ?? 20.0;
    }

    /**
     * Get date format for a country.
     */
    public function getDateFormat(string $countryCode): string
    {
        $config = $this->getConfig($countryCode);

        return $config['date_format'] ?? 'd/m/Y';
    }

    /**
     * Get number format settings for a country.
     */
    public function getNumberFormat(string $countryCode): array
    {
        $config = $this->getConfig($countryCode);

        return [
            'decimal_separator' => $config['decimal_separator'] ?? ',',
            'thousands_separator' => $config['thousands_separator'] ?? ' ',
        ];
    }

    /**
     * Auto-configure organization based on country.
     */
    public function autoConfigureOrganization(string $countryCode): array
    {
        $config = $this->getConfig($countryCode);

        return [
            'country' => $countryCode,
            'timezone' => $config['timezone'] ?? 'Europe/Paris',
            'default_currency' => $config['currency'] ?? 'EUR',
            'fiscal_year_start_month' => 1, // January for both FR and DE
            'settings' => [
                'locale' => $config['locale'] ?? 'en',
                'date_format' => $config['date_format'] ?? 'd/m/Y',
                'number_format' => $this->getNumberFormat($countryCode),
                'emission_source' => $this->getEmissionSource($countryCode),
                'vat_rate' => $this->getVatRate($countryCode),
                'banking_provider' => $config['banking']['provider'] ?? null,
            ],
        ];
    }

    /**
     * Format configuration for response.
     */
    private function formatConfig(array $config): array
    {
        return [
            'name' => $config['name'] ?? '',
            'currency' => $config['currency'] ?? 'EUR',
            'currency_symbol' => $config['currency_symbol'] ?? '',
            'locale' => $config['locale'] ?? 'en',
            'timezone' => $config['timezone'] ?? 'UTC',
            'vat_standard' => $config['vat_standard'] ?? 20,
            'vat_reduced' => $config['vat_reduced'] ?? null,
            'date_format' => $config['date_format'] ?? 'd/m/Y',
            'decimal_separator' => $config['decimal_separator'] ?? ',',
            'thousands_separator' => $config['thousands_separator'] ?? ' ',
            'banking' => [
                'provider' => $config['banking']['provider'] ?? null,
                'supported' => ! empty($config['banking']['provider']),
            ],
            'regulations' => $config['regulations'] ?? [],
            'emission_source' => $this->getEmissionSource($config['code'] ?? ''),
        ];
    }

    /**
     * Get default configuration for unsupported countries.
     */
    private function getDefaultConfig(string $countryCode): array
    {
        return [
            'name' => $countryCode,
            'currency' => 'EUR',
            'currency_symbol' => '',
            'locale' => 'en',
            'timezone' => 'UTC',
            'vat_standard' => 20,
            'date_format' => 'd/m/Y',
            'decimal_separator' => '.',
            'thousands_separator' => ',',
            'banking' => [
                'provider' => null,
                'supported' => false,
            ],
            'regulations' => [],
            'emission_source' => 'ecoinvent',
        ];
    }
}
