<?php

namespace App\Services\Carbon;

use App\Models\Category;
use App\Models\EmissionFactor;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class FactorRepository
{
    /**
     * Cache TTL in seconds (1 hour).
     */
    private const CACHE_TTL = 3600;

    /**
     * Find emission factor by ID.
     */
    public function find(string $id): ?EmissionFactor
    {
        return EmissionFactor::find($id);
    }

    /**
     * Search emission factors using Meilisearch.
     */
    public function search(
        string $query,
        ?string $country = null,
        ?int $scope = null,
        ?string $unit = null,
        int $limit = 20
    ): Collection {
        $search = EmissionFactor::search($query);

        // Apply filters after search
        $results = $search->get();

        return $results->filter(function ($factor) use ($country, $scope, $unit) {
            if ($country && $factor->country && $factor->country !== $country) {
                return false;
            }
            if ($scope !== null && $factor->scope !== $scope) {
                return false;
            }
            if ($unit && $factor->unit !== $unit) {
                return false;
            }

            return $factor->isValid();
        })->take($limit);
    }

    /**
     * Find factors for a specific category.
     */
    public function findByCategory(string $categoryId, ?string $country = null): Collection
    {
        $cacheKey = "factors_category_{$categoryId}_{$country}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($categoryId, $country) {
            return EmissionFactor::where('category_id', $categoryId)
                ->when($country, fn ($q) => $q->forCountry($country))
                ->active()
                ->validAt(now()->toDateString())
                ->orderBy('factor_kg_co2e', 'desc')
                ->get();
        });
    }

    /**
     * Find factor by source and source ID.
     */
    public function findBySource(string $source, string $sourceId): ?EmissionFactor
    {
        return EmissionFactor::where('source', $source)
            ->where('source_id', $sourceId)
            ->first();
    }

    /**
     * Find best matching factor for a category and unit.
     */
    public function findBestMatch(
        string $categoryId,
        string $unit,
        ?string $country = null,
        ?string $date = null
    ): ?EmissionFactor {
        $date = $date ?? now()->toDateString();
        $cacheKey = "best_factor_{$categoryId}_{$unit}_{$country}_{$date}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($categoryId, $unit, $country, $date) {
            $query = EmissionFactor::where('category_id', $categoryId)
                ->where('unit', $unit)
                ->validAt($date)
                ->active();

            // Prefer country-specific factors
            if ($country) {
                $countryFactor = (clone $query)->where('country', $country)->first();
                if ($countryFactor) {
                    return $countryFactor;
                }
            }

            // Fall back to generic factor
            return $query->whereNull('country')->first()
                ?? $query->first();
        });
    }

    /**
     * Find default factors for spend-based calculation.
     */
    public function findSpendBasedFactor(
        string $categoryId,
        string $currency = 'EUR',
        ?string $country = null
    ): ?EmissionFactor {
        return $this->findBestMatch($categoryId, $currency, $country);
    }

    /**
     * Get all factors for a scope.
     */
    public function getByScope(int $scope, ?string $country = null): Collection
    {
        $cacheKey = "factors_scope_{$scope}_{$country}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($scope, $country) {
            return EmissionFactor::forScope($scope)
                ->when($country, fn ($q) => $q->forCountry($country))
                ->active()
                ->validAt(now()->toDateString())
                ->get();
        });
    }

    /**
     * Get factors from a specific source (ADEME, UBA, etc.).
     */
    public function getBySource(string $source): Collection
    {
        return EmissionFactor::fromSource($source)
            ->active()
            ->validAt(now()->toDateString())
            ->get();
    }

    /**
     * Get electricity factors for a country.
     */
    public function getElectricityFactor(string $country, string $method = 'location'): ?EmissionFactor
    {
        $cacheKey = "electricity_factor_{$country}_{$method}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($country, $method) {
            $query = EmissionFactor::where('unit', 'kWh')
                ->forScope(2)
                ->where('country', $country)
                ->active()
                ->validAt(now()->toDateString());

            if ($method === 'market') {
                $query->where('methodology', 'market-based');
            } else {
                $query->where('methodology', 'location-based');
            }

            return $query->first();
        });
    }

    /**
     * Get fuel factors by fuel type.
     */
    public function getFuelFactor(string $fuelType, ?string $country = null): ?EmissionFactor
    {
        $cacheKey = "fuel_factor_{$fuelType}_{$country}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($fuelType, $country) {
            return EmissionFactor::where('unit', 'liters')
                ->forScope(1)
                ->where('name', 'like', "%{$fuelType}%")
                ->when($country, fn ($q) => $q->forCountry($country))
                ->active()
                ->validAt(now()->toDateString())
                ->first();
        });
    }

    /**
     * Get travel factors by transport mode.
     */
    public function getTravelFactor(string $mode, ?string $country = null): ?EmissionFactor
    {
        $modes = [
            'flight' => ['unit' => 'passenger-km', 'scope' => 3],
            'train' => ['unit' => 'passenger-km', 'scope' => 3],
            'car' => ['unit' => 'km', 'scope' => 3],
            'taxi' => ['unit' => 'km', 'scope' => 3],
        ];

        if (! isset($modes[$mode])) {
            return null;
        }

        $config = $modes[$mode];
        $cacheKey = "travel_factor_{$mode}_{$country}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($mode, $config, $country) {
            return EmissionFactor::where('unit', $config['unit'])
                ->forScope($config['scope'])
                ->where('name', 'like', "%{$mode}%")
                ->when($country, fn ($q) => $q->forCountry($country))
                ->active()
                ->validAt(now()->toDateString())
                ->first();
        });
    }

    /**
     * Clear all factor caches.
     */
    public function clearCache(): void
    {
        Cache::tags(['emission_factors'])->flush();
    }

    /**
     * Get statistics about available factors.
     */
    public function getStatistics(): array
    {
        return Cache::remember('factor_statistics', self::CACHE_TTL, function () {
            return [
                'total' => EmissionFactor::count(),
                'active' => EmissionFactor::active()->count(),
                'by_source' => EmissionFactor::selectRaw('source, COUNT(*) as count')
                    ->groupBy('source')
                    ->pluck('count', 'source')
                    ->toArray(),
                'by_scope' => EmissionFactor::selectRaw('scope, COUNT(*) as count')
                    ->groupBy('scope')
                    ->pluck('count', 'scope')
                    ->toArray(),
                'by_country' => EmissionFactor::selectRaw('country, COUNT(*) as count')
                    ->whereNotNull('country')
                    ->groupBy('country')
                    ->pluck('count', 'country')
                    ->toArray(),
            ];
        });
    }
}
