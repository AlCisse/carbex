<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Create compliance tables for CSRD and ISO standards tracking.
 *
 * Tasks T177 - Phase 10 (TrackZero Features)
 * Constitution Carbex v3.0 - Section 8 (Conformité)
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // CSRD Disclosure Requirements
        Schema::create('csrd_frameworks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code', 50)->unique(); // E1-1, S1-2, G1-1, etc.
            $table->string('category', 50); // environment, social, governance
            $table->string('topic', 100); // climate_change, pollution, water, biodiversity
            $table->string('name');
            $table->string('name_en')->nullable();
            $table->string('name_de')->nullable();
            $table->text('description')->nullable();
            $table->text('description_en')->nullable();
            $table->text('description_de')->nullable();
            $table->json('required_disclosures')->nullable(); // List of required data points
            $table->json('reporting_frequency')->nullable(); // annual, quarterly
            $table->boolean('is_mandatory')->default(true);
            $table->integer('esrs_reference')->nullable(); // ESRS number
            $table->integer('display_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // ISO Standards Reference
        Schema::create('iso_standards', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code', 50)->unique(); // ISO-14001, ISO-14064-1, ISO-50001
            $table->string('name');
            $table->string('name_en')->nullable();
            $table->string('name_de')->nullable();
            $table->text('description')->nullable();
            $table->text('description_en')->nullable();
            $table->text('description_de')->nullable();
            $table->string('category', 50); // environmental, energy, quality
            $table->json('requirements')->nullable(); // List of requirements
            $table->string('certification_body')->nullable();
            $table->integer('validity_years')->default(3);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Organization compliance status for CSRD
        Schema::create('organization_csrd_compliance', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('organization_id');
            $table->uuid('csrd_framework_id');
            $table->integer('year');
            $table->string('status', 50)->default('not_started'); // not_started, in_progress, compliant, non_compliant
            $table->decimal('completion_percentage', 5, 2)->default(0);
            $table->json('data_points')->nullable(); // Collected data for this disclosure
            $table->json('evidence_documents')->nullable(); // Links to supporting docs
            $table->text('notes')->nullable();
            $table->uuid('reviewed_by')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();

            $table->foreign('organization_id')->references('id')->on('organizations')->cascadeOnDelete();
            $table->foreign('csrd_framework_id')->references('id')->on('csrd_frameworks')->cascadeOnDelete();
            $table->unique(['organization_id', 'csrd_framework_id', 'year']);
        });

        // Organization ISO certifications
        Schema::create('organization_iso_certifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('organization_id');
            $table->uuid('iso_standard_id');
            $table->string('certification_number')->nullable();
            $table->string('status', 50)->default('not_certified'); // not_certified, in_progress, certified, expired
            $table->date('certification_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->date('next_audit_date')->nullable();
            $table->string('certifying_body')->nullable();
            $table->json('scope_description')->nullable();
            $table->json('audit_history')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('organization_id')->references('id')->on('organizations')->cascadeOnDelete();
            $table->foreign('iso_standard_id')->references('id')->on('iso_standards')->cascadeOnDelete();
            $table->unique(['organization_id', 'iso_standard_id']);
        });

        // Compliance tasks and action items
        Schema::create('compliance_tasks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('organization_id');
            $table->string('type', 50); // csrd, iso, internal
            $table->uuid('reference_id')->nullable(); // csrd_framework_id or iso_standard_id
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('status', 50)->default('pending'); // pending, in_progress, completed, overdue
            $table->string('priority', 20)->default('medium'); // low, medium, high, critical
            $table->uuid('assigned_to')->nullable();
            $table->date('due_date')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->uuid('completed_by')->nullable();
            $table->json('checklist')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('organization_id')->references('id')->on('organizations')->cascadeOnDelete();
            $table->index(['organization_id', 'status']);
            $table->index(['organization_id', 'due_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('compliance_tasks');
        Schema::dropIfExists('organization_iso_certifications');
        Schema::dropIfExists('organization_csrd_compliance');
        Schema::dropIfExists('iso_standards');
        Schema::dropIfExists('csrd_frameworks');
    }
};
