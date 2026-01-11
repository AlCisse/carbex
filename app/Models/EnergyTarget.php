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
 * Energy Target Model - ISO 50001:2018 Section 6.6
 *
 * Energy targets are quantifiable energy objectives.
 * They should be:
 * - Consistent with the energy policy
 * - Measurable (if practicable)
 * - Monitored and updated as appropriate
 * - Communicated within the organization
 */
class EnergyTarget extends Model
{
    use HasFactory, HasUuids, SoftDeletes, BelongsToOrganization;

    protected $fillable = [
        'organization_id',
        'site_id',
        'energy_baseline_id',
        'name',
        'name_de',
        'description',
        'target_type',
        'baseline_year',
        'target_year',
        'start_date',
        'end_date',
        'baseline_value',
        'target_value',
        'current_value',
        'unit',
        'target_reduction_percent',
        'progress_percent',
        'annual_target',
        'milestones',
        'progress_history',
        'status',
        'is_sbti_aligned',
        'action_plan',
        'investment_required',
        'expected_savings_annual',
        'payback_years',
        'responsible_person',
        'responsible_department',
        'created_by',
        'approved_by',
        'approved_at',
        'last_review_date',
        'next_review_date',
        'metadata',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'baseline_value' => 'decimal:4',
        'target_value' => 'decimal:4',
        'current_value' => 'decimal:4',
        'target_reduction_percent' => 'decimal:4',
        'progress_percent' => 'decimal:4',
        'annual_target' => 'decimal:4',
        'milestones' => 'array',
        'progress_history' => 'array',
        'is_sbti_aligned' => 'boolean',
        'action_plan' => 'array',
        'investment_required' => 'decimal:2',
        'expected_savings_annual' => 'decimal:2',
        'payback_years' => 'decimal:2',
        'approved_at' => 'datetime',
        'last_review_date' => 'date',
        'next_review_date' => 'date',
        'metadata' => 'array',
    ];

    /**
     * Target types
     */
    public const TARGET_TYPES = [
        'absolute_reduction' => [
            'name' => 'Absolute Reduction',
            'name_de' => 'Absolute Reduktion',
            'description' => 'Reduce total energy consumption by fixed amount',
            'direction' => 'decrease',
        ],
        'intensity_reduction' => [
            'name' => 'Intensity Reduction',
            'name_de' => 'Intensitätsreduktion',
            'description' => 'Reduce energy per unit of activity',
            'direction' => 'decrease',
        ],
        'renewable_share' => [
            'name' => 'Renewable Energy Share',
            'name_de' => 'Anteil erneuerbarer Energie',
            'description' => 'Achieve target percentage of renewable energy',
            'direction' => 'increase',
        ],
        'efficiency_improvement' => [
            'name' => 'Efficiency Improvement',
            'name_de' => 'Effizienzverbesserung',
            'description' => 'Improve energy efficiency of systems',
            'direction' => 'increase',
        ],
        'cost_reduction' => [
            'name' => 'Cost Reduction',
            'name_de' => 'Kostenreduktion',
            'description' => 'Reduce energy costs',
            'direction' => 'decrease',
        ],
        'carbon_reduction' => [
            'name' => 'Carbon Reduction',
            'name_de' => 'CO₂-Reduktion',
            'description' => 'Reduce carbon emissions from energy',
            'direction' => 'decrease',
        ],
    ];

    /**
     * Target statuses
     */
    public const STATUSES = [
        'planned' => ['name' => 'Planned', 'name_de' => 'Geplant', 'color' => 'gray'],
        'active' => ['name' => 'Active', 'name_de' => 'Aktiv', 'color' => 'blue'],
        'on_track' => ['name' => 'On Track', 'name_de' => 'Auf Kurs', 'color' => 'green'],
        'at_risk' => ['name' => 'At Risk', 'name_de' => 'Gefährdet', 'color' => 'yellow'],
        'achieved' => ['name' => 'Achieved', 'name_de' => 'Erreicht', 'color' => 'green'],
        'missed' => ['name' => 'Missed', 'name_de' => 'Verfehlt', 'color' => 'red'],
        'cancelled' => ['name' => 'Cancelled', 'name_de' => 'Abgebrochen', 'color' => 'gray'],
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

    public function energyBaseline(): BelongsTo
    {
        return $this->belongsTo(EnergyBaseline::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['active', 'on_track', 'at_risk']);
    }

    public function scopeAchieved($query)
    {
        return $query->where('status', 'achieved');
    }

    public function scopeForYear($query, int $year)
    {
        return $query->where('target_year', $year);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('target_type', $type);
    }

    public function scopeSbtiAligned($query)
    {
        return $query->where('is_sbti_aligned', true);
    }

    /**
     * Accessors
     */
    public function getTargetTypeLabelAttribute(): string
    {
        return self::TARGET_TYPES[$this->target_type]['name'] ?? $this->target_type;
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status]['name'] ?? $this->status;
    }

    public function getStatusColorAttribute(): string
    {
        return self::STATUSES[$this->status]['color'] ?? 'gray';
    }

    public function getIsActiveAttribute(): bool
    {
        return in_array($this->status, ['active', 'on_track', 'at_risk']);
    }

    public function getIsAchievedAttribute(): bool
    {
        return $this->status === 'achieved';
    }

    public function getYearsRemainingAttribute(): int
    {
        return max(0, $this->target_year - now()->year);
    }

    /**
     * Calculate progress percentage
     */
    public function calculateProgress(): float
    {
        if ($this->baseline_value === null || $this->target_value === null || $this->current_value === null) {
            return 0;
        }

        $totalChange = abs($this->baseline_value - $this->target_value);
        if ($totalChange == 0) {
            return 100;
        }

        $actualChange = abs($this->baseline_value - $this->current_value);
        $direction = self::TARGET_TYPES[$this->target_type]['direction'] ?? 'decrease';

        // Check if moving in right direction
        if ($direction === 'decrease') {
            $progress = $this->current_value <= $this->baseline_value
                ? ($actualChange / $totalChange) * 100
                : 0;
        } else {
            $progress = $this->current_value >= $this->baseline_value
                ? ($actualChange / $totalChange) * 100
                : 0;
        }

        return min(100, max(0, round($progress, 4)));
    }

    /**
     * Update status based on progress
     */
    public function updateStatus(): string
    {
        $progress = $this->calculateProgress();
        $this->progress_percent = $progress;

        // Check if achieved
        if ($progress >= 100) {
            $this->status = 'achieved';
            $this->save();
            return $this->status;
        }

        // Calculate expected progress based on time
        $totalYears = $this->target_year - $this->baseline_year;
        $yearsElapsed = now()->year - $this->baseline_year;
        $expectedProgress = $totalYears > 0 ? ($yearsElapsed / $totalYears) * 100 : 0;

        // Determine status
        if ($progress >= $expectedProgress * 0.9) {
            $this->status = 'on_track';
        } elseif ($progress >= $expectedProgress * 0.7) {
            $this->status = 'at_risk';
        } else {
            $this->status = 'active';
        }

        $this->save();
        return $this->status;
    }

    /**
     * Add progress update to history
     */
    public function addProgressUpdate(float $value, ?string $notes = null): void
    {
        $history = $this->progress_history ?? [];
        $history[] = [
            'date' => now()->toIso8601String(),
            'value' => $value,
            'progress_percent' => $this->calculateProgress(),
            'notes' => $notes,
        ];

        $this->progress_history = $history;
        $this->current_value = $value;
        $this->updateStatus();
    }

    /**
     * Calculate annual target based on linear trajectory
     */
    public function getAnnualTargetForYear(int $year): ?float
    {
        if ($year < $this->baseline_year || $year > $this->target_year) {
            return null;
        }

        $totalYears = $this->target_year - $this->baseline_year;
        if ($totalYears == 0) {
            return $this->target_value;
        }

        $yearIndex = $year - $this->baseline_year;
        $totalChange = $this->target_value - $this->baseline_value;
        $annualChange = $totalChange / $totalYears;

        return round($this->baseline_value + ($annualChange * $yearIndex), 4);
    }

    /**
     * Calculate ROI based on investment and savings
     */
    public function calculateRoi(): ?float
    {
        if (!$this->investment_required || !$this->expected_savings_annual || $this->investment_required == 0) {
            return null;
        }

        // Simple ROI over target period
        $years = $this->target_year - $this->baseline_year;
        $totalSavings = $this->expected_savings_annual * $years;

        return round((($totalSavings - $this->investment_required) / $this->investment_required) * 100, 2);
    }
}
