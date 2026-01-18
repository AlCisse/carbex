<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOrganization;
use App\Models\Concerns\HasUuid;
use App\Models\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmissionRecord extends Model
{
    use BelongsToOrganization, HasFactory, HasUuid, LogsActivity, SoftDeletes;

    protected $fillable = [
        'organization_id',
        'assessment_id',
        'transaction_id',
        'activity_id',
        'site_id',
        'category_id',
        'emission_factor_id',
        'year',
        'date',
        'period_start',
        'period_end',
        'scope',
        'ghg_category',
        'scope_3_category',
        'quantity',
        'unit',
        'factor_value',
        'factor_unit',
        'factor_source',
        'co2e_kg',
        'co2_kg',
        'ch4_kg',
        'n2o_kg',
        'uncertainty_percent',
        'calculation_method',
        'data_quality',
        'source_type',
        'is_estimated',
        'notes',
        'factor_snapshot',
        'metadata',
    ];

    protected $casts = [
        'date' => 'date',
        'period_start' => 'date',
        'period_end' => 'date',
        'scope' => 'integer',
        'scope_3_category' => 'integer',
        'quantity' => 'decimal:4',
        'factor_value' => 'decimal:10',
        'co2e_kg' => 'decimal:6',
        'co2_kg' => 'decimal:6',
        'ch4_kg' => 'decimal:6',
        'n2o_kg' => 'decimal:6',
        'uncertainty_percent' => 'decimal:2',
        'is_estimated' => 'boolean',
        'factor_snapshot' => 'array',
        'metadata' => 'array',
    ];

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    public function activity(): BelongsTo
    {
        return $this->belongsTo(Activity::class);
    }

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function emissionFactor(): BelongsTo
    {
        return $this->belongsTo(EmissionFactor::class);
    }

    /**
     * Get CO2e in tonnes.
     */
    public function getCo2eTonnesAttribute(): float
    {
        return $this->co2e_kg / 1000;
    }

    /**
     * Get the source label for display.
     */
    public function getSourceLabelAttribute(): string
    {
        return match ($this->source_type) {
            'transaction' => __('linscarbon.emission_sources.transaction'),
            'activity' => __('linscarbon.emission_sources.activity'),
            'manual' => __('linscarbon.emission_sources.manual'),
            'import' => __('linscarbon.emission_sources.import'),
            default => $this->source_type,
        };
    }

    /**
     * Get the scope label.
     */
    public function getScopeLabelAttribute(): string
    {
        return match ($this->scope) {
            1 => __('linscarbon.ghg_scopes.1.name'),
            2 => __('linscarbon.ghg_scopes.2.name'),
            3 => __('linscarbon.ghg_scopes.3.name'),
            default => "Scope {$this->scope}",
        };
    }

    public function scopeForAssessment($query, string $assessmentId)
    {
        return $query->where('assessment_id', $assessmentId);
    }

    public function scopeForScope($query, int $scope)
    {
        return $query->where('scope', $scope);
    }

    public function scopeForCategory($query, string $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeForSite($query, string $siteId)
    {
        return $query->where('site_id', $siteId);
    }

    public function scopeInPeriod($query, string $startDate, string $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    public function scopeFromTransactions($query)
    {
        return $query->where('source_type', 'transaction');
    }

    public function scopeFromActivities($query)
    {
        return $query->where('source_type', 'activity');
    }
}
