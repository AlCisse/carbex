<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * ReductionTarget (Trajectoire SBTi) - Reduction targets aligned with SBTi
     * Constitution Carbex v3.0 - Section 7, 2.9
     *
     * SBTi Recommendations:
     * - Scope 1 & 2: Minimum 4.2% annual reduction
     * - Scope 3: Minimum 2.5% annual reduction
     * - Aligned with Paris Agreement 1.5C target
     */
    public function up(): void
    {
        Schema::create('reduction_targets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('organization_id');

            // Reference year for baseline emissions
            $table->smallInteger('baseline_year')->unsigned();

            // Target achievement year
            $table->smallInteger('target_year')->unsigned();

            // Reduction percentages per scope
            $table->decimal('scope_1_reduction', 5, 2); // % reduction target
            $table->decimal('scope_2_reduction', 5, 2);
            $table->decimal('scope_3_reduction', 5, 2);

            // SBTi alignment flag
            $table->boolean('is_sbti_aligned')->default(false);

            // Additional notes
            $table->text('notes')->nullable();

            $table->timestamps();

            // Constraints
            $table->foreign('organization_id')
                ->references('id')
                ->on('organizations')
                ->onDelete('cascade');

            // One target per organization per baseline/target year combination
            $table->unique(['organization_id', 'baseline_year', 'target_year']);
            $table->index('organization_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reduction_targets');
    }
};
