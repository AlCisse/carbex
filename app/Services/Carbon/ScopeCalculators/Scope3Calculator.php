<?php

namespace App\Services\Carbon\ScopeCalculators;

use App\Models\EmissionFactor;

/**
 * Scope 3 Calculator - Other Indirect Emissions
 *
 * Complete implementation of all 15 GHG Protocol Scope 3 categories:
 *
 * UPSTREAM (Categories 1-8):
 * 1. Purchased goods and services
 * 2. Capital goods
 * 3. Fuel and energy related activities (not in Scope 1/2)
 * 4. Upstream transportation and distribution
 * 5. Waste generated in operations
 * 6. Business travel
 * 7. Employee commuting
 * 8. Upstream leased assets
 *
 * DOWNSTREAM (Categories 9-15):
 * 9. Downstream transportation and distribution
 * 10. Processing of sold products
 * 11. Use of sold products
 * 12. End-of-life treatment of sold products
 * 13. Downstream leased assets
 * 14. Franchises
 * 15. Investments
 */
class Scope3Calculator
{
    /*
    |--------------------------------------------------------------------------
    | Emission Factors Constants
    |--------------------------------------------------------------------------
    */

    /**
     * Spend-based factors by industry (kg CO2e per EUR).
     * Source: ADEME/Exiobase hybrid method
     */
    private const SPEND_BASED_FACTORS = [
        // IT & Technology
        'it_hardware' => 0.42,
        'it_software' => 0.08,
        'cloud_services' => 0.15,
        'telecom_services' => 0.12,

        // Office & Operations
        'office_supplies' => 0.20,
        'furniture' => 0.30,
        'cleaning_services' => 0.15,
        'security_services' => 0.10,

        // Professional Services
        'consulting' => 0.12,
        'legal_services' => 0.10,
        'accounting' => 0.10,
        'marketing' => 0.18,
        'recruitment' => 0.08,

        // Food & Catering
        'catering' => 0.45,
        'food_beverages' => 0.50,
        'restaurant' => 0.40,

        // Logistics
        'courier_services' => 0.35,
        'postal_services' => 0.25,
        'warehousing' => 0.20,

        // Insurance & Finance
        'insurance' => 0.05,
        'banking_services' => 0.04,

        // Utilities
        'water_services' => 0.30,
        'waste_services' => 0.40,

        // Construction & Maintenance
        'construction' => 0.55,
        'maintenance' => 0.35,

        // Default
        'default' => 0.25,
    ];

    /**
     * Capital goods emission factors (kg CO2e per EUR).
     */
    private const CAPITAL_GOODS_FACTORS = [
        'buildings' => 0.65,
        'vehicles' => 0.45,
        'machinery' => 0.50,
        'it_infrastructure' => 0.40,
        'office_equipment' => 0.35,
        'furniture' => 0.30,
        'default' => 0.45,
    ];

    /**
     * Fuel & energy upstream factors (kg CO2e per kWh).
     * Well-to-tank emissions
     */
    private const FUEL_UPSTREAM_FACTORS = [
        'electricity_FR' => 0.0117,  // Nuclear + renewable mix
        'electricity_DE' => 0.0850,  // Coal/gas heavy mix
        'electricity_EU' => 0.0450,  // EU average
        'natural_gas' => 0.0260,
        'diesel' => 0.0580,
        'gasoline' => 0.0520,
        'lpg' => 0.0350,
        'heating_oil' => 0.0550,
    ];

    /**
     * Transportation factors (kg CO2e per tonne-km).
     */
    private const FREIGHT_FACTORS = [
        'road_truck_articulated' => 0.0810,
        'road_truck_rigid' => 0.1370,
        'road_van' => 0.2490,
        'rail_freight' => 0.0280,
        'sea_container' => 0.0160,
        'sea_bulk' => 0.0080,
        'air_freight' => 0.6020,
        'air_freight_long' => 0.4940,
        'inland_waterway' => 0.0320,
        'default' => 0.1000,
    ];

    /**
     * Travel emission factors (kg CO2e per passenger-km).
     */
    private const TRAVEL_FACTORS = [
        'flight_short' => 0.255,      // < 1500 km
        'flight_medium' => 0.156,     // 1500-4000 km
        'flight_long' => 0.150,       // > 4000 km
        'flight_average' => 0.195,
        'train_tgv' => 0.00293,       // French TGV
        'train_ice' => 0.029,         // German ICE
        'train_ter' => 0.025,
        'train_average' => 0.035,
        'car_average' => 0.193,
        'car_electric' => 0.050,
        'car_hybrid' => 0.120,
        'taxi' => 0.210,
        'bus_long' => 0.089,
        'bus_urban' => 0.068,
        'metro' => 0.003,
        'tram' => 0.004,
        'ferry' => 0.115,
    ];

    /**
     * Hotel emission factors (kg CO2e per night).
     */
    private const HOTEL_FACTORS = [
        'budget' => 15.0,
        'standard' => 25.0,
        'business' => 35.0,
        'luxury' => 45.0,
        'average' => 25.0,
    ];

    /**
     * Waste treatment factors (kg CO2e per kg waste).
     */
    private const WASTE_FACTORS = [
        // By waste type and treatment
        'paper_recycling' => 0.021,
        'paper_landfill' => 0.880,
        'paper_incineration' => 0.040,
        'plastic_recycling' => 0.050,
        'plastic_landfill' => 0.040,
        'plastic_incineration' => 2.530,
        'glass_recycling' => 0.021,
        'glass_landfill' => 0.009,
        'metal_recycling' => 0.050,
        'metal_landfill' => 0.009,
        'organic_composting' => 0.010,
        'organic_anaerobic' => -0.040, // Negative = avoided emissions
        'organic_landfill' => 0.580,
        'ewaste_recycling' => 0.200,
        'mixed_landfill' => 0.450,
        'mixed_incineration' => 0.350,
        'mixed_recycling' => 0.100,
        'default' => 0.300,
    ];

    /**
     * Product use phase factors (kg CO2e per unit/year).
     */
    private const PRODUCT_USE_FACTORS = [
        'laptop' => 45.0,      // Based on typical energy consumption
        'desktop' => 90.0,
        'server' => 1500.0,
        'smartphone' => 5.0,
        'printer' => 50.0,
        'appliance_small' => 30.0,
        'appliance_large' => 200.0,
        'vehicle_gasoline' => 2500.0,  // Average annual use
        'vehicle_diesel' => 2200.0,
        'vehicle_electric' => 500.0,
    ];

    /**
     * End-of-life treatment factors (kg CO2e per kg).
     */
    private const END_OF_LIFE_FACTORS = [
        'electronics_recycling' => 0.150,
        'electronics_landfill' => 0.500,
        'vehicle_recycling' => 0.100,
        'vehicle_shredding' => 0.200,
        'packaging_recycling' => 0.030,
        'packaging_landfill' => 0.400,
        'textile_recycling' => 0.080,
        'textile_landfill' => 0.300,
        'default' => 0.250,
    ];

    /**
     * Investment factors (kg CO2e per EUR invested).
     */
    private const INVESTMENT_FACTORS = [
        'equity_general' => 0.35,
        'equity_tech' => 0.15,
        'equity_energy' => 0.80,
        'equity_finance' => 0.05,
        'equity_manufacturing' => 0.55,
        'bonds_government' => 0.05,
        'bonds_corporate' => 0.25,
        'real_estate' => 0.40,
        'default' => 0.30,
    ];

    /*
    |--------------------------------------------------------------------------
    | Main Calculate Method
    |--------------------------------------------------------------------------
    */

    /**
     * Calculate Scope 3 emissions based on category.
     */
    public function calculate(float $quantity, EmissionFactor $factor, array $metadata = []): array
    {
        $category = $metadata['scope_3_category'] ?? null;

        // Route to specific category calculator if specified
        if ($category) {
            return $this->calculateByCategory((int) $category, $quantity, $factor, $metadata);
        }

        // Otherwise, determine by unit
        $unit = strtolower($factor->unit);

        return match ($unit) {
            'eur', 'usd', 'gbp', 'chf' => $this->calculateSpendBased($quantity, $factor, $metadata),
            'km', 'miles' => $this->calculateDistanceBased($quantity, $factor, $metadata),
            'passenger-km', 'pkm' => $this->calculatePassengerKm($quantity, $factor, $metadata),
            'tonne-km', 'tkm' => $this->calculateTonneKm($quantity, $factor, $metadata),
            'nights' => $this->calculateHotelNights($quantity, $factor, $metadata),
            'kg', 't', 'tonne' => $this->calculateMassBased($quantity, $factor, $metadata),
            'kwh', 'mwh' => $this->calculateEnergyBased($quantity, $factor, $metadata),
            'litre', 'l', 'm3' => $this->calculateVolumeBased($quantity, $factor, $metadata),
            default => $this->calculateDirect($quantity, $factor),
        };
    }

    /**
     * Calculate by specific Scope 3 category.
     */
    public function calculateByCategory(int $category, float $quantity, EmissionFactor $factor, array $metadata = []): array
    {
        return match ($category) {
            1 => $this->calculateCategory1PurchasedGoods($quantity, $factor, $metadata),
            2 => $this->calculateCategory2CapitalGoods($quantity, $factor, $metadata),
            3 => $this->calculateCategory3FuelEnergy($quantity, $factor, $metadata),
            4 => $this->calculateCategory4UpstreamTransport($quantity, $factor, $metadata),
            5 => $this->calculateCategory5Waste($quantity, $factor, $metadata),
            6 => $this->calculateCategory6BusinessTravel($quantity, $factor, $metadata),
            7 => $this->calculateCategory7Commuting($quantity, $factor, $metadata),
            8 => $this->calculateCategory8UpstreamLeased($quantity, $factor, $metadata),
            9 => $this->calculateCategory9DownstreamTransport($quantity, $factor, $metadata),
            10 => $this->calculateCategory10Processing($quantity, $factor, $metadata),
            11 => $this->calculateCategory11ProductUse($quantity, $factor, $metadata),
            12 => $this->calculateCategory12EndOfLife($quantity, $factor, $metadata),
            13 => $this->calculateCategory13DownstreamLeased($quantity, $factor, $metadata),
            14 => $this->calculateCategory14Franchises($quantity, $factor, $metadata),
            15 => $this->calculateCategory15Investments($quantity, $factor, $metadata),
            default => $this->calculateDirect($quantity, $factor),
        };
    }

    /*
    |--------------------------------------------------------------------------
    | Category 1: Purchased Goods and Services
    |--------------------------------------------------------------------------
    */

    /**
     * Calculate Category 1 emissions.
     */
    public function calculateCategory1PurchasedGoods(float $amount, EmissionFactor $factor, array $metadata = []): array
    {
        $categoryType = $metadata['category_type'] ?? 'default';
        $method = $metadata['method'] ?? 'spend_based';

        if ($method === 'supplier_specific' && $factor->factor_kg_co2e > 0) {
            // Use supplier-specific data
            $co2e = $amount * $factor->factor_kg_co2e;
            $notes = "Supplier-specific factor";
        } else {
            // Spend-based estimation
            $factorValue = self::SPEND_BASED_FACTORS[$categoryType] ?? self::SPEND_BASED_FACTORS['default'];
            $co2e = abs($amount) * $factorValue;
            $notes = "Spend-based: {$categoryType}, factor: {$factorValue} kg/EUR";
        }

        return $this->formatResult($co2e, $factor, [
            'category' => 1,
            'category_name' => 'Purchased goods and services',
            'notes' => $notes,
            'is_estimated' => $method !== 'supplier_specific',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Category 2: Capital Goods
    |--------------------------------------------------------------------------
    */

    /**
     * Calculate Category 2 emissions.
     */
    public function calculateCategory2CapitalGoods(float $amount, EmissionFactor $factor, array $metadata = []): array
    {
        $assetType = $metadata['asset_type'] ?? 'default';
        $depreciationYears = $metadata['depreciation_years'] ?? 1;

        $factorValue = $factor->factor_kg_co2e > 0
            ? $factor->factor_kg_co2e
            : (self::CAPITAL_GOODS_FACTORS[$assetType] ?? self::CAPITAL_GOODS_FACTORS['default']);

        // Amortize over depreciation period if specified
        $annualAmount = $depreciationYears > 1 ? $amount / $depreciationYears : $amount;
        $co2e = $annualAmount * $factorValue;

        return $this->formatResult($co2e, $factor, [
            'category' => 2,
            'category_name' => 'Capital goods',
            'notes' => "Asset: {$assetType}, depreciation: {$depreciationYears} years",
            'is_estimated' => $factor->factor_kg_co2e <= 0,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Category 3: Fuel and Energy Related Activities
    |--------------------------------------------------------------------------
    */

    /**
     * Calculate Category 3 emissions (upstream of Scope 1/2).
     */
    public function calculateCategory3FuelEnergy(float $quantity, EmissionFactor $factor, array $metadata = []): array
    {
        $fuelType = $metadata['fuel_type'] ?? 'electricity_EU';
        $country = $metadata['country'] ?? 'EU';

        // Look up well-to-tank factor
        $key = $fuelType . '_' . $country;
        $wttFactor = self::FUEL_UPSTREAM_FACTORS[$key]
            ?? self::FUEL_UPSTREAM_FACTORS[$fuelType]
            ?? 0.045; // Default average

        $co2e = $quantity * $wttFactor;

        return $this->formatResult($co2e, $factor, [
            'category' => 3,
            'category_name' => 'Fuel and energy related activities',
            'notes' => "WTT emissions for {$fuelType}: {$quantity} kWh",
            'is_estimated' => true,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Category 4: Upstream Transportation and Distribution
    |--------------------------------------------------------------------------
    */

    /**
     * Calculate Category 4 emissions.
     */
    public function calculateCategory4UpstreamTransport(float $quantity, EmissionFactor $factor, array $metadata = []): array
    {
        $transportMode = $metadata['transport_mode'] ?? 'default';
        $unit = strtolower($factor->unit);

        if ($unit === 'tonne-km' || $unit === 'tkm') {
            $factorValue = self::FREIGHT_FACTORS[$transportMode] ?? self::FREIGHT_FACTORS['default'];
            $co2e = $quantity * $factorValue;
        } else {
            // Spend-based fallback
            $co2e = $quantity * 0.30; // Average logistics spend factor
        }

        return $this->formatResult($co2e, $factor, [
            'category' => 4,
            'category_name' => 'Upstream transportation and distribution',
            'notes' => "Mode: {$transportMode}",
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Category 5: Waste Generated in Operations
    |--------------------------------------------------------------------------
    */

    /**
     * Calculate Category 5 emissions.
     */
    public function calculateCategory5Waste(float $kg, EmissionFactor $factor, array $metadata = []): array
    {
        $wasteType = $metadata['waste_type'] ?? 'mixed';
        $treatment = $metadata['treatment'] ?? 'landfill';

        $key = "{$wasteType}_{$treatment}";
        $factorValue = $factor->factor_kg_co2e > 0
            ? $factor->factor_kg_co2e
            : (self::WASTE_FACTORS[$key] ?? self::WASTE_FACTORS['default']);

        // Handle tonnes vs kg
        $kgQuantity = strtolower($factor->unit) === 't' || strtolower($factor->unit) === 'tonne'
            ? $kg * 1000
            : $kg;

        $co2e = $kgQuantity * $factorValue;

        return $this->formatResult($co2e, $factor, [
            'category' => 5,
            'category_name' => 'Waste generated in operations',
            'notes' => "Waste: {$wasteType}, treatment: {$treatment}",
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Category 6: Business Travel
    |--------------------------------------------------------------------------
    */

    /**
     * Calculate Category 6 emissions.
     */
    public function calculateCategory6BusinessTravel(float $quantity, EmissionFactor $factor, array $metadata = []): array
    {
        $travelType = $metadata['travel_type'] ?? 'flight_average';
        $class = $metadata['travel_class'] ?? 'economy';
        $unit = strtolower($factor->unit);

        // Class multiplier for flights
        $classMultiplier = match ($class) {
            'business' => 2.0,
            'first' => 3.0,
            'premium_economy' => 1.5,
            default => 1.0,
        };

        if ($unit === 'nights') {
            // Hotel stays
            $hotelType = $metadata['hotel_type'] ?? 'average';
            $factorValue = self::HOTEL_FACTORS[$hotelType] ?? self::HOTEL_FACTORS['average'];
            $co2e = $quantity * $factorValue;
            $notes = "Hotel: {$hotelType}, {$quantity} nights";
        } elseif (in_array($unit, ['km', 'miles', 'passenger-km', 'pkm'])) {
            // Distance-based
            $factorValue = $factor->factor_kg_co2e > 0
                ? $factor->factor_kg_co2e
                : (self::TRAVEL_FACTORS[$travelType] ?? self::TRAVEL_FACTORS['flight_average']);

            $distance = $unit === 'miles' ? $quantity * 1.60934 : $quantity;
            $co2e = $distance * $factorValue * $classMultiplier;
            $notes = "Travel: {$travelType}, class: {$class}";
        } else {
            // Spend-based fallback
            $co2e = $quantity * 0.30;
            $notes = "Spend-based estimation";
        }

        return $this->formatResult($co2e, $factor, [
            'category' => 6,
            'category_name' => 'Business travel',
            'notes' => $notes,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Category 7: Employee Commuting
    |--------------------------------------------------------------------------
    */

    /**
     * Calculate Category 7 emissions.
     */
    public function calculateCategory7Commuting(float $quantity, EmissionFactor $factor, array $metadata = []): array
    {
        $transportMode = $metadata['transport_mode'] ?? 'car_average';

        $factorValue = $factor->factor_kg_co2e > 0
            ? $factor->factor_kg_co2e
            : (self::TRAVEL_FACTORS[$transportMode] ?? self::TRAVEL_FACTORS['car_average']);

        $co2e = $quantity * $factorValue;

        return $this->formatResult($co2e, $factor, [
            'category' => 7,
            'category_name' => 'Employee commuting',
            'notes' => "Mode: {$transportMode}",
        ]);
    }

    /**
     * Calculate total employee commuting emissions with modal split.
     */
    public function calculateCommuting(
        int $employeeCount,
        float $avgDistanceKm,
        int $workDaysPerYear = 220,
        array $modalSplit = [],
        float $remoteWorkPercentage = 0.0
    ): array {
        $defaultSplit = [
            'car_average' => 0.55,
            'car_electric' => 0.05,
            'bus_urban' => 0.10,
            'metro' => 0.10,
            'train_ter' => 0.05,
            'bike_walk' => 0.15,
        ];

        $split = array_merge($defaultSplit, $modalSplit);
        $effectiveWorkDays = $workDaysPerYear * (1 - $remoteWorkPercentage);

        $totalCo2e = 0;
        $breakdown = [];

        foreach ($split as $mode => $percentage) {
            if ($mode === 'bike_walk') {
                $breakdown[$mode] = ['distance_km' => 0, 'co2e_kg' => 0];
                continue;
            }

            $factor = self::TRAVEL_FACTORS[$mode] ?? 0;
            $modeDistance = $avgDistanceKm * 2 * $effectiveWorkDays * $percentage * $employeeCount;
            $modeCo2e = $modeDistance * $factor;

            $breakdown[$mode] = [
                'distance_km' => round($modeDistance, 2),
                'co2e_kg' => round($modeCo2e, 2),
            ];

            $totalCo2e += $modeCo2e;
        }

        return [
            'category' => 7,
            'category_name' => 'Employee commuting',
            'total_co2e_kg' => round($totalCo2e, 2),
            'per_employee_kg' => $employeeCount > 0 ? round($totalCo2e / $employeeCount, 2) : 0,
            'breakdown' => $breakdown,
            'assumptions' => [
                'employee_count' => $employeeCount,
                'work_days_per_year' => $effectiveWorkDays,
                'avg_distance_km' => $avgDistanceKm,
                'remote_work_percentage' => $remoteWorkPercentage * 100,
                'modal_split' => $split,
            ],
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Category 8: Upstream Leased Assets
    |--------------------------------------------------------------------------
    */

    /**
     * Calculate Category 8 emissions.
     */
    public function calculateCategory8UpstreamLeased(float $quantity, EmissionFactor $factor, array $metadata = []): array
    {
        $assetType = $metadata['asset_type'] ?? 'office_space';

        // For leased assets, typically calculate based on energy use or area
        if (isset($metadata['energy_kwh'])) {
            $co2e = $metadata['energy_kwh'] * ($factor->factor_kg_co2e ?: 0.4);
        } elseif (isset($metadata['area_m2'])) {
            // Average office emissions per m² per year
            $factorPerM2 = match ($assetType) {
                'office_space' => 50,
                'warehouse' => 30,
                'retail' => 80,
                'data_center' => 500,
                default => 50,
            };
            $co2e = $metadata['area_m2'] * $factorPerM2;
        } else {
            $co2e = $quantity * ($factor->factor_kg_co2e ?: 0.25);
        }

        return $this->formatResult($co2e, $factor, [
            'category' => 8,
            'category_name' => 'Upstream leased assets',
            'notes' => "Asset type: {$assetType}",
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Category 9: Downstream Transportation and Distribution
    |--------------------------------------------------------------------------
    */

    /**
     * Calculate Category 9 emissions.
     */
    public function calculateCategory9DownstreamTransport(float $quantity, EmissionFactor $factor, array $metadata = []): array
    {
        $transportMode = $metadata['transport_mode'] ?? 'road_truck_articulated';

        $factorValue = $factor->factor_kg_co2e > 0
            ? $factor->factor_kg_co2e
            : (self::FREIGHT_FACTORS[$transportMode] ?? self::FREIGHT_FACTORS['default']);

        $co2e = $quantity * $factorValue;

        return $this->formatResult($co2e, $factor, [
            'category' => 9,
            'category_name' => 'Downstream transportation and distribution',
            'notes' => "Mode: {$transportMode}",
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Category 10: Processing of Sold Products
    |--------------------------------------------------------------------------
    */

    /**
     * Calculate Category 10 emissions.
     */
    public function calculateCategory10Processing(float $quantity, EmissionFactor $factor, array $metadata = []): array
    {
        $processType = $metadata['process_type'] ?? 'default';

        // Use specific factor if available
        $co2e = $quantity * ($factor->factor_kg_co2e ?: 0.50);

        return $this->formatResult($co2e, $factor, [
            'category' => 10,
            'category_name' => 'Processing of sold products',
            'notes' => "Process: {$processType}",
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Category 11: Use of Sold Products
    |--------------------------------------------------------------------------
    */

    /**
     * Calculate Category 11 emissions.
     */
    public function calculateCategory11ProductUse(float $quantity, EmissionFactor $factor, array $metadata = []): array
    {
        $productType = $metadata['product_type'] ?? 'default';
        $lifetimeYears = $metadata['lifetime_years'] ?? 1;
        $unitsSold = $metadata['units_sold'] ?? $quantity;

        // Get annual use factor
        $annualFactor = self::PRODUCT_USE_FACTORS[$productType] ?? 50;

        $totalCo2e = $unitsSold * $annualFactor * $lifetimeYears;

        return $this->formatResult($totalCo2e, $factor, [
            'category' => 11,
            'category_name' => 'Use of sold products',
            'notes' => "Product: {$productType}, lifetime: {$lifetimeYears} years, units: {$unitsSold}",
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Category 12: End-of-Life Treatment of Sold Products
    |--------------------------------------------------------------------------
    */

    /**
     * Calculate Category 12 emissions.
     */
    public function calculateCategory12EndOfLife(float $quantity, EmissionFactor $factor, array $metadata = []): array
    {
        $productType = $metadata['product_type'] ?? 'default';
        $treatment = $metadata['treatment'] ?? 'recycling';

        $key = "{$productType}_{$treatment}";
        $factorValue = $factor->factor_kg_co2e > 0
            ? $factor->factor_kg_co2e
            : (self::END_OF_LIFE_FACTORS[$key] ?? self::END_OF_LIFE_FACTORS['default']);

        $co2e = $quantity * $factorValue;

        return $this->formatResult($co2e, $factor, [
            'category' => 12,
            'category_name' => 'End-of-life treatment of sold products',
            'notes' => "Product: {$productType}, treatment: {$treatment}",
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Category 13: Downstream Leased Assets
    |--------------------------------------------------------------------------
    */

    /**
     * Calculate Category 13 emissions.
     */
    public function calculateCategory13DownstreamLeased(float $quantity, EmissionFactor $factor, array $metadata = []): array
    {
        $assetType = $metadata['asset_type'] ?? 'building';

        // Similar to upstream leased, but for assets leased TO others
        if (isset($metadata['energy_kwh'])) {
            $co2e = $metadata['energy_kwh'] * ($factor->factor_kg_co2e ?: 0.4);
        } elseif (isset($metadata['area_m2'])) {
            $factorPerM2 = match ($assetType) {
                'building' => 50,
                'vehicle' => 200,
                'equipment' => 100,
                default => 50,
            };
            $co2e = $metadata['area_m2'] * $factorPerM2;
        } else {
            $co2e = $quantity * ($factor->factor_kg_co2e ?: 0.25);
        }

        return $this->formatResult($co2e, $factor, [
            'category' => 13,
            'category_name' => 'Downstream leased assets',
            'notes' => "Asset type: {$assetType}",
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Category 14: Franchises
    |--------------------------------------------------------------------------
    */

    /**
     * Calculate Category 14 emissions.
     */
    public function calculateCategory14Franchises(float $quantity, EmissionFactor $factor, array $metadata = []): array
    {
        $franchiseType = $metadata['franchise_type'] ?? 'default';
        $franchiseCount = $metadata['franchise_count'] ?? 1;

        // Average emissions per franchise per year
        $avgPerFranchise = match ($franchiseType) {
            'restaurant' => 150000,   // kg CO2e
            'retail_small' => 50000,
            'retail_large' => 200000,
            'hotel' => 500000,
            default => 100000,
        };

        $co2e = $franchiseCount * $avgPerFranchise * ($quantity / 100); // Quantity as percentage

        return $this->formatResult($co2e, $factor, [
            'category' => 14,
            'category_name' => 'Franchises',
            'notes' => "Type: {$franchiseType}, count: {$franchiseCount}",
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Category 15: Investments
    |--------------------------------------------------------------------------
    */

    /**
     * Calculate Category 15 emissions.
     */
    public function calculateCategory15Investments(float $amount, EmissionFactor $factor, array $metadata = []): array
    {
        $investmentType = $metadata['investment_type'] ?? 'default';
        $sharePercentage = $metadata['share_percentage'] ?? 100;

        $factorValue = $factor->factor_kg_co2e > 0
            ? $factor->factor_kg_co2e
            : (self::INVESTMENT_FACTORS[$investmentType] ?? self::INVESTMENT_FACTORS['default']);

        // Apply ownership share
        $effectiveAmount = $amount * ($sharePercentage / 100);
        $co2e = $effectiveAmount * $factorValue;

        return $this->formatResult($co2e, $factor, [
            'category' => 15,
            'category_name' => 'Investments',
            'notes' => "Type: {$investmentType}, share: {$sharePercentage}%",
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Helper Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Spend-based calculation (generic).
     */
    private function calculateSpendBased(float $amount, EmissionFactor $factor, array $metadata): array
    {
        $categoryType = $metadata['category_type'] ?? 'default';

        $factorValue = $factor->factor_kg_co2e > 0
            ? $factor->factor_kg_co2e
            : (self::SPEND_BASED_FACTORS[$categoryType] ?? self::SPEND_BASED_FACTORS['default']);

        return $this->formatResult(abs($amount) * $factorValue, $factor, [
            'notes' => "Spend-based: {$categoryType}",
            'is_estimated' => $factor->factor_kg_co2e <= 0,
        ]);
    }

    /**
     * Distance-based calculation.
     */
    private function calculateDistanceBased(float $km, EmissionFactor $factor, array $metadata): array
    {
        $transportMode = $metadata['transport_mode'] ?? 'car_average';
        $passengers = $metadata['passengers'] ?? 1;

        $factorValue = $factor->factor_kg_co2e > 0
            ? $factor->factor_kg_co2e
            : (self::TRAVEL_FACTORS[$transportMode] ?? self::TRAVEL_FACTORS['car_average']);

        $effectiveKm = $km / max(1, $passengers);

        return $this->formatResult($effectiveKm * $factorValue, $factor, [
            'notes' => "Distance: {$km} km, mode: {$transportMode}",
        ]);
    }

    /**
     * Passenger-km calculation.
     */
    private function calculatePassengerKm(float $pkm, EmissionFactor $factor, array $metadata): array
    {
        $class = $metadata['travel_class'] ?? 'economy';
        $multiplier = match ($class) {
            'business' => 2.0,
            'first' => 3.0,
            default => 1.0,
        };

        return $this->formatResult($pkm * $factor->factor_kg_co2e * $multiplier, $factor, [
            'notes' => "Travel: {$pkm} pkm, class: {$class}",
        ]);
    }

    /**
     * Tonne-km calculation for freight.
     */
    private function calculateTonneKm(float $tkm, EmissionFactor $factor, array $metadata): array
    {
        $transportMode = $metadata['transport_mode'] ?? 'road_truck_articulated';

        $factorValue = $factor->factor_kg_co2e > 0
            ? $factor->factor_kg_co2e
            : (self::FREIGHT_FACTORS[$transportMode] ?? self::FREIGHT_FACTORS['default']);

        return $this->formatResult($tkm * $factorValue, $factor, [
            'notes' => "Freight: {$tkm} tkm, mode: {$transportMode}",
        ]);
    }

    /**
     * Hotel nights calculation.
     */
    private function calculateHotelNights(float $nights, EmissionFactor $factor, array $metadata): array
    {
        $hotelType = $metadata['hotel_type'] ?? 'average';

        $factorValue = $factor->factor_kg_co2e > 0
            ? $factor->factor_kg_co2e
            : (self::HOTEL_FACTORS[$hotelType] ?? self::HOTEL_FACTORS['average']);

        return $this->formatResult($nights * $factorValue, $factor, [
            'notes' => "Hotel: {$nights} nights, type: {$hotelType}",
        ]);
    }

    /**
     * Mass-based calculation.
     */
    private function calculateMassBased(float $mass, EmissionFactor $factor, array $metadata): array
    {
        $unit = strtolower($factor->unit);
        $kg = ($unit === 't' || $unit === 'tonne') ? $mass * 1000 : $mass;

        return $this->formatResult($kg * $factor->factor_kg_co2e, $factor, [
            'notes' => "Mass: {$mass} {$factor->unit}",
        ]);
    }

    /**
     * Energy-based calculation.
     */
    private function calculateEnergyBased(float $energy, EmissionFactor $factor, array $metadata): array
    {
        $unit = strtolower($factor->unit);
        $kwh = $unit === 'mwh' ? $energy * 1000 : $energy;

        return $this->formatResult($kwh * $factor->factor_kg_co2e, $factor, [
            'notes' => "Energy: {$energy} {$factor->unit}",
        ]);
    }

    /**
     * Volume-based calculation.
     */
    private function calculateVolumeBased(float $volume, EmissionFactor $factor, array $metadata): array
    {
        return $this->formatResult($volume * $factor->factor_kg_co2e, $factor, [
            'notes' => "Volume: {$volume} {$factor->unit}",
        ]);
    }

    /**
     * Direct calculation.
     */
    private function calculateDirect(float $quantity, EmissionFactor $factor): array
    {
        return $this->formatResult($quantity * $factor->factor_kg_co2e, $factor);
    }

    /**
     * Format result array.
     */
    private function formatResult(float $co2e, EmissionFactor $factor, array $extra = []): array
    {
        return array_merge([
            'co2e_kg' => round($co2e, 6),
            'co2_kg' => $factor->factor_kg_co2 ? round($co2e * ($factor->factor_kg_co2 / $factor->factor_kg_co2e), 6) : null,
            'ch4_kg' => $factor->factor_kg_ch4 ? round($co2e * ($factor->factor_kg_ch4 / $factor->factor_kg_co2e), 6) : null,
            'n2o_kg' => $factor->factor_kg_n2o ? round($co2e * ($factor->factor_kg_n2o / $factor->factor_kg_co2e), 6) : null,
            'is_estimated' => false,
            'notes' => null,
        ], $extra);
    }

    /**
     * Estimate flight emissions from spend.
     */
    public function estimateFlightFromSpend(float $amount, string $currency = 'EUR'): array
    {
        $pricePerKm = 0.12;
        $estimatedKm = $amount / $pricePerKm;

        $type = match (true) {
            $estimatedKm < 1500 => 'short',
            $estimatedKm < 4000 => 'medium',
            default => 'long',
        };

        return [
            'estimated_km' => round($estimatedKm, 2),
            'flight_type' => $type,
            'co2e_kg' => round($estimatedKm * self::TRAVEL_FACTORS["flight_{$type}"], 2),
        ];
    }

    /**
     * Get all category names.
     */
    public static function getCategoryNames(): array
    {
        return [
            1 => 'Purchased goods and services',
            2 => 'Capital goods',
            3 => 'Fuel and energy related activities',
            4 => 'Upstream transportation and distribution',
            5 => 'Waste generated in operations',
            6 => 'Business travel',
            7 => 'Employee commuting',
            8 => 'Upstream leased assets',
            9 => 'Downstream transportation and distribution',
            10 => 'Processing of sold products',
            11 => 'Use of sold products',
            12 => 'End-of-life treatment of sold products',
            13 => 'Downstream leased assets',
            14 => 'Franchises',
            15 => 'Investments',
        ];
    }

    /**
     * Get upstream categories (1-8).
     */
    public static function getUpstreamCategories(): array
    {
        return array_slice(self::getCategoryNames(), 0, 8, true);
    }

    /**
     * Get downstream categories (9-15).
     */
    public static function getDownstreamCategories(): array
    {
        return array_slice(self::getCategoryNames(), 8, 7, true);
    }
}
