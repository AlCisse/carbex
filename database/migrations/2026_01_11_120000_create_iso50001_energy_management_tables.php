<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * ISO 50001:2018 Energy Management System Tables
 *
 * Implements:
 * - Energy Review (Section 6.3)
 * - Energy Performance Indicators - EnPIs (Section 6.4)
 * - Energy Baseline - EnB (Section 6.5)
 * - Energy Objectives and Targets (Section 6.6)
 * - Energy Audit (Section 9.2)
 */
return new class extends Migration
{
    public function up(): void
    {
        // 1. Energy Reviews Table (ISO 50001 Section 6.3)
        Schema::create('energy_reviews', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('site_id')->nullable()->constrained()->nullOnDelete();

            // Review period
            $table->year('review_year');
            $table->date('period_start');
            $table->date('period_end');
            $table->enum('status', ['draft', 'in_progress', 'completed', 'approved'])->default('draft');

            // Energy use analysis (Section 6.3.a)
            $table->decimal('total_energy_kwh', 15, 2)->nullable();
            $table->decimal('total_energy_cost', 15, 2)->nullable();
            $table->string('currency', 3)->default('EUR');
            $table->json('energy_sources')->nullable(); // electricity, gas, fuel, etc.
            $table->json('energy_by_source')->nullable(); // breakdown by source

            // Significant Energy Uses - SEUs (Section 6.3.b)
            $table->json('significant_energy_uses')->nullable();
            $table->decimal('seu_threshold_percent', 5, 2)->default(5.00); // % of total to be SEU

            // Variables affecting SEUs (Section 6.3.c)
            $table->json('relevant_variables')->nullable(); // production volume, weather, occupancy

            // Current energy performance (Section 6.3.d)
            $table->decimal('energy_intensity', 15, 6)->nullable(); // kWh per unit
            $table->string('intensity_unit', 50)->nullable(); // per m², per employee, per unit

            // Opportunities for improvement (Section 6.3.e)
            $table->json('improvement_opportunities')->nullable();
            $table->decimal('potential_savings_kwh', 15, 2)->nullable();
            $table->decimal('potential_savings_cost', 15, 2)->nullable();

            // Comparison with previous period
            $table->decimal('previous_period_kwh', 15, 2)->nullable();
            $table->decimal('change_percent', 8, 4)->nullable();

            // Documentation
            $table->text('methodology')->nullable();
            $table->text('findings')->nullable();
            $table->text('recommendations')->nullable();
            $table->json('data_sources')->nullable();

            // Approval
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();

            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['organization_id', 'review_year', 'site_id'], 'energy_review_org_year_site_unique');
            $table->index(['organization_id', 'status']);
        });

        // 2. Energy Performance Indicators - EnPIs (ISO 50001 Section 6.4)
        Schema::create('energy_performance_indicators', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('site_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignUuid('energy_review_id')->nullable()->constrained('energy_reviews')->nullOnDelete();

            // Indicator definition
            $table->string('name');
            $table->string('name_de')->nullable();
            $table->string('code', 20); // EnPI-1, EnPI-2, etc.
            $table->text('description')->nullable();

            // Type of indicator
            $table->enum('indicator_type', [
                'simple_metric',        // kWh total
                'ratio',               // kWh/m², kWh/unit
                'regression_model',    // Statistical model
                'engineering_model',   // Based on equipment specs
            ])->default('ratio');

            // Calculation parameters
            $table->string('numerator_metric', 100); // energy_kwh, cost_eur
            $table->string('numerator_unit', 20)->default('kWh');
            $table->string('denominator_metric', 100)->nullable(); // area_m2, employees, units
            $table->string('denominator_unit', 20)->nullable();
            $table->json('normalization_factors')->nullable(); // weather, production

            // Current values
            $table->year('measurement_year');
            $table->decimal('current_value', 20, 6)->nullable();
            $table->decimal('baseline_value', 20, 6)->nullable();
            $table->decimal('target_value', 20, 6)->nullable();
            $table->string('unit', 50)->nullable(); // kWh/m², kWh/FTE

            // Performance tracking
            $table->decimal('improvement_percent', 8, 4)->nullable();
            $table->boolean('target_achieved')->default(false);
            $table->enum('trend', ['improving', 'stable', 'declining'])->nullable();

            // Statistical model (for regression-based EnPIs)
            $table->json('model_parameters')->nullable();
            $table->decimal('r_squared', 5, 4)->nullable();
            $table->decimal('standard_error', 10, 4)->nullable();

            // Boundaries
            $table->json('applicable_sbus')->nullable(); // Significant boundary uses
            $table->json('excluded_areas')->nullable();

            // Data quality
            $table->enum('data_quality', ['measured', 'calculated', 'estimated'])->default('calculated');
            $table->decimal('uncertainty_percent', 5, 2)->nullable();
            $table->text('notes')->nullable();

            $table->boolean('is_active')->default(true);
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['organization_id', 'measurement_year']);
            $table->index(['organization_id', 'code']);
            $table->index('is_active');
        });

        // 3. Energy Baselines - EnB (ISO 50001 Section 6.5)
        Schema::create('energy_baselines', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('site_id')->nullable()->constrained()->nullOnDelete();

            // Baseline definition
            $table->string('name');
            $table->year('baseline_year');
            $table->date('period_start');
            $table->date('period_end');
            $table->boolean('is_current')->default(true);

            // Energy data
            $table->decimal('total_energy_kwh', 15, 2);
            $table->decimal('electricity_kwh', 15, 2)->nullable();
            $table->decimal('natural_gas_kwh', 15, 2)->nullable();
            $table->decimal('fuel_kwh', 15, 2)->nullable();
            $table->decimal('other_energy_kwh', 15, 2)->nullable();
            $table->json('energy_breakdown')->nullable();

            // Normalization data
            $table->decimal('floor_area_m2', 12, 2)->nullable();
            $table->integer('employee_count')->nullable();
            $table->decimal('production_units', 15, 2)->nullable();
            $table->string('production_unit_name', 50)->nullable();
            $table->decimal('heating_degree_days', 10, 2)->nullable();
            $table->decimal('cooling_degree_days', 10, 2)->nullable();
            $table->json('other_variables')->nullable();

            // Calculated intensities
            $table->decimal('energy_per_m2', 12, 4)->nullable();
            $table->decimal('energy_per_employee', 12, 4)->nullable();
            $table->decimal('energy_per_unit', 12, 4)->nullable();

            // Associated emissions
            $table->decimal('co2e_tonnes', 15, 4)->nullable();
            $table->decimal('co2e_per_kwh', 10, 6)->nullable();

            // Documentation
            $table->text('justification')->nullable();
            $table->text('methodology')->nullable();
            $table->json('data_sources')->nullable();

            // Revision tracking
            $table->uuid('replaces_baseline_id')->nullable();
            $table->text('revision_reason')->nullable();
            $table->enum('revision_trigger', [
                'initial',
                'structural_change',
                'methodology_change',
                'data_correction',
                'new_seu',
                'regulatory_requirement',
            ])->default('initial');

            // Approval
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();

            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['organization_id', 'is_current']);
            $table->index(['organization_id', 'baseline_year']);
        });

        // 4. Energy Targets (ISO 50001 Section 6.6)
        Schema::create('energy_targets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('site_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignUuid('energy_baseline_id')->nullable()->constrained('energy_baselines')->nullOnDelete();

            // Target definition
            $table->string('name');
            $table->string('name_de')->nullable();
            $table->text('description')->nullable();
            $table->enum('target_type', [
                'absolute_reduction',     // Reduce kWh by X
                'intensity_reduction',    // Reduce kWh/m² by X%
                'renewable_share',        // Achieve X% renewable
                'efficiency_improvement', // Improve efficiency by X%
                'cost_reduction',         // Reduce energy cost by X
                'carbon_reduction',       // Reduce CO2e by X
            ]);

            // Timeline
            $table->year('baseline_year');
            $table->year('target_year');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();

            // Values
            $table->decimal('baseline_value', 20, 4);
            $table->decimal('target_value', 20, 4);
            $table->decimal('current_value', 20, 4)->nullable();
            $table->string('unit', 50);
            $table->decimal('target_reduction_percent', 8, 4)->nullable();

            // Progress tracking
            $table->decimal('progress_percent', 8, 4)->nullable();
            $table->decimal('annual_target', 20, 4)->nullable();
            $table->json('milestones')->nullable();
            $table->json('progress_history')->nullable();

            // Status
            $table->enum('status', ['planned', 'active', 'on_track', 'at_risk', 'achieved', 'missed', 'cancelled'])->default('planned');
            $table->boolean('is_sbti_aligned')->default(false);

            // Associated actions
            $table->json('action_plan')->nullable();
            $table->decimal('investment_required', 15, 2)->nullable();
            $table->decimal('expected_savings_annual', 15, 2)->nullable();
            $table->decimal('payback_years', 5, 2)->nullable();

            // Responsibility
            $table->string('responsible_person')->nullable();
            $table->string('responsible_department')->nullable();

            // Review
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->date('last_review_date')->nullable();
            $table->date('next_review_date')->nullable();

            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['organization_id', 'status']);
            $table->index(['organization_id', 'target_year']);
        });

        // 5. Energy Audits (ISO 50001 Section 9.2)
        Schema::create('energy_audits', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('site_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignUuid('energy_review_id')->nullable()->constrained('energy_reviews')->nullOnDelete();

            // Audit definition
            $table->enum('audit_type', ['internal', 'external', 'certification', 'surveillance']);
            $table->string('audit_standard')->default('ISO 50001:2018');
            $table->year('audit_year');
            $table->date('audit_date');
            $table->date('audit_end_date')->nullable();

            // Auditor details
            $table->string('auditor_name')->nullable();
            $table->string('auditor_organization')->nullable();
            $table->string('auditor_certification')->nullable();
            $table->string('lead_auditor')->nullable();
            $table->json('audit_team')->nullable();

            // Scope
            $table->text('scope_description')->nullable();
            $table->json('areas_audited')->nullable();
            $table->json('processes_audited')->nullable();
            $table->json('seus_audited')->nullable();

            // Findings
            $table->enum('overall_result', ['conforming', 'minor_nc', 'major_nc', 'critical'])->nullable();
            $table->integer('nonconformities_major')->default(0);
            $table->integer('nonconformities_minor')->default(0);
            $table->integer('observations')->default(0);
            $table->integer('opportunities_improvement')->default(0);
            $table->json('findings_detail')->nullable();

            // Checklist results per ISO 50001 clause
            $table->json('clause_results')->nullable(); // Results by ISO 50001 clause

            // Corrective actions
            $table->json('corrective_actions')->nullable();
            $table->date('corrective_actions_due')->nullable();
            $table->boolean('corrective_actions_closed')->default(false);

            // EnMS effectiveness
            $table->boolean('policy_reviewed')->default(false);
            $table->boolean('objectives_reviewed')->default(false);
            $table->boolean('enpis_reviewed')->default(false);
            $table->boolean('seus_reviewed')->default(false);
            $table->boolean('legal_compliance_reviewed')->default(false);
            $table->decimal('enms_effectiveness_score', 5, 2)->nullable();

            // Documentation
            $table->string('report_path')->nullable();
            $table->text('executive_summary')->nullable();
            $table->text('recommendations')->nullable();
            $table->json('evidence_documents')->nullable();

            // Next audit
            $table->date('next_audit_date')->nullable();

            // Approval
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();

            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['organization_id', 'audit_year']);
            $table->index(['organization_id', 'audit_type']);
        });

        // 6. Add ISO 50001 fields to organizations table
        Schema::table('organizations', function (Blueprint $table) {
            // Energy Policy (Section 5.2)
            if (!Schema::hasColumn('organizations', 'energy_policy')) {
                $table->text('energy_policy')->nullable()->after('verification_level');
            }
            if (!Schema::hasColumn('organizations', 'energy_policy_date')) {
                $table->date('energy_policy_date')->nullable()->after('energy_policy');
            }

            // EnMS scope
            if (!Schema::hasColumn('organizations', 'enms_scope')) {
                $table->text('enms_scope')->nullable()->after('energy_policy_date');
            }
            if (!Schema::hasColumn('organizations', 'enms_boundaries')) {
                $table->json('enms_boundaries')->nullable()->after('enms_scope');
            }

            // Certification status
            if (!Schema::hasColumn('organizations', 'iso50001_certified')) {
                $table->boolean('iso50001_certified')->default(false)->after('enms_boundaries');
            }
            if (!Schema::hasColumn('organizations', 'iso50001_cert_date')) {
                $table->date('iso50001_cert_date')->nullable()->after('iso50001_certified');
            }
            if (!Schema::hasColumn('organizations', 'iso50001_cert_expiry')) {
                $table->date('iso50001_cert_expiry')->nullable()->after('iso50001_cert_date');
            }
            if (!Schema::hasColumn('organizations', 'iso50001_registrar')) {
                $table->string('iso50001_registrar')->nullable()->after('iso50001_cert_expiry');
            }

            // Energy management representative
            if (!Schema::hasColumn('organizations', 'energy_manager_name')) {
                $table->string('energy_manager_name')->nullable()->after('iso50001_registrar');
            }
            if (!Schema::hasColumn('organizations', 'energy_manager_email')) {
                $table->string('energy_manager_email')->nullable()->after('energy_manager_name');
            }
        });

        // 7. Add energy fields to sites table
        Schema::table('sites', function (Blueprint $table) {
            if (!Schema::hasColumn('sites', 'is_significant_energy_user')) {
                $table->boolean('is_significant_energy_user')->default(false)->after('is_trackzero_member');
            }
            if (!Schema::hasColumn('sites', 'annual_energy_kwh')) {
                $table->decimal('annual_energy_kwh', 15, 2)->nullable()->after('is_significant_energy_user');
            }
            if (!Schema::hasColumn('sites', 'energy_class')) {
                $table->string('energy_class', 10)->nullable()->after('annual_energy_kwh'); // A, B, C, D, E, F, G
            }
            if (!Schema::hasColumn('sites', 'heating_type')) {
                $table->string('heating_type', 50)->nullable()->after('energy_class');
            }
            if (!Schema::hasColumn('sites', 'cooling_type')) {
                $table->string('cooling_type', 50)->nullable()->after('heating_type');
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('energy_audits');
        Schema::dropIfExists('energy_targets');
        Schema::dropIfExists('energy_baselines');
        Schema::dropIfExists('energy_performance_indicators');
        Schema::dropIfExists('energy_reviews');

        Schema::table('organizations', function (Blueprint $table) {
            $columns = [
                'energy_policy', 'energy_policy_date', 'enms_scope', 'enms_boundaries',
                'iso50001_certified', 'iso50001_cert_date', 'iso50001_cert_expiry', 'iso50001_registrar',
                'energy_manager_name', 'energy_manager_email',
            ];
            foreach ($columns as $column) {
                if (Schema::hasColumn('organizations', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::table('sites', function (Blueprint $table) {
            $columns = ['is_significant_energy_user', 'annual_energy_kwh', 'energy_class', 'heating_type', 'cooling_type'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('sites', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
