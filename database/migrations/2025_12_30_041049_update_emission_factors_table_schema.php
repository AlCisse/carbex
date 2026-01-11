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
        // Drop foreign key constraint from emission_records first
        Schema::table('emission_records', function (Blueprint $table) {
            $table->dropForeign(['emission_factor_id']);
        });

        // Drop the table and recreate with correct schema
        Schema::dropIfExists('emission_factors');

        Schema::create('emission_factors', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('category_id')->nullable();

            // Source identification
            $table->string('source'); // ademe, uba, ghg_protocol, custom
            $table->string('source_id')->nullable();
            $table->string('source_url')->nullable();

            // Multilingual names
            $table->string('name'); // Default (French)
            $table->string('name_en')->nullable();
            $table->string('name_de')->nullable();
            $table->text('description')->nullable();

            // Factor values (kgCO2e per unit)
            $table->decimal('factor_kg_co2e', 15, 10);
            $table->decimal('factor_kg_co2', 15, 10)->nullable();
            $table->decimal('factor_kg_ch4', 15, 10)->nullable();
            $table->decimal('factor_kg_n2o', 15, 10)->nullable();

            // Unit
            $table->string('unit'); // kWh, L, km, EUR, kg, t, m3, etc.

            // Uncertainty
            $table->decimal('uncertainty_percent', 5, 2)->nullable();

            // Scope & classification
            $table->tinyInteger('scope')->nullable(); // 1, 2, or 3
            $table->string('ghg_category')->nullable();
            $table->string('sector')->nullable();

            // Geographic scope
            $table->string('country', 2)->nullable(); // ISO country code
            $table->string('region')->nullable();

            // Validity
            $table->date('valid_from')->nullable();
            $table->date('valid_until')->nullable();

            // Methodology
            $table->string('methodology')->nullable(); // location-based, market-based, etc.

            // Status
            $table->boolean('is_active')->default(true);

            // Additional data
            $table->json('metadata')->nullable();

            $table->timestamps();

            // Indexes
            $table->index('source');
            $table->index('country');
            $table->index('scope');
            $table->index('unit');
            $table->index('is_active');
            $table->index(['source', 'source_id']);
            $table->unique(['source', 'source_id']);
        });

        // Recreate foreign key on emission_records
        Schema::table('emission_records', function (Blueprint $table) {
            $table->foreign('emission_factor_id')
                ->references('id')
                ->on('emission_factors')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Cannot easily reverse this - would need to recreate original schema
    }
};
