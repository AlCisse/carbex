<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOrganization;
use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * ReductionTarget (Trajectoire SBTi) - Reduction targets aligned with SBTi
 *
 * Constitution Carbex v3.0 - Section 7, 2.9
 *
 * SBTi Recommendations:
 * - Scope 1 & 2: Minimum 4.2% annual reduction
 * - Scope 3: Minimum 2.5% annual reduction
 * - Aligned with Paris Agreement 1.5C target
 *
 * @property string $id
 * @property string $organization_id
 * @property int $baseline_year
 * @property int $target_year
 * @property float $scope_1_reduction
 * @property float $scope_2_reduction
 * @property float $scope_3_reduction
 * @property bool $is_sbti_aligned
 * @property string|null $notes
 */
class ReductionTarget extends Model
{
    use BelongsToOrganization, HasFactory, HasUuid;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'organization_id',
        'baseline_year',
        'target_year',
        'scope_1_reduction',
        'scope_2_reduction',
        'scope_3_reduction',
        'is_sbti_aligned',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'baseline_year' => 'integer',
        'target_year' => 'integer',
        'scope_1_reduction' => 'decimal:2',
        'scope_2_reduction' => 'decimal:2',
        'scope_3_reduction' => 'decimal:2',
        'is_sbti_aligned' => 'boolean',
    ];

    /**
     * SBTi minimum annual reduction rates.
     */
    public const SBTI_SCOPE_1_2_MIN_RATE = 4.2; // % per year

    public const SBTI_SCOPE_3_MIN_RATE = 2.5; // % per year

    /**
     * Get the number of years between baseline and target.
     */
    public function getYearsToTargetAttribute(): int
    {
        return $this->target_year - $this->baseline_year;
    }

    /**
     * Calculate annual reduction rate for Scope 1.
     */
    public function getScope1AnnualRateAttribute(): float
    {
        if ($this->years_to_target === 0) {
            return 0;
        }

        return round($this->scope_1_reduction / $this->years_to_target, 2);
    }

    /**
     * Calculate annual reduction rate for Scope 2.
     */
    public function getScope2AnnualRateAttribute(): float
    {
        if ($this->years_to_target === 0) {
            return 0;
        }

        return round($this->scope_2_reduction / $this->years_to_target, 2);
    }

    /**
     * Calculate annual reduction rate for Scope 3.
     */
    public function getScope3AnnualRateAttribute(): float
    {
        if ($this->years_to_target === 0) {
            return 0;
        }

        return round($this->scope_3_reduction / $this->years_to_target, 2);
    }

    /**
     * Check if Scope 1 target meets SBTi minimum.
     */
    public function isScope1SbtiCompliant(): bool
    {
        return $this->scope_1_annual_rate >= self::SBTI_SCOPE_1_2_MIN_RATE;
    }

    /**
     * Check if Scope 2 target meets SBTi minimum.
     */
    public function isScope2SbtiCompliant(): bool
    {
        return $this->scope_2_annual_rate >= self::SBTI_SCOPE_1_2_MIN_RATE;
    }

    /**
     * Check if Scope 3 target meets SBTi minimum.
     */
    public function isScope3SbtiCompliant(): bool
    {
        return $this->scope_3_annual_rate >= self::SBTI_SCOPE_3_MIN_RATE;
    }

    /**
     * Check if all scopes meet SBTi minimum requirements.
     */
    public function isFullySbtiCompliant(): bool
    {
        return $this->isScope1SbtiCompliant()
            && $this->isScope2SbtiCompliant()
            && $this->isScope3SbtiCompliant();
    }

    /**
     * Get SBTi compliance status per scope.
     */
    public function getSbtiComplianceAttribute(): array
    {
        return [
            'scope_1' => $this->isScope1SbtiCompliant(),
            'scope_2' => $this->isScope2SbtiCompliant(),
            'scope_3' => $this->isScope3SbtiCompliant(),
            'overall' => $this->isFullySbtiCompliant(),
        ];
    }

    /**
     * Get recommended SBTi-aligned reduction targets.
     */
    public static function getSbtiRecommendedTargets(int $baselineYear, int $targetYear): array
    {
        $years = $targetYear - $baselineYear;

        return [
            'scope_1_reduction' => round(self::SBTI_SCOPE_1_2_MIN_RATE * $years, 2),
            'scope_2_reduction' => round(self::SBTI_SCOPE_1_2_MIN_RATE * $years, 2),
            'scope_3_reduction' => round(self::SBTI_SCOPE_3_MIN_RATE * $years, 2),
        ];
    }

    /**
     * Calculate expected emissions for a given year based on baseline.
     */
    public function getExpectedEmissionsForYear(int $year, float $baselineEmissions, int $scope): float
    {
        if ($year <= $this->baseline_year) {
            return $baselineEmissions;
        }

        if ($year >= $this->target_year) {
            $reductionPercent = match ($scope) {
                1 => $this->scope_1_reduction,
                2 => $this->scope_2_reduction,
                3 => $this->scope_3_reduction,
                default => 0,
            };

            return $baselineEmissions * (1 - $reductionPercent / 100);
        }

        // Linear interpolation for intermediate years
        $progress = ($year - $this->baseline_year) / $this->years_to_target;
        $reductionPercent = match ($scope) {
            1 => $this->scope_1_reduction * $progress,
            2 => $this->scope_2_reduction * $progress,
            3 => $this->scope_3_reduction * $progress,
            default => 0,
        };

        return $baselineEmissions * (1 - $reductionPercent / 100);
    }

    /**
     * Scope to filter by baseline year.
     */
    public function scopeForBaselineYear(Builder $query, int $year): Builder
    {
        return $query->where('baseline_year', $year);
    }

    /**
     * Scope to filter by target year.
     */
    public function scopeForTargetYear(Builder $query, int $year): Builder
    {
        return $query->where('target_year', $year);
    }

    /**
     * Scope to filter SBTi aligned targets.
     */
    public function scopeSbtiAligned(Builder $query): Builder
    {
        return $query->where('is_sbti_aligned', true);
    }

    /**
     * Scope to get current active targets (where target year is in future).
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('target_year', '>=', now()->year);
    }
}
