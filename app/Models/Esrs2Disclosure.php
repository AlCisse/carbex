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
 * ESRS 2 General Disclosures Model
 *
 * European Sustainability Reporting Standards - Set 1 (2023)
 * ESRS 2 covers cross-cutting disclosures applicable to all undertakings.
 *
 * Categories:
 * - BP: Basis for preparation
 * - GOV: Governance
 * - SBM: Strategy, business model and value chain
 * - IRO: Impacts, risks and opportunities management
 */
class Esrs2Disclosure extends Model
{
    use HasFactory, HasUuids, SoftDeletes, BelongsToOrganization;

    protected $table = 'esrs2_disclosures';

    protected $fillable = [
        'organization_id',
        'reporting_year',
        'disclosure_code',
        'disclosure_name',
        'disclosure_name_de',
        'category',
        'status',
        'completion_percent',
        'narrative_disclosure',
        'narrative_disclosure_de',
        'data_points',
        'quantitative_data',
        'supporting_documents',
        'cross_references',
        'prepared_by',
        'reviewed_by',
        'reviewed_at',
        'approved_by',
        'approved_at',
        'review_notes',
        'metadata',
    ];

    protected $casts = [
        'completion_percent' => 'decimal:2',
        'data_points' => 'array',
        'quantitative_data' => 'array',
        'supporting_documents' => 'array',
        'cross_references' => 'array',
        'reviewed_at' => 'datetime',
        'approved_at' => 'datetime',
        'metadata' => 'array',
    ];

    /**
     * ESRS 2 Disclosure Requirements
     */
    public const DISCLOSURES = [
        // Basis for preparation
        'BP-1' => [
            'name' => 'General basis for preparation of the sustainability statements',
            'name_de' => 'Allgemeine Grundlage für die Erstellung der Nachhaltigkeitserklärungen',
            'category' => 'bp',
            'mandatory' => true,
        ],
        'BP-2' => [
            'name' => 'Disclosures in relation to specific circumstances',
            'name_de' => 'Angaben zu besonderen Umständen',
            'category' => 'bp',
            'mandatory' => true,
        ],

        // Governance
        'GOV-1' => [
            'name' => 'Role of the administrative, management and supervisory bodies',
            'name_de' => 'Rolle der Verwaltungs-, Leitungs- und Aufsichtsorgane',
            'category' => 'gov',
            'mandatory' => true,
        ],
        'GOV-2' => [
            'name' => 'Information provided to and sustainability matters addressed by administrative, management and supervisory bodies',
            'name_de' => 'Von den Verwaltungs-, Leitungs- und Aufsichtsorganen behandelte Nachhaltigkeitsthemen',
            'category' => 'gov',
            'mandatory' => true,
        ],
        'GOV-3' => [
            'name' => 'Integration of sustainability-related performance in incentive schemes',
            'name_de' => 'Integration von Nachhaltigkeitsleistungen in Vergütungssysteme',
            'category' => 'gov',
            'mandatory' => true,
        ],
        'GOV-4' => [
            'name' => 'Statement on due diligence',
            'name_de' => 'Erklärung zur Sorgfaltspflicht',
            'category' => 'gov',
            'mandatory' => true,
        ],
        'GOV-5' => [
            'name' => 'Risk management and internal controls over sustainability reporting',
            'name_de' => 'Risikomanagement und interne Kontrollen für die Nachhaltigkeitsberichterstattung',
            'category' => 'gov',
            'mandatory' => true,
        ],

        // Strategy, business model and value chain
        'SBM-1' => [
            'name' => 'Strategy, business model and value chain',
            'name_de' => 'Strategie, Geschäftsmodell und Wertschöpfungskette',
            'category' => 'sbm',
            'mandatory' => true,
        ],
        'SBM-2' => [
            'name' => 'Interests and views of stakeholders',
            'name_de' => 'Interessen und Ansichten der Stakeholder',
            'category' => 'sbm',
            'mandatory' => true,
        ],
        'SBM-3' => [
            'name' => 'Material impacts, risks and opportunities and their interaction with strategy and business model',
            'name_de' => 'Wesentliche Auswirkungen, Risiken und Chancen',
            'category' => 'sbm',
            'mandatory' => true,
        ],

        // Impact, risk and opportunity management
        'IRO-1' => [
            'name' => 'Description of the processes to identify and assess material impacts, risks and opportunities',
            'name_de' => 'Beschreibung der Prozesse zur Identifizierung wesentlicher Auswirkungen, Risiken und Chancen',
            'category' => 'iro',
            'mandatory' => true,
        ],
        'IRO-2' => [
            'name' => 'Disclosure requirements in ESRS covered by the sustainability statement',
            'name_de' => 'Von der Nachhaltigkeitserklärung abgedeckte ESRS-Angabepflichten',
            'category' => 'iro',
            'mandatory' => true,
        ],
    ];

    /**
     * Status labels
     */
    public const STATUSES = [
        'not_started' => 'Not Started',
        'in_progress' => 'In Progress',
        'draft' => 'Draft',
        'completed' => 'Completed',
        'verified' => 'Verified',
    ];

    /**
     * Category labels
     */
    public const CATEGORIES = [
        'bp' => 'Basis for Preparation',
        'gov' => 'Governance',
        'sbm' => 'Strategy, Business Model, Value Chain',
        'iro' => 'Impacts, Risks, Opportunities',
    ];

    /**
     * Relationships
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function preparer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'prepared_by');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Scopes
     */
    public function scopeForYear($query, int $year)
    {
        return $query->where('reporting_year', $year);
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function scopeCompleted($query)
    {
        return $query->whereIn('status', ['completed', 'verified']);
    }

    public function scopePending($query)
    {
        return $query->whereIn('status', ['not_started', 'in_progress', 'draft']);
    }

    /**
     * Accessors
     */
    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function getCategoryLabelAttribute(): string
    {
        return self::CATEGORIES[$this->category] ?? $this->category;
    }

    public function getIsCompleteAttribute(): bool
    {
        return in_array($this->status, ['completed', 'verified']);
    }

    public function getIsVerifiedAttribute(): bool
    {
        return $this->status === 'verified';
    }

    public function getDisclosureInfoAttribute(): ?array
    {
        return self::DISCLOSURES[$this->disclosure_code] ?? null;
    }

    /**
     * Check if disclosure is mandatory
     */
    public function isMandatory(): bool
    {
        return self::DISCLOSURES[$this->disclosure_code]['mandatory'] ?? true;
    }

    /**
     * Get data point count
     */
    public function getDataPointCountAttribute(): int
    {
        return count($this->data_points ?? []);
    }

    /**
     * Calculate completion based on data points
     */
    public function calculateCompletion(): float
    {
        $dataPoints = $this->data_points ?? [];
        if (empty($dataPoints)) {
            return $this->narrative_disclosure ? 50.0 : 0.0;
        }

        $filled = 0;
        foreach ($dataPoints as $point) {
            if (!empty($point['value'])) {
                $filled++;
            }
        }

        $base = $this->narrative_disclosure ? 50.0 : 0.0;
        $dataPointsPercent = count($dataPoints) > 0
            ? ($filled / count($dataPoints)) * 50.0
            : 0.0;

        return round($base + $dataPointsPercent, 2);
    }
}
