<?php

namespace App\Livewire\Compliance;

use App\Models\Assessment;
use App\Models\Organization;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

/**
 * ComplianceChecklist Livewire Component
 *
 * Dynamic compliance checklist based on organization size and sector.
 * Covers CSRD, ISO 14001/14064-1, and other regulatory requirements.
 *
 * Part of Phase 10: Extended regulatory compliance (T177-T179).
 *
 * @see specs/001-linscarbon-mvp-platform/tasks.md T177-T179
 */
#[Layout('layouts.app')]
#[Title('Conformité réglementaire - LinsCarbon')]
class ComplianceChecklist extends Component
{
    public string $selectedFramework = 'all';

    public array $frameworks = [];

    public array $checklist = [];

    public array $deadlines = [];

    public array $organizationProfile = [];

    public bool $showCsrdDetails = false;

    public bool $showIsoDetails = false;

    public function mount(): void
    {
        $this->loadOrganizationProfile();
        $this->loadFrameworks();
        $this->loadChecklist();
        $this->loadDeadlines();
    }

    protected function loadOrganizationProfile(): void
    {
        $organization = Auth::user()?->organization;

        if (! $organization) {
            return;
        }

        $employeeCount = $organization->employee_count ?? 0;
        $annualRevenue = $organization->annual_revenue ?? 0;

        // Determine company size category
        $sizeCategory = match (true) {
            $employeeCount >= 500 || $annualRevenue >= 50000000 => 'large',
            $employeeCount >= 250 || $annualRevenue >= 40000000 => 'medium_large',
            $employeeCount >= 50 || $annualRevenue >= 10000000 => 'medium',
            default => 'small',
        };

        $this->organizationProfile = [
            'name' => $organization->name,
            'sector' => $organization->sector,
            'country' => $organization->country,
            'employee_count' => $employeeCount,
            'annual_revenue' => $annualRevenue,
            'size_category' => $sizeCategory,
            'is_listed' => $organization->settings['is_listed'] ?? false,
            'has_csrd_obligation' => $this->hasCsrdObligation($sizeCategory, $organization),
        ];
    }

    protected function hasCsrdObligation(string $sizeCategory, Organization $organization): bool
    {
        // CSRD applies to:
        // - All large companies (>500 employees or >€50M turnover)
        // - All listed companies (except micro)
        // - From 2025: All companies >250 employees

        if ($sizeCategory === 'large') {
            return true;
        }

        if ($organization->settings['is_listed'] ?? false) {
            return true;
        }

        return $sizeCategory === 'medium_large';
    }

    protected function loadFrameworks(): void
    {
        $this->frameworks = [
            'csrd' => [
                'code' => 'csrd',
                'name' => 'CSRD / ESRS',
                'full_name' => 'Corporate Sustainability Reporting Directive',
                'description' => __('linscarbon.compliance.frameworks.csrd_desc'),
                'applicable' => $this->organizationProfile['has_csrd_obligation'] ?? false,
                'icon' => 'document-text',
                'color' => 'blue',
            ],
            'iso14064' => [
                'code' => 'iso14064',
                'name' => 'ISO 14064-1',
                'full_name' => 'Greenhouse Gas Accounting & Verification',
                'description' => __('linscarbon.compliance.frameworks.iso14064_desc'),
                'applicable' => true,
                'icon' => 'shield-check',
                'color' => 'green',
            ],
            'iso14001' => [
                'code' => 'iso14001',
                'name' => 'ISO 14001',
                'full_name' => 'Environmental Management System',
                'description' => __('linscarbon.compliance.frameworks.iso14001_desc'),
                'applicable' => true,
                'icon' => 'cog',
                'color' => 'teal',
            ],
            'beges' => [
                'code' => 'beges',
                'name' => 'BEGES',
                'full_name' => 'Bilan des Émissions de GES',
                'description' => __('linscarbon.compliance.frameworks.beges_desc'),
                'applicable' => ($this->organizationProfile['country'] ?? '') === 'FR'
                    && ($this->organizationProfile['employee_count'] ?? 0) >= 500,
                'icon' => 'chart-bar',
                'color' => 'purple',
            ],
            'ghg' => [
                'code' => 'ghg',
                'name' => 'GHG Protocol',
                'full_name' => 'Greenhouse Gas Protocol',
                'description' => __('linscarbon.compliance.frameworks.ghg_desc'),
                'applicable' => true,
                'icon' => 'globe',
                'color' => 'emerald',
            ],
        ];
    }

    protected function loadChecklist(): void
    {
        $organization = Auth::user()?->organization;
        $latestAssessment = $organization?->assessments()->latest()->first();

        $this->checklist = [];

        // CSRD / ESRS E1 Requirements
        if ($this->organizationProfile['has_csrd_obligation'] ?? false) {
            $this->checklist['csrd'] = $this->getCsrdChecklist($organization, $latestAssessment);
        }

        // ISO 14064-1 Requirements
        $this->checklist['iso14064'] = $this->getIso14064Checklist($organization, $latestAssessment);

        // ISO 14001 Requirements
        $this->checklist['iso14001'] = $this->getIso14001Checklist($organization, $latestAssessment);

        // BEGES Requirements (France only)
        if (($this->organizationProfile['country'] ?? '') === 'FR') {
            $this->checklist['beges'] = $this->getBegesChecklist($organization, $latestAssessment);
        }

        // GHG Protocol
        $this->checklist['ghg'] = $this->getGhgChecklist($organization, $latestAssessment);
    }

    protected function getCsrdChecklist(?Organization $organization, ?Assessment $assessment): array
    {
        $hasScope1 = $assessment && $assessment->scope1_total > 0;
        $hasScope2 = $assessment && $assessment->scope2_total > 0;
        $hasScope3 = $assessment && $assessment->scope3_total > 0;
        $hasTargets = $organization?->reductionTargets()->count() > 0;
        $hasActions = $organization?->actions()->count() > 0;

        return [
            'name' => 'CSRD / ESRS E1',
            'items' => [
                [
                    'code' => 'E1-1',
                    'title' => __('linscarbon.compliance.csrd.e1_1'),
                    'description' => __('linscarbon.compliance.csrd.e1_1_desc'),
                    'completed' => $hasTargets,
                    'priority' => 'high',
                ],
                [
                    'code' => 'E1-2',
                    'title' => __('linscarbon.compliance.csrd.e1_2'),
                    'description' => __('linscarbon.compliance.csrd.e1_2_desc'),
                    'completed' => $hasActions,
                    'priority' => 'high',
                ],
                [
                    'code' => 'E1-4',
                    'title' => __('linscarbon.compliance.csrd.e1_4'),
                    'description' => __('linscarbon.compliance.csrd.e1_4_desc'),
                    'completed' => $hasScope1 && $hasScope2,
                    'priority' => 'high',
                ],
                [
                    'code' => 'E1-5',
                    'title' => __('linscarbon.compliance.csrd.e1_5'),
                    'description' => __('linscarbon.compliance.csrd.e1_5_desc'),
                    'completed' => false, // Would need energy consumption data
                    'priority' => 'medium',
                ],
                [
                    'code' => 'E1-6',
                    'title' => __('linscarbon.compliance.csrd.e1_6'),
                    'description' => __('linscarbon.compliance.csrd.e1_6_desc'),
                    'completed' => $hasScope1 && $hasScope2 && $hasScope3,
                    'priority' => 'high',
                ],
                [
                    'code' => 'E1-7',
                    'title' => __('linscarbon.compliance.csrd.e1_7'),
                    'description' => __('linscarbon.compliance.csrd.e1_7_desc'),
                    'completed' => false, // Would need carbon credit data
                    'priority' => 'low',
                ],
                [
                    'code' => 'E1-9',
                    'title' => __('linscarbon.compliance.csrd.e1_9'),
                    'description' => __('linscarbon.compliance.csrd.e1_9_desc'),
                    'completed' => false, // Would need financial impact data
                    'priority' => 'medium',
                ],
            ],
        ];
    }

    protected function getIso14064Checklist(?Organization $organization, ?Assessment $assessment): array
    {
        $hasAssessment = $assessment !== null;
        $hasScope1 = $assessment && $assessment->scope1_total > 0;
        $hasScope2 = $assessment && $assessment->scope2_total > 0;

        return [
            'name' => 'ISO 14064-1',
            'items' => [
                [
                    'code' => '4.1',
                    'title' => __('linscarbon.compliance.iso14064.boundaries'),
                    'description' => __('linscarbon.compliance.iso14064.boundaries_desc'),
                    'completed' => $organization?->sites()->count() > 0,
                    'priority' => 'high',
                ],
                [
                    'code' => '4.2',
                    'title' => __('linscarbon.compliance.iso14064.sources'),
                    'description' => __('linscarbon.compliance.iso14064.sources_desc'),
                    'completed' => $hasScope1,
                    'priority' => 'high',
                ],
                [
                    'code' => '5.1',
                    'title' => __('linscarbon.compliance.iso14064.quantification'),
                    'description' => __('linscarbon.compliance.iso14064.quantification_desc'),
                    'completed' => $hasAssessment,
                    'priority' => 'high',
                ],
                [
                    'code' => '5.2',
                    'title' => __('linscarbon.compliance.iso14064.emission_factors'),
                    'description' => __('linscarbon.compliance.iso14064.emission_factors_desc'),
                    'completed' => true, // Using ADEME factors
                    'priority' => 'medium',
                ],
                [
                    'code' => '6.1',
                    'title' => __('linscarbon.compliance.iso14064.documentation'),
                    'description' => __('linscarbon.compliance.iso14064.documentation_desc'),
                    'completed' => $organization?->reports()->count() > 0,
                    'priority' => 'medium',
                ],
                [
                    'code' => '7.1',
                    'title' => __('linscarbon.compliance.iso14064.verification'),
                    'description' => __('linscarbon.compliance.iso14064.verification_desc'),
                    'completed' => false, // Would need external verification
                    'priority' => 'low',
                ],
            ],
        ];
    }

    protected function getIso14001Checklist(?Organization $organization, ?Assessment $assessment): array
    {
        $hasAssessment = $assessment !== null;
        $hasTargets = $organization?->reductionTargets()->count() > 0;
        $hasActions = $organization?->actions()->count() > 0;

        return [
            'name' => 'ISO 14001',
            'items' => [
                [
                    'code' => '4.1',
                    'title' => __('linscarbon.compliance.iso14001.context'),
                    'description' => __('linscarbon.compliance.iso14001.context_desc'),
                    'completed' => $organization !== null,
                    'priority' => 'high',
                ],
                [
                    'code' => '6.1.2',
                    'title' => __('linscarbon.compliance.iso14001.aspects'),
                    'description' => __('linscarbon.compliance.iso14001.aspects_desc'),
                    'completed' => $hasAssessment,
                    'priority' => 'high',
                ],
                [
                    'code' => '6.2',
                    'title' => __('linscarbon.compliance.iso14001.objectives'),
                    'description' => __('linscarbon.compliance.iso14001.objectives_desc'),
                    'completed' => $hasTargets,
                    'priority' => 'high',
                ],
                [
                    'code' => '8.1',
                    'title' => __('linscarbon.compliance.iso14001.operational_control'),
                    'description' => __('linscarbon.compliance.iso14001.operational_control_desc'),
                    'completed' => $hasActions,
                    'priority' => 'medium',
                ],
                [
                    'code' => '9.1',
                    'title' => __('linscarbon.compliance.iso14001.monitoring'),
                    'description' => __('linscarbon.compliance.iso14001.monitoring_desc'),
                    'completed' => $hasAssessment,
                    'priority' => 'medium',
                ],
                [
                    'code' => '10.2',
                    'title' => __('linscarbon.compliance.iso14001.continual_improvement'),
                    'description' => __('linscarbon.compliance.iso14001.continual_improvement_desc'),
                    'completed' => $hasTargets && $hasActions,
                    'priority' => 'high',
                ],
            ],
        ];
    }

    protected function getBegesChecklist(?Organization $organization, ?Assessment $assessment): array
    {
        $hasAssessment = $assessment !== null;
        $hasScope1 = $assessment && $assessment->scope1_total > 0;
        $hasScope2 = $assessment && $assessment->scope2_total > 0;

        return [
            'name' => 'BEGES (France)',
            'items' => [
                [
                    'code' => 'ART-1',
                    'title' => __('linscarbon.compliance.beges.scope12'),
                    'description' => __('linscarbon.compliance.beges.scope12_desc'),
                    'completed' => $hasScope1 && $hasScope2,
                    'priority' => 'high',
                ],
                [
                    'code' => 'ART-2',
                    'title' => __('linscarbon.compliance.beges.action_plan'),
                    'description' => __('linscarbon.compliance.beges.action_plan_desc'),
                    'completed' => $organization?->actions()->count() > 0,
                    'priority' => 'high',
                ],
                [
                    'code' => 'ART-3',
                    'title' => __('linscarbon.compliance.beges.publication'),
                    'description' => __('linscarbon.compliance.beges.publication_desc'),
                    'completed' => false, // Would need ADEME publication
                    'priority' => 'medium',
                ],
                [
                    'code' => 'ART-4',
                    'title' => __('linscarbon.compliance.beges.update'),
                    'description' => __('linscarbon.compliance.beges.update_desc'),
                    'completed' => $organization?->assessments()->count() >= 2,
                    'priority' => 'medium',
                ],
            ],
        ];
    }

    protected function getGhgChecklist(?Organization $organization, ?Assessment $assessment): array
    {
        $hasScope1 = $assessment && $assessment->scope1_total > 0;
        $hasScope2 = $assessment && $assessment->scope2_total > 0;
        $hasScope3 = $assessment && $assessment->scope3_total > 0;

        return [
            'name' => 'GHG Protocol',
            'items' => [
                [
                    'code' => 'SCOPE1',
                    'title' => __('linscarbon.compliance.ghg.scope1'),
                    'description' => __('linscarbon.compliance.ghg.scope1_desc'),
                    'completed' => $hasScope1,
                    'priority' => 'high',
                ],
                [
                    'code' => 'SCOPE2',
                    'title' => __('linscarbon.compliance.ghg.scope2'),
                    'description' => __('linscarbon.compliance.ghg.scope2_desc'),
                    'completed' => $hasScope2,
                    'priority' => 'high',
                ],
                [
                    'code' => 'SCOPE3',
                    'title' => __('linscarbon.compliance.ghg.scope3'),
                    'description' => __('linscarbon.compliance.ghg.scope3_desc'),
                    'completed' => $hasScope3,
                    'priority' => 'medium',
                ],
                [
                    'code' => 'BOUNDARY',
                    'title' => __('linscarbon.compliance.ghg.boundary'),
                    'description' => __('linscarbon.compliance.ghg.boundary_desc'),
                    'completed' => $organization?->sites()->count() > 0,
                    'priority' => 'high',
                ],
            ],
        ];
    }

    protected function loadDeadlines(): void
    {
        $currentYear = (int) date('Y');

        $this->deadlines = [];

        // CSRD deadlines
        if ($this->organizationProfile['has_csrd_obligation'] ?? false) {
            $this->deadlines[] = [
                'framework' => 'CSRD',
                'title' => __('linscarbon.compliance.deadlines.csrd_report'),
                'date' => Carbon::create($currentYear + 1, 1, 1),
                'description' => __('linscarbon.compliance.deadlines.csrd_report_desc'),
                'urgent' => true,
            ];
        }

        // BEGES deadline (France)
        if (($this->organizationProfile['country'] ?? '') === 'FR'
            && ($this->organizationProfile['employee_count'] ?? 0) >= 500) {
            $this->deadlines[] = [
                'framework' => 'BEGES',
                'title' => __('linscarbon.compliance.deadlines.beges_update'),
                'date' => Carbon::create($currentYear + 1, 12, 31),
                'description' => __('linscarbon.compliance.deadlines.beges_update_desc'),
                'urgent' => false,
            ];
        }

        // Sort by date
        usort($this->deadlines, fn ($a, $b) => $a['date']->timestamp <=> $b['date']->timestamp);
    }

    public function getCompletionPercentage(string $framework): int
    {
        if (! isset($this->checklist[$framework])) {
            return 0;
        }

        $items = $this->checklist[$framework]['items'];
        $completed = collect($items)->where('completed', true)->count();

        return $items ? (int) round(($completed / count($items)) * 100) : 0;
    }

    public function getOverallCompletion(): int
    {
        $totalItems = 0;
        $completedItems = 0;

        foreach ($this->checklist as $framework => $data) {
            $items = $data['items'] ?? [];
            $totalItems += count($items);
            $completedItems += collect($items)->where('completed', true)->count();
        }

        return $totalItems > 0 ? (int) round(($completedItems / $totalItems) * 100) : 0;
    }

    public function toggleCsrdDetails(): void
    {
        $this->showCsrdDetails = ! $this->showCsrdDetails;
    }

    public function toggleIsoDetails(): void
    {
        $this->showIsoDetails = ! $this->showIsoDetails;
    }

    public function render()
    {
        return view('livewire.compliance.compliance-checklist');
    }
}
