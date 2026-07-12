<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * CSRD Full Compliance Tables
 *
 * Corporate Sustainability Reporting Directive (EU) 2022/2464
 * ESRS Set 1 (2023)
 *
 * Implements:
 * - ESRS 2 General Disclosures tracking
 * - Climate Transition Plan documentation
 * - EU Taxonomy Article 8 reporting
 * - Value Chain Due Diligence (LkSG/CSDDD)
 */
return new class extends Migration
{
    public function up(): void
    {
        // 1. ESRS 2 General Disclosures
        Schema::create('esrs2_disclosures', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('organization_id')->constrained()->cascadeOnDelete();
            $table->year('reporting_year');

            // Disclosure identification
            $table->string('disclosure_code', 20); // GOV-1, SBM-1, etc.
            $table->string('disclosure_name');
            $table->string('disclosure_name_de')->nullable();
            $table->enum('category', ['bp', 'gov', 'sbm', 'iro']); // Basis, Governance, Strategy, IRO

            // Status
            $table->enum('status', ['not_started', 'in_progress', 'draft', 'completed', 'verified'])->default('not_started');
            $table->decimal('completion_percent', 5, 2)->default(0);

            // Content
            $table->text('narrative_disclosure')->nullable();
            $table->text('narrative_disclosure_de')->nullable();
            $table->json('data_points')->nullable();
            $table->json('quantitative_data')->nullable();

            // Evidence
            $table->json('supporting_documents')->nullable();
            $table->json('cross_references')->nullable(); // Links to other disclosures

            // Review
            $table->foreignId('prepared_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();

            $table->text('review_notes')->nullable();
            $table->json('metadata')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->unique(['organization_id', 'reporting_year', 'disclosure_code'], 'esrs2_org_year_code_unique');
            $table->index(['organization_id', 'status']);
        });

        // 2. Climate Transition Plans (ESRS E1-1)
        Schema::create('climate_transition_plans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('organization_id')->constrained()->cascadeOnDelete();
            $table->year('plan_year');
            $table->enum('status', ['draft', 'approved', 'published', 'under_review'])->default('draft');

            // Paris alignment
            $table->enum('temperature_target', ['1.5C', 'well_below_2C', '2C'])->default('1.5C');
            $table->boolean('is_paris_aligned')->default(false);
            $table->boolean('is_sbti_committed')->default(false);
            $table->boolean('is_sbti_validated')->default(false);
            $table->date('sbti_commitment_date')->nullable();
            $table->date('sbti_validation_date')->nullable();

            // Base year
            $table->year('base_year');
            $table->decimal('base_year_emissions_scope1', 15, 4)->nullable();
            $table->decimal('base_year_emissions_scope2', 15, 4)->nullable();
            $table->decimal('base_year_emissions_scope3', 15, 4)->nullable();
            $table->decimal('base_year_emissions_total', 15, 4)->nullable();

            // Targets
            $table->json('interim_targets')->nullable(); // [{year: 2030, reduction_percent: 42, scope: 'all'}]
            $table->year('net_zero_target_year')->nullable();
            $table->decimal('net_zero_residual_emissions_percent', 5, 2)->nullable(); // Max 10% for SBTi

            // Decarbonization levers
            $table->json('decarbonization_levers')->nullable();
            $table->decimal('planned_capex_climate', 15, 2)->nullable();
            $table->decimal('planned_opex_climate', 15, 2)->nullable();

            // Carbon pricing assumption
            $table->decimal('internal_carbon_price', 10, 2)->nullable();
            $table->string('carbon_price_currency', 3)->default('EUR');

            // Locked-in emissions
            $table->decimal('locked_in_emissions_tco2e', 15, 4)->nullable();
            $table->text('locked_in_emissions_description')->nullable();

            // Carbon credits/offsets policy
            $table->boolean('uses_carbon_credits')->default(false);
            $table->text('carbon_credits_policy')->nullable();
            $table->decimal('carbon_credits_max_percent', 5, 2)->nullable();

            // Governance
            $table->text('board_oversight_description')->nullable();
            $table->text('management_accountability')->nullable();
            $table->boolean('linked_to_remuneration')->default(false);
            $table->text('remuneration_description')->nullable();

            // Risks and opportunities
            $table->json('transition_risks')->nullable();
            $table->json('physical_risks')->nullable();
            $table->json('climate_opportunities')->nullable();

            // Financial impacts
            $table->decimal('estimated_transition_cost', 15, 2)->nullable();
            $table->decimal('estimated_stranded_assets', 15, 2)->nullable();

            // Review and approval
            $table->foreignId('prepared_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->date('next_review_date')->nullable();

            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['organization_id', 'plan_year']);
            $table->index(['organization_id', 'status']);
        });

        // 3. EU Taxonomy Reporting (Article 8)
        Schema::create('eu_taxonomy_reports', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('organization_id')->constrained()->cascadeOnDelete();
            $table->year('reporting_year');

            // KPIs
            $table->decimal('turnover_total', 20, 2)->nullable();
            $table->decimal('turnover_eligible', 20, 2)->nullable();
            $table->decimal('turnover_aligned', 20, 2)->nullable();
            $table->decimal('turnover_eligible_percent', 8, 4)->nullable();
            $table->decimal('turnover_aligned_percent', 8, 4)->nullable();

            $table->decimal('capex_total', 20, 2)->nullable();
            $table->decimal('capex_eligible', 20, 2)->nullable();
            $table->decimal('capex_aligned', 20, 2)->nullable();
            $table->decimal('capex_eligible_percent', 8, 4)->nullable();
            $table->decimal('capex_aligned_percent', 8, 4)->nullable();

            $table->decimal('opex_total', 20, 2)->nullable();
            $table->decimal('opex_eligible', 20, 2)->nullable();
            $table->decimal('opex_aligned', 20, 2)->nullable();
            $table->decimal('opex_eligible_percent', 8, 4)->nullable();
            $table->decimal('opex_aligned_percent', 8, 4)->nullable();

            // Environmental objectives contribution
            $table->boolean('contributes_climate_mitigation')->default(false);
            $table->boolean('contributes_climate_adaptation')->default(false);
            $table->boolean('contributes_water')->default(false);
            $table->boolean('contributes_circular_economy')->default(false);
            $table->boolean('contributes_pollution')->default(false);
            $table->boolean('contributes_biodiversity')->default(false);

            // DNSH assessment
            $table->boolean('dnsh_climate_mitigation')->default(false);
            $table->boolean('dnsh_climate_adaptation')->default(false);
            $table->boolean('dnsh_water')->default(false);
            $table->boolean('dnsh_circular_economy')->default(false);
            $table->boolean('dnsh_pollution')->default(false);
            $table->boolean('dnsh_biodiversity')->default(false);

            // Minimum safeguards
            $table->boolean('oecd_guidelines_compliant')->default(false);
            $table->boolean('un_guiding_principles_compliant')->default(false);
            $table->boolean('ilo_conventions_compliant')->default(false);
            $table->boolean('human_rights_declaration_compliant')->default(false);

            // Activity breakdown
            $table->json('eligible_activities')->nullable();
            $table->json('aligned_activities')->nullable();

            // Methodology
            $table->text('methodology_description')->nullable();
            $table->json('data_sources')->nullable();

            // Review
            $table->foreignId('prepared_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();

            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['organization_id', 'reporting_year']);
        });

        // 4. Value Chain Due Diligence (LkSG/CSDDD)
        Schema::create('value_chain_due_diligence', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('organization_id')->constrained()->cascadeOnDelete();
            $table->year('assessment_year');

            // LkSG (German Supply Chain Act) compliance
            $table->boolean('lksg_applicable')->default(false);
            $table->enum('lksg_status', ['not_started', 'in_progress', 'compliant', 'non_compliant'])->nullable();

            // Policy
            $table->boolean('has_human_rights_policy')->default(false);
            $table->date('human_rights_policy_date')->nullable();
            $table->boolean('has_environmental_policy')->default(false);
            $table->date('environmental_policy_date')->nullable();

            // Risk assessment
            $table->json('identified_risks')->nullable();
            $table->json('risk_prioritization')->nullable();
            $table->json('high_risk_countries')->nullable();
            $table->json('high_risk_sectors')->nullable();

            // Prevention measures
            $table->json('prevention_measures')->nullable();
            $table->json('contractual_assurances')->nullable();
            $table->boolean('supplier_code_of_conduct')->default(false);

            // Monitoring
            $table->json('monitoring_mechanisms')->nullable();
            $table->integer('supplier_audits_conducted')->default(0);
            $table->integer('suppliers_assessed')->default(0);

            // Remediation
            $table->json('grievance_mechanism')->nullable();
            $table->boolean('has_whistleblower_channel')->default(false);
            $table->integer('complaints_received')->default(0);
            $table->integer('complaints_resolved')->default(0);

            // Reporting
            $table->boolean('annual_report_published')->default(false);
            $table->string('report_url')->nullable();

            // Review
            $table->foreignId('responsible_person_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();

            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['organization_id', 'assessment_year']);
        });

        // 5. Add CSRD-specific fields to organizations
        Schema::table('organizations', function (Blueprint $table) {
            // CSRD applicability
            if (!Schema::hasColumn('organizations', 'csrd_applicable')) {
                $table->boolean('csrd_applicable')->default(false)->after('iso50001_registrar');
            }
            if (!Schema::hasColumn('organizations', 'csrd_applicable_from')) {
                $table->year('csrd_applicable_from')->nullable()->after('csrd_applicable');
            }
            if (!Schema::hasColumn('organizations', 'csrd_company_category')) {
                $table->string('csrd_company_category', 50)->nullable()->after('csrd_applicable_from');
            }

            // Financial data for thresholds
            if (!Schema::hasColumn('organizations', 'balance_sheet_total')) {
                $table->decimal('balance_sheet_total', 20, 2)->nullable()->after('annual_turnover');
            }

            // Assurance
            if (!Schema::hasColumn('organizations', 'sustainability_auditor')) {
                $table->string('sustainability_auditor')->nullable()->after('csrd_company_category');
            }
            if (!Schema::hasColumn('organizations', 'sustainability_assurance_level')) {
                $table->enum('sustainability_assurance_level', ['none', 'limited', 'reasonable'])->default('none')->after('sustainability_auditor');
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('value_chain_due_diligence');
        Schema::dropIfExists('eu_taxonomy_reports');
        Schema::dropIfExists('climate_transition_plans');
        Schema::dropIfExists('esrs2_disclosures');

        Schema::table('organizations', function (Blueprint $table) {
            $columns = [
                'csrd_applicable', 'csrd_applicable_from', 'csrd_company_category',
                'balance_sheet_total', 'sustainability_auditor', 'sustainability_assurance_level',
            ];
            foreach ($columns as $column) {
                if (Schema::hasColumn('organizations', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
