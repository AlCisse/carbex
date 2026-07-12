<?php

namespace Tests\Unit;

use App\Services\Carbon\EquivalentCalculator;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for EquivalentCalculator - T093
 */
class EquivalentCalculatorTest extends TestCase
{
    private EquivalentCalculator $calculator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->calculator = new EquivalentCalculator;
    }

    public function test_paris_ny_flights_calculation(): void
    {
        // 1775 kg = 1 Paris-NY round trip
        $this->assertEquals(1.0, $this->calculator->toParisNyFlights(1775));
        $this->assertEquals(2.0, $this->calculator->toParisNyFlights(3550));
        $this->assertEquals(0.5, $this->calculator->toParisNyFlights(887.5));
    }

    public function test_earth_tours_calculation(): void
    {
        // 6000 kg = 1 tour of Earth by car
        $this->assertEquals(1.0, $this->calculator->toEarthTours(6000));
        $this->assertEquals(2.0, $this->calculator->toEarthTours(12000));
        $this->assertEquals(0.5, $this->calculator->toEarthTours(3000));
    }

    public function test_hotel_nights_calculation(): void
    {
        // 25 kg = 1 hotel night
        $this->assertEquals(1, $this->calculator->toHotelNights(25));
        $this->assertEquals(4, $this->calculator->toHotelNights(100));
        $this->assertEquals(40, $this->calculator->toHotelNights(1000));
    }

    public function test_smartphone_charges_calculation(): void
    {
        // 0.008 kg = 1 smartphone charge
        $this->assertEquals(125, $this->calculator->toSmartphoneCharges(1));
        $this->assertEquals(1250, $this->calculator->toSmartphoneCharges(10));
    }

    public function test_streaming_hours_calculation(): void
    {
        // 0.036 kg = 1 hour streaming
        $this->assertEquals(28, $this->calculator->toStreamingHours(1));
        $this->assertEquals(278, $this->calculator->toStreamingHours(10));
    }

    public function test_car_km_calculation(): void
    {
        // 0.15 kg = 1 km by car
        $this->assertEquals(7, $this->calculator->toCarKm(1));
        $this->assertEquals(67, $this->calculator->toCarKm(10));
        $this->assertEquals(6667, $this->calculator->toCarKm(1000));
    }

    public function test_french_person_years_calculation(): void
    {
        // 9000 kg = 1 French person year
        $this->assertEquals(1.0, $this->calculator->toFrenchPersonYears(9000));
        $this->assertEquals(0.5, $this->calculator->toFrenchPersonYears(4500));
        $this->assertEquals(2.0, $this->calculator->toFrenchPersonYears(18000));
    }

    public function test_trees_needed_calculation(): void
    {
        // 25 kg = 1 tree absorbs per year
        $this->assertEquals(1, $this->calculator->toTreesNeeded(25));
        $this->assertEquals(4, $this->calculator->toTreesNeeded(100));
        $this->assertEquals(40, $this->calculator->toTreesNeeded(1000));
        // Ceiling for partial trees
        $this->assertEquals(1, $this->calculator->toTreesNeeded(10));
    }

    public function test_calculate_returns_all_equivalents(): void
    {
        $result = $this->calculator->calculate(10000);

        $this->assertArrayHasKey('paris_ny_flights', $result);
        $this->assertArrayHasKey('earth_tours', $result);
        $this->assertArrayHasKey('hotel_nights', $result);
        $this->assertArrayHasKey('smartphone_charges', $result);
        $this->assertArrayHasKey('streaming_hours', $result);
        $this->assertArrayHasKey('car_km', $result);
        $this->assertArrayHasKey('french_person_years', $result);
        $this->assertArrayHasKey('trees_needed', $result);
    }

    public function test_get_top_equivalents_for_large_amounts(): void
    {
        // > 10 tonnes should use bigger equivalents
        $result = $this->calculator->getTopEquivalents(15000, 3);

        $this->assertCount(3, $result);
        $types = array_column($result, 'type');
        $this->assertContains('paris_ny_flights', $types);
        $this->assertContains('earth_tours', $types);
        $this->assertContains('french_person_years', $types);
    }

    public function test_get_top_equivalents_for_medium_amounts(): void
    {
        // 1-10 tonnes should use medium equivalents
        $result = $this->calculator->getTopEquivalents(5000, 3);

        $this->assertCount(3, $result);
        $types = array_column($result, 'type');
        $this->assertContains('paris_ny_flights', $types);
        $this->assertContains('hotel_nights', $types);
        $this->assertContains('car_km', $types);
    }

    public function test_get_top_equivalents_for_small_amounts(): void
    {
        // < 1 tonne should use smaller equivalents
        $result = $this->calculator->getTopEquivalents(500, 3);

        $this->assertCount(3, $result);
        $types = array_column($result, 'type');
        $this->assertContains('hotel_nights', $types);
        $this->assertContains('car_km', $types);
        $this->assertContains('streaming_hours', $types);
    }

    public function test_get_top_equivalents_structure(): void
    {
        $result = $this->calculator->getTopEquivalents(5000, 1);

        $equivalent = $result[0];
        $this->assertArrayHasKey('type', $equivalent);
        $this->assertArrayHasKey('value', $equivalent);
        $this->assertArrayHasKey('icon', $equivalent);
        $this->assertArrayHasKey('label', $equivalent);
        $this->assertArrayHasKey('unit', $equivalent);
    }

    public function test_format_number_with_millions(): void
    {
        $this->assertEquals('1.5M', $this->calculator->formatNumber(1500000));
        $this->assertEquals('10M', $this->calculator->formatNumber(10000000));
    }

    public function test_format_number_with_thousands(): void
    {
        $this->assertEquals('1.5K', $this->calculator->formatNumber(1500));
        $this->assertEquals('10K', $this->calculator->formatNumber(10000));
    }

    public function test_format_number_with_small_values(): void
    {
        $this->assertEquals('500', $this->calculator->formatNumber(500));
        $this->assertEquals('5,5', $this->calculator->formatNumber(5.5));
    }

    public function test_zero_emissions_returns_zeros(): void
    {
        $result = $this->calculator->calculate(0);

        $this->assertEquals(0, $result['paris_ny_flights']);
        $this->assertEquals(0, $result['earth_tours']);
        $this->assertEquals(0, $result['hotel_nights']);
        $this->assertEquals(0, $result['car_km']);
    }

    public function test_realistic_sme_emissions(): void
    {
        // A typical small French company: ~50 tonnes CO2e/year = 50,000 kg
        $kgCo2e = 50000;

        $result = $this->calculator->calculate($kgCo2e);

        // ~28 Paris-NY flights
        $this->assertEqualsWithDelta(28.2, $result['paris_ny_flights'], 0.5);

        // ~8 Earth tours
        $this->assertEqualsWithDelta(8.33, $result['earth_tours'], 0.1);

        // ~5.5 French person years
        $this->assertEqualsWithDelta(5.56, $result['french_person_years'], 0.1);

        // ~2000 trees needed
        $this->assertEquals(2000, $result['trees_needed']);
    }
}
