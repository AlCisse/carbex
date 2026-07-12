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
        // ISO 50001 fields
        'energy_policy',
        'energy_policy_date',
        'enms_scope',
        'enms_boundaries',
        'iso50001_certified',
        'iso50001_cert_date',
        'iso50001_cert_expiry',
        'iso50001_registrar',
        'energy_manager_name',
        'energy_manager_email',
        // CSRD fields
        'csrd_applicable',
        'csrd_applicable_from',
        'csrd_company_category',
        'balance_sheet_total',
        'sustainability_auditor',
        'sustainability_assurance_level',
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
        // ISO 50001 casts
        'energy_policy_date' => 'date',
        'enms_boundaries' => 'array',
        'iso50001_certified' => 'boolean',
        'iso50001_cert_date' => 'date',
        'iso50001_cert_expiry' => 'date',
        // CSRD casts
        'csrd_applicable' => 'boolean',
        'balance_sheet_total' => 'decimal:2',
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
     * Get the ESRS 2 disclosures for the organization.
     * CSRD/ESRS Set 1 (2023)
     */
    public function esrs2Disclosures(): HasMany
    {
        return $this->hasMany(Esrs2Disclosure::class);
    }

    /**
     * Get the climate transition plans for the organization.
     * ESRS E1-1
     */
    public function climateTransitionPlans(): HasMany
    {
        return $this->hasMany(ClimateTransitionPlan::class);
    }

    /**
     * Get the current climate transition plan.
     */
    public function currentClimateTransitionPlan(): HasOne
    {
        return $this->hasOne(ClimateTransitionPlan::class)
            ->whereIn('status', ['approved', 'published'])
            ->latest('plan_year');
    }

    /**
     * Get the EU Taxonomy reports for the organization.
     * EU Regulation 2020/852 Article 8
     */
    public function euTaxonomyReports(): HasMany
    {
        return $this->hasMany(EuTaxonomyReport::class);
    }

    /**
     * Get the value chain due diligence records.
     * LkSG / CSDDD compliance
     */
    public function valueChainDueDiligence(): HasMany
    {
        return $this->hasMany(ValueChainDueDiligence::class);
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

    /**
     * Get the energy reviews for the organization.
     * ISO 50001:2018 Section 6.3
     */
    public function energyReviews(): HasMany
    {
        return $this->hasMany(EnergyReview::class);
    }

    /**
     * Get the energy performance indicators for the organization.
     * ISO 50001:2018 Section 6.4
     */
    public function energyPerformanceIndicators(): HasMany
    {
        return $this->hasMany(EnergyPerformanceIndicator::class);
    }

    /**
     * Get the energy baselines for the organization.
     * ISO 50001:2018 Section 6.5
     */
    public function energyBaselines(): HasMany
    {
        return $this->hasMany(EnergyBaseline::class);
    }

    /**
     * Get the current energy baseline.
     */
    public function currentEnergyBaseline(): HasOne
    {
        return $this->hasOne(EnergyBaseline::class)->where('is_current', true);
    }

    /**
     * Get the energy targets for the organization.
     * ISO 50001:2018 Section 6.6
     */
    public function energyTargets(): HasMany
    {
        return $this->hasMany(EnergyTarget::class);
    }

    /**
     * Get the energy audits for the organization.
     * ISO 50001:2018 Section 9.2
     */
    public function energyAudits(): HasMany
    {
        return $this->hasMany(EnergyAudit::class);
    }

    /**
     * Check if organization is ISO 50001 certified.
     */
    public function isIso50001Certified(): bool
    {
        return $this->iso50001_certified
            && $this->iso50001_cert_expiry
            && $this->iso50001_cert_expiry > now();
    }

    /**
     * Check if CSRD reporting is applicable.
     * Based on EU thresholds for different company categories.
     */
    public function isCsrdApplicable(?int $year = null): bool
    {
        $year = $year ?? now()->year;

        // Large companies (from FY 2024, reports in 2025)
        if ($year >= 2024) {
            // Balance sheet > €25M OR Turnover > €50M AND > 250 employees
            $isLarge = (
                ($this->balance_sheet_total > 25_000_000 || $this->annual_turnover > 50_000_000)
                && ($this->employee_count ?? 0) > 250
            );
            if ($isLarge) {
                return true;
            }
        }

        // Listed SMEs (from FY 2026, reports in 2027)
        if ($year >= 2026) {
            // Listed companies meeting 2 of 3: Balance > €5M, Turnover > €10M, Employees > 10
            $criteria = 0;
            if ($this->balance_sheet_total > 5_000_000) {
                $criteria++;
            }
            if ($this->annual_turnover > 10_000_000) {
                $criteria++;
            }
            if (($this->employee_count ?? 0) > 10) {
                $criteria++;
            }

            // Note: This should also check if the company is publicly listed
            if ($criteria >= 2) {
                return true;
            }
        }

        return $this->csrd_applicable ?? false;
    }

    /**
     * Get CSRD company category.
     */
    public function getCsrdCategoryAttribute(): ?string
    {
        $employees = $this->employee_count ?? 0;
        $turnover = $this->annual_turnover ?? 0;
        $balance = $this->balance_sheet_total ?? 0;

        // Large undertaking
        if ($employees > 250 || $turnover > 50_000_000 || $balance > 25_000_000) {
            return 'large';
        }

        // Small/Medium
        if ($employees > 10 || $turnover > 900_000 || $balance > 450_000) {
            return 'sme';
        }

        return 'micro';
    }

    /**
     * Get CSRD first reporting year based on company category.
     */
    public function getCsrdFirstReportingYearAttribute(): ?int
    {
        $category = $this->csrd_category;

        return match ($category) {
            'large' => 2025, // FY 2024
            'sme' => 2027,   // FY 2026 (if listed)
            default => null,
        };
    }
}
