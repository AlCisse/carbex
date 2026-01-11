<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Organization extends Model
{
    use HasFactory, HasUuid, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'legal_name',
        'slug',
        'country',
        'locale',
        'timezone',
        'currency',
        'default_currency',
        'business_id',
        'vat_number',
        'registration_number',
        'sector',
        'size',
        'employee_count',
        'annual_turnover',
        'address_line_1',
        'address_line_2',
        'city',
        'postal_code',
        'state',
        'phone',
        'email',
        'website',
        'fiscal_year_start_month',
        'settings',
        'onboarding_completed',
        'onboarded_at',
        'status',
        // ISO 14064-1 fields
        'consolidation_method',
        'boundary_description',
        'boundary_definition_date',
        'base_year',
        'base_year_emissions_tco2e',
        'base_year_justification',
        'recalculation_policy',
        'recalculation_threshold_percent',
        'last_verification_date',
        'verification_level',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'settings' => 'array',
        'onboarding_completed' => 'boolean',
        'onboarded_at' => 'datetime',
        'annual_turnover' => 'decimal:2',
        // ISO 14064-1 casts
        'boundary_definition_date' => 'date',
        'base_year_emissions_tco2e' => 'decimal:4',
        'recalculation_threshold_percent' => 'decimal:2',
        'last_verification_date' => 'date',
    ];

    /**
     * Get the users for the organization.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the sites for the organization.
     */
    public function sites(): HasMany
    {
        return $this->hasMany(Site::class);
    }

    /**
     * Get the primary site for the organization.
     */
    public function primarySite(): HasOne
    {
        return $this->hasOne(Site::class)->where('is_primary', true);
    }

    /**
     * Get the bank connections for the organization.
     */
    public function bankConnections(): HasMany
    {
        return $this->hasMany(BankConnection::class);
    }

    /**
     * Get the bank accounts through bank connections.
     */
    public function bankAccounts(): HasManyThrough
    {
        return $this->hasManyThrough(BankAccount::class, BankConnection::class);
    }

    /**
     * Get the transactions for the organization.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Get the activities for the organization.
     */
    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }

    /**
     * Get the emission records for the organization.
     */
    public function emissionRecords(): HasMany
    {
        return $this->hasMany(EmissionRecord::class);
    }

    /**
     * Get the assessments (bilans) for the organization.
     */
    public function assessments(): HasMany
    {
        return $this->hasMany(Assessment::class);
    }

    /**
     * Get the current active assessment.
     */
    public function currentAssessment(): HasOne
    {
        return $this->hasOne(Assessment::class)
            ->where('status', 'active')
            ->latest('year');
    }

    /**
     * Get the actions (plan de transition) for the organization.
     */
    public function actions(): HasMany
    {
        return $this->hasMany(Action::class);
    }

    /**
     * Get the reduction targets (trajectoire) for the organization.
     */
    public function reductionTargets(): HasMany
    {
        return $this->hasMany(ReductionTarget::class);
    }

    /**
     * Get the reports for the organization.
     */
    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }

    /**
     * Get the subscription for the organization.
     */
    public function subscription(): HasOne
    {
        return $this->hasOne(Subscription::class)->latest();
    }

    /**
     * Get the invoices for the organization.
     */
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * Get the country configuration.
     */
    public function getCountryConfigAttribute(): array
    {
        return config("countries.countries.{$this->country}", []);
    }

    /**
     * Check if organization is on trial.
     */
    public function onTrial(): bool
    {
        $subscription = $this->subscription;

        return $subscription && $subscription->onTrial();
    }

    /**
     * Check if organization has an active subscription.
     */
    public function hasActiveSubscription(): bool
    {
        $subscription = $this->subscription;

        return $subscription && $subscription->active();
    }

    /**
     * Get the owner of the organization.
     */
    public function owner(): HasOne
    {
        return $this->hasOne(User::class)->where('role', 'owner');
    }

    /**
     * Scope active organizations.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope by country.
     */
    public function scopeCountry($query, string $country)
    {
        return $query->where('country', $country);
    }

    /**
     * Get the suppliers for the organization.
     */
    public function suppliers(): HasMany
    {
        return $this->hasMany(Supplier::class);
    }

    /**
     * Get the badges earned by this organization.
     */
    public function badges(): BelongsToMany
    {
        return $this->belongsToMany(Badge::class, 'organization_badges')
            ->withPivot(['earned_at', 'share_token', 'metadata'])
            ->withTimestamps();
    }

    /**
     * Get the GHG removals (carbon sinks/offsets) for the organization.
     * ISO 14064-1 Section 5.2.4
     */
    public function ghgRemovals(): HasMany
    {
        return $this->hasMany(GhgRemoval::class);
    }

    /**
     * Get the GHG verifications for the organization.
     * ISO 14064-1 Section 7 / ISO 14064-3
     */
    public function ghgVerifications(): HasMany
    {
        return $this->hasMany(GhgVerification::class);
    }

    /**
     * Get the current verification level label.
     */
    public function getVerificationLevelLabelAttribute(): string
    {
        return match ($this->verification_level) {
            'internal' => 'Internal Review',
            'limited' => 'Limited Assurance',
            'reasonable' => 'Reasonable Assurance',
            default => 'Not Verified',
        };
    }

    /**
     * Get the consolidation method label.
     */
    public function getConsolidationMethodLabelAttribute(): string
    {
        return match ($this->consolidation_method) {
            'operational_control' => 'Operational Control',
            'financial_control' => 'Financial Control',
            'equity_share' => 'Equity Share',
            default => 'Not Defined',
        };
    }
}
