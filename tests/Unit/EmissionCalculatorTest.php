<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\EmissionFactor;
use App\Services\Carbon\ScopeCalculators\Scope1Calculator;
use App\Services\Carbon\ScopeCalculators\Scope2Calculator;
use App\Services\Carbon\ScopeCalculators\Scope3Calculator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Unit tests for EmissionCalculator and Scope Calculators - T092
 */
class EmissionCalculatorTest extends TestCase
{
    use RefreshDatabase;

    private Scope1Calculator $scope1Calculator;

    private Scope2Calculator $scope2Calculator;

    private Scope3Calculator $scope3Calculator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->scope1Calculator = new Scope1Calculator;
        $this->scope2Calculator = new Scope2Calculator;
        $this->scope3Calculator = new Scope3Calculator;
    }

    // =====================
    // Scope 1 Calculator Tests
    // =====================

    public function test_scope1_calculates_fuel_emissions_in_liters(): void
    {
        $factor = EmissionFactor::factory()->forFuel('diesel')->create([
            'factor_kg_co2e' => 2.68,
            'factor_kg_co2' => 2.64,
            'factor_kg_ch4' => 0.001,
            'factor_kg_n2o' => 0.0001,
        ]);

        $result = $this->scope1Calculator->calculate(100, $factor, ['fuel_type' => 'diesel']);

        $this->assertEquals(268, $result['co2e_kg']);
        $this->assertEquals(264, $result['co2_kg']);
        $this->assertFalse($result['is_estimated']);
        $this->assertStringContainsString('diesel', $result['notes']);
    }

    public function test_scope1_calculates_energy_emissions_in_kwh(): void
    {
        $factor = EmissionFactor::factory()->create([
            'unit' => 'kWh',
            'factor_kg_co2e' => 0.2,
        ]);

        $result = $this->scope1Calculator->calculate(1000, $factor);

        $this->assertEquals(200, $result['co2e_kg']);
        $this->assertStringContainsString('kWh', $result['notes']);
    }

    public function test_scope1_calculates_gas_emissions_in_m3(): void
    {
        $factor = EmissionFactor::factory()->create([
            'unit' => 'm3',
            'factor_kg_co2e' => 2.0,
        ]);

        $result = $this->scope1Calculator->calculate(50, $factor);

        $this->assertEquals(100, $result['co2e_kg']);
        $this->assertStringContainsString('m3', $result['notes']);
    }

    public function test_scope1_calculates_distance_emissions_in_km(): void
    {
        $factor = EmissionFactor::factory()->create([
            'unit' => 'km',
            'factor_kg_co2e' => 0.15,
        ]);

        $result = $this->scope1Calculator->calculate(1000, $factor, ['vehicle_type' => 'company_car']);

        $this->assertEquals(150, $result['co2e_kg']);
        $this->assertStringContainsString('km', $result['notes']);
    }

    public function test_scope1_detects_fuel_type_from_name(): void
    {
        $dieselFactor = EmissionFactor::factory()->create([
            'name' => 'Gazole routier',
            'unit' => 'L',
            'factor_kg_co2e' => 2.68,
        ]);

        $result = $this->scope1Calculator->calculate(100, $dieselFactor);

        $this->assertStringContainsString('diesel', strtolower($result['notes']));
    }

    public function test_scope1_handles_unknown_units(): void
    {
        $factor = EmissionFactor::factory()->create([
            'unit' => 'unknown_unit',
            'factor_kg_co2e' => 1.5,
        ]);

        $result = $this->scope1Calculator->calculate(100, $factor);

        // Should fall back to direct multiplication
        $this->assertEquals(150, $result['co2e_kg']);
    }

    // =====================
    // Scope 2 Calculator Tests
    // =====================

    public function test_scope2_calculates_electricity_emissions(): void
    {
        $factor = EmissionFactor::factory()->forElectricity('FR')->create();

        $result = $this->scope2Calculator->calculate(10000, $factor);

        $this->assertArrayHasKey('co2e_kg', $result);
        $this->assertGreaterThan(0, $result['co2e_kg']);
        // French electricity: ~0.052 kg/kWh -> 10000 * 0.052 = 520 kg
        $this->assertEqualsWithDelta(520, $result['co2e_kg'], 50);
    }

    public function test_scope2_location_based_vs_market_based(): void
    {
        $locationFactor = EmissionFactor::factory()->forElectricity('DE')->create([
            'methodology' => 'location-based',
            'factor_kg_co2e' => 0.366,
        ]);

        $marketFactor = EmissionFactor::factory()->forElectricity('DE')->create([
            'methodology' => 'market-based',
            'factor_kg_co2e' => 0.45,
        ]);

        $locationResult = $this->scope2Calculator->calculate(1000, $locationFactor);
        $marketResult = $this->scope2Calculator->calculate(1000, $marketFactor);

        $this->assertEquals(366, $locationResult['co2e_kg']);
        $this->assertEquals(450, $marketResult['co2e_kg']);
    }

    // =====================
    // Scope 3 Calculator Tests
    // =====================

    public function test_scope3_calculates_spend_based_emissions(): void
    {
        $factor = EmissionFactor::factory()->spendBased()->create([
            'factor_kg_co2e' => 0.5, // 0.5 kg CO2e per EUR
        ]);

        $result = $this->scope3Calculator->calculate(10000, $factor);

        $this->assertEquals(5000, $result['co2e_kg']);
    }

    public function test_scope3_calculates_travel_emissions(): void
    {
        $factor = EmissionFactor::factory()->scope3()->create([
            'unit' => 'passenger-km',
            'factor_kg_co2e' => 0.255, // Average flight
        ]);

        // Paris-NY round trip: ~11,600 km
        $result = $this->scope3Calculator->calculate(11600, $factor);

        $this->assertEqualsWithDelta(2958, $result['co2e_kg'], 10);
    }

    public function test_scope3_calculates_freight_emissions(): void
    {
        $factor = EmissionFactor::factory()->scope3()->create([
            'unit' => 'tonne-km',
            'factor_kg_co2e' => 0.1, // Road freight
        ]);

        // 5 tonnes over 500 km
        $result = $this->scope3Calculator->calculate(2500, $factor);

        $this->assertEquals(250, $result['co2e_kg']);
    }

    // =====================
    // Common Tests
    // =====================

    public function test_result_structure(): void
    {
        $factor = EmissionFactor::factory()->create([
            'factor_kg_co2e' => 1.0,
            'factor_kg_co2' => 0.95,
            'factor_kg_ch4' => 0.03,
            'factor_kg_n2o' => 0.02,
        ]);

        $result = $this->scope1Calculator->calculate(100, $factor);

        $this->assertArrayHasKey('co2e_kg', $result);
        $this->assertArrayHasKey('co2_kg', $result);
        $this->assertArrayHasKey('ch4_kg', $result);
        $this->assertArrayHasKey('n2o_kg', $result);
        $this->assertArrayHasKey('is_estimated', $result);
        $this->assertArrayHasKey('notes', $result);
    }

    public function test_individual_ghg_breakdown(): void
    {
        $factor = EmissionFactor::factory()->create([
            'unit' => 'kg',
            'factor_kg_co2e' => 3.0,
            'factor_kg_co2' => 2.8,
            'factor_kg_ch4' => 0.15,
            'factor_kg_n2o' => 0.05,
        ]);

        $result = $this->scope1Calculator->calculate(100, $factor);

        $this->assertEquals(280, $result['co2_kg']);
        $this->assertEquals(15, $result['ch4_kg']);
        $this->assertEquals(5, $result['n2o_kg']);
    }

    public function test_zero_quantity_returns_zero_emissions(): void
    {
        $factor = EmissionFactor::factory()->create([
            'factor_kg_co2e' => 2.0,
        ]);

        $result = $this->scope1Calculator->calculate(0, $factor);

        $this->assertEquals(0, $result['co2e_kg']);
    }

    public function test_negative_quantity_handled(): void
    {
        $factor = EmissionFactor::factory()->create([
            'unit' => 'kg',
            'factor_kg_co2e' => 2.0,
        ]);

        // Negative quantities should be handled (e.g., for refunds)
        $result = $this->scope1Calculator->calculate(-50, $factor);

        $this->assertEquals(-100, $result['co2e_kg']);
    }

    // =====================
    // Real-world Scenarios
    // =====================

    public function test_realistic_company_car_scenario(): void
    {
        // Company car: 15,000 km/year, average consumption 7L/100km
        $fuelConsumption = 15000 * 0.07; // 1,050 liters

        $factor = EmissionFactor::factory()->forFuel('diesel')->create([
            'factor_kg_co2e' => 2.68,
        ]);

        $result = $this->scope1Calculator->calculate($fuelConsumption, $factor);

        // Expected: ~2,814 kg CO2e
        $this->assertEqualsWithDelta(2814, $result['co2e_kg'], 10);
    }

    public function test_realistic_office_electricity_scenario(): void
    {
        // Small office: 50,000 kWh/year (France)
        $factor = EmissionFactor::factory()->forElectricity('FR')->create([
            'factor_kg_co2e' => 0.052,
        ]);

        $result = $this->scope2Calculator->calculate(50000, $factor);

        // Expected: ~2,600 kg CO2e (France's low-carbon electricity)
        $this->assertEqualsWithDelta(2600, $result['co2e_kg'], 100);
    }

    public function test_realistic_business_travel_scenario(): void
    {
        // Business trip: Paris-Berlin flight (1,700 km round trip)
        $factor = EmissionFactor::factory()->scope3()->create([
            'unit' => 'passenger-km',
            'factor_kg_co2e' => 0.195, // Short-haul flight
        ]);

        $result = $this->scope3Calculator->calculate(1700, $factor);

        // Expected: ~331 kg CO2e
        $this->assertEqualsWithDelta(331.5, $result['co2e_kg'], 10);
    }

    public function test_realistic_purchasing_scenario(): void
    {
        // Office supplies: 5,000 EUR/year
        $factor = EmissionFactor::factory()->spendBased()->create([
            'factor_kg_co2e' => 0.42, // Office supplies sector average
        ]);

        $result = $this->scope3Calculator->calculate(5000, $factor);

        // Expected: ~2,100 kg CO2e
        $this->assertEqualsWithDelta(2100, $result['co2e_kg'], 100);
    }
}
