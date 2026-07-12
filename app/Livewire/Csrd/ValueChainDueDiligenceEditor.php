<?php

namespace App\Livewire\Csrd;

use App\Models\Organization;
use App\Models\ValueChainDueDiligence;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

/**
 * Value Chain Due Diligence Editor Component
 *
 * LkSG / CSDDD compliance management:
 * - Human rights and environmental policies
 * - Risk assessment and prioritization
 * - Prevention measures
 * - Monitoring and grievance mechanisms
 * - Reporting
 */
#[Layout('components.layouts.app')]
#[Title('Value Chain Due Diligence')]
class ValueChainDueDiligenceEditor extends Component
{
    public int $selectedYear;

    public string $activeSection = 'policies';

    // LkSG status
    public bool $lksgApplicable = false;

    #[Validate('nullable|in:not_started,in_progress,compliant,non_compliant')]
    public ?string $lksgStatus = null;

    // Policies
    public bool $hasHumanRightsPolicy = false;

    public ?string $humanRightsPolicyDate = null;

    public bool $hasEnvironmentalPolicy = false;

    public ?string $environmentalPolicyDate = null;

    // Risk assessment
    public array $identifiedRisks = [];

    public array $riskPrioritization = [];

    public array $highRiskCountries = [];

    public array $highRiskSectors = [];

    // Prevention measures
    public array $preventionMeasures = [];

    public array $contractualAssurances = [];

    public bool $supplierCodeOfConduct = false;

    // Monitoring
    public array $monitoringMechanisms = [];

    public int $supplierAuditsConducted = 0;

    public int $suppliersAssessed = 0;

    // Grievance mechanism
    public array $grievanceMechanism = [];

    public bool $hasWhistleblowerChannel = false;

    public int $complaintsReceived = 0;

    public int $complaintsResolved = 0;

    // Reporting
    public bool $annualReportPublished = false;

    public ?string $reportUrl = null;

    protected ?string $recordId = null;

    public function mount(): void
    {
        $this->selectedYear = now()->year;
        $this->loadRecord();
    }

    #[Computed]
    public function organization(): ?Organization
    {
        return Auth::user()?->organization;
    }

    #[Computed]
    public function record(): ?ValueChainDueDiligence
    {
        if (!$this->organization) {
            return null;
        }

        return ValueChainDueDiligence::where('organization_id', $this->organization->id)
            ->forYear($this->selectedYear)
            ->first();
    }

    #[Computed]
    public function riskCategories(): array
    {
        return ValueChainDueDiligence::RISK_CATEGORIES;
    }

    #[Computed]
    public function highRiskCountriesDefault(): array
    {
        return ValueChainDueDiligence::HIGH_RISK_COUNTRIES_DEFAULT;
    }

    #[Computed]
    public function highRiskSectorsDefault(): array
    {
        return ValueChainDueDiligence::HIGH_RISK_SECTORS;
    }

    #[Computed]
    public function lksgStatuses(): array
    {
        return ValueChainDueDiligence::LKSG_STATUSES;
    }

    #[Computed]
    public function complianceScore(): float
    {
        $checks = [
            $this->hasHumanRightsPolicy,
            $this->hasEnvironmentalPolicy,
            count($this->identifiedRisks) > 0,
            count($this->preventionMeasures) > 0,
            $this->supplierCodeOfConduct,
            count($this->monitoringMechanisms) > 0,
            count($this->grievanceMechanism) > 0,
            $this->hasWhistleblowerChannel,
            $this->annualReportPublished,
        ];

        return round(count(array_filter($checks)) / count($checks) * 100, 1);
    }

    #[Computed]
    public function complaintResolutionRate(): ?float
    {
        if ($this->complaintsReceived === 0) {
            return null;
        }

        return round(($this->complaintsResolved / $this->complaintsReceived) * 100, 1);
    }

    #[Computed]
    public function isLksgRequired(): bool
    {
        if (!$this->organization) {
            return false;
        }

        $employees = $this->organization->employee_count ?? 0;

        if ($this->selectedYear >= 2024) {
            return $employees >= 1000;
        }

        if ($this->selectedYear >= 2023) {
            return $employees >= 3000;
        }

        return false;
    }

    public function loadRecord(): void
    {
        $record = $this->record;

        if ($record) {
            $this->recordId = $record->id;
            $this->lksgApplicable = $record->lksg_applicable ?? false;
            $this->lksgStatus = $record->lksg_status;
            $this->hasHumanRightsPolicy = $record->has_human_rights_policy ?? false;
            $this->humanRightsPolicyDate = $record->human_rights_policy_date?->format('Y-m-d');
            $this->hasEnvironmentalPolicy = $record->has_environmental_policy ?? false;
            $this->environmentalPolicyDate = $record->environmental_policy_date?->format('Y-m-d');
            $this->identifiedRisks = $record->identified_risks ?? [];
            $this->riskPrioritization = $record->risk_prioritization ?? [];
            $this->highRiskCountries = $record->high_risk_countries ?? [];
            $this->highRiskSectors = $record->high_risk_sectors ?? [];
            $this->preventionMeasures = $record->prevention_measures ?? [];
            $this->contractualAssurances = $record->contractual_assurances ?? [];
            $this->supplierCodeOfConduct = $record->supplier_code_of_conduct ?? false;
            $this->monitoringMechanisms = $record->monitoring_mechanisms ?? [];
            $this->supplierAuditsConducted = $record->supplier_audits_conducted ?? 0;
            $this->suppliersAssessed = $record->suppliers_assessed ?? 0;
            $this->grievanceMechanism = $record->grievance_mechanism ?? [];
            $this->hasWhistleblowerChannel = $record->has_whistleblower_channel ?? false;
            $this->complaintsReceived = $record->complaints_received ?? 0;
            $this->complaintsResolved = $record->complaints_resolved ?? 0;
            $this->annualReportPublished = $record->annual_report_published ?? false;
            $this->reportUrl = $record->report_url;
        } else {
            // Set default LkSG applicability based on employee count
            $this->lksgApplicable = $this->isLksgRequired;
        }
    }

    public function setYear(int $year): void
    {
        $this->selectedYear = $year;
        unset($this->record);
        $this->loadRecord();
    }

    public function setSection(string $section): void
    {
        $this->activeSection = $section;
    }

    public function addRisk(): void
    {
        $this->identifiedRisks[] = [
            'category' => 'human_rights',
            'subcategory' => '',
            'description' => '',
            'location' => '',
            'priority' => 'medium',
            'likelihood' => 'medium',
            'impact' => 'medium',
        ];
    }

    public function removeRisk(int $index): void
    {
        unset($this->identifiedRisks[$index]);
        $this->identifiedRisks = array_values($this->identifiedRisks);
    }

    public function addPreventionMeasure(): void
    {
        $this->preventionMeasures[] = [
            'name' => '',
            'description' => '',
            'risk_addressed' => '',
            'responsible_person' => '',
            'implementation_date' => '',
            'status' => 'planned',
        ];
    }

    public function removePreventionMeasure(int $index): void
    {
        unset($this->preventionMeasures[$index]);
        $this->preventionMeasures = array_values($this->preventionMeasures);
    }

    public function addContractualAssurance(): void
    {
        $this->contractualAssurances[] = [
            'name' => '',
            'type' => 'supplier_contract',
            'description' => '',
        ];
    }

    public function removeContractualAssurance(int $index): void
    {
        unset($this->contractualAssurances[$index]);
        $this->contractualAssurances = array_values($this->contractualAssurances);
    }

    public function addMonitoringMechanism(): void
    {
        $this->monitoringMechanisms[] = [
            'name' => '',
            'type' => 'audit',
            'frequency' => 'annual',
            'description' => '',
        ];
    }

    public function removeMonitoringMechanism(int $index): void
    {
        unset($this->monitoringMechanisms[$index]);
        $this->monitoringMechanisms = array_values($this->monitoringMechanisms);
    }

    public function toggleHighRiskCountry(string $code): void
    {
        if (in_array($code, $this->highRiskCountries)) {
            $this->highRiskCountries = array_values(array_diff($this->highRiskCountries, [$code]));
        } else {
            $this->highRiskCountries[] = $code;
        }
    }

    public function toggleHighRiskSector(string $sector): void
    {
        if (in_array($sector, $this->highRiskSectors)) {
            $this->highRiskSectors = array_values(array_diff($this->highRiskSectors, [$sector]));
        } else {
            $this->highRiskSectors[] = $sector;
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
            'assessment_year' => $this->selectedYear,
            'lksg_applicable' => $this->lksgApplicable,
            'lksg_status' => $this->lksgStatus,
            'has_human_rights_policy' => $this->hasHumanRightsPolicy,
            'human_rights_policy_date' => $this->humanRightsPolicyDate,
            'has_environmental_policy' => $this->hasEnvironmentalPolicy,
            'environmental_policy_date' => $this->environmentalPolicyDate,
            'identified_risks' => $this->identifiedRisks,
            'risk_prioritization' => $this->riskPrioritization,
            'high_risk_countries' => $this->highRiskCountries,
            'high_risk_sectors' => $this->highRiskSectors,
            'prevention_measures' => $this->preventionMeasures,
            'contractual_assurances' => $this->contractualAssurances,
            'supplier_code_of_conduct' => $this->supplierCodeOfConduct,
            'monitoring_mechanisms' => $this->monitoringMechanisms,
            'supplier_audits_conducted' => $this->supplierAuditsConducted,
            'suppliers_assessed' => $this->suppliersAssessed,
            'grievance_mechanism' => $this->grievanceMechanism,
            'has_whistleblower_channel' => $this->hasWhistleblowerChannel,
            'complaints_received' => $this->complaintsReceived,
            'complaints_resolved' => $this->complaintsResolved,
            'annual_report_published' => $this->annualReportPublished,
            'report_url' => $this->reportUrl,
            'responsible_person_id' => Auth::id(),
        ];

        // Determine LkSG status based on compliance score
        if ($this->complianceScore >= 90) {
            $data['lksg_status'] = 'compliant';
        } elseif ($this->complianceScore > 0) {
            $data['lksg_status'] = 'in_progress';
        }

        if ($this->recordId) {
            ValueChainDueDiligence::where('id', $this->recordId)->update($data);
            $message = __('Due diligence record updated successfully');
        } else {
            $record = ValueChainDueDiligence::create($data);
            $this->recordId = $record->id;
            $message = __('Due diligence record created successfully');
        }

        unset($this->record);

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => $message,
        ]);
    }

    public function markAsReviewed(): void
    {
        if (!$this->recordId) {
            return;
        }

        ValueChainDueDiligence::where('id', $this->recordId)->update([
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        unset($this->record);

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => __('Record marked as reviewed'),
        ]);
    }

    public function render()
    {
        return view('livewire.csrd.value-chain-due-diligence-editor');
    }
}
