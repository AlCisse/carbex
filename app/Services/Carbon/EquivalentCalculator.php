<?php

namespace App\Services\Carbon;

/**
 * EquivalentCalculator - Convert CO2 emissions to relatable equivalents
 *
 * Constitution LinsCarbon v3.0 - Section 2.6, T053
 *
 * Converts kgCO2e into everyday equivalents that are easier to understand:
 * - Flights (Paris-New York round trip)
 * - Car trips (Tour of the Earth)
 * - Hotel nights
 * - Smartphone charges
 * - Streaming hours
 */
class EquivalentCalculator
{
    /**
     * Conversion factors (in kgCO2e).
     */

    // Flight Paris-New York round trip per person (economy class)
    public const PARIS_NY_KG = 1775;

    // Tour of the Earth by car (40,000 km in average car)
    public const TOUR_TERRE_KG = 6000;

    // One night in hotel (average)
    public const NUIT_HOTEL_KG = 25;

    // Full smartphone charge
    public const SMARTPHONE_CHARGE_KG = 0.008;

    // One hour of video streaming
    public const STREAMING_HOUR_KG = 0.036;

    // One km by car (average French car)
    public const CAR_KM_KG = 0.15;

    // One year of average French person emissions
    public const FRENCH_PERSON_YEAR_KG = 9000;

    // One tree absorbs per year
    public const TREE_YEAR_KG = 25;

    /**
     * Calculate all equivalents for a given CO2 amount.
     */
    public function calculate(float $kgCo2e): array
    {
        return [
            'paris_ny_flights' => $this->toParisNyFlights($kgCo2e),
            'earth_tours' => $this->toEarthTours($kgCo2e),
            'hotel_nights' => $this->toHotelNights($kgCo2e),
            'smartphone_charges' => $this->toSmartphoneCharges($kgCo2e),
            'streaming_hours' => $this->toStreamingHours($kgCo2e),
            'car_km' => $this->toCarKm($kgCo2e),
            'french_person_years' => $this->toFrenchPersonYears($kgCo2e),
            'trees_needed' => $this->toTreesNeeded($kgCo2e),
        ];
    }

    /**
     * Get the top 3 most relevant equivalents based on the CO2 amount.
     */
    public function getTopEquivalents(float $kgCo2e, int $count = 3): array
    {
        $equivalents = [];

        // For large amounts (> 10 tonnes), use bigger equivalents
        if ($kgCo2e > 10000) {
            $equivalents[] = [
                'type' => 'paris_ny_flights',
                'value' => $this->toParisNyFlights($kgCo2e),
                'icon' => 'airplane',
                'label' => 'linscarbon.equivalents.paris_ny',
                'unit' => 'linscarbon.equivalents.round_trips',
            ];
            $equivalents[] = [
                'type' => 'earth_tours',
                'value' => $this->toEarthTours($kgCo2e),
                'icon' => 'globe',
                'label' => 'linscarbon.equivalents.earth_tours',
                'unit' => 'linscarbon.equivalents.tours',
            ];
            $equivalents[] = [
                'type' => 'french_person_years',
                'value' => $this->toFrenchPersonYears($kgCo2e),
                'icon' => 'user',
                'label' => 'linscarbon.equivalents.french_person',
                'unit' => 'linscarbon.equivalents.years',
            ];
        }
        // For medium amounts (1-10 tonnes)
        elseif ($kgCo2e > 1000) {
            $equivalents[] = [
                'type' => 'paris_ny_flights',
                'value' => $this->toParisNyFlights($kgCo2e),
                'icon' => 'airplane',
                'label' => 'linscarbon.equivalents.paris_ny',
                'unit' => 'linscarbon.equivalents.round_trips',
            ];
            $equivalents[] = [
                'type' => 'hotel_nights',
                'value' => $this->toHotelNights($kgCo2e),
                'icon' => 'building',
                'label' => 'linscarbon.equivalents.hotel_nights',
                'unit' => 'linscarbon.equivalents.nights',
            ];
            $equivalents[] = [
                'type' => 'car_km',
                'value' => $this->toCarKm($kgCo2e),
                'icon' => 'car',
                'label' => 'linscarbon.equivalents.car_km',
                'unit' => 'km',
            ];
        }
        // For smaller amounts (< 1 tonne)
        else {
            $equivalents[] = [
                'type' => 'hotel_nights',
                'value' => $this->toHotelNights($kgCo2e),
                'icon' => 'building',
                'label' => 'linscarbon.equivalents.hotel_nights',
                'unit' => 'linscarbon.equivalents.nights',
            ];
            $equivalents[] = [
                'type' => 'car_km',
                'value' => $this->toCarKm($kgCo2e),
                'icon' => 'car',
                'label' => 'linscarbon.equivalents.car_km',
                'unit' => 'km',
            ];
            $equivalents[] = [
                'type' => 'streaming_hours',
                'value' => $this->toStreamingHours($kgCo2e),
                'icon' => 'play',
                'label' => 'linscarbon.equivalents.streaming',
                'unit' => 'linscarbon.equivalents.hours',
            ];
        }

        // Add trees needed for offset
        $equivalents[] = [
            'type' => 'trees_needed',
            'value' => $this->toTreesNeeded($kgCo2e),
            'icon' => 'tree',
            'label' => 'linscarbon.equivalents.trees_needed',
            'unit' => 'linscarbon.equivalents.trees',
        ];

        return array_slice($equivalents, 0, $count);
    }

    /**
     * Convert to Paris-New York round trip flights.
     */
    public function toParisNyFlights(float $kgCo2e): float
    {
        return round($kgCo2e / self::PARIS_NY_KG, 1);
    }

    /**
     * Convert to tours of the Earth by car.
     */
    public function toEarthTours(float $kgCo2e): float
    {
        return round($kgCo2e / self::TOUR_TERRE_KG, 2);
    }

    /**
     * Convert to hotel nights.
     */
    public function toHotelNights(float $kgCo2e): int
    {
        return (int) round($kgCo2e / self::NUIT_HOTEL_KG);
    }

    /**
     * Convert to smartphone charges.
     */
    public function toSmartphoneCharges(float $kgCo2e): int
    {
        return (int) round($kgCo2e / self::SMARTPHONE_CHARGE_KG);
    }

    /**
     * Convert to video streaming hours.
     */
    public function toStreamingHours(float $kgCo2e): int
    {
        return (int) round($kgCo2e / self::STREAMING_HOUR_KG);
    }

    /**
     * Convert to car kilometers.
     */
    public function toCarKm(float $kgCo2e): int
    {
        return (int) round($kgCo2e / self::CAR_KM_KG);
    }

    /**
     * Convert to French person years (average annual footprint).
     */
    public function toFrenchPersonYears(float $kgCo2e): float
    {
        return round($kgCo2e / self::FRENCH_PERSON_YEAR_KG, 2);
    }

    /**
     * Calculate number of trees needed to offset (per year).
     */
    public function toTreesNeeded(float $kgCo2e): int
    {
        return (int) ceil($kgCo2e / self::TREE_YEAR_KG);
    }

    /**
     * Format a number for display (with K/M suffix for large numbers).
     */
    public function formatNumber(float $value): string
    {
        if ($value >= 1000000) {
            return round($value / 1000000, 1).'M';
        }
        if ($value >= 1000) {
            return round($value / 1000, 1).'K';
        }

        return number_format($value, $value < 10 ? 1 : 0, ',', ' ');
    }
}
