<?php

namespace App\Livewire\Csrd;

use App\Models\EuTaxonomyReport;
use App\Models\Organization;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

/**
 * EU Taxonomy Report Editor Component
 *
 * Article 8 Disclosure management:
 * - KPIs (Turnover, CapEx, OpEx)
 * - Environmental objectives contribution
 * - DNSH assessment
 * - Minimum safeguards compliance
 */
#[Layout('components.layouts.app')]
#[Title('EU Taxonomy Report')]
class EuTaxonomyReportEditor extends Component
{
    public int $selectedYear;

    public string $activeSection = 'kpis';

    // KPIs
    #[Validate('nullable|numeric|min:0')]
    public ?float $turnoverTotal = null;

    #[Validate('nullable|numeric|min:0')]
    public ?float $turnoverEligible = null;

    #[Validate('nullable|numeric|min:0')]
    public ?float $turnoverAligned = null;

    #[Validate('nullable|numeric|min:0')]
    public ?float $capexTotal = null;

    #[Validate('nullable|numeric|min:0')]
    public ?float $capexEligible = null;

    #[Validate('nullable|numeric|min:0')]
    public ?float $capexAligned = null;

    #[Validate('nullable|numeric|min:0')]
    public ?float $opexTotal = null;

    #[Validate('nullable|numeric|min:0')]
    public ?float $opexEligible = null;

    #[Validate('nullable|numeric|min:0')]
    public ?float $opexAligned = null;

    // Environmental objectives contribution
    public bool $contributesClimateMitigation = false;

    public bool $contributesClimateAdaptation = false;

    public bool $contributesWater = false;

    public bool $contributesCircularEconomy = false;

    public bool $contributesPollution = false;

    public bool $contributesBiodiversity = false;

    // DNSH assessment
    public bool $dnshClimateMitigation = false;

    public bool $dnshClimateAdaptation = false;

    public bool $dnshWater = false;

    public bool $dnshCircularEconomy = false;

    public bool $dnshPollution = false;

    public bool $dnshBiodiversity = false;

    // Minimum safeguards
    public bool $oecdGuidelinesCompliant = false;

    public bool $unGuidingPrinciplesCompliant = false;

    public bool $iloConventionsCompliant = false;

    public bool $humanRightsDeclarationCompliant = false;

    // Activities
    public array $eligibleActivities = [];

    public array $alignedActivities = [];

    // Methodology
    public ?string $methodologyDescription = null;

    public array $dataSources = [];

    protected ?string $reportId = null;

    public function mount(): void
    {
        $this->selectedYear = now()->year - 1; // Reporting is for previous year
        $this->loadReport();
    }

    #[Computed]
    public function organization(): ?Organization
    {
        return Auth::user()?->organization;
    }

    #[Computed]
    public function report(): ?EuTaxonomyReport
    {
        if (!$this->organization) {
            return null;
        }

        return EuTaxonomyReport::where('organization_id', $this->organization->id)
            ->forYear($this->selectedYear)
            ->first();
    }

    #[Computed]
    public function environmentalObjectives(): array
    {
        return EuTaxonomyReport::ENVIRONMENTAL_OBJECTIVES;
    }

    #[Computed]
    public function commonActivities(): array
    {
        return EuTaxonomyReport::COMMON_ACTIVITIES;
    }

    #[Computed]
    public function turnoverEligiblePercent(): float
    {
        if (!$this->turnoverTotal || $this->turnoverTotal <= 0) {
            return 0;
        }
        return round(($this->turnoverEligible ?? 0) / $this->turnoverTotal * 100, 2);
    }

    #[Computed]
    public function turnoverAlignedPercent(): float
    {
        if (!$this->turnoverTotal || $this->turnoverTotal <= 0) {
            return 0;
        }
        return round(($this->turnoverAligned ?? 0) / $this->turnoverTotal * 100, 2);
    }

    #[Computed]
    public function capexEligiblePercent(): float
    {
        if (!$this->capexTotal || $this->capexTotal <= 0) {
            return 0;
        }
        return round(($this->capexEligible ?? 0) / $this->capexTotal * 100, 2);
    }

    #[Computed]
    public function capexAlignedPercent(): float
    {
        if (!$this->capexTotal || $this->capexTotal <= 0) {
            return 0;
        }
        return round(($this->capexAligned ?? 0) / $this->capexTotal * 100, 2);
    }

    #[Computed]
    public function opexEligiblePercent(): float
    {
        if (!$this->opexTotal || $this->opexTotal <= 0) {
            return 0;
        }
        return round(($this->opexEligible ?? 0) / $this->opexTotal * 100, 2);
    }

    #[Computed]
    public function opexAlignedPercent(): float
    {
        if (!$this->opexTotal || $this->opexTotal <= 0) {
            return 0;
        }
        return round(($this->opexAligned ?? 0) / $this->opexTotal * 100, 2);
    }

    #[Computed]
    public function minimumSafeguardsMet(): bool
    {
        return $this->oecdGuidelinesCompliant
            && $this->unGuidingPrinciplesCompliant
            && $this->iloConventionsCompliant
            && $this->humanRightsDeclarationCompliant;
    }

    #[Computed]
    public function dnshMet(): bool
    {
        return $this->dnshClimateMitigation
            && $this->dnshClimateAdaptation
            && $this->dnshWater
            && $this->dnshCircularEconomy
            && $this->dnshPollution
            && $this->dnshBiodiversity;
    }

    public function loadReport(): void
    {
        $report = $this->report;

        if ($report) {
            $this->reportId = $report->id;
            $this->turnoverTotal = $report->turnover_total;
            $this->turnoverEligible = $report->turnover_eligible;
            $this->turnoverAligned = $report->turnover_aligned;
            $this->capexTotal = $report->capex_total;
            $this->capexEligible = $report->capex_eligible;
            $this->capexAligned = $report->capex_aligned;
            $this->opexTotal = $report->opex_total;
            $this->opexEligible = $report->opex_eligible;
            $this->opexAligned = $report->opex_aligned;
            $this->contributesClimateMitigation = $report->contributes_climate_mitigation ?? false;
            $this->contributesClimateAdaptation = $report->contributes_climate_adaptation ?? false;
            $this->contributesWater = $report->contributes_water ?? false;
            $this->contributesCircularEconomy = $report->contributes_circular_economy ?? false;
            $this->contributesPollution = $report->contributes_pollution ?? false;
            $this->contributesBiodiversity = $report->contributes_biodiversity ?? false;
            $this->dnshClimateMitigation = $report->dnsh_climate_mitigation ?? false;
            $this->dnshClimateAdaptation = $report->dnsh_climate_adaptation ?? false;
            $this->dnshWater = $report->dnsh_water ?? false;
            $this->dnshCircularEconomy = $report->dnsh_circular_economy ?? false;
            $this->dnshPollution = $report->dnsh_pollution ?? false;
            $this->dnshBiodiversity = $report->dnsh_biodiversity ?? false;
            $this->oecdGuidelinesCompliant = $report->oecd_guidelines_compliant ?? false;
            $this->unGuidingPrinciplesCompliant = $report->un_guiding_principles_compliant ?? false;
            $this->iloConventionsCompliant = $report->ilo_conventions_compliant ?? false;
            $this->humanRightsDeclarationCompliant = $report->human_rights_declaration_compliant ?? false;
            $this->eligibleActivities = $report->eligible_activities ?? [];
            $this->alignedActivities = $report->aligned_activities ?? [];
            $this->methodologyDescription = $report->methodology_description;
            $this->dataSources = $report->data_sources ?? [];
        }
    }

    public function setYear(int $year): void
    {
        $this->selectedYear = $year;
        unset($this->report);
        $this->loadReport();
    }

    public function setSection(string $section): void
    {
        $this->activeSection = $section;
    }

    public function toggleActivity(string $type, string $activityCode): void
    {
        if ($type === 'eligible') {
            if (in_array($activityCode, $this->eligibleActivities)) {
                $this->eligibleActivities = array_values(array_diff($this->eligibleActivities, [$activityCode]));
            } else {
                $this->eligibleActivities[] = $activityCode;
            }
        } else {
            if (in_array($activityCode, $this->alignedActivities)) {
                $this->alignedActivities = array_values(array_diff($this->alignedActivities, [$activityCode]));
            } else {
                $this->alignedActivities[] = $activityCode;
            }
        }
    }

    public function addDataSource(): void
    {
        $this->dataSources[] = [
            'name' => '',
            'type' => 'internal',
            'description' => '',
        ];
    }

    public function removeDataSource(int $index): void
    {
        unset($this->dataSources[$index]);
        $this->dataSources = array_values($this->dataSources);
    }

    public function save(): void
    {
        $this->validate();

        if (!$this->organization) {
            return;
        }

        $data = [
            'organization_id' => $this->organization->id,
            'reporting_year' => $this->selectedYear,
            'turnover_total' => $this->turnoverTotal,
            'turnover_eligible' => $this->turnoverEligible,
            'turnover_aligned' => $this->turnoverAligned,
            'turnover_eligible_percent' => $this->turnoverEligiblePercent,
            'turnover_aligned_percent' => $this->turnoverAlignedPercent,
            'capex_total' => $this->capexTotal,
            'capex_eligible' => $this->capexEligible,
            'capex_aligned' => $this->capexAligned,
            'capex_eligible_percent' => $this->capexEligiblePercent,
            'capex_aligned_percent' => $this->capexAlignedPercent,
            'opex_total' => $this->opexTotal,
            'opex_eligible' => $this->opexEligible,
            'opex_aligned' => $this->opexAligned,
            'opex_eligible_percent' => $this->opexEligiblePercent,
            'opex_aligned_percent' => $this->opexAlignedPercent,
            'contributes_climate_mitigation' => $this->contributesClimateMitigation,
            'contributes_climate_adaptation' => $this->contributesClimateAdaptation,
            'contributes_water' => $this->contributesWater,
            'contributes_circular_economy' => $this->contributesCircularEconomy,
            'contributes_pollution' => $this->contributesPollution,
            'contributes_biodiversity' => $this->contributesBiodiversity,
            'dnsh_climate_mitigation' => $this->dnshClimateMitigation,
            'dnsh_climate_adaptation' => $this->dnshClimateAdaptation,
            'dnsh_water' => $this->dnshWater,
            'dnsh_circular_economy' => $this->dnshCircularEconomy,
            'dnsh_pollution' => $this->dnshPollution,
            'dnsh_biodiversity' => $this->dnshBiodiversity,
            'oecd_guidelines_compliant' => $this->oecdGuidelinesCompliant,
            'un_guiding_principles_compliant' => $this->unGuidingPrinciplesCompliant,
            'ilo_conventions_compliant' => $this->iloConventionsCompliant,
            'human_rights_declaration_compliant' => $this->humanRightsDeclarationCompliant,
            'eligible_activities' => $this->eligibleActivities,
            'aligned_activities' => $this->alignedActivities,
            'methodology_description' => $this->methodologyDescription,
            'data_sources' => $this->dataSources,
            'prepared_by' => Auth::id(),
        ];

        if ($this->reportId) {
            EuTaxonomyReport::where('id', $this->reportId)->update($data);
            $message = __('EU Taxonomy report updated successfully');
        } else {
            $report = EuTaxonomyReport::create($data);
            $this->reportId = $report->id;
            $message = __('EU Taxonomy report created successfully');
        }

        unset($this->report);

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => $message,
        ]);
    }

    public function verify(): void
    {
        if (!$this->reportId) {
            return;
        }

        EuTaxonomyReport::where('id', $this->reportId)->update([
            'verified_by' => Auth::id(),
            'verified_at' => now(),
        ]);

        unset($this->report);

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => __('Report verified successfully'),
        ]);
    }

    public function render()
    {
        return view('livewire.csrd.eu-taxonomy-report-editor');
    }
}
