<?php

namespace App\Livewire\Csrd;

use App\Models\ClimateTransitionPlan;
use App\Models\Organization;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

/**
 * Climate Transition Plan Editor Component
 *
 * ESRS E1-1 Climate Transition Plan management:
 * - Paris-aligned temperature targets
 * - SBTi commitment tracking
 * - Base year and emissions
 * - Interim and net-zero targets
 * - Decarbonization levers
 * - Governance and financial planning
 */
#[Layout('components.layouts.app')]
#[Title('Climate Transition Plan')]
class ClimateTransitionPlanEditor extends Component
{
    public int $selectedYear;

    public string $activeSection = 'targets';

    // Form fields
    #[Validate('required|in:draft,approved,published,under_review')]
    public string $status = 'draft';

    #[Validate('required|in:1.5C,well_below_2C,2C')]
    public string $temperatureTarget = '1.5C';

    public bool $isParisAligned = false;

    public bool $isSbtiCommitted = false;

    public bool $isSbtiValidated = false;

    public ?string $sbtiCommitmentDate = null;

    public ?string $sbtiValidationDate = null;

    #[Validate('required|integer|min:2015|max:2030')]
    public ?int $baseYear = null;

    public ?float $baseYearEmissionsScope1 = null;

    public ?float $baseYearEmissionsScope2 = null;

    public ?float $baseYearEmissionsScope3 = null;

    public array $interimTargets = [];

    public ?int $netZeroTargetYear = null;

    public ?float $netZeroResidualEmissionsPercent = null;

    public array $decarbonizationLevers = [];

    public ?float $plannedCapexClimate = null;

    public ?float $plannedOpexClimate = null;

    public ?float $internalCarbonPrice = null;

    public string $carbonPriceCurrency = 'EUR';

    public ?float $lockedInEmissionsTco2e = null;

    public ?string $lockedInEmissionsDescription = null;

    public bool $usesCarbonCredits = false;

    public ?string $carbonCreditsPolicy = null;

    public ?float $carbonCreditsMaxPercent = null;

    public ?string $boardOversightDescription = null;

    public ?string $managementAccountability = null;

    public bool $linkedToRemuneration = false;

    public ?string $remunerationDescription = null;

    public array $transitionRisks = [];

    public array $physicalRisks = [];

    public array $climateOpportunities = [];

    public ?float $estimatedTransitionCost = null;

    public ?float $estimatedStrandedAssets = null;

    protected ?string $planId = null;

    public function mount(): void
    {
        $this->selectedYear = now()->year;
        $this->baseYear = now()->year - 1;
        $this->netZeroTargetYear = 2050;
        $this->loadPlan();
    }

    #[Computed]
    public function organization(): ?Organization
    {
        return Auth::user()?->organization;
    }

    #[Computed]
    public function plan(): ?ClimateTransitionPlan
    {
        if (!$this->organization) {
            return null;
        }

        return ClimateTransitionPlan::where('organization_id', $this->organization->id)
            ->forYear($this->selectedYear)
            ->first();
    }

    #[Computed]
    public function temperatureTargets(): array
    {
        return ClimateTransitionPlan::TEMPERATURE_TARGETS;
    }

    #[Computed]
    public function availableLevers(): array
    {
        return ClimateTransitionPlan::DECARBONIZATION_LEVERS;
    }

    #[Computed]
    public function statuses(): array
    {
        return ClimateTransitionPlan::STATUSES;
    }

    #[Computed]
    public function baseYearEmissionsTotal(): float
    {
        return ($this->baseYearEmissionsScope1 ?? 0) +
            ($this->baseYearEmissionsScope2 ?? 0) +
            ($this->baseYearEmissionsScope3 ?? 0);
    }

    #[Computed]
    public function requiredAnnualReduction(): ?float
    {
        if (!$this->baseYear || !$this->netZeroTargetYear || $this->baseYearEmissionsTotal <= 0) {
            return null;
        }

        $years = $this->netZeroTargetYear - $this->baseYear;
        if ($years <= 0) {
            return null;
        }

        $residualPercent = $this->netZeroResidualEmissionsPercent ?? 10;
        $targetReduction = (100 - $residualPercent) / 100;

        return round((1 - pow(1 - $targetReduction, 1 / $years)) * 100, 2);
    }

    #[Computed]
    public function isSbtiCompliant(): bool
    {
        if (!$this->isSbtiCommitted && !$this->isSbtiValidated) {
            return false;
        }

        if (!in_array($this->temperatureTarget, ['1.5C', 'well_below_2C'])) {
            return false;
        }

        if ($this->netZeroTargetYear > 2050) {
            return false;
        }

        if (($this->netZeroResidualEmissionsPercent ?? 0) > 10) {
            return false;
        }

        return true;
    }

    public function loadPlan(): void
    {
        $plan = $this->plan;

        if ($plan) {
            $this->planId = $plan->id;
            $this->status = $plan->status ?? 'draft';
            $this->temperatureTarget = $plan->temperature_target ?? '1.5C';
            $this->isParisAligned = $plan->is_paris_aligned ?? false;
            $this->isSbtiCommitted = $plan->is_sbti_committed ?? false;
            $this->isSbtiValidated = $plan->is_sbti_validated ?? false;
            $this->sbtiCommitmentDate = $plan->sbti_commitment_date?->format('Y-m-d');
            $this->sbtiValidationDate = $plan->sbti_validation_date?->format('Y-m-d');
            $this->baseYear = $plan->base_year;
            $this->baseYearEmissionsScope1 = $plan->base_year_emissions_scope1;
            $this->baseYearEmissionsScope2 = $plan->base_year_emissions_scope2;
            $this->baseYearEmissionsScope3 = $plan->base_year_emissions_scope3;
            $this->interimTargets = $plan->interim_targets ?? [];
            $this->netZeroTargetYear = $plan->net_zero_target_year;
            $this->netZeroResidualEmissionsPercent = $plan->net_zero_residual_emissions_percent;
            $this->decarbonizationLevers = $plan->decarbonization_levers ?? [];
            $this->plannedCapexClimate = $plan->planned_capex_climate;
            $this->plannedOpexClimate = $plan->planned_opex_climate;
            $this->internalCarbonPrice = $plan->internal_carbon_price;
            $this->carbonPriceCurrency = $plan->carbon_price_currency ?? 'EUR';
            $this->lockedInEmissionsTco2e = $plan->locked_in_emissions_tco2e;
            $this->lockedInEmissionsDescription = $plan->locked_in_emissions_description;
            $this->usesCarbonCredits = $plan->uses_carbon_credits ?? false;
            $this->carbonCreditsPolicy = $plan->carbon_credits_policy;
            $this->carbonCreditsMaxPercent = $plan->carbon_credits_max_percent;
            $this->boardOversightDescription = $plan->board_oversight_description;
            $this->managementAccountability = $plan->management_accountability;
            $this->linkedToRemuneration = $plan->linked_to_remuneration ?? false;
            $this->remunerationDescription = $plan->remuneration_description;
            $this->transitionRisks = $plan->transition_risks ?? [];
            $this->physicalRisks = $plan->physical_risks ?? [];
            $this->climateOpportunities = $plan->climate_opportunities ?? [];
            $this->estimatedTransitionCost = $plan->estimated_transition_cost;
            $this->estimatedStrandedAssets = $plan->estimated_stranded_assets;
        }
    }

    public function setYear(int $year): void
    {
        $this->selectedYear = $year;
        unset($this->plan);
        $this->loadPlan();
    }

    public function setSection(string $section): void
    {
        $this->activeSection = $section;
    }

    public function addInterimTarget(): void
    {
        $this->interimTargets[] = [
            'year' => now()->year + 5,
            'reduction_percent' => 30,
            'scope' => 'all',
            'description' => '',
        ];
    }

    public function removeInterimTarget(int $index): void
    {
        unset($this->interimTargets[$index]);
        $this->interimTargets = array_values($this->interimTargets);
    }

    public function toggleLever(string $lever): void
    {
        if (in_array($lever, $this->decarbonizationLevers)) {
            $this->decarbonizationLevers = array_values(array_diff($this->decarbonizationLevers, [$lever]));
        } else {
            $this->decarbonizationLevers[] = $lever;
        }
    }

    public function addRisk(string $type): void
    {
        $risk = [
            'name' => '',
            'description' => '',
            'likelihood' => 'medium',
            'impact' => 'medium',
            'mitigation' => '',
        ];

        if ($type === 'transition') {
            $this->transitionRisks[] = $risk;
        } elseif ($type === 'physical') {
            $this->physicalRisks[] = $risk;
        } else {
            $this->climateOpportunities[] = [
                'name' => '',
                'description' => '',
                'potential_value' => null,
            ];
        }
    }

    public function removeRisk(string $type, int $index): void
    {
        if ($type === 'transition') {
            unset($this->transitionRisks[$index]);
            $this->transitionRisks = array_values($this->transitionRisks);
        } elseif ($type === 'physical') {
            unset($this->physicalRisks[$index]);
            $this->physicalRisks = array_values($this->physicalRisks);
        } else {
            unset($this->climateOpportunities[$index]);
            $this->climateOpportunities = array_values($this->climateOpportunities);
        }
    }

    public function save(): void
    {
        $this->validate();

        if (!$this->organization) {
            return;
        }

        $data = [
            'organization_id' => $this->organization->id,
            'plan_year' => $this->selectedYear,
            'status' => $this->status,
            'temperature_target' => $this->temperatureTarget,
            'is_paris_aligned' => $this->isParisAligned,
            'is_sbti_committed' => $this->isSbtiCommitted,
            'is_sbti_validated' => $this->isSbtiValidated,
            'sbti_commitment_date' => $this->sbtiCommitmentDate,
            'sbti_validation_date' => $this->sbtiValidationDate,
            'base_year' => $this->baseYear,
            'base_year_emissions_scope1' => $this->baseYearEmissionsScope1,
            'base_year_emissions_scope2' => $this->baseYearEmissionsScope2,
            'base_year_emissions_scope3' => $this->baseYearEmissionsScope3,
            'base_year_emissions_total' => $this->baseYearEmissionsTotal,
            'interim_targets' => $this->interimTargets,
            'net_zero_target_year' => $this->netZeroTargetYear,
            'net_zero_residual_emissions_percent' => $this->netZeroResidualEmissionsPercent,
            'decarbonization_levers' => $this->decarbonizationLevers,
            'planned_capex_climate' => $this->plannedCapexClimate,
            'planned_opex_climate' => $this->plannedOpexClimate,
            'internal_carbon_price' => $this->internalCarbonPrice,
            'carbon_price_currency' => $this->carbonPriceCurrency,
            'locked_in_emissions_tco2e' => $this->lockedInEmissionsTco2e,
            'locked_in_emissions_description' => $this->lockedInEmissionsDescription,
            'uses_carbon_credits' => $this->usesCarbonCredits,
            'carbon_credits_policy' => $this->carbonCreditsPolicy,
            'carbon_credits_max_percent' => $this->carbonCreditsMaxPercent,
            'board_oversight_description' => $this->boardOversightDescription,
            'management_accountability' => $this->managementAccountability,
            'linked_to_remuneration' => $this->linkedToRemuneration,
            'remuneration_description' => $this->remunerationDescription,
            'transition_risks' => $this->transitionRisks,
            'physical_risks' => $this->physicalRisks,
            'climate_opportunities' => $this->climateOpportunities,
            'estimated_transition_cost' => $this->estimatedTransitionCost,
            'estimated_stranded_assets' => $this->estimatedStrandedAssets,
            'prepared_by' => Auth::id(),
        ];

        if ($this->planId) {
            ClimateTransitionPlan::where('id', $this->planId)->update($data);
            $message = __('Transition plan updated successfully');
        } else {
            $plan = ClimateTransitionPlan::create($data);
            $this->planId = $plan->id;
            $message = __('Transition plan created successfully');
        }

        unset($this->plan);

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => $message,
        ]);
    }

    public function approve(): void
    {
        if (!$this->planId) {
            return;
        }

        ClimateTransitionPlan::where('id', $this->planId)->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        $this->status = 'approved';
        unset($this->plan);

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => __('Transition plan approved'),
        ]);
    }

    public function render()
    {
        return view('livewire.csrd.climate-transition-plan-editor');
    }
}
