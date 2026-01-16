<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOrganization;
use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Site extends Model
{
    use BelongsToOrganization, HasFactory, HasUuid, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'organization_id',
        'name',
        'code',
        'description',
        'type',
        'address_line_1',
        'address_line_2',
        'city',
        'postal_code',
        'country',
        'latitude',
        'longitude',
        'floor_area_m2',
        'employee_count',
        'electricity_provider',
        'renewable_energy',
        'renewable_percentage',
        'energy_rating',
        'building_type',
        'occupancy_rate',
        'construction_year',
        'annual_energy_kwh',
        'heating_type',
        'cooling_type',
        'is_primary',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'floor_area_m2' => 'decimal:2',
        'renewable_energy' => 'boolean',
        'renewable_percentage' => 'decimal:2',
        'occupancy_rate' => 'decimal:2',
        'construction_year' => 'integer',
        'annual_energy_kwh' => 'decimal:2',
        'is_primary' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Get the activities for the site.
     */
    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }

    /**
     * Get the emission records for the site.
     */
    public function emissionRecords(): HasMany
    {
        return $this->hasMany(EmissionRecord::class);
    }

    /**
     * Get the full address as a string.
     */
    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([
            $this->address_line_1,
            $this->address_line_2,
            $this->postal_code,
            $this->city,
            $this->country,
        ]);

        return implode(', ', $parts);
    }

    /**
     * Scope to active sites.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to primary site.
     */
    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }

    /**
     * Get the total emissions for this site (in kg CO2e).
     */
    public function getTotalEmissionsAttribute(): float
    {
        return $this->emissionRecords()->sum('co2e_kg') ?? 0;
    }

    /**
     * Get emissions per square meter (if floor area is set).
     */
    public function getEmissionsPerM2Attribute(): ?float
    {
        if (! $this->floor_area_m2 || $this->floor_area_m2 == 0) {
            return null;
        }

        return $this->total_emissions / $this->floor_area_m2;
    }

    /**
     * Get emissions per employee (if employee count is set).
     */
    public function getEmissionsPerEmployeeAttribute(): ?float
    {
        if (! $this->employee_count || $this->employee_count == 0) {
            return null;
        }

        return $this->total_emissions / $this->employee_count;
    }

    /**
     * Get energy efficiency label based on emissions per m2.
     */
    public function getEfficiencyLabelAttribute(): string
    {
        $emissionsPerM2 = $this->emissions_per_m2;

        if ($emissionsPerM2 === null) {
            return 'N/A';
        }

        // Thresholds in kg CO2e per m2 (annual)
        return match (true) {
            $emissionsPerM2 < 5 => 'A',
            $emissionsPerM2 < 10 => 'B',
            $emissionsPerM2 < 20 => 'C',
            $emissionsPerM2 < 35 => 'D',
            $emissionsPerM2 < 55 => 'E',
            $emissionsPerM2 < 80 => 'F',
            default => 'G',
        };
    }

    /**
     * Get the energy rating color class.
     */
    public function getEnergyRatingColorAttribute(): string
    {
        return match ($this->energy_rating) {
            'A' => 'bg-green-500',
            'B' => 'bg-lime-500',
            'C' => 'bg-yellow-400',
            'D' => 'bg-amber-500',
            'E' => 'bg-orange-500',
            'F' => 'bg-red-400',
            'G' => 'bg-red-600',
            default => 'bg-gray-400',
        };
    }

    /**
     * Get building type label.
     */
    public function getBuildingTypeLabelAttribute(): string
    {
        return match ($this->building_type) {
            'office_modern' => __('carbex.sites.building_types.office_modern'),
            'office_traditional' => __('carbex.sites.building_types.office_traditional'),
            'warehouse_heated' => __('carbex.sites.building_types.warehouse_heated'),
            'warehouse_unheated' => __('carbex.sites.building_types.warehouse_unheated'),
            'retail_standalone' => __('carbex.sites.building_types.retail_standalone'),
            'retail_mall' => __('carbex.sites.building_types.retail_mall'),
            'factory_light' => __('carbex.sites.building_types.factory_light'),
            'factory_heavy' => __('carbex.sites.building_types.factory_heavy'),
            'datacenter' => __('carbex.sites.building_types.datacenter'),
            'mixed_use' => __('carbex.sites.building_types.mixed_use'),
            'other' => __('carbex.sites.building_types.other'),
            default => $this->building_type ?? '-',
        };
    }
}
