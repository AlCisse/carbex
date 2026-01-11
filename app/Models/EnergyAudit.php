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
 * Energy Audit Model - ISO 50001:2018 Section 9.2
 *
 * Internal audits evaluate the EnMS to ensure it:
 * - Conforms to ISO 50001 requirements
 * - Conforms to the organization's requirements
 * - Achieves energy performance improvement
 * - Is effectively implemented and maintained
 */
class EnergyAudit extends Model
{
    use HasFactory, HasUuids, SoftDeletes, BelongsToOrganization;

    protected $fillable = [
        'organization_id',
        'site_id',
        'energy_review_id',
        'audit_type',
        'audit_standard',
        'audit_year',
        'audit_date',
        'audit_end_date',
        'auditor_name',
        'auditor_organization',
        'auditor_certification',
        'lead_auditor',
        'audit_team',
        'scope_description',
        'areas_audited',
        'processes_audited',
        'seus_audited',
        'overall_result',
        'nonconformities_major',
        'nonconformities_minor',
        'observations',
        'opportunities_improvement',
        'findings_detail',
        'clause_results',
        'corrective_actions',
        'corrective_actions_due',
        'corrective_actions_closed',
        'policy_reviewed',
        'objectives_reviewed',
        'enpis_reviewed',
        'seus_reviewed',
        'legal_compliance_reviewed',
        'enms_effectiveness_score',
        'report_path',
        'executive_summary',
        'recommendations',
        'evidence_documents',
        'next_audit_date',
        'reviewed_by',
        'reviewed_at',
        'metadata',
    ];

    protected $casts = [
        'audit_date' => 'date',
        'audit_end_date' => 'date',
        'audit_team' => 'array',
        'areas_audited' => 'array',
        'processes_audited' => 'array',
        'seus_audited' => 'array',
        'findings_detail' => 'array',
        'clause_results' => 'array',
        'corrective_actions' => 'array',
        'corrective_actions_due' => 'date',
        'corrective_actions_closed' => 'boolean',
        'policy_reviewed' => 'boolean',
        'objectives_reviewed' => 'boolean',
        'enpis_reviewed' => 'boolean',
        'seus_reviewed' => 'boolean',
        'legal_compliance_reviewed' => 'boolean',
        'enms_effectiveness_score' => 'decimal:2',
        'evidence_documents' => 'array',
        'next_audit_date' => 'date',
        'reviewed_at' => 'datetime',
        'metadata' => 'array',
    ];

    /**
     * Audit types
     */
    public const AUDIT_TYPES = [
        'internal' => [
            'name' => 'Internal Audit',
            'name_de' => 'Internes Audit',
            'description' => 'Conducted by organization or on its behalf',
        ],
        'external' => [
            'name' => 'External Audit',
            'name_de' => 'Externes Audit',
            'description' => 'Conducted by external party',
        ],
        'certification' => [
            'name' => 'Certification Audit',
            'name_de' => 'Zertifizierungsaudit',
            'description' => 'Initial certification audit',
        ],
        'surveillance' => [
            'name' => 'Surveillance Audit',
            'name_de' => 'Überwachungsaudit',
            'description' => 'Periodic surveillance audit',
        ],
    ];

    /**
     * Overall audit results
     */
    public const RESULTS = [
        'conforming' => [
            'name' => 'Conforming',
            'name_de' => 'Konform',
            'color' => 'green',
            'description' => 'No nonconformities found',
        ],
        'minor_nc' => [
            'name' => 'Minor Nonconformities',
            'name_de' => 'Kleinere Abweichungen',
            'color' => 'yellow',
            'description' => 'Minor issues identified',
        ],
        'major_nc' => [
            'name' => 'Major Nonconformities',
            'name_de' => 'Wesentliche Abweichungen',
            'color' => 'orange',
            'description' => 'Major issues requiring attention',
        ],
        'critical' => [
            'name' => 'Critical',
            'name_de' => 'Kritisch',
            'color' => 'red',
            'description' => 'Critical issues found',
        ],
    ];

    /**
     * ISO 50001:2018 clauses for audit checklist
     */
    public const ISO50001_CLAUSES = [
        '4.1' => 'Understanding the organization and its context',
        '4.2' => 'Understanding the needs and expectations of interested parties',
        '4.3' => 'Determining the scope of the EnMS',
        '4.4' => 'Energy management system',
        '5.1' => 'Leadership and commitment',
        '5.2' => 'Energy policy',
        '5.3' => 'Organizational roles, responsibilities and authorities',
        '6.1' => 'Actions to address risks and opportunities',
        '6.2' => 'Objectives, energy targets, and planning to achieve them',
        '6.3' => 'Energy review',
        '6.4' => 'Energy performance indicators',
        '6.5' => 'Energy baseline',
        '6.6' => 'Planning for collection of energy data',
        '7.1' => 'Resources',
        '7.2' => 'Competence',
        '7.3' => 'Awareness',
        '7.4' => 'Communication',
        '7.5' => 'Documented information',
        '8.1' => 'Operational planning and control',
        '8.2' => 'Design',
        '8.3' => 'Procurement',
        '9.1' => 'Monitoring, measurement, analysis and evaluation',
        '9.2' => 'Internal audit',
        '9.3' => 'Management review',
        '10.1' => 'Nonconformity and corrective action',
        '10.2' => 'Continual improvement',
    ];

    /**
     * Relationships
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    public function energyReview(): BelongsTo
    {
        return $this->belongsTo(EnergyReview::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Scopes
     */
    public function scopeForYear($query, int $year)
    {
        return $query->where('audit_year', $year);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('audit_type', $type);
    }

    public function scopeWithOpenActions($query)
    {
        return $query->where('corrective_actions_closed', false)
            ->whereNotNull('corrective_actions');
    }

    public function scopeConforming($query)
    {
        return $query->where('overall_result', 'conforming');
    }

    /**
     * Accessors
     */
    public function getAuditTypeLabelAttribute(): string
    {
        return self::AUDIT_TYPES[$this->audit_type]['name'] ?? $this->audit_type;
    }

    public function getResultLabelAttribute(): string
    {
        return self::RESULTS[$this->overall_result]['name'] ?? $this->overall_result ?? 'Pending';
    }

    public function getResultColorAttribute(): string
    {
        return self::RESULTS[$this->overall_result]['color'] ?? 'gray';
    }

    public function getTotalFindingsAttribute(): int
    {
        return $this->nonconformities_major
            + $this->nonconformities_minor
            + $this->observations
            + $this->opportunities_improvement;
    }

    public function getHasOpenActionsAttribute(): bool
    {
        return !$this->corrective_actions_closed && !empty($this->corrective_actions);
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->corrective_actions_due
            && $this->corrective_actions_due < now()
            && !$this->corrective_actions_closed;
    }

    /**
     * Calculate compliance score based on clause results
     */
    public function getComplianceScoreAttribute(): ?float
    {
        if (empty($this->clause_results)) {
            return null;
        }

        $totalClauses = count($this->clause_results);
        $conformingClauses = 0;

        foreach ($this->clause_results as $clause => $result) {
            if (in_array($result['status'] ?? '', ['conforming', 'observation'])) {
                $conformingClauses++;
            }
        }

        return $totalClauses > 0 ? round(($conformingClauses / $totalClauses) * 100, 1) : 0;
    }

    /**
     * Initialize clause results with default values
     */
    public function initializeClauseResults(): void
    {
        $results = [];
        foreach (self::ISO50001_CLAUSES as $clause => $description) {
            $results[$clause] = [
                'clause' => $clause,
                'description' => $description,
                'status' => 'not_assessed',
                'notes' => null,
                'evidence' => null,
            ];
        }
        $this->clause_results = $results;
    }

    /**
     * Update a clause result
     */
    public function updateClauseResult(
        string $clause,
        string $status,
        ?string $notes = null,
        ?string $evidence = null
    ): void {
        $results = $this->clause_results ?? [];

        $results[$clause] = [
            'clause' => $clause,
            'description' => self::ISO50001_CLAUSES[$clause] ?? $clause,
            'status' => $status, // conforming, observation, minor_nc, major_nc, not_applicable, not_assessed
            'notes' => $notes,
            'evidence' => $evidence,
            'assessed_at' => now()->toIso8601String(),
        ];

        $this->clause_results = $results;
    }

    /**
     * Add a finding
     */
    public function addFinding(
        string $type,
        string $clause,
        string $description,
        ?string $evidence = null
    ): void {
        $findings = $this->findings_detail ?? [];

        $findings[] = [
            'id' => count($findings) + 1,
            'type' => $type, // major_nc, minor_nc, observation, opportunity
            'clause' => $clause,
            'description' => $description,
            'evidence' => $evidence,
            'identified_at' => now()->toIso8601String(),
            'status' => 'open',
        ];

        $this->findings_detail = $findings;

        // Update counts
        match ($type) {
            'major_nc' => $this->nonconformities_major++,
            'minor_nc' => $this->nonconformities_minor++,
            'observation' => $this->observations++,
            'opportunity' => $this->opportunities_improvement++,
            default => null,
        };
    }

    /**
     * Determine overall result based on findings
     */
    public function determineOverallResult(): string
    {
        if ($this->nonconformities_major > 0) {
            $this->overall_result = 'major_nc';
        } elseif ($this->nonconformities_minor > 0) {
            $this->overall_result = 'minor_nc';
        } else {
            $this->overall_result = 'conforming';
        }

        return $this->overall_result;
    }
}
