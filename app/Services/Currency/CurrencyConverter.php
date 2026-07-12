<?php

namespace App\Services\Currency;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

/**
 * Currency Converter Service
 *
 * Handles currency conversion using European Central Bank (ECB) exchange rates.
 * Supports caching for performance and fallback mechanisms for reliability.
 */
class CurrencyConverter
{
    /**
     * ECB exchange rates API endpoint.
     */
    protected const ECB_API_URL = 'https://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml';

    /**
     * ECB historical rates API endpoint.
     */
    protected const ECB_HISTORICAL_URL = 'https://www.ecb.europa.eu/stats/eurofxref/eurofxref-hist-90d.xml';

    /**
     * Cache key for exchange rates.
     */
    protected const CACHE_KEY = 'ecb_exchange_rates';

    /**
     * Cache key for historical rates.
     */
    protected const HISTORICAL_CACHE_KEY = 'ecb_historical_rates';

    /**
     * Cache TTL in seconds (24 hours).
     */
    protected const CACHE_TTL = 86400;

    /**
     * Base currency (EUR for ECB rates).
     */
    protected const BASE_CURRENCY = 'EUR';

    /**
     * Fallback rates in case ECB API is unavailable.
     * These should be updated periodically.
     */
    protected array $fallbackRates = [
        'USD' => 1.09,
        'GBP' => 0.86,
        'CHF' => 0.95,
        'JPY' => 160.50,
        'CAD' => 1.47,
        'AUD' => 1.65,
        'CNY' => 7.85,
        'SEK' => 11.20,
        'NOK' => 11.65,
        'DKK' => 7.46,
        'PLN' => 4.32,
        'CZK' => 25.10,
        'HUF' => 395.00,
        'RON' => 4.97,
        'BGN' => 1.96,
        'HRK' => 7.53,
        'INR' => 91.00,
        'BRL' => 5.35,
        'MXN' => 18.80,
        'ZAR' => 20.50,
        'SGD' => 1.46,
        'HKD' => 8.50,
        'KRW' => 1420.00,
        'NZD' => 1.78,
    ];

    /**
     * Convert amount from one currency to another.
     */
    public function convert(
        float $amount,
        string $fromCurrency,
        string $toCurrency,
        ?string $date = null
    ): float {
        // Same currency, no conversion needed
        if ($fromCurrency === $toCurrency) {
            return $amount;
        }

        $rates = $date ? $this->getHistoricalRates($date) : $this->getRates();

        // Convert to EUR first (base currency)
        $amountInEur = $fromCurrency === self::BASE_CURRENCY
            ? $amount
            : $amount / $this->getRate($rates, $fromCurrency);

        // Convert from EUR to target currency
        return $toCurrency === self::BASE_CURRENCY
            ? $amountInEur
            : $amountInEur * $this->getRate($rates, $toCurrency);
    }

    /**
     * Convert amount to EUR.
     */
    public function toEur(float $amount, string $fromCurrency, ?string $date = null): float
    {
        return $this->convert($amount, $fromCurrency, self::BASE_CURRENCY, $date);
    }

    /**
     * Convert amount from EUR.
     */
    public function fromEur(float $amount, string $toCurrency, ?string $date = null): float
    {
        return $this->convert($amount, self::BASE_CURRENCY, $toCurrency, $date);
    }

    /**
     * Get current exchange rate between two currencies.
     */
    public function getExchangeRate(string $fromCurrency, string $toCurrency): float
    {
        if ($fromCurrency === $toCurrency) {
            return 1.0;
        }

        $rates = $this->getRates();

        $fromRate = $fromCurrency === self::BASE_CURRENCY ? 1.0 : $this->getRate($rates, $fromCurrency);
        $toRate = $toCurrency === self::BASE_CURRENCY ? 1.0 : $this->getRate($rates, $toCurrency);

        return $toRate / $fromRate;
    }

    /**
     * Get all available exchange rates.
     */
    public function getRates(): array
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            return $this->fetchRatesFromEcb();
        });
    }

    /**
     * Get historical rates for a specific date.
     */
    public function getHistoricalRates(string $date): array
    {
        $historicalRates = Cache::remember(self::HISTORICAL_CACHE_KEY, self::CACHE_TTL, function () {
            return $this->fetchHistoricalRatesFromEcb();
        });

        // Find the closest available date
        $targetDate = Carbon::parse($date)->format('Y-m-d');

        if (isset($historicalRates[$targetDate])) {
            return $historicalRates[$targetDate];
        }

        // Find the closest previous date
        $dates = array_keys($historicalRates);
        rsort($dates);

        foreach ($dates as $availableDate) {
            if ($availableDate <= $targetDate) {
                return $historicalRates[$availableDate];
            }
        }

        // Fallback to current rates
        return $this->getRates();
    }

    /**
     * Refresh exchange rates from ECB.
     */
    public function refreshRates(): array
    {
        Cache::forget(self::CACHE_KEY);
        Cache::forget(self::HISTORICAL_CACHE_KEY);

        return $this->getRates();
    }

    /**
     * Get rate for a specific currency.
     */
    protected function getRate(array $rates, string $currency): float
    {
        if (!isset($rates[$currency])) {
            Log::warning("Exchange rate not found for currency: {$currency}, using fallback");

            if (isset($this->fallbackRates[$currency])) {
                return $this->fallbackRates[$currency];
            }

            throw new \InvalidArgumentException("Unsupported currency: {$currency}");
        }

        return $rates[$currency];
    }

    /**
     * Fetch rates from ECB API.
     */
    protected function fetchRatesFromEcb(): array
    {
        try {
            $response = Http::timeout(10)->get(self::ECB_API_URL);

            if ($response->successful()) {
                return $this->parseEcbXml($response->body());
            }

            Log::warning('ECB API returned non-successful response', [
                'status' => $response->status(),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch ECB exchange rates', [
                'error' => $e->getMessage(),
            ]);
        }

        return $this->fallbackRates;
    }

    /**
     * Fetch historical rates from ECB API.
     */
    protected function fetchHistoricalRatesFromEcb(): array
    {
        try {
            $response = Http::timeout(30)->get(self::ECB_HISTORICAL_URL);

            if ($response->successful()) {
                return $this->parseEcbHistoricalXml($response->body());
            }
        } catch (\Exception $e) {
            Log::error('Failed to fetch ECB historical rates', [
                'error' => $e->getMessage(),
            ]);
        }

        // Return current rates with today's date as fallback
        return [Carbon::today()->format('Y-m-d') => $this->fallbackRates];
    }

    /**
     * Parse ECB XML response for daily rates.
     */
    protected function parseEcbXml(string $xml): array
    {
        $rates = [];

        try {
            $doc = new \SimpleXMLElement($xml);
            $doc->registerXPathNamespace('gesmes', 'http://www.gesmes.org/xml/2002-08-01');
            $doc->registerXPathNamespace('ecb', 'http://www.ecb.int/vocabulary/2002-08-01/eurofxref');

            $cubes = $doc->xpath('//ecb:Cube[@currency]');

            foreach ($cubes as $cube) {
                $currency = (string) $cube['currency'];
                $rate = (float) $cube['rate'];
                $rates[$currency] = $rate;
            }
        } catch (\Exception $e) {
            Log::error('Failed to parse ECB XML', ['error' => $e->getMessage()]);
            return $this->fallbackRates;
        }

        return !empty($rates) ? $rates : $this->fallbackRates;
    }

    /**
     * Parse ECB XML response for historical rates.
     */
    protected function parseEcbHistoricalXml(string $xml): array
    {
        $historicalRates = [];

        try {
            $doc = new \SimpleXMLElement($xml);
            $doc->registerXPathNamespace('gesmes', 'http://www.gesmes.org/xml/2002-08-01');
            $doc->registerXPathNamespace('ecb', 'http://www.ecb.int/vocabulary/2002-08-01/eurofxref');

            $timeCubes = $doc->xpath('//ecb:Cube[@time]');

            foreach ($timeCubes as $timeCube) {
                $date = (string) $timeCube['time'];
                $rates = [];

                foreach ($timeCube->children('http://www.ecb.int/vocabulary/2002-08-01/eurofxref') as $cube) {
                    if (isset($cube['currency']) && isset($cube['rate'])) {
                        $rates[(string) $cube['currency']] = (float) $cube['rate'];
                    }
                }

                if (!empty($rates)) {
                    $historicalRates[$date] = $rates;
                }
            }
        } catch (\Exception $e) {
            Log::error('Failed to parse ECB historical XML', ['error' => $e->getMessage()]);
        }

        return $historicalRates;
    }

    /**
     * Get list of supported currencies.
     */
    public function getSupportedCurrencies(): array
    {
        $rates = $this->getRates();

        return array_merge([self::BASE_CURRENCY], array_keys($rates));
    }

    /**
     * Check if a currency is supported.
     */
    public function isSupported(string $currency): bool
    {
        if ($currency === self::BASE_CURRENCY) {
            return true;
        }

        $rates = $this->getRates();

        return isset($rates[$currency]) || isset($this->fallbackRates[$currency]);
    }

    /**
     * Get the date of the last rate update.
     */
    public function getLastUpdateDate(): ?string
    {
        try {
            $response = Http::timeout(10)->get(self::ECB_API_URL);

            if ($response->successful()) {
                $doc = new \SimpleXMLElement($response->body());
                $doc->registerXPathNamespace('ecb', 'http://www.ecb.int/vocabulary/2002-08-01/eurofxref');

                $timeCube = $doc->xpath('//ecb:Cube[@time]');

                if (!empty($timeCube)) {
                    return (string) $timeCube[0]['time'];
                }
            }
        } catch (\Exception $e) {
            Log::error('Failed to get ECB update date', ['error' => $e->getMessage()]);
        }

        return null;
    }
}
