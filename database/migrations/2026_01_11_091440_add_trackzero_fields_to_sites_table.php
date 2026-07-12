<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Add TrackZero-style fields to sites table.
 *
 * Tasks T173 - Phase 10 (TrackZero Features)
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $columns = [
            'energy_rating' => fn(Blueprint $table) => $table->string('energy_rating', 10)->nullable(),
            'building_type' => fn(Blueprint $table) => $table->string('building_type', 50)->nullable(),
            'occupancy_rate' => fn(Blueprint $table) => $table->decimal('occupancy_rate', 5, 2)->nullable(),
            'construction_year' => fn(Blueprint $table) => $table->integer('construction_year')->nullable(),
            'heating_type' => fn(Blueprint $table) => $table->string('heating_type', 50)->nullable(),
            'cooling_type' => fn(Blueprint $table) => $table->string('cooling_type', 50)->nullable(),
            'has_solar_panels' => fn(Blueprint $table) => $table->boolean('has_solar_panels')->default(false),
            'solar_capacity_kwp' => fn(Blueprint $table) => $table->decimal('solar_capacity_kwp', 10, 2)->nullable(),
            'has_ev_charging' => fn(Blueprint $table) => $table->boolean('has_ev_charging')->default(false),
            'ev_charging_points' => fn(Blueprint $table) => $table->integer('ev_charging_points')->nullable(),
            'operating_hours_per_week' => fn(Blueprint $table) => $table->integer('operating_hours_per_week')->nullable(),
            'operating_schedule' => fn(Blueprint $table) => $table->json('operating_schedule')->nullable(),
            'is_leased' => fn(Blueprint $table) => $table->boolean('is_leased')->default(false),
            'lease_expiry_date' => fn(Blueprint $table) => $table->date('lease_expiry_date')->nullable(),
            'certifications' => fn(Blueprint $table) => $table->json('certifications')->nullable(),
            'last_energy_audit' => fn(Blueprint $table) => $table->date('last_energy_audit')->nullable(),
            'next_energy_audit' => fn(Blueprint $table) => $table->date('next_energy_audit')->nullable(),
            'baseline_emissions_tco2e' => fn(Blueprint $table) => $table->decimal('baseline_emissions_tco2e', 12, 2)->nullable(),
            'target_reduction_percent' => fn(Blueprint $table) => $table->decimal('target_reduction_percent', 5, 2)->nullable(),
            'baseline_year' => fn(Blueprint $table) => $table->integer('baseline_year')->nullable(),
            'tags' => fn(Blueprint $table) => $table->json('tags')->nullable(),
            'notes' => fn(Blueprint $table) => $table->text('notes')->nullable(),
        ];

        foreach ($columns as $columnName => $addColumn) {
            if (!Schema::hasColumn('sites', $columnName)) {
                Schema::table('sites', function (Blueprint $table) use ($addColumn) {
                    $addColumn($table);
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $columns = [
            'energy_rating',
            'building_type',
            'occupancy_rate',
            'construction_year',
            'heating_type',
            'cooling_type',
            'has_solar_panels',
            'solar_capacity_kwp',
            'has_ev_charging',
            'ev_charging_points',
            'operating_hours_per_week',
            'operating_schedule',
            'is_leased',
            'lease_expiry_date',
            'certifications',
            'last_energy_audit',
            'next_energy_audit',
            'baseline_emissions_tco2e',
            'target_reduction_percent',
            'baseline_year',
            'tags',
            'notes',
        ];

        foreach ($columns as $column) {
            if (Schema::hasColumn('sites', $column)) {
                Schema::table('sites', function (Blueprint $table) use ($column) {
                    $table->dropColumn($column);
                });
            }
        }
    }
};
