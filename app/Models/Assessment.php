<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOrganization;
use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Assessment (Bilan) - Annual carbon assessment entity
 *
 * Constitution Carbex v3.0 - Section 7, 2.10
 *
 * @property string $id
 * @property string $organization_id
 * @property int $year
 * @property float|null $revenue
 * @property int|null $employee_count
 * @property string $status
 * @property array|null $progress
 */
class Assessment extends Model
{
    use BelongsToOrganization, HasFactory, HasUuid;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'organization_id',
        'year',
        'revenue',
        'employee_count',
        'status',
        'progress',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'year' => 'integer',
        'revenue' => 'decimal:2',
        'employee_count' => 'integer',
        'progress' => 'array',
    ];

    /**
     * Status constants.
     */
    public const STATUS_DRAFT = 'draft';

    public const STATUS_ACTIVE = 'active';

    public const STATUS_COMPLETED = 'completed';

    /**
     * Get the emission records for this assessment.
     */
    public function emissionRecords(): HasMany
    {
        return $this->hasMany(EmissionRecord::class);
    }

    /**
     * Get the reports generated for this assessment.
     */
    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }

    /**
     * Calculate total emissions for this assessment in kg CO2e.
     */
    public function getTotalEmissionsKgAttribute(): float
    {
        return $this->emissionRecords()->sum('co2e_kg') ?? 0;
    }

    /**
     * Calculate total emissions for this assessment in tonnes CO2e.
     */
    public function getTotalEmissionsTonnesAttribute(): float
    {
        return $this->total_emissions_kg / 1000;
    }

    /**
     * Get emissions by scope.
     */
    public function getEmissionsByScopeAttribute(): array
    {
        return $this->emissionRecords()
            ->selectRaw('scope, SUM(co2e_kg) as total')
            ->groupBy('scope')
            ->pluck('total', 'scope')
            ->toArray();
    }

    /**
     * Get completion percentage.
     */
    public function getCompletionPercentAttribute(): int
    {
        if (empty($this->progress)) {
            return 0;
        }

        $categories = collect($this->progress);
        $total = $categories->count();

        if ($total === 0) {
            return 0;
        }

        $completed = $categories->filter(fn ($status) => $status === 'completed')->count();

        return (int) round(($completed / $total) * 100);
    }

    /**
     * Check if assessment is draft.
     */
    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    /**
     * Check if assessment is active.
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    /**
     * Check if assessment is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * Activate the assessment.
     */
    public function activate(): bool
    {
        if ($this->status === self::STATUS_DRAFT) {
            $this->status = self::STATUS_ACTIVE;

            return $this->save();
        }

        return false;
    }

    /**
     * Mark assessment as completed.
     */
    public function complete(): bool
    {
        if ($this->status === self::STATUS_ACTIVE) {
            $this->status = self::STATUS_COMPLETED;

            return $this->save();
        }

        return false;
    }

    /**
     * Reopen a completed assessment.
     */
    public function reopen(): bool
    {
        if ($this->status === self::STATUS_COMPLETED) {
            $this->status = self::STATUS_ACTIVE;

            return $this->save();
        }

        return false;
    }

    /**
     * Update progress for a category.
     */
    public function updateCategoryProgress(string $categoryCode, string $status): void
    {
        $progress = $this->progress ?? [];
        $progress[$categoryCode] = $status;
        $this->progress = $progress;
        $this->save();
    }

    /**
     * Scope to filter by year.
     */
    public function scopeForYear(Builder $query, int $year): Builder
    {
        return $query->where('year', $year);
    }

    /**
     * Scope to filter by status.
     */
    public function scopeStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to filter active assessments.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Scope to filter completed assessments.
     */
    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }
}
