<?php

namespace App\Models\Concerns;

use App\Models\Organization;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

/**
 * Trait BelongsToOrganization
 *
 * Provides multi-tenant functionality by automatically scoping
 * queries to the current user's organization.
 */
trait BelongsToOrganization
{
    /**
     * Boot the trait.
     */
    public static function bootBelongsToOrganization(): void
    {
        // Automatically scope queries to current organization
        static::addGlobalScope('organization', function (Builder $builder) {
            if (Auth::check() && Auth::user()->organization_id) {
                $builder->where(
                    $builder->getModel()->getTable() . '.organization_id',
                    Auth::user()->organization_id
                );
            }
        });

        // Automatically set organization_id on create
        static::creating(function ($model) {
            if (Auth::check() && ! $model->organization_id) {
                $model->organization_id = Auth::user()->organization_id;
            }
        });
    }

    /**
     * Get the organization that owns this model.
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Scope a query to a specific organization.
     */
    public function scopeForOrganization(Builder $query, string $organizationId): Builder
    {
        return $query->withoutGlobalScope('organization')
            ->where('organization_id', $organizationId);
    }

    /**
     * Scope a query to all organizations (bypass tenant scoping).
     */
    public function scopeWithoutOrganizationScope(Builder $query): Builder
    {
        return $query->withoutGlobalScope('organization');
    }
}
