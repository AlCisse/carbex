<?php

namespace App\Services\Carbon\ScopeCalculators;

use App\Models\EmissionFactor;

/**
 * Scope 1 Calculator - Direct Emissions
 *
 * Handles emissions from:
 * - Mobile combustion (company vehicles)
 * - Stationary combustion (boilers, furnaces, generators)
 * - Process emissions
 * - Fugitive emissions (refrigerants, gas leaks)
 */
class Scope1Calculator
{
    /**
     * Fuel density for volume to mass conversion (kg/L).
     */
    private const FUEL_DENSITIES = [
        'diesel' => 0.835,
        'petrol' => 0.745,
        'gasoline' => 0.745,
        'lpg' => 0.51,
        'natural_gas' => 0.717, // per m3
        'heating_oil' => 0.84,
    ];

    /**
     * Calculate Scope 1 emissions.
     */
    public function calculate(float $quantity, EmissionFactor $factor, array $metadata = []): array
    {
        $result = [
            'co2e_kg' => 0,
            'co2_kg' => null,
            'ch4_kg' => null,
            'n2o_kg' => null,
            'is_estimated' => false,
            'notes' => null,
        ];

        // Determine calculation method based on unit
        $unit = strtolower($factor->unit);

        switch ($unit) {
            case 'liters':
            case 'l':
            case 'litres':
                $result = $this->calculateFromFuel($quantity, $factor, $metadata);
                break;

            case 'kwh':
                $result = $this->calculateFromEnergy($quantity, $factor);
                break;

            case 'm3':
            case 'cubic_meters':
                $result = $this->calculateFromGas($quantity, $factor, $metadata);
                break;

            case 'kg':
                $result = $this->calculateFromMass($quantity, $factor);
                break;

            case 'km':
            case 'miles':
                $result = $this->calculateFromDistance($quantity, $factor, $metadata);
                break;

            default:
                // Direct multiplication for other units
                $result['co2e_kg'] = $quantity * $factor->factor_kg_co2e;
        }

        // Add individual GHG if available
        if ($factor->factor_kg_co2) {
            $result['co2_kg'] = $quantity * $factor->factor_kg_co2;
        }
        if ($factor->factor_kg_ch4) {
            $result['ch4_kg'] = $quantity * $factor->factor_kg_ch4;
        }
        if ($factor->factor_kg_n2o) {
            $result['n2o_kg'] = $quantity * $factor->factor_kg_n2o;
        }

        return $result;
    }

    /**
     * Calculate emissions from fuel consumption (liters).
     */
    private function calculateFromFuel(float $liters, EmissionFactor $factor, array $metadata): array
    {
        $fuelType = $metadata['fuel_type'] ?? $this->detectFuelType($factor->name);
        $density = self::FUEL_DENSITIES[$fuelType] ?? 0.8;

        // Convert to kg if needed (some factors are per kg)
        $quantityInKg = $liters * $density;

        // Use factor directly (should be per liter)
        $co2e = $liters * $factor->factor_kg_co2e;

        return [
            'co2e_kg' => $co2e,
            'co2_kg' => $factor->factor_kg_co2 ? $liters * $factor->factor_kg_co2 : null,
            'ch4_kg' => $factor->factor_kg_ch4 ? $liters * $factor->factor_kg_ch4 : null,
            'n2o_kg' => $factor->factor_kg_n2o ? $liters * $factor->factor_kg_n2o : null,
            'is_estimated' => false,
            'notes' => "Fuel: {$fuelType}, {$liters}L",
        ];
    }

    /**
     * Calculate emissions from energy consumption (kWh).
     */
    private function calculateFromEnergy(float $kwh, EmissionFactor $factor): array
    {
        return [
            'co2e_kg' => $kwh * $factor->factor_kg_co2e,
            'co2_kg' => $factor->factor_kg_co2 ? $kwh * $factor->factor_kg_co2 : null,
            'ch4_kg' => $factor->factor_kg_ch4 ? $kwh * $factor->factor_kg_ch4 : null,
            'n2o_kg' => $factor->factor_kg_n2o ? $kwh * $factor->factor_kg_n2o : null,
            'is_estimated' => false,
            'notes' => "Energy: {$kwh} kWh",
        ];
    }

    /**
     * Calculate emissions from gas consumption (m3).
     */
    private function calculateFromGas(float $m3, EmissionFactor $factor, array $metadata): array
    {
        // Natural gas: ~10.55 kWh/m3 (HHV)
        $kwh = $m3 * 10.55;

        return [
            'co2e_kg' => $m3 * $factor->factor_kg_co2e,
            'co2_kg' => $factor->factor_kg_co2 ? $m3 * $factor->factor_kg_co2 : null,
            'ch4_kg' => $factor->factor_kg_ch4 ? $m3 * $factor->factor_kg_ch4 : null,
            'n2o_kg' => $factor->factor_kg_n2o ? $m3 * $factor->factor_kg_n2o : null,
            'is_estimated' => false,
            'notes' => "Gas: {$m3} m3 (~{$kwh} kWh)",
        ];
    }

    /**
     * Calculate emissions from mass (kg).
     */
    private function calculateFromMass(float $kg, EmissionFactor $factor): array
    {
        return [
            'co2e_kg' => $kg * $factor->factor_kg_co2e,
            'co2_kg' => $factor->factor_kg_co2 ? $kg * $factor->factor_kg_co2 : null,
            'ch4_kg' => $factor->factor_kg_ch4 ? $kg * $factor->factor_kg_ch4 : null,
            'n2o_kg' => $factor->factor_kg_n2o ? $kg * $factor->factor_kg_n2o : null,
            'is_estimated' => false,
            'notes' => null,
        ];
    }

    /**
     * Calculate emissions from distance (km).
     */
    private function calculateFromDistance(float $km, EmissionFactor $factor, array $metadata): array
    {
        $vehicleType = $metadata['vehicle_type'] ?? 'average';

        return [
            'co2e_kg' => $km * $factor->factor_kg_co2e,
            'co2_kg' => $factor->factor_kg_co2 ? $km * $factor->factor_kg_co2 : null,
            'ch4_kg' => $factor->factor_kg_ch4 ? $km * $factor->factor_kg_ch4 : null,
            'n2o_kg' => $factor->factor_kg_n2o ? $km * $factor->factor_kg_n2o : null,
            'is_estimated' => false,
            'notes' => "Distance: {$km} km, vehicle: {$vehicleType}",
        ];
    }

    /**
     * Detect fuel type from factor name.
     */
    private function detectFuelType(string $name): string
    {
        $name = strtolower($name);

        if (str_contains($name, 'diesel') || str_contains($name, 'gazole')) {
            return 'diesel';
        }
        if (str_contains($name, 'petrol') || str_contains($name, 'essence') || str_contains($name, 'gasoline') || str_contains($name, 'benzin')) {
            return 'petrol';
        }
        if (str_contains($name, 'lpg') || str_contains($name, 'gpl')) {
            return 'lpg';
        }
        if (str_contains($name, 'natural gas') || str_contains($name, 'gaz naturel') || str_contains($name, 'erdgas')) {
            return 'natural_gas';
        }
        if (str_contains($name, 'heating oil') || str_contains($name, 'fioul') || str_contains($name, 'heizol')) {
            return 'heating_oil';
        }

        return 'diesel'; // Default
    }
}
