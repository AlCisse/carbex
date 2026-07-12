<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOrganization;
use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Energy Consumption Model
 *
 * Stores energy consumption data retrieved from providers.
 */
class EnergyConsumption extends Model
{
    use BelongsToOrganization, HasFactory, HasUuid;

    protected $fillable = [
        'organization_id',
        'site_id',
        'energy_connection_id',
        'energy_type',
        'date',
        'time_start',
        'time_end',
        'granularity',
        'consumption',
        'unit',
        'peak_power',
        'off_peak_consumption',
        'peak_consumption',
        'outdoor_temperature',
        'emission_factor',
        'emissions_kg',
        'emission_factor_source',
        'data_quality',
        'is_validated',
        'provider_reference',
        'raw_data',
    ];

    protected $casts = [
        'date' => 'date',
        'consumption' => 'decimal:3',
        'peak_power' => 'decimal:3',
        'off_peak_consumption' => 'decimal:3',
        'peak_consumption' => 'decimal:3',
        'outdoor_temperature' => 'decimal:2',
        'emission_factor' => 'decimal:6',
        'emissions_kg' => 'decimal:6',
        'is_validated' => 'boolean',
        'raw_data' => 'array',
    ];

    /**
     * Get the energy connection.
     */
    public function energyConnection(): BelongsTo
    {
        return $this->belongsTo(EnergyConnection::class);
    }

    /**
     * Get the site.
     */
    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    /**
     * Calculate emissions using the configured factor.
     */
    public function calculateEmissions(): void
    {
        if (!$this->emission_factor) {
            $this->loadEmissionFactor();
        }

        if ($this->emission_factor && $this->consumption) {
            $this->emissions_kg = $this->consumption * $this->emission_factor;
        }
    }

    /**
     * Load emission factor from configuration.
     */
    public function loadEmissionFactor(): void
    {
        $country = $this->organization?->country ?? 'FR';
        $type = $this->energy_type;

        $factorConfig = config("energy.emission_factors.{$type}.{$country}");

        if ($factorConfig) {
            // Convert to same unit if needed
            if ($this->unit === 'kWh' && isset($factorConfig['factor_kwh'])) {
                $this->emission_factor = $factorConfig['factor_kwh'];
            } elseif ($this->unit === $factorConfig['unit']) {
                $this->emission_factor = $factorConfig['factor'];
            } else {
                $this->emission_factor = $factorConfig['factor'];
            }

            $this->emission_factor_source = $factorConfig['source'];
        }
    }

    /**
     * Get the period label.
     */
    public function getPeriodLabelAttribute(): string
    {
        if ($this->granularity === 'hourly' && $this->time_start) {
            return $this->date->format('d/m/Y') . ' ' . $this->time_start->format('H:i');
        }

        return match ($this->granularity) {
            'daily' => $this->date->format('d/m/Y'),
            'monthly' => $this->date->format('m/Y'),
            'yearly' => $this->date->format('Y'),
            default => $this->date->format('d/m/Y'),
        };
    }

    /**
     * Scope by date range.
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    /**
     * Scope by energy type.
     */
    public function scopeEnergyType($query, string $type)
    {
        return $query->where('energy_type', $type);
    }

    /**
     * Scope by granularity.
     */
    public function scopeGranularity($query, string $granularity)
    {
        return $query->where('granularity', $granularity);
    }

    /**
     * Get daily aggregation.
     */
    public static function aggregateDaily($query)
    {
        return $query->selectRaw('
            date,
            energy_type,
            SUM(consumption) as total_consumption,
            SUM(emissions_kg) as total_emissions,
            AVG(outdoor_temperature) as avg_temperature,
            COUNT(*) as reading_count
        ')
            ->groupBy('date', 'energy_type')
            ->orderBy('date');
    }

    /**
     * Get monthly aggregation.
     */
    public static function aggregateMonthly($query)
    {
        return $query->selectRaw("
            DATE_TRUNC('month', date) as month,
            energy_type,
            SUM(consumption) as total_consumption,
            SUM(emissions_kg) as total_emissions,
            AVG(outdoor_temperature) as avg_temperature,
            COUNT(*) as reading_count
        ")
            ->groupByRaw("DATE_TRUNC('month', date), energy_type")
            ->orderByRaw("DATE_TRUNC('month', date)");
    }
}
