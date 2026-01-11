<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\BelongsToOrganization;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * GHG Verification Model - ISO 14064-3 Verification & Validation
 *
 * Tracks the verification and assurance process for GHG inventories:
 * - Internal review procedures
 * - External verification/audit
 * - Assurance levels (reasonable/limited)
 * - Verification statements
 *
 * ISO 14064-1 Section 7: Verification requirements
 * ISO 14064-3: Specification for validation and verification
 */
class GhgVerification extends Model
{
    use HasFactory, HasUuids, SoftDeletes, BelongsToOrganization;

    protected $fillable = [
        'organization_id',
        'assessment_id',
        'report_id',
        'verification_type',
        'assurance_level',
        'status',
        'scope_description',
        'verification_standard',
        'verification_criteria',
        'materiality_threshold',
        'materiality_unit',
        'verifier_organization',
        'verifier_name',
        'verifier_accreditation',
        'verifier_contact',
        'verification_start_date',
        'verification_end_date',
        'statement_date',
        'opinion_type',
        'opinion_statement',
        'findings',
        'non_conformities',
        'corrective_actions',
        'recommendations',
        'scope_1_verified',
        'scope_2_verified',
        'scope_3_verified',
        'removals_verified',
        'base_year_verified',
        'methodology_verified',
        'data_quality_assessed',
        'uncertainty_assessed',
        'completeness_assessed',
        'consistency_assessed',
        'accuracy_assessed',
        'transparency_assessed',
        'relevance_assessed',
        'checklist_results',
        'evidence_documents',
        'verification_report_path',
        'statement_path',
        'next_verification_date',
        'internal_reviewer_id',
        'internal_review_date',
        'internal_review_notes',
        'approved_by',
        'approved_at',
        'metadata',
    ];

    protected $casts = [
        'materiality_threshold' => 'decimal:2',
        'verification_start_date' => 'date',
        'verification_end_date' => 'date',
        'statement_date' => 'date',
        'next_verification_date' => 'date',
        'internal_review_date' => 'date',
        'scope_1_verified' => 'boolean',
        'scope_2_verified' => 'boolean',
        'scope_3_verified' => 'boolean',
        'removals_verified' => 'boolean',
        'base_year_verified' => 'boolean',
        'methodology_verified' => 'boolean',
        'data_quality_assessed' => 'boolean',
        'uncertainty_assessed' => 'boolean',
        'completeness_assessed' => 'boolean',
        'consistency_assessed' => 'boolean',
        'accuracy_assessed' => 'boolean',
        'transparency_assessed' => 'boolean',
        'relevance_assessed' => 'boolean',
        'findings' => 'array',
        'non_conformities' => 'array',
        'corrective_actions' => 'array',
        'recommendations' => 'array',
        'checklist_results' => 'array',
        'evidence_documents' => 'array',
        'metadata' => 'array',
        'approved_at' => 'datetime',
    ];

    /**
     * Verification types
     */
    public const VERIFICATION_TYPES = [
        'internal' => [
            'name' => 'Internal Review',
            'name_de' => 'Interne Überprüfung',
            'description' => 'Internal QA/QC review process',
        ],
        'external_limited' => [
            'name' => 'External Limited Assurance',
            'name_de' => 'Externe begrenzte Prüfungssicherheit',
            'description' => 'Third-party limited assurance engagement',
        ],
        'external_reasonable' => [
            'name' => 'External Reasonable Assurance',
            'name_de' => 'Externe hinreichende Prüfungssicherheit',
            'description' => 'Third-party reasonable assurance engagement',
        ],
        'certification' => [
            'name' => 'Certification Audit',
            'name_de' => 'Zertifizierungsaudit',
            'description' => 'Full certification audit (e.g., ISO 14064-1)',
        ],
    ];

    /**
     * Assurance levels per ISAE 3410
     */
    public const ASSURANCE_LEVELS = [
        'none' => 'No Assurance',
        'limited' => 'Limited Assurance',
        'reasonable' => 'Reasonable Assurance',
    ];

    /**
     * Verification statuses
     */
    public const STATUSES = [
        'planned' => 'Planned',
        'in_progress' => 'In Progress',
        'pending_review' => 'Pending Review',
        'verified' => 'Verified',
        'verified_with_comments' => 'Verified with Comments',
        'not_verified' => 'Not Verified',
        'cancelled' => 'Cancelled',
    ];

    /**
     * Opinion types
     */
    public const OPINION_TYPES = [
        'unqualified' => 'Unqualified (Clean)',
        'qualified' => 'Qualified',
        'adverse' => 'Adverse',
        'disclaimer' => 'Disclaimer of Opinion',
    ];

    /**
     * Verification standards
     */
    public const VERIFICATION_STANDARDS = [
        'iso_14064_3' => 'ISO 14064-3:2019',
        'isae_3410' => 'ISAE 3410',
        'isae_3000' => 'ISAE 3000 (Revised)',
        'aa1000as' => 'AA1000AS v3',
        'internal' => 'Internal QA/QC Procedures',
    ];

    /**
     * ISO 14064-1 verification checklist items
     */
    public const CHECKLIST_ITEMS = [
        'organizational_boundary' => 'Organizational boundary clearly defined',
        'consolidation_approach' => 'Consolidation approach documented',
        'operational_boundary' => 'Operational boundary documented',
        'scope_classification' => 'Emissions correctly classified by scope',
        'emission_sources' => 'All material emission sources identified',
        'removal_sinks' => 'GHG removals/sinks identified and quantified',
        'base_year' => 'Base year established and documented',
        'recalculation_policy' => 'Recalculation policy documented',
        'emission_factors' => 'Emission factors appropriate and referenced',
        'gwp_values' => 'GWP values from recognized source (IPCC)',
        'calculation_methodology' => 'Calculation methodology documented',
        'data_quality' => 'Data quality assessment performed',
        'uncertainty_assessment' => 'Uncertainty assessment conducted',
        'completeness' => 'Inventory is complete (no material omissions)',
        'consistency' => 'Consistent methodology year-over-year',
        'accuracy' => 'Calculations accurate and verifiable',
        'transparency' => 'Assumptions and limitations disclosed',
        'relevance' => 'Information relevant for decision-making',
    ];

    /**
     * Relationships
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class);
    }

    public function internalReviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'internal_reviewer_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Scopes
     */
    public function scopeForAssessment($query, string $assessmentId)
    {
        return $query->where('assessment_id', $assessmentId);
    }

    public function scopeVerified($query)
    {
        return $query->whereIn('status', ['verified', 'verified_with_comments']);
    }

    public function scopeExternal($query)
    {
        return $query->whereIn('verification_type', ['external_limited', 'external_reasonable', 'certification']);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('verification_type', $type);
    }

    /**
     * Accessors
     */
    public function getIsVerifiedAttribute(): bool
    {
        return in_array($this->status, ['verified', 'verified_with_comments']);
    }

    public function getIsExternalAttribute(): bool
    {
        return in_array($this->verification_type, ['external_limited', 'external_reasonable', 'certification']);
    }

    public function getVerificationTypeLabelAttribute(): string
    {
        return self::VERIFICATION_TYPES[$this->verification_type]['name'] ?? $this->verification_type;
    }

    public function getAssuranceLevelLabelAttribute(): string
    {
        return self::ASSURANCE_LEVELS[$this->assurance_level] ?? $this->assurance_level;
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    /**
     * Calculate checklist completion percentage
     */
    public function getChecklistCompletionAttribute(): float
    {
        if (empty($this->checklist_results)) {
            return 0;
        }

        $totalItems = count(self::CHECKLIST_ITEMS);
        $completedItems = collect($this->checklist_results)
            ->filter(fn ($result) => $result['status'] === 'passed' || $result['status'] === 'passed_with_comments')
            ->count();

        return round(($completedItems / $totalItems) * 100, 1);
    }

    /**
     * Get verification quality score (ISO 14064-1 principles)
     */
    public function getQualityScoreAttribute(): array
    {
        $principles = [
            'completeness' => $this->completeness_assessed,
            'consistency' => $this->consistency_assessed,
            'accuracy' => $this->accuracy_assessed,
            'transparency' => $this->transparency_assessed,
            'relevance' => $this->relevance_assessed,
        ];

        $assessed = collect($principles)->filter()->count();
        $total = count($principles);

        return [
            'score' => $total > 0 ? round(($assessed / $total) * 100) : 0,
            'principles' => $principles,
        ];
    }

    /**
     * Check if verification is due for renewal
     */
    public function isDueForRenewal(): bool
    {
        if (!$this->next_verification_date) {
            return false;
        }

        return $this->next_verification_date <= now()->addDays(90);
    }

    /**
     * Initialize checklist with default items
     */
    public function initializeChecklist(): void
    {
        $checklist = [];
        foreach (self::CHECKLIST_ITEMS as $key => $description) {
            $checklist[$key] = [
                'description' => $description,
                'status' => 'not_assessed',
                'notes' => null,
                'evidence' => null,
            ];
        }
        $this->checklist_results = $checklist;
    }

    /**
     * Update checklist item
     */
    public function updateChecklistItem(string $item, string $status, ?string $notes = null, ?string $evidence = null): void
    {
        $checklist = $this->checklist_results ?? [];
        $checklist[$item] = [
            'description' => self::CHECKLIST_ITEMS[$item] ?? $item,
            'status' => $status, // passed, passed_with_comments, failed, not_applicable, not_assessed
            'notes' => $notes,
            'evidence' => $evidence,
            'assessed_at' => now()->toIso8601String(),
        ];
        $this->checklist_results = $checklist;
    }
}
