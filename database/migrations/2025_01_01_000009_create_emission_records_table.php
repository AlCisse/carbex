<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('emission_records', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('organization_id');
            $table->uuid('activity_id');
            $table->uuid('emission_factor_id');
            $table->uuid('category_id');
            $table->uuid('site_id')->nullable();

            // Time Period
            $table->date('date');
            $table->year('year');
            $table->tinyInteger('month');
            $table->tinyInteger('quarter');

            // Scope Classification
            $table->tinyInteger('scope'); // 1, 2, or 3
            $table->string('ghg_category');
            $table->tinyInteger('scope_3_category')->nullable();

            // Activity Data
            $table->decimal('activity_quantity', 15, 4);
            $table->string('activity_unit');

            // Emission Factor Snapshot (for audit trail)
            $table->decimal('factor_value', 15, 8);
            $table->string('factor_unit');
            $table->string('factor_source');
            $table->string('factor_version')->nullable();

            // Calculated Emissions (kgCO2e)
            $table->decimal('emissions_co2', 15, 4);
            $table->decimal('emissions_ch4', 15, 4)->default(0);
            $table->decimal('emissions_n2o', 15, 4)->default(0);
            $table->decimal('emissions_total', 15, 4); // Total kgCO2e

            // Emissions in tonnes for reporting
            $table->decimal('emissions_tonnes', 15, 6); // tCO2e

            // Calculation Method
            $table->enum('calculation_method', [
                'spend_based',
                'activity_based',
                'average_data',
                'supplier_specific',
            ])->default('activity_based');

            // Data Quality
            $table->enum('data_quality', ['measured', 'calculated', 'estimated'])->default('calculated');
            $table->decimal('uncertainty_percent', 5, 2)->nullable();

            // Scope 2 specific (location vs market based)
            $table->enum('scope_2_method', ['location_based', 'market_based'])->nullable();

            // Audit Trail
            $table->json('calculation_details')->nullable(); // Full calculation breakdown
            $table->unsignedBigInteger('calculated_by')->nullable(); // User or 'system'
            $table->timestamp('calculated_at');

            // Status
            $table->boolean('is_verified')->default(false);
            $table->unsignedBigInteger('verified_by')->nullable();
            $table->timestamp('verified_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Foreign Keys
            $table->foreign('organization_id')
                ->references('id')
                ->on('organizations')
                ->onDelete('cascade');

            $table->foreign('activity_id')
                ->references('id')
                ->on('activities')
                ->onDelete('cascade');

            $table->foreign('emission_factor_id')
                ->references('id')
                ->on('emission_factors')
                ->onDelete('restrict');

            $table->foreign('category_id')
                ->references('id')
                ->on('categories')
                ->onDelete('restrict');

            $table->foreign('site_id')
                ->references('id')
                ->on('sites')
                ->onDelete('set null');

            // Indexes
            $table->index('organization_id');
            $table->index('activity_id');
            $table->index('category_id');
            $table->index('date');
            $table->index('year');
            $table->index('scope');
            $table->index(['organization_id', 'year']);
            $table->index(['organization_id', 'year', 'scope']);
            $table->index(['organization_id', 'year', 'month']);
            $table->index(['organization_id', 'category_id', 'year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('emission_records');
    }
};
