<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('energy_consumptions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('organization_id');
            $table->uuid('site_id')->nullable();
            $table->uuid('energy_connection_id');

            // Energy type
            $table->string('energy_type'); // electricity, gas

            // Time period
            $table->date('date');
            $table->time('time_start')->nullable(); // For hourly data
            $table->time('time_end')->nullable();
            $table->string('granularity')->default('daily'); // hourly, daily, monthly

            // Consumption data
            $table->decimal('consumption', 12, 3); // kWh for electricity, m³ or kWh for gas
            $table->string('unit')->default('kWh');

            // Power data (electricity only)
            $table->decimal('peak_power', 10, 3)->nullable(); // kW
            $table->decimal('off_peak_consumption', 12, 3)->nullable(); // HC
            $table->decimal('peak_consumption', 12, 3)->nullable(); // HP

            // Temperature correlation (optional)
            $table->decimal('outdoor_temperature', 5, 2)->nullable(); // °C

            // Carbon calculation
            $table->decimal('emission_factor', 10, 6)->nullable(); // kg CO2e per unit
            $table->decimal('emissions_kg', 12, 6)->nullable(); // Total kg CO2e
            $table->string('emission_factor_source')->nullable();

            // Quality indicators
            $table->string('data_quality')->default('measured'); // measured, estimated, interpolated
            $table->boolean('is_validated')->default(false);

            // Raw data reference
            $table->string('provider_reference')->nullable();
            $table->json('raw_data')->nullable();

            $table->timestamps();

            $table->foreign('organization_id')
                ->references('id')
                ->on('organizations')
                ->onDelete('cascade');

            $table->foreign('site_id')
                ->references('id')
                ->on('sites')
                ->onDelete('set null');

            $table->foreign('energy_connection_id')
                ->references('id')
                ->on('energy_connections')
                ->onDelete('cascade');

            // Unique constraint to prevent duplicates
            $table->unique(['energy_connection_id', 'date', 'time_start', 'granularity'], 'energy_consumption_unique');

            $table->index(['organization_id', 'energy_type', 'date']);
            $table->index(['site_id', 'date']);
            $table->index(['date', 'granularity']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('energy_consumptions');
    }
};
