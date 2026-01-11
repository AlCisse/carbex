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
        Schema::create('reports', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('organization_id');
            $table->unsignedBigInteger('generated_by');

            // Report Type
            $table->enum('type', [
                'monthly',
                'quarterly',
                'annual',
                'beges',           // French BEGES report
                'ghg_inventory',   // Full GHG inventory
                'custom',
            ])->default('monthly');

            // Report Details
            $table->string('name');
            $table->text('description')->nullable();

            // Period
            $table->date('period_start');
            $table->date('period_end');
            $table->year('year')->nullable();
            $table->tinyInteger('quarter')->nullable();
            $table->tinyInteger('month')->nullable();

            // Scope Inclusion
            $table->boolean('include_scope_1')->default(true);
            $table->boolean('include_scope_2')->default(true);
            $table->boolean('include_scope_3')->default(true);

            // Scope 2 Method
            $table->enum('scope_2_method', ['location_based', 'market_based', 'both'])
                ->default('location_based');

            // Summary Data (cached for quick access)
            $table->decimal('total_emissions', 15, 4)->nullable(); // tCO2e
            $table->decimal('scope_1_emissions', 15, 4)->nullable();
            $table->decimal('scope_2_emissions', 15, 4)->nullable();
            $table->decimal('scope_3_emissions', 15, 4)->nullable();
            $table->json('emissions_by_category')->nullable();

            // Comparison
            $table->decimal('previous_period_emissions', 15, 4)->nullable();
            $table->decimal('change_percent', 8, 2)->nullable();

            // File Storage
            $table->string('file_path')->nullable(); // S3 path
            $table->string('file_name')->nullable();
            $table->string('file_format')->default('pdf'); // pdf, xlsx, csv
            $table->integer('file_size')->nullable(); // bytes

            // Generation Status
            $table->enum('status', [
                'pending',
                'generating',
                'completed',
                'failed',
            ])->default('pending');
            $table->text('error_message')->nullable();
            $table->timestamp('generated_at')->nullable();

            // Settings
            $table->json('settings')->nullable(); // Report-specific settings
            $table->json('filters')->nullable(); // Applied filters

            // Sharing
            $table->boolean('is_public')->default(false);
            $table->string('share_token')->nullable();
            $table->timestamp('share_expires_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Foreign Keys
            $table->foreign('organization_id')
                ->references('id')
                ->on('organizations')
                ->onDelete('cascade');

            $table->foreign('generated_by')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            // Indexes
            $table->index('organization_id');
            $table->index('type');
            $table->index('status');
            $table->index('year');
            $table->index(['organization_id', 'type']);
            $table->index(['organization_id', 'year']);
            $table->index('share_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
