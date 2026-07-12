<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Task T173: Add enhanced site fields for TrackZero-style multi-site management.
     */
    public function up(): void
    {
        Schema::table('sites', function (Blueprint $table) {
            // Energy rating (A-G scale like EU EPC, or custom)
            $table->string('energy_rating', 10)->nullable()->after('renewable_percentage');

            // Building type (more specific than site type)
            $table->enum('building_type', [
                'office_modern',
                'office_traditional',
                'warehouse_heated',
                'warehouse_unheated',
                'retail_standalone',
                'retail_mall',
                'factory_light',
                'factory_heavy',
                'datacenter',
                'mixed_use',
                'other'
            ])->nullable()->after('energy_rating');

            // Occupancy rate (percentage of workspace utilization)
            $table->decimal('occupancy_rate', 5, 2)->nullable()->after('building_type');

            // Additional fields for better site comparison
            $table->year('construction_year')->nullable()->after('occupancy_rate');
            $table->decimal('annual_energy_kwh', 15, 2)->nullable()->after('construction_year');
            $table->string('heating_type')->nullable()->after('annual_energy_kwh');
            $table->string('cooling_type')->nullable()->after('heating_type');
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
                'annual_energy_kwh',
                'heating_type',
                'cooling_type',
            ]);
        });
    }
};
