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
        Schema::create('categories', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // Basic Information
            $table->string('code')->unique(); // e.g., fuel_gasoline, electricity
            $table->string('name');
            $table->text('description')->nullable();

            // GHG Protocol Classification
            $table->tinyInteger('scope'); // 1, 2, or 3
            $table->string('ghg_category'); // e.g., mobile_combustion, purchased_electricity

            // For Scope 3, the sub-category (1-15)
            $table->tinyInteger('scope_3_category')->nullable();

            // Parent category for hierarchy
            $table->uuid('parent_id')->nullable();

            // MCC Code Mapping (JSON array)
            $table->json('mcc_codes')->nullable();

            // Keywords for AI categorization (JSON array)
            $table->json('keywords')->nullable();

            // Default Unit
            $table->string('default_unit')->default('EUR'); // EUR, kWh, liter, km, etc.

            // Calculation Method
            $table->enum('calculation_method', [
                'spend_based',      // Use monetary amount × factor
                'activity_based',   // Use quantity × factor
                'hybrid',           // Can use either
            ])->default('spend_based');

            // Display
            $table->string('icon')->nullable();
            $table->string('color')->nullable();
            $table->integer('sort_order')->default(0);

            // Status
            $table->boolean('is_active')->default(true);

            // Translations (JSON)
            $table->json('translations')->nullable();

            $table->timestamps();

            // Indexes
            $table->index('scope');
            $table->index('ghg_category');
            $table->index('is_active');
            $table->index('parent_id');
        });

        // Add foreign key after table creation (self-referencing)
        Schema::table('categories', function (Blueprint $table) {
            $table->foreign('parent_id')
                ->references('id')
                ->on('categories')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
