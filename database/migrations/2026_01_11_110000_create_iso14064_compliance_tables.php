<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * ISO 14064-1 Full Compliance Tables
 *
 * Implements:
 * - GHG Removals/Sinks (Section 5.2.4)
 * - Verification & Assurance (Section 7)
 * - Organizational Boundaries (Section 5.1)
 * - Base Year & Recalculation Policy (Section 5.4)
 * - Uncertainty Assessment (Section 5.3)
 */
return new class extends Migration
{
    public function up(): void
    {
        // 1. GHG Removals / Carbon Sinks Table
        Schema::create('ghg_removals', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('assessment_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignUuid('site_id')->nullable()->constrained()->nullOnDelete();

            // Removal classification
            $table->string('removal_type', 50); // biological_sequestration, technological_removal, carbon_offset, avoided_emissions
            $table->string('removal_category', 50); // reforestation, daccs, vcs, etc.
            $table->text('description')->nullable();

            // Quantification
            $table->decimal('quantity_tonnes_co2e', 15, 4);
            $table->date('removal_date');
            $table->date('period_start')->nullable();
            $table->date('period_end')->nullable();

            // Methodology
            $table->string('methodology')->nullable();
            $table->string('methodology_reference')->nullable();

            // Project details (for offsets)
            $table->string('project_name')->nullable();
            $table->string('project_location')->nullable();
            $table->string('project_id')->nullable();

            // Certificate/Credit details
            $table->string('certificate_id')->nullable();
            $table->string('certificate_registry')->nullable();
            $table->string('certificate_url')->nullable();
            $table->year('vintage_year')->nullable();

            // Verification
            $table->string('verification_standard')->nullable();
            $table->string('verification_body')->nullable();
            $table->date('verification_date')->nullable();
            $table->enum('verification_status', ['pending', 'verified', 'rejected', 'expired'])->default('pending');

            // Permanence & Additionality (critical for net-zero)
            $table->integer('permanence_years')->nullable();
            $table->decimal('permanence_risk', 5, 2)->nullable(); // % risk of reversal
            $table->boolean('additionality_confirmed')->default(false);

            // Cost tracking
            $table->decimal('cost_per_tonne', 10, 2)->nullable();
            $table->decimal('total_cost', 15, 2)->nullable();
            $table->string('currency', 3)->default('EUR');

            // Data quality
            $table->enum('data_quality', ['measured', 'calculated', 'estimated'])->default('calculated');
            $table->decimal('uncertainty_percent', 5, 2)->nullable();
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable();

            // Audit
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['organization_id', 'removal_date']);
            $table->index(['assessment_id', 'removal_type']);
            $table->index('certificate_id');
        });

        // 2. GHG Verifications Table (ISO 14064-3)
        Schema::create('ghg_verifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('assessment_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignUuid('report_id')->nullable()->constrained()->nullOnDelete();

            // Verification type & level
            $table->enum('verification_type', ['internal', 'external_limited', 'external_reasonable', 'certification']);
            $table->enum('assurance_level', ['none', 'limited', 'reasonable'])->default('none');
            $table->enum('status', ['planned', 'in_progress', 'pending_review', 'verified', 'verified_with_comments', 'not_verified', 'cancelled'])->default('planned');

            // Scope
            $table->text('scope_description')->nullable();
            $table->string('verification_standard')->nullable(); // ISO 14064-3, ISAE 3410
            $table->string('verification_criteria')->nullable();

            // Materiality
            $table->decimal('materiality_threshold', 10, 2)->nullable();
            $table->string('materiality_unit', 20)->default('tCO2e');

            // Verifier details
            $table->string('verifier_organization')->nullable();
            $table->string('verifier_name')->nullable();
            $table->string('verifier_accreditation')->nullable();
            $table->string('verifier_contact')->nullable();

            // Dates
            $table->date('verification_start_date')->nullable();
            $table->date('verification_end_date')->nullable();
            $table->date('statement_date')->nullable();

            // Opinion
            $table->enum('opinion_type', ['unqualified', 'qualified', 'adverse', 'disclaimer'])->nullable();
            $table->text('opinion_statement')->nullable();

            // Findings
            $table->json('findings')->nullable();
            $table->json('non_conformities')->nullable();
            $table->json('corrective_actions')->nullable();
            $table->json('recommendations')->nullable();

            // Scope verification flags
            $table->boolean('scope_1_verified')->default(false);
            $table->boolean('scope_2_verified')->default(false);
            $table->boolean('scope_3_verified')->default(false);
            $table->boolean('removals_verified')->default(false);
            $table->boolean('base_year_verified')->default(false);
            $table->boolean('methodology_verified')->default(false);

            // ISO 14064-1 Principles assessment
            $table->boolean('data_quality_assessed')->default(false);
            $table->boolean('uncertainty_assessed')->default(false);
            $table->boolean('completeness_assessed')->default(false);
            $table->boolean('consistency_assessed')->default(false);
            $table->boolean('accuracy_assessed')->default(false);
            $table->boolean('transparency_assessed')->default(false);
            $table->boolean('relevance_assessed')->default(false);

            // Checklist & evidence
            $table->json('checklist_results')->nullable();
            $table->json('evidence_documents')->nullable();
            $table->string('verification_report_path')->nullable();
            $table->string('statement_path')->nullable();

            // Next verification
            $table->date('next_verification_date')->nullable();

            // Internal review
            $table->foreignId('internal_reviewer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('internal_review_date')->nullable();
            $table->text('internal_review_notes')->nullable();

            // Approval
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();

            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['organization_id', 'status']);
            $table->index(['assessment_id', 'verification_type']);
        });

        // 3. Add ISO 14064-1 fields to organizations table
        Schema::table('organizations', function (Blueprint $table) {
            // Organizational boundary (ISO 14064-1 Section 5.1)
            if (!Schema::hasColumn('organizations', 'consolidation_method')) {
                $table->enum('consolidation_method', ['operational_control', 'financial_control', 'equity_share'])
                    ->default('operational_control')
                    ->after('sector');
            }
            if (!Schema::hasColumn('organizations', 'boundary_description')) {
                $table->text('boundary_description')->nullable()->after('consolidation_method');
            }
            if (!Schema::hasColumn('organizations', 'boundary_definition_date')) {
                $table->date('boundary_definition_date')->nullable()->after('boundary_description');
            }

            // Base year (ISO 14064-1 Section 5.4)
            if (!Schema::hasColumn('organizations', 'base_year')) {
                $table->year('base_year')->nullable()->after('boundary_definition_date');
            }
            if (!Schema::hasColumn('organizations', 'base_year_emissions_tco2e')) {
                $table->decimal('base_year_emissions_tco2e', 15, 4)->nullable()->after('base_year');
            }
            if (!Schema::hasColumn('organizations', 'base_year_justification')) {
                $table->text('base_year_justification')->nullable()->after('base_year_emissions_tco2e');
            }

            // Recalculation policy
            if (!Schema::hasColumn('organizations', 'recalculation_policy')) {
                $table->text('recalculation_policy')->nullable()->after('base_year_justification');
            }
            if (!Schema::hasColumn('organizations', 'recalculation_threshold_percent')) {
                $table->decimal('recalculation_threshold_percent', 5, 2)->default(5.00)->after('recalculation_policy');
            }

            // Verification status
            if (!Schema::hasColumn('organizations', 'last_verification_date')) {
                $table->date('last_verification_date')->nullable()->after('recalculation_threshold_percent');
            }
            if (!Schema::hasColumn('organizations', 'verification_level')) {
                $table->enum('verification_level', ['none', 'internal', 'limited', 'reasonable'])
                    ->default('none')
                    ->after('last_verification_date');
            }
        });

        // 4. Add ISO 14064-1 fields to assessments table
        Schema::table('assessments', function (Blueprint $table) {
            // Base year flag
            if (!Schema::hasColumn('assessments', 'is_base_year')) {
                $table->boolean('is_base_year')->default(false)->after('year');
            }

            // Recalculation tracking
            if (!Schema::hasColumn('assessments', 'is_recalculated')) {
                $table->boolean('is_recalculated')->default(false)->after('is_base_year');
            }
            if (!Schema::hasColumn('assessments', 'recalculation_reason')) {
                $table->string('recalculation_reason')->nullable()->after('is_recalculated');
            }
            if (!Schema::hasColumn('assessments', 'original_assessment_id')) {
                $table->uuid('original_assessment_id')->nullable()->after('recalculation_reason');
            }

            // Total removals
            if (!Schema::hasColumn('assessments', 'total_removals_tco2e')) {
                $table->decimal('total_removals_tco2e', 15, 4)->default(0)->after('total_emissions');
            }
            if (!Schema::hasColumn('assessments', 'net_emissions_tco2e')) {
                $table->decimal('net_emissions_tco2e', 15, 4)->nullable()->after('total_removals_tco2e');
            }

            // Uncertainty
            if (!Schema::hasColumn('assessments', 'overall_uncertainty_percent')) {
                $table->decimal('overall_uncertainty_percent', 5, 2)->nullable()->after('net_emissions_tco2e');
            }
            if (!Schema::hasColumn('assessments', 'uncertainty_methodology')) {
                $table->string('uncertainty_methodology')->nullable()->after('overall_uncertainty_percent');
            }

            // Verification status
            if (!Schema::hasColumn('assessments', 'verification_status')) {
                $table->enum('verification_status', ['draft', 'internal_review', 'external_verification', 'verified', 'published'])
                    ->default('draft')
                    ->after('status');
            }

            // Completeness tracking
            if (!Schema::hasColumn('assessments', 'completeness_percent')) {
                $table->decimal('completeness_percent', 5, 2)->nullable()->after('verification_status');
            }
            if (!Schema::hasColumn('assessments', 'excluded_sources')) {
                $table->json('excluded_sources')->nullable()->after('completeness_percent');
            }
            if (!Schema::hasColumn('assessments', 'exclusion_justification')) {
                $table->text('exclusion_justification')->nullable()->after('excluded_sources');
            }
        });

        // 5. Enhance emission_records for better uncertainty tracking
        Schema::table('emission_records', function (Blueprint $table) {
            if (!Schema::hasColumn('emission_records', 'uncertainty_low_kg')) {
                $table->decimal('uncertainty_low_kg', 15, 4)->nullable()->after('uncertainty_percent');
            }
            if (!Schema::hasColumn('emission_records', 'uncertainty_high_kg')) {
                $table->decimal('uncertainty_high_kg', 15, 4)->nullable()->after('uncertainty_low_kg');
            }
            if (!Schema::hasColumn('emission_records', 'uncertainty_type')) {
                $table->enum('uncertainty_type', ['factor_based', 'measured', 'expert_judgment', 'monte_carlo'])
                    ->default('factor_based')
                    ->after('uncertainty_high_kg');
            }
        });

        // 6. Recalculation Events Audit Table
        Schema::create('ghg_recalculation_events', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('assessment_id')->nullable()->constrained()->nullOnDelete();

            $table->enum('event_type', ['base_year_change', 'methodology_change', 'boundary_change', 'error_correction', 'data_improvement', 'structural_change']);
            $table->text('description');
            $table->year('affected_year_from');
            $table->year('affected_year_to')->nullable();

            // Impact
            $table->decimal('previous_emissions_tco2e', 15, 4)->nullable();
            $table->decimal('recalculated_emissions_tco2e', 15, 4)->nullable();
            $table->decimal('change_percent', 8, 4)->nullable();

            // Documentation
            $table->text('justification');
            $table->json('affected_scopes')->nullable(); // [1, 2, 3]
            $table->json('affected_categories')->nullable();
            $table->string('methodology_before')->nullable();
            $table->string('methodology_after')->nullable();

            // Approval
            $table->foreignId('requested_by')->constrained('users');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');

            $table->timestamps();

            $table->index(['organization_id', 'event_type']);
            $table->index(['assessment_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ghg_recalculation_events');
        Schema::dropIfExists('ghg_verifications');
        Schema::dropIfExists('ghg_removals');

        Schema::table('emission_records', function (Blueprint $table) {
            $columns = ['uncertainty_low_kg', 'uncertainty_high_kg', 'uncertainty_type'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('emission_records', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::table('assessments', function (Blueprint $table) {
            $columns = [
                'is_base_year', 'is_recalculated', 'recalculation_reason', 'original_assessment_id',
                'total_removals_tco2e', 'net_emissions_tco2e', 'overall_uncertainty_percent',
                'uncertainty_methodology', 'verification_status', 'completeness_percent',
                'excluded_sources', 'exclusion_justification',
            ];
            foreach ($columns as $column) {
                if (Schema::hasColumn('assessments', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::table('organizations', function (Blueprint $table) {
            $columns = [
                'consolidation_method', 'boundary_description', 'boundary_definition_date',
                'base_year', 'base_year_emissions_tco2e', 'base_year_justification',
                'recalculation_policy', 'recalculation_threshold_percent',
                'last_verification_date', 'verification_level',
            ];
            foreach ($columns as $column) {
                if (Schema::hasColumn('organizations', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
