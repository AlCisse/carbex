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
        Schema::create('emission_factors', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('category_id')->nullable();

            // Identification
            $table->string('code')->unique(); // Unique factor code
            $table->string('source'); // ademe, uba, ecoinvent, ghg_protocol
            $table->string('source_id')->nullable(); // ID in source database
            $table->string('version')->nullable(); // Version of the source

            // Basic Information
            $table->string('name');
            $table->text('description')->nullable();

            // Geographic Scope
            $table->string('country', 2)->nullable(); // ISO country code, null = global
            $table->string('region')->nullable();

            // Factor Values (kgCO2e per unit)
            $table->decimal('factor_co2', 15, 8); // CO2 component
            $table->decimal('factor_ch4', 15, 8)->default(0); // CH4 component
            $table->decimal('factor_n2o', 15, 8)->default(0); // N2O component
            $table->decimal('factor_total', 15, 8); // Total kgCO2e

            // Unit
            $table->string('unit'); // kWh, liter, km, EUR, kg, etc.
            $table->string('unit_numerator')->default('kgCO2e');

            // Uncertainty
            $table->decimal('uncertainty_percent', 5, 2)->nullable();
            $table->enum('quality', ['high', 'medium', 'low'])->default('medium');

            // Validity Period
            $table->date('valid_from');
            $table->date('valid_to')->nullable();

            // Scope
            $table->tinyInteger('scope')->nullable(); // 1, 2, or 3
            $table->string('ghg_category')->nullable();

            // Additional Data
            $table->json('metadata')->nullable(); // Additional source-specific data
            $table->json('tags')->nullable(); // For search/filtering

            // Status
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false); // Default for this category

            // Search (for Meilisearch)
            $table->text('search_text')->nullable();

            $table->timestamps();

            // Foreign Keys
            $table->foreign('category_id')
                ->references('id')
                ->on('categories')
                ->onDelete('set null');

            // Indexes
            $table->index('source');
            $table->index('country');
            $table->index('category_id');
            $table->index('is_active');
            $table->index(['country', 'category_id', 'is_active']);
            $table->index(['source', 'source_id']);
            $table->index('valid_from');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('emission_factors');
    }
};
