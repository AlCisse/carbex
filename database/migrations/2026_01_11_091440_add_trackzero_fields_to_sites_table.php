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
        Schema::table('sites', function (Blueprint $table) {
            // Building characteristics
            $table->string('energy_rating', 10)->nullable()->after('floor_area_m2'); // A, B, C, D, E, F, G
            $table->string('building_type', 50)->nullable()->after('energy_rating'); // office, warehouse, retail, factory, etc.
            $table->decimal('occupancy_rate', 5, 2)->nullable()->after('building_type'); // Percentage 0-100
            $table->integer('construction_year')->nullable()->after('occupancy_rate');

            // Energy & sustainability
            $table->string('heating_type', 50)->nullable()->after('renewable_percentage'); // gas, electric, district, heat_pump
            $table->string('cooling_type', 50)->nullable()->after('heating_type'); // none, split, central, district
            $table->boolean('has_solar_panels')->default(false)->after('cooling_type');
            $table->decimal('solar_capacity_kwp', 10, 2)->nullable()->after('has_solar_panels');
            $table->boolean('has_ev_charging')->default(false)->after('solar_capacity_kwp');
            $table->integer('ev_charging_points')->nullable()->after('has_ev_charging');

            // Operational
            $table->integer('operating_hours_per_week')->nullable()->after('ev_charging_points'); // Hours per week
            $table->json('operating_schedule')->nullable()->after('operating_hours_per_week'); // Mon-Sun schedule
            $table->boolean('is_leased')->default(false)->after('operating_schedule');
            $table->date('lease_expiry_date')->nullable()->after('is_leased');

            // Compliance & certifications
            $table->json('certifications')->nullable()->after('lease_expiry_date'); // BREEAM, LEED, HQE, etc.
            $table->date('last_energy_audit')->nullable()->after('certifications');
            $table->date('next_energy_audit')->nullable()->after('last_energy_audit');

            // Performance metrics
            $table->decimal('baseline_emissions_tco2e', 12, 2)->nullable()->after('next_energy_audit');
            $table->decimal('target_reduction_percent', 5, 2)->nullable()->after('baseline_emissions_tco2e');
            $table->integer('baseline_year')->nullable()->after('target_reduction_percent');

            // Notes & tags
            $table->json('tags')->nullable()->after('baseline_year');
            $table->text('notes')->nullable()->after('tags');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sites', function (Blueprint $table) {
            $table->dropColumn([
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
            ]);
        });
    }
};
