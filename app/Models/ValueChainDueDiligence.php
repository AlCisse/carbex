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
 * Value Chain Due Diligence Model
 *
 * Implements:
 * - LkSG (Lieferkettensorgfaltspflichtengesetz) - German Supply Chain Due Diligence Act
 * - CSDDD (Corporate Sustainability Due Diligence Directive) - EU Directive
 *
 * Requirements:
 * - Risk analysis of own operations and suppliers
 * - Prevention and remediation measures
 * - Grievance mechanism
 * - Documentation and reporting
 */
class ValueChainDueDiligence extends Model
{
    use HasFactory, HasUuids, SoftDeletes, BelongsToOrganization;

    protected $table = 'value_chain_due_diligence';

    protected $fillable = [
        'organization_id',
        'assessment_year',
        'lksg_applicable',
        'lksg_status',
        'has_human_rights_policy',
        'human_rights_policy_date',
        'has_environmental_policy',
        'environmental_policy_date',
        'identified_risks',
        'risk_prioritization',
        'high_risk_countries',
        'high_risk_sectors',
        'prevention_measures',
        'contractual_assurances',
        'supplier_code_of_conduct',
        'monitoring_mechanisms',
        'supplier_audits_conducted',
        'suppliers_assessed',
        'grievance_mechanism',
        'has_whistleblower_channel',
        'complaints_received',
        'complaints_resolved',
        'annual_report_published',
        'report_url',
        'responsible_person_id',
        'reviewed_by',
        'reviewed_at',
        'metadata',
    ];

    protected $casts = [
        'lksg_applicable' => 'boolean',
        'has_human_rights_policy' => 'boolean',
        'human_rights_policy_date' => 'date',
        'has_environmental_policy' => 'boolean',
        'environmental_policy_date' => 'date',
        'identified_risks' => 'array',
        'risk_prioritization' => 'array',
        'high_risk_countries' => 'array',
        'high_risk_sectors' => 'array',
        'prevention_measures' => 'array',
        'contractual_assurances' => 'array',
        'supplier_code_of_conduct' => 'boolean',
        'monitoring_mechanisms' => 'array',
        'grievance_mechanism' => 'array',
        'has_whistleblower_channel' => 'boolean',
        'annual_report_published' => 'boolean',
        'reviewed_at' => 'datetime',
        'metadata' => 'array',
    ];

    /**
     * LkSG Status options
     */
    public const LKSG_STATUSES = [
        'not_started' => 'Not Started',
        'in_progress' => 'In Progress',
        'compliant' => 'Compliant',
        'non_compliant' => 'Non-Compliant',
    ];

    /**
     * Risk categories per LkSG
     */
    public const RISK_CATEGORIES = [
        'human_rights' => [
            'child_labor' => 'Child labor (ILO C138, C182)',
            'forced_labor' => 'Forced labor (ILO C29, C105)',
            'slavery' => 'Slavery and human trafficking',
            'freedom_association' => 'Freedom of association (ILO C87)',
            'discrimination' => 'Discrimination (ILO C100, C111)',
            'fair_wages' => 'Fair wages (ILO C131)',
            'working_hours' => 'Working hours (ILO C1)',
            'occupational_safety' => 'Occupational safety (ILO C155)',
            'land_rights' => 'Land and property rights',
            'indigenous_rights' => 'Indigenous peoples rights',
            'private_security' => 'Private security forces conduct',
        ],
        'environmental' => [
            'mercury' => 'Mercury (Minamata Convention)',
            'pops' => 'Persistent Organic Pollutants (Stockholm Convention)',
            'hazardous_waste' => 'Hazardous waste (Basel Convention)',
            'biodiversity' => 'Biodiversity destruction',
            'water_pollution' => 'Water pollution',
            'air_pollution' => 'Air pollution',
            'deforestation' => 'Deforestation',
        ],
    ];

    /**
     * High-risk countries (example list based on common indices)
     */
    public const HIGH_RISK_COUNTRIES_DEFAULT = [
        'MM' => 'Myanmar',
        'CN' => 'China',
        'BD' => 'Bangladesh',
        'PK' => 'Pakistan',
        'TH' => 'Thailand',
        'VN' => 'Vietnam',
        'IN' => 'India',
        'ID' => 'Indonesia',
        'TR' => 'Turkey',
        'BR' => 'Brazil',
    ];

    /**
     * High-risk sectors
     */
    public const HIGH_RISK_SECTORS = [
        'textiles' => 'Textiles and apparel',
        'electronics' => 'Electronics and ICT',
        'agriculture' => 'Agriculture and food',
        'mining' => 'Mining and minerals',
        'construction' => 'Construction',
        'automotive' => 'Automotive',
        'chemicals' => 'Chemicals',
        'leather' => 'Leather goods',
        'palm_oil' => 'Palm oil',
        'cocoa' => 'Cocoa and coffee',
    ];

    /**
     * Relationships
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function responsiblePerson(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsible_person_id');
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
        return $query->where('assessment_year', $year);
    }

    public function scopeLksgApplicable($query)
    {
        return $query->where('lksg_applicable', true);
    }

    public function scopeCompliant($query)
    {
        return $query->where('lksg_status', 'compliant');
    }

    /**
     * Accessors
     */
    public function getStatusLabelAttribute(): string
    {
        return self::LKSG_STATUSES[$this->lksg_status] ?? $this->lksg_status ?? 'Unknown';
    }

    public function getIsCompliantAttribute(): bool
    {
        return $this->lksg_status === 'compliant';
    }

    /**
     * Calculate compliance score
     */
    public function getComplianceScoreAttribute(): float
    {
        $checks = [
            $this->has_human_rights_policy,
            $this->has_environmental_policy,
            !empty($this->identified_risks),
            !empty($this->prevention_measures),
            $this->supplier_code_of_conduct,
            !empty($this->monitoring_mechanisms),
            !empty($this->grievance_mechanism),
            $this->has_whistleblower_channel,
            $this->annual_report_published,
        ];

        return round(count(array_filter($checks)) / count($checks) * 100, 1);
    }

    /**
     * Get risk count by category
     */
    public function getRiskCountByCategoryAttribute(): array
    {
        $risks = $this->identified_risks ?? [];
        $counts = [
            'human_rights' => 0,
            'environmental' => 0,
            'total' => count($risks),
        ];

        foreach ($risks as $risk) {
            $category = $risk['category'] ?? 'unknown';
            if (isset($counts[$category])) {
                $counts[$category]++;
            }
        }

        return $counts;
    }

    /**
     * Get high priority risks
     */
    public function getHighPriorityRisksAttribute(): array
    {
        $risks = $this->identified_risks ?? [];

        return array_filter($risks, function ($risk) {
            return ($risk['priority'] ?? 'low') === 'high';
        });
    }

    /**
     * Calculate complaint resolution rate
     */
    public function getComplaintResolutionRateAttribute(): ?float
    {
        if ($this->complaints_received === 0) {
            return null;
        }

        return round(($this->complaints_resolved / $this->complaints_received) * 100, 1);
    }

    /**
     * Check if LkSG reporting is required
     * LkSG applies to companies with:
     * - >= 3000 employees (from 2023)
     * - >= 1000 employees (from 2024)
     */
    public function isLksgRequired(int $employeeCount, int $year = null): bool
    {
        $year = $year ?? now()->year;

        if ($year >= 2024) {
            return $employeeCount >= 1000;
        }

        if ($year >= 2023) {
            return $employeeCount >= 3000;
        }

        return false;
    }

    /**
     * Generate compliance checklist
     */
    public function getComplianceChecklistAttribute(): array
    {
        return [
            'policy' => [
                'human_rights_policy' => [
                    'status' => $this->has_human_rights_policy,
                    'label' => 'Human rights policy statement',
                    'required' => true,
                ],
                'environmental_policy' => [
                    'status' => $this->has_environmental_policy,
                    'label' => 'Environmental policy statement',
                    'required' => true,
                ],
            ],
            'risk_management' => [
                'risk_analysis' => [
                    'status' => !empty($this->identified_risks),
                    'label' => 'Risk analysis conducted',
                    'required' => true,
                ],
                'risk_prioritization' => [
                    'status' => !empty($this->risk_prioritization),
                    'label' => 'Risks prioritized',
                    'required' => true,
                ],
            ],
            'prevention' => [
                'prevention_measures' => [
                    'status' => !empty($this->prevention_measures),
                    'label' => 'Prevention measures defined',
                    'required' => true,
                ],
                'supplier_code' => [
                    'status' => $this->supplier_code_of_conduct,
                    'label' => 'Supplier code of conduct',
                    'required' => true,
                ],
                'contractual_assurances' => [
                    'status' => !empty($this->contractual_assurances),
                    'label' => 'Contractual assurances with suppliers',
                    'required' => true,
                ],
            ],
            'monitoring' => [
                'monitoring_mechanisms' => [
                    'status' => !empty($this->monitoring_mechanisms),
                    'label' => 'Monitoring mechanisms established',
                    'required' => true,
                ],
                'supplier_audits' => [
                    'status' => $this->supplier_audits_conducted > 0,
                    'label' => 'Supplier audits conducted',
                    'required' => false,
                ],
            ],
            'grievance' => [
                'grievance_mechanism' => [
                    'status' => !empty($this->grievance_mechanism),
                    'label' => 'Grievance mechanism established',
                    'required' => true,
                ],
                'whistleblower_channel' => [
                    'status' => $this->has_whistleblower_channel,
                    'label' => 'Whistleblower channel available',
                    'required' => true,
                ],
            ],
            'reporting' => [
                'annual_report' => [
                    'status' => $this->annual_report_published,
                    'label' => 'Annual report published',
                    'required' => true,
                ],
            ],
        ];
    }
}
