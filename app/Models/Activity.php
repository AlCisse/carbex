<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOrganization;
use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Activity extends Model
{
    use BelongsToOrganization, HasFactory, HasUuid, SoftDeletes;

    protected $fillable = [
        'organization_id',
        'site_id',
        'category_id',
        'name',
        'description',
        'date',
        'period_start',
        'period_end',
        'quantity',
        'unit',
        'source',
        'document_path',
        'document_name',
        'status',
        'notes',
        'created_by',
        'validated_by',
        'validated_at',
        'metadata',
    ];

    protected $casts = [
        'date' => 'date',
        'period_start' => 'date',
        'period_end' => 'date',
        'quantity' => 'decimal:4',
        'validated_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function validatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    public function emissionRecord(): HasOne
    {
        return $this->hasOne(EmissionRecord::class);
    }

    /**
     * Check if activity is validated.
     */
    public function isValidated(): bool
    {
        return $this->validated_at !== null;
    }

    /**
     * Check if activity has a document attached.
     */
    public function hasDocument(): bool
    {
        return $this->document_path !== null;
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeValidated($query)
    {
        return $query->where('status', 'validated');
    }

    public function scopeFromSource($query, string $source)
    {
        return $query->where('source', $source);
    }

    public function scopeInPeriod($query, string $startDate, string $endDate)
    {
        return $query->where(function ($q) use ($startDate, $endDate) {
            $q->whereBetween('date', [$startDate, $endDate])
                ->orWhere(function ($q2) use ($startDate, $endDate) {
                    $q2->where('period_start', '<=', $endDate)
                        ->where('period_end', '>=', $startDate);
                });
        });
    }

    public function scopeForSite($query, string $siteId)
    {
        return $query->where('site_id', $siteId);
    }
}
