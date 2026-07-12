<?php

namespace App\Services\Carbon\ScopeCalculators;

use App\Models\EmissionFactor;
use App\Services\Carbon\FactorRepository;

/**
 * Scope 2 Calculator - Indirect Energy Emissions
 *
 * Handles emissions from:
 * - Purchased electricity
 * - Purchased steam
 * - Purchased heating
 * - Purchased cooling
 *
 * Supports both location-based and market-based methods.
 */
class Scope2Calculator
{
    /**
     * Default grid emission factors (kg CO2e/kWh) by country.
     * Used as fallback when specific factors are not available.
     */
    private const DEFAULT_GRID_FACTORS = [
        'FR' => 0.0569,  // France (nuclear-heavy, low carbon)
        'DE' => 0.366,   // Germany (coal/renewables mix)
        'UK' => 0.233,   // UK
        'ES' => 0.259,   // Spain
        'IT' => 0.315,   // Italy
        'EU' => 0.276,   // EU average
    ];

    public function __construct(
        private ?FactorRepository $factorRepository = null
    ) {}

    /**
     * Calculate Scope 2 emissions.
     */
    public function calculate(float $quantity, EmissionFactor $factor, array $metadata = []): array
    {
        $method = $metadata['method'] ?? 'location';
        $unit = strtolower($factor->unit);

        return match ($unit) {
            'kwh' => $this->calculateFromElectricity($quantity, $factor, $metadata),
            'mwh' => $this->calculateFromElectricity($quantity * 1000, $factor, $metadata),
            'gj' => $this->calculateFromHeat($quantity, $factor, $metadata),
            'mj' => $this->calculateFromHeat($quantity / 1000, $factor, $metadata),
            default => $this->calculateDirect($quantity, $factor),
        };
    }

    /**
     * Calculate emissions from electricity consumption.
     */
    private function calculateFromElectricity(float $kwh, EmissionFactor $factor, array $metadata): array
    {
        $method = $metadata['method'] ?? 'location';
        $renewablePercentage = $metadata['renewable_percentage'] ?? 0;

        // Apply renewable energy reduction
        $effectiveKwh = $kwh * (1 - ($renewablePercentage / 100));

        // For market-based, check if we have supplier-specific factor
        if ($method === 'market' && isset($metadata['supplier_factor'])) {
            $factorValue = $metadata['supplier_factor'];
        } else {
            $factorValue = $factor->factor_kg_co2e;
        }

        $co2e = $effectiveKwh * $factorValue;

        return [
            'co2e_kg' => $co2e,
            'co2_kg' => $factor->factor_kg_co2 ? $effectiveKwh * $factor->factor_kg_co2 : null,
            'ch4_kg' => $factor->factor_kg_ch4 ? $effectiveKwh * $factor->factor_kg_ch4 : null,
            'n2o_kg' => $factor->factor_kg_n2o ? $effectiveKwh * $factor->factor_kg_n2o : null,
            'is_estimated' => false,
            'notes' => $this->buildNotes($kwh, $method, $renewablePercentage, $factor),
        ];
    }

    /**
     * Calculate emissions from heat/steam consumption.
     */
    private function calculateFromHeat(float $gj, EmissionFactor $factor, array $metadata): array
    {
        // Convert GJ to kWh if needed (1 GJ = 277.78 kWh)
        $kwh = $gj * 277.78;

        return [
            'co2e_kg' => $gj * $factor->factor_kg_co2e,
            'co2_kg' => $factor->factor_kg_co2 ? $gj * $factor->factor_kg_co2 : null,
            'ch4_kg' => null,
            'n2o_kg' => null,
            'is_estimated' => false,
            'notes' => "Heat: {$gj} GJ (~{$kwh} kWh)",
        ];
    }

    /**
     * Direct calculation for other units.
     */
    private function calculateDirect(float $quantity, EmissionFactor $factor): array
    {
        return [
            'co2e_kg' => $quantity * $factor->factor_kg_co2e,
            'co2_kg' => $factor->factor_kg_co2 ? $quantity * $factor->factor_kg_co2 : null,
            'ch4_kg' => $factor->factor_kg_ch4 ? $quantity * $factor->factor_kg_ch4 : null,
            'n2o_kg' => $factor->factor_kg_n2o ? $quantity * $factor->factor_kg_n2o : null,
            'is_estimated' => false,
            'notes' => null,
        ];
    }

    /**
     * Calculate using location-based method.
     */
    public function calculateLocationBased(float $kwh, string $country): array
    {
        $factor = $this->getGridFactor($country, 'location');

        return [
            'co2e_kg' => $kwh * $factor,
            'method' => 'location-based',
            'factor_used' => $factor,
            'country' => $country,
        ];
    }

    /**
     * Calculate using market-based method.
     */
    public function calculateMarketBased(
        float $kwh,
        string $country,
        ?float $supplierFactor = null,
        float $renewablePercentage = 0
    ): array {
        // Priority: 1. Supplier factor, 2. Residual mix, 3. Grid average
        if ($supplierFactor !== null) {
            $factor = $supplierFactor;
            $source = 'supplier';
        } else {
            $factor = $this->getResidualMixFactor($country);
            $source = 'residual_mix';
        }

        // Apply renewable reduction
        $effectiveKwh = $kwh * (1 - ($renewablePercentage / 100));

        return [
            'co2e_kg' => $effectiveKwh * $factor,
            'method' => 'market-based',
            'factor_used' => $factor,
            'factor_source' => $source,
            'renewable_percentage' => $renewablePercentage,
            'country' => $country,
        ];
    }

    /**
     * Get grid emission factor for a country.
     */
    private function getGridFactor(string $country, string $method = 'location'): float
    {
        // Try to get from repository
        if ($this->factorRepository) {
            $factor = $this->factorRepository->getElectricityFactor($country, $method);
            if ($factor) {
                return $factor->factor_kg_co2e;
            }
        }

        // Fallback to default
        return self::DEFAULT_GRID_FACTORS[$country] ?? self::DEFAULT_GRID_FACTORS['EU'];
    }

    /**
     * Get residual mix factor for market-based calculation.
     * Residual mix excludes tracked renewable energy.
     */
    private function getResidualMixFactor(string $country): float
    {
        // Residual mix factors are typically higher than grid average
        // as they exclude green tariffs and GOOs
        $residualMixFactors = [
            'FR' => 0.0876,   // France residual mix
            'DE' => 0.498,    // Germany residual mix
            'UK' => 0.312,    // UK residual mix
            'EU' => 0.380,    // EU average residual mix
        ];

        return $residualMixFactors[$country] ?? $residualMixFactors['EU'];
    }

    /**
     * Build notes for the calculation.
     */
    private function buildNotes(float $kwh, string $method, float $renewablePercentage, EmissionFactor $factor): string
    {
        $notes = "Electricity: {$kwh} kWh, method: {$method}";

        if ($renewablePercentage > 0) {
            $notes .= ", renewable: {$renewablePercentage}%";
        }

        return $notes;
    }

    /**
     * Estimate electricity consumption from bill amount.
     */
    public function estimateFromBill(float $amount, string $currency, string $country): float
    {
        // Average electricity prices per kWh (2024)
        $prices = [
            'FR' => 0.2276, // EUR/kWh
            'DE' => 0.4351, // EUR/kWh
        ];

        $pricePerKwh = $prices[$country] ?? 0.30;

        // Convert to EUR if needed
        if ($currency !== 'EUR') {
            // Simple conversion (should use real rates)
            $amount = match ($currency) {
                'USD' => $amount * 0.92,
                'GBP' => $amount * 1.17,
                'CHF' => $amount * 1.06,
                default => $amount,
            };
        }

        return $amount / $pricePerKwh;
    }
}
