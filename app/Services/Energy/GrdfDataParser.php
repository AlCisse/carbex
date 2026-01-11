<?php

namespace App\Services\Energy;

use App\Models\EnergyConnection;
use App\Models\EnergyConsumption;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

/**
 * GRDF Data Parser
 *
 * Parses and normalizes gas consumption data from GRDF ADICT API responses.
 */
class GrdfDataParser
{
    /**
     * Default conversion coefficient (kWh per m³).
     * Varies by gas quality and region.
     */
    private const DEFAULT_CONVERSION_COEFFICIENT = 11.2;

    /**
     * Parse consumption data from GRDF API response.
     *
     * @param array $response Raw API response
     * @param EnergyConnection $connection
     * @param string $granularity daily|monthly
     * @return Collection
     */
    public function parseConsumption(
        array $response,
        EnergyConnection $connection,
        string $granularity = 'daily'
    ): Collection {
        $readings = $this->extractReadings($response);
        $parsed = collect();

        foreach ($readings as $reading) {
            try {
                $consumption = $this->parseReading($reading, $connection, $granularity);
                if ($consumption) {
                    $parsed->push($consumption);
                }
            } catch (\Exception $e) {
                Log::warning('Failed to parse GRDF reading', [
                    'reading' => $reading,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $parsed;
    }

    /**
     * Extract readings from various response formats.
     */
    private function extractReadings(array $response): array
    {
        // ADICT format
        if (isset($response['consommations'])) {
            return $response['consommations'];
        }

        // Alternative formats
        if (isset($response['releves'])) {
            return $response['releves'];
        }

        if (isset($response['donnees'])) {
            return $response['donnees'];
        }

        // Direct array of readings
        if (isset($response[0]) && (isset($response[0]['date_debut']) || isset($response[0]['date']))) {
            return $response;
        }

        return [];
    }

    /**
     * Parse a single reading into EnergyConsumption attributes.
     */
    private function parseReading(
        array $reading,
        EnergyConnection $connection,
        string $granularity
    ): ?array {
        $date = $this->parseDate($reading);
        $consumption = $this->parseConsumption($reading);

        if (!$date || $consumption === null) {
            return null;
        }

        // Get conversion coefficient
        $conversionCoef = $reading['coefficient_conversion']
            ?? $connection->metadata['coefficient_conversion']
            ?? self::DEFAULT_CONVERSION_COEFFICIENT;

        // Convert m³ to kWh if needed
        $unit = 'kWh';
        $valueKwh = $consumption['kwh'] ?? null;

        if ($valueKwh === null && $consumption['m3'] !== null) {
            $valueKwh = $consumption['m3'] * $conversionCoef;
        }

        if ($valueKwh === null) {
            return null;
        }

        $attributes = [
            'organization_id' => $connection->organization_id,
            'site_id' => $connection->site_id,
            'energy_connection_id' => $connection->id,
            'energy_type' => 'gas',
            'date' => $date->format('Y-m-d'),
            'granularity' => $granularity,
            'consumption' => round($valueKwh, 3),
            'unit' => $unit,
            'data_quality' => $this->parseQuality($reading),
            'provider_reference' => $reading['id_releve'] ?? null,
            'raw_data' => $reading,
        ];

        // Store original m³ value in metadata
        if ($consumption['m3'] !== null) {
            $attributes['raw_data']['volume_m3'] = $consumption['m3'];
            $attributes['raw_data']['conversion_coefficient'] = $conversionCoef;
        }

        return $attributes;
    }

    /**
     * Parse date from reading.
     */
    private function parseDate(array $reading): ?Carbon
    {
        // Try different date field names
        $dateValue = $reading['date_debut']
            ?? $reading['date']
            ?? $reading['date_releve']
            ?? $reading['jour']
            ?? null;

        if (!$dateValue) {
            return null;
        }

        try {
            return Carbon::parse($dateValue);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Parse consumption values from reading.
     *
     * @return array{m3: ?float, kwh: ?float}
     */
    private function parseConsumption(array $reading): array
    {
        $m3 = null;
        $kwh = null;

        // Volume in m³
        $m3Value = $reading['consommation_m3']
            ?? $reading['volume_m3']
            ?? $reading['conso_m3']
            ?? $reading['volume']
            ?? null;

        if ($m3Value !== null && $m3Value !== '') {
            if (is_string($m3Value)) {
                $m3Value = str_replace(',', '.', $m3Value);
            }
            $m3 = (float) $m3Value;
        }

        // Energy in kWh
        $kwhValue = $reading['consommation_kwh']
            ?? $reading['energie_kwh']
            ?? $reading['conso_kwh']
            ?? $reading['energie']
            ?? null;

        if ($kwhValue !== null && $kwhValue !== '') {
            if (is_string($kwhValue)) {
                $kwhValue = str_replace(',', '.', $kwhValue);
            }
            $kwh = (float) $kwhValue;
        }

        return ['m3' => $m3, 'kwh' => $kwh];
    }

    /**
     * Parse data quality indicator.
     */
    private function parseQuality(array $reading): string
    {
        $quality = $reading['qualite']
            ?? $reading['statut']
            ?? $reading['type_releve']
            ?? null;

        if (!$quality) {
            return 'measured';
        }

        $quality = strtolower($quality);

        return match (true) {
            str_contains($quality, 'estim') => 'estimated',
            str_contains($quality, 'index') => 'measured',
            str_contains($quality, 'mesure') => 'measured',
            str_contains($quality, 'calcul') => 'calculated',
            default => 'measured',
        };
    }

    /**
     * Store parsed consumption data.
     */
    public function storeConsumption(
        Collection $parsedData,
        EnergyConnection $connection
    ): array {
        $stats = [
            'inserted' => 0,
            'updated' => 0,
            'skipped' => 0,
            'errors' => 0,
        ];

        foreach ($parsedData as $data) {
            try {
                // Upsert based on unique constraint
                $existing = EnergyConsumption::where([
                    'energy_connection_id' => $data['energy_connection_id'],
                    'date' => $data['date'],
                    'granularity' => $data['granularity'],
                ])->first();

                if ($existing) {
                    // Only update if value changed significantly (>1% difference)
                    $difference = abs($existing->consumption - $data['consumption']);
                    if ($difference > $existing->consumption * 0.01) {
                        $existing->update($data);
                        $stats['updated']++;
                    } else {
                        $stats['skipped']++;
                    }
                } else {
                    $consumption = EnergyConsumption::create($data);

                    // Calculate emissions
                    $consumption->calculateEmissions();
                    $consumption->save();

                    $stats['inserted']++;
                }
            } catch (\Exception $e) {
                Log::error('Failed to store gas consumption', [
                    'data' => $data,
                    'error' => $e->getMessage(),
                ]);
                $stats['errors']++;
            }
        }

        return $stats;
    }

    /**
     * Aggregate daily data to monthly.
     */
    public function aggregateToMonthly(
        EnergyConnection $connection,
        int $year,
        int $month
    ): ?EnergyConsumption {
        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        $dailyData = EnergyConsumption::where('energy_connection_id', $connection->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->where('granularity', 'daily')
            ->where('energy_type', 'gas')
            ->get();

        if ($dailyData->isEmpty()) {
            return null;
        }

        $totalConsumption = $dailyData->sum('consumption');
        $totalEmissions = $dailyData->sum('emissions_kg');
        $avgTemperature = $dailyData->whereNotNull('outdoor_temperature')->avg('outdoor_temperature');

        // Get total volume from raw data
        $totalVolume = $dailyData->sum(function ($item) {
            return $item->raw_data['volume_m3'] ?? 0;
        });

        return EnergyConsumption::updateOrCreate(
            [
                'energy_connection_id' => $connection->id,
                'date' => $startDate->format('Y-m-d'),
                'granularity' => 'monthly',
                'energy_type' => 'gas',
            ],
            [
                'organization_id' => $connection->organization_id,
                'site_id' => $connection->site_id,
                'consumption' => $totalConsumption,
                'unit' => 'kWh',
                'emissions_kg' => $totalEmissions,
                'outdoor_temperature' => $avgTemperature,
                'data_quality' => 'aggregated',
                'raw_data' => [
                    'total_volume_m3' => $totalVolume,
                    'days_count' => $dailyData->count(),
                ],
            ]
        );
    }

    /**
     * Calculate degree days for heating analysis.
     *
     * @param float $avgTemperature Average outdoor temperature
     * @param float $baseTemperature Base temperature for heating (default 18°C)
     */
    public function calculateDegreeDays(float $avgTemperature, float $baseTemperature = 18.0): float
    {
        if ($avgTemperature >= $baseTemperature) {
            return 0;
        }

        return $baseTemperature - $avgTemperature;
    }

    /**
     * Estimate heating efficiency based on consumption and degree days.
     */
    public function estimateHeatingEfficiency(
        float $consumptionKwh,
        float $degreeDays,
        float $heatedAreaM2
    ): ?float {
        if ($degreeDays <= 0 || $heatedAreaM2 <= 0) {
            return null;
        }

        // kWh per degree-day per m²
        return $consumptionKwh / ($degreeDays * $heatedAreaM2);
    }
}
