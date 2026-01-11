<?php

namespace App\Services\Energy;

use App\Models\EnergyConnection;
use App\Models\EnergyConsumption;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

/**
 * Enedis Data Parser
 *
 * Parses and normalizes electricity consumption data from Enedis API responses.
 */
class EnedisDataParser
{
    /**
     * Parse consumption data from Enedis API response.
     *
     * @param array $response Raw API response
     * @param EnergyConnection $connection
     * @param string $granularity hourly|daily
     * @return Collection<EnergyConsumption>
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
                Log::warning('Failed to parse Enedis reading', [
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
        // DataConnect v5 format
        if (isset($response['meter_reading']['interval_reading'])) {
            return $response['meter_reading']['interval_reading'];
        }

        // Alternative format
        if (isset($response['interval_reading'])) {
            return $response['interval_reading'];
        }

        // Direct array of readings
        if (isset($response[0]) && isset($response[0]['date'])) {
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
        $value = $this->parseValue($reading);

        if (!$date || $value === null) {
            return null;
        }

        $attributes = [
            'organization_id' => $connection->organization_id,
            'site_id' => $connection->site_id,
            'energy_connection_id' => $connection->id,
            'energy_type' => 'electricity',
            'date' => $date->format('Y-m-d'),
            'granularity' => $granularity,
            'consumption' => $value,
            'unit' => 'kWh',
            'data_quality' => $this->parseQuality($reading),
            'provider_reference' => $reading['reading_id'] ?? null,
            'raw_data' => $reading,
        ];

        // Parse time for hourly data
        if ($granularity === 'hourly') {
            $times = $this->parseTime($reading);
            $attributes['time_start'] = $times['start'];
            $attributes['time_end'] = $times['end'];
        }

        // Parse peak/off-peak if available
        if (isset($reading['value_hc']) || isset($reading['off_peak_value'])) {
            $attributes['off_peak_consumption'] = $reading['value_hc'] ?? $reading['off_peak_value'] ?? null;
            $attributes['peak_consumption'] = $reading['value_hp'] ?? $reading['peak_value'] ?? null;
        }

        // Parse max power if available
        if (isset($reading['max_power'])) {
            $attributes['peak_power'] = $reading['max_power'];
        }

        return $attributes;
    }

    /**
     * Parse date from reading.
     */
    private function parseDate(array $reading): ?Carbon
    {
        // Try different date field names
        $dateValue = $reading['date'] ?? $reading['timestamp'] ?? $reading['reading_datetime'] ?? null;

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
     * Parse value from reading.
     */
    private function parseValue(array $reading): ?float
    {
        $value = $reading['value'] ?? $reading['consumption'] ?? $reading['energie'] ?? null;

        if ($value === null || $value === '') {
            return null;
        }

        // Handle string values with comma as decimal separator
        if (is_string($value)) {
            $value = str_replace(',', '.', $value);
        }

        $floatValue = (float) $value;

        // Convert Wh to kWh if needed
        if (isset($reading['unit']) && strtolower($reading['unit']) === 'wh') {
            $floatValue = $floatValue / 1000;
        }

        return $floatValue;
    }

    /**
     * Parse time from reading.
     */
    private function parseTime(array $reading): array
    {
        $timeStart = $reading['time_start'] ?? $reading['interval_start'] ?? null;
        $timeEnd = $reading['time_end'] ?? $reading['interval_end'] ?? null;

        // If only timestamp is provided, derive time
        if (!$timeStart && isset($reading['timestamp'])) {
            try {
                $dt = Carbon::parse($reading['timestamp']);
                $timeStart = $dt->format('H:i');
                $timeEnd = $dt->addHour()->format('H:i');
            } catch (\Exception $e) {
                $timeStart = null;
                $timeEnd = null;
            }
        }

        return [
            'start' => $timeStart,
            'end' => $timeEnd,
        ];
    }

    /**
     * Parse data quality indicator.
     */
    private function parseQuality(array $reading): string
    {
        $quality = $reading['quality'] ?? $reading['status'] ?? $reading['data_quality'] ?? null;

        if (!$quality) {
            return 'measured';
        }

        $quality = strtolower($quality);

        return match (true) {
            str_contains($quality, 'estim') => 'estimated',
            str_contains($quality, 'interpol') => 'interpolated',
            str_contains($quality, 'provis') => 'provisional',
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
                    'time_start' => $data['time_start'] ?? null,
                    'granularity' => $data['granularity'],
                ])->first();

                if ($existing) {
                    // Only update if value changed
                    if ($existing->consumption != $data['consumption']) {
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
                Log::error('Failed to store energy consumption', [
                    'data' => $data,
                    'error' => $e->getMessage(),
                ]);
                $stats['errors']++;
            }
        }

        return $stats;
    }

    /**
     * Aggregate hourly data to daily.
     */
    public function aggregateToDaily(
        EnergyConnection $connection,
        Carbon $date
    ): ?EnergyConsumption {
        $hourlyData = EnergyConsumption::where('energy_connection_id', $connection->id)
            ->where('date', $date->format('Y-m-d'))
            ->where('granularity', 'hourly')
            ->get();

        if ($hourlyData->isEmpty()) {
            return null;
        }

        $totalConsumption = $hourlyData->sum('consumption');
        $peakPower = $hourlyData->max('peak_power');
        $offPeakConsumption = $hourlyData->sum('off_peak_consumption');
        $peakConsumption = $hourlyData->sum('peak_consumption');

        return EnergyConsumption::updateOrCreate(
            [
                'energy_connection_id' => $connection->id,
                'date' => $date->format('Y-m-d'),
                'granularity' => 'daily',
            ],
            [
                'organization_id' => $connection->organization_id,
                'site_id' => $connection->site_id,
                'energy_type' => 'electricity',
                'consumption' => $totalConsumption,
                'unit' => 'kWh',
                'peak_power' => $peakPower,
                'off_peak_consumption' => $offPeakConsumption ?: null,
                'peak_consumption' => $peakConsumption ?: null,
                'data_quality' => 'aggregated',
            ]
        );
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
            ->get();

        if ($dailyData->isEmpty()) {
            return null;
        }

        $totalConsumption = $dailyData->sum('consumption');
        $totalEmissions = $dailyData->sum('emissions_kg');
        $avgTemperature = $dailyData->avg('outdoor_temperature');

        return EnergyConsumption::updateOrCreate(
            [
                'energy_connection_id' => $connection->id,
                'date' => $startDate->format('Y-m-d'),
                'granularity' => 'monthly',
            ],
            [
                'organization_id' => $connection->organization_id,
                'site_id' => $connection->site_id,
                'energy_type' => 'electricity',
                'consumption' => $totalConsumption,
                'unit' => 'kWh',
                'emissions_kg' => $totalEmissions,
                'outdoor_temperature' => $avgTemperature,
                'data_quality' => 'aggregated',
            ]
        );
    }
}
