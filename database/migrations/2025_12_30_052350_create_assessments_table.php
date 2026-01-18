<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Assessment (Bilan) - Annual carbon assessment entity
     * Constitution LinsCarbon v3.0 - Section 7, 2.10
     */
    public function up(): void
    {
        Schema::create('assessments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('organization_id');

            // Assessment period
            $table->year('year');

            // Organization context for this assessment
            $table->decimal('revenue', 15, 2)->nullable();         // Chiffre d'affaires
            $table->unsignedInteger('employee_count')->nullable(); // Nombre de collaborateurs

            // Lifecycle status
            $table->enum('status', ['draft', 'active', 'completed'])->default('draft');

            // Progress tracking per scope/category
            $table->json('progress')->nullable();

            $table->timestamps();

            // Constraints
            $table->foreign('organization_id')
                ->references('id')
                ->on('organizations')
                ->onDelete('cascade');

            // One assessment per organization per year
            $table->unique(['organization_id', 'year']);
            $table->index(['organization_id', 'status']);
        });

        // Add assessment_id to emission_records if not exists
        if (! Schema::hasColumn('emission_records', 'assessment_id')) {
            Schema::table('emission_records', function (Blueprint $table) {
                $table->uuid('assessment_id')->nullable()->after('organization_id');

                $table->foreign('assessment_id')
                    ->references('id')
                    ->on('assessments')
                    ->onDelete('set null');

                $table->index(['assessment_id', 'scope']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove foreign key from emission_records first
        if (Schema::hasColumn('emission_records', 'assessment_id')) {
            Schema::table('emission_records', function (Blueprint $table) {
                $table->dropForeign(['assessment_id']);
                $table->dropIndex(['assessment_id', 'scope']);
                $table->dropColumn('assessment_id');
            });
        }

        Schema::dropIfExists('assessments');
    }
};
