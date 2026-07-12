<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * EU Compliance Tables for CSRD 2025, ESRS, and German Regulations
 */
return new class extends Migration
{
    public function up(): void
    {
        // ESRS E1 Climate Indicators
        Schema::create('esrs_indicators', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('assessment_id')->nullable()->constrained()->nullOnDelete();
            $table->year('year');

            // Indicator identification
            $table->string('indicator_code', 20); // E1-1, E1-5-a, etc.
            $table->string('indicator_name');
            $table->string('indicator_name_de')->nullable();
            $table->string('indicator_name_en')->nullable();
            $table->string('category', 50); // strategy, governance, actions, targets, metrics, risks

            // Values
            $table->decimal('value', 20, 4)->nullable();
            $table->string('unit', 50)->nullable();
            $table->enum('data_quality', ['measured', 'calculated', 'estimated', 'supplier'])->default('calculated');
            $table->string('methodology')->nullable();
            $table->json('calculation_details')->nullable();
            $table->json('source_data')->nullable();

            // Compliance
            $table->boolean('is_mandatory')->default(true);
            $table->boolean('is_verified')->default(false);
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Unique constraint per organization/assessment/indicator
            $table->unique(['organization_id', 'assessment_id', 'indicator_code'], 'esrs_org_assessment_indicator_unique');
            $table->index(['organization_id', 'year']);
            $table->index('indicator_code');
        });

        // Double Materiality Assessment for CSRD
        Schema::create('materiality_assessments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('organization_id')->constrained()->cascadeOnDelete();
            $table->year('year');

            // Topic identification
            $table->string('topic_code', 50); // E1, S1, G1, etc.
            $table->string('topic_name');
            $table->string('topic_name_de')->nullable();
            $table->enum('esrs_category', ['environment', 'social', 'governance']);

            // Impact materiality (outside-in)
            $table->integer('impact_severity')->nullable(); // 1-5 scale
            $table->integer('impact_likelihood')->nullable(); // 1-5 scale
            $table->decimal('impact_score', 5, 2)->nullable();
            $table->text('impact_description')->nullable();

            // Financial materiality (inside-out)
            $table->integer('financial_magnitude')->nullable(); // 1-5 scale
            $table->integer('financial_likelihood')->nullable(); // 1-5 scale
            $table->decimal('financial_score', 5, 2)->nullable();
            $table->text('financial_description')->nullable();

            // Combined assessment
            $table->boolean('is_material')->default(false);
            $table->enum('materiality_type', ['impact', 'financial', 'double', 'not_material'])->nullable();
            $table->decimal('combined_score', 5, 2)->nullable();

            // Evidence
            $table->json('stakeholder_input')->nullable();
            $table->json('evidence_documents')->nullable();
            $table->text('justification')->nullable();

            // Review
            $table->foreignId('assessed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('assessed_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->unique(['organization_id', 'year', 'topic_code']);
            $table->index(['organization_id', 'year']);
            $table->index('is_material');
        });

        // PSD2 Audit Log for German BaFin Compliance
        Schema::create('psd2_audit_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignUuid('bank_connection_id')->nullable()->constrained()->nullOnDelete();

            // Event details
            $table->string('event_type', 50); // consent_granted, data_access, token_refresh, sca_challenge
            $table->string('event_subtype', 50)->nullable();
            $table->enum('status', ['success', 'failure', 'pending', 'cancelled']);

            // Request details
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->string('request_id')->nullable();

            // PSD2 specific
            $table->string('aspsp_id')->nullable(); // Bank ID
            $table->string('consent_id')->nullable();
            $table->boolean('sca_required')->default(false);
            $table->string('sca_method')->nullable(); // redirect, decoupled, embedded

            // Data accessed
            $table->json('data_categories')->nullable(); // accounts, transactions, balances
            $table->dateTime('data_from')->nullable();
            $table->dateTime('data_to')->nullable();

            // Security
            $table->text('error_message')->nullable();
            $table->json('metadata')->nullable();

            $table->timestamp('created_at');

            $table->index(['organization_id', 'created_at']);
            $table->index(['bank_connection_id', 'event_type']);
            $table->index('consent_id');
        });

        // Add consent fields to users table for GDPR/BDSG
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'marketing_consent')) {
                $table->boolean('marketing_consent')->default(false)->after('notification_preferences');
            }
            if (!Schema::hasColumn('users', 'marketing_consent_at')) {
                $table->timestamp('marketing_consent_at')->nullable()->after('marketing_consent');
            }
            if (!Schema::hasColumn('users', 'analytics_consent')) {
                $table->boolean('analytics_consent')->default(false)->after('marketing_consent_at');
            }
            if (!Schema::hasColumn('users', 'analytics_consent_at')) {
                $table->timestamp('analytics_consent_at')->nullable()->after('analytics_consent');
            }
            if (!Schema::hasColumn('users', 'ai_consent')) {
                $table->boolean('ai_consent')->default(true)->after('analytics_consent_at');
            }
            if (!Schema::hasColumn('users', 'ai_consent_at')) {
                $table->timestamp('ai_consent_at')->nullable()->after('ai_consent');
            }
            if (!Schema::hasColumn('users', 'terms_accepted_at')) {
                $table->timestamp('terms_accepted_at')->nullable()->after('ai_consent_at');
            }
            if (!Schema::hasColumn('users', 'privacy_accepted_at')) {
                $table->timestamp('privacy_accepted_at')->nullable()->after('terms_accepted_at');
            }
        });

        // Add renewable energy tracking to emission_records
        Schema::table('emission_records', function (Blueprint $table) {
            if (!Schema::hasColumn('emission_records', 'is_renewable')) {
                $table->boolean('is_renewable')->default(false)->after('is_verified');
            }
            if (!Schema::hasColumn('emission_records', 'calculation_method')) {
                $table->string('calculation_method', 50)->nullable()->after('is_renewable');
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('psd2_audit_logs');
        Schema::dropIfExists('materiality_assessments');
        Schema::dropIfExists('esrs_indicators');

        Schema::table('users', function (Blueprint $table) {
            $columns = [
                'marketing_consent', 'marketing_consent_at',
                'analytics_consent', 'analytics_consent_at',
                'ai_consent', 'ai_consent_at',
                'terms_accepted_at', 'privacy_accepted_at',
            ];
            foreach ($columns as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::table('emission_records', function (Blueprint $table) {
            if (Schema::hasColumn('emission_records', 'is_renewable')) {
                $table->dropColumn('is_renewable');
            }
            if (Schema::hasColumn('emission_records', 'calculation_method')) {
                $table->dropColumn('calculation_method');
            }
        });
    }
};
