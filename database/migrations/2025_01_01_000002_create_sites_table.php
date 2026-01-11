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
        Schema::create('sites', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('organization_id');

            // Basic Information
            $table->string('name');
            $table->string('code')->nullable(); // Internal reference code
            $table->text('description')->nullable();

            // Type
            $table->enum('type', [
                'headquarters',
                'office',
                'warehouse',
                'factory',
                'retail',
                'remote',
                'other'
            ])->default('office');

            // Address
            $table->string('address_line_1')->nullable();
            $table->string('address_line_2')->nullable();
            $table->string('city')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('country', 2)->default('FR');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();

            // Site Metrics
            $table->decimal('floor_area_m2', 10, 2)->nullable(); // Square meters
            $table->integer('employee_count')->nullable();

            // Energy
            $table->string('electricity_provider')->nullable();
            $table->boolean('renewable_energy')->default(false);
            $table->decimal('renewable_percentage', 5, 2)->default(0);

            // Status
            $table->boolean('is_primary')->default(false);
            $table->boolean('is_active')->default(true);

            $table->timestamps();
            $table->softDeletes();

            // Foreign Keys
            $table->foreign('organization_id')
                ->references('id')
                ->on('organizations')
                ->onDelete('cascade');

            // Indexes
            $table->index('organization_id');
            $table->index(['organization_id', 'is_active']);
            $table->unique(['organization_id', 'code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sites');
    }
};
