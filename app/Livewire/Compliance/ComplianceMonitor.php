<?php

namespace App\Livewire\Compliance;

use App\Models\ComplianceTask;
use App\Models\CsrdFramework;
use App\Models\IsoStandard;
use App\Models\Organization;
use App\Models\OrganizationCsrdCompliance;
use App\Models\OrganizationIsoCertification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

/**
 * ComplianceMonitor Livewire Component
 *
 * Dashboard for monitoring CSRD and ISO compliance status.
 *
 * Tasks T178 - Phase 10 (TrackZero Features)
 * Constitution Carbex v3.0 - Section 8 (Conformité)
 */
#[Layout('components.layouts.app')]
#[Title('Compliance Monitor')]
class ComplianceMonitor extends Component
{
    public string $activeTab = 'overview';

    public int $selectedYear;

    public ?string $selectedCsrdCategory = null;

    public ?string $selectedIsoCategory = null;

    public bool $showTaskModal = false;

    public array $taskForm = [];

    public ?string $editingTaskId = null;

    public function mount(): void
    {
        $this->selectedYear = now()->year;
        $this->resetTaskForm();
    }

    // ==================== Computed Properties ====================

    #[Computed]
    public function organization(): ?Organization
    {
        return Auth::user()?->organization;
    }

    #[Computed]
    public function csrdFrameworks(): Collection
    {
        return CsrdFramework::active()
            ->ordered()
            ->when($this->selectedCsrdCategory, fn ($q) => $q->byCategory($this->selectedCsrdCategory))
            ->get();
    }

    #[Computed]
    public function isoStandards(): Collection
    {
        return IsoStandard::active()
            ->when($this->selectedIsoCategory, fn ($q) => $q->byCategory($this->selectedIsoCategory))
            ->orderBy('code')
            ->get();
    }

    #[Computed]
    public function csrdCompliance(): Collection
    {
        if (! $this->organization) {
            return collect();
        }

        return OrganizationCsrdCompliance::where('organization_id', $this->organization->id)
            ->forYear($this->selectedYear)
            ->with('framework')
            ->get()
            ->keyBy('csrd_framework_id');
    }

    #[Computed]
    public function isoCertifications(): Collection
    {
        if (! $this->organization) {
            return collect();
        }

        return OrganizationIsoCertification::where('organization_id', $this->organization->id)
            ->with('standard')
            ->get()
            ->keyBy('iso_standard_id');
    }

    #[Computed]
    public function complianceTasks(): Collection
    {
        if (! $this->organization) {
            return collect();
        }

        return ComplianceTask::where('organization_id', $this->organization->id)
            ->orderByUrgency()
            ->with(['assignee'])
            ->limit(20)
            ->get();
    }

    #[Computed]
    public function overdueTasks(): Collection
    {
        return $this->complianceTasks->filter(fn ($task) => $task->isOverdue());
    }

    #[Computed]
    public function upcomingTasks(): Collection
    {
        return $this->complianceTasks
            ->filter(fn ($task) => $task->isDueSoon() && ! $task->isOverdue())
            ->take(5);
    }

    #[Computed]
    public function csrdStats(): array
    {
        $total = $this->csrdFrameworks->count();
        $compliant = $this->csrdCompliance->where('status', OrganizationCsrdCompliance::STATUS_COMPLIANT)->count();
        $inProgress = $this->csrdCompliance->where('status', OrganizationCsrdCompliance::STATUS_IN_PROGRESS)->count();
        $nonCompliant = $this->csrdCompliance->where('status', OrganizationCsrdCompliance::STATUS_NON_COMPLIANT)->count();
        $notStarted = $total - $compliant - $inProgress - $nonCompliant;

        return [
            'total' => $total,
            'compliant' => $compliant,
            'in_progress' => $inProgress,
            'non_compliant' => $nonCompliant,
            'not_started' => $notStarted,
            'percentage' => $total > 0 ? round(($compliant / $total) * 100) : 0,
        ];
    }

    #[Computed]
    public function isoStats(): array
    {
        $total = $this->isoStandards->count();
        $certified = $this->isoCertifications->where('status', OrganizationIsoCertification::STATUS_CERTIFIED)->count();
        $inProgress = $this->isoCertifications->where('status', OrganizationIsoCertification::STATUS_IN_PROGRESS)->count();
        $expiringSoon = $this->isoCertifications->filter(fn ($cert) => $cert->isExpiringSoon())->count();

        return [
            'total' => $total,
            'certified' => $certified,
            'in_progress' => $inProgress,
            'expiring_soon' => $expiringSoon,
            'percentage' => $total > 0 ? round(($certified / $total) * 100) : 0,
        ];
    }

    #[Computed]
    public function csrdCategories(): array
    {
        return [
            CsrdFramework::CATEGORY_ENVIRONMENT => __('carbex.compliance.categories.environment'),
            CsrdFramework::CATEGORY_SOCIAL => __('carbex.compliance.categories.social'),
            CsrdFramework::CATEGORY_GOVERNANCE => __('carbex.compliance.categories.governance'),
        ];
    }

    #[Computed]
    public function isoCategories(): array
    {
        return [
            IsoStandard::CATEGORY_ENVIRONMENTAL => __('carbex.compliance.iso_categories.environmental'),
            IsoStandard::CATEGORY_ENERGY => __('carbex.compliance.iso_categories.energy'),
            IsoStandard::CATEGORY_CARBON => __('carbex.compliance.iso_categories.carbon'),
            IsoStandard::CATEGORY_QUALITY => __('carbex.compliance.iso_categories.quality'),
        ];
    }

    // ==================== Tab Methods ====================

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function setYear(int $year): void
    {
        $this->selectedYear = $year;
        unset($this->csrdCompliance);
    }

    public function setCsrdCategory(?string $category): void
    {
        $this->selectedCsrdCategory = $category;
        unset($this->csrdFrameworks);
    }

    public function setIsoCategory(?string $category): void
    {
        $this->selectedIsoCategory = $category;
        unset($this->isoStandards);
    }

    // ==================== CSRD Compliance Methods ====================

    public function updateCsrdStatus(string $frameworkId, string $status): void
    {
        if (! $this->organization) {
            return;
        }

        $compliance = OrganizationCsrdCompliance::firstOrCreate(
            [
                'organization_id' => $this->organization->id,
                'csrd_framework_id' => $frameworkId,
                'year' => $this->selectedYear,
            ],
            ['status' => OrganizationCsrdCompliance::STATUS_NOT_STARTED]
        );

        $compliance->status = $status;
        $compliance->save();

        unset($this->csrdCompliance, $this->csrdStats);

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => __('carbex.compliance.status_updated'),
        ]);
    }

    // ==================== ISO Certification Methods ====================

    public function updateIsoStatus(string $standardId, string $status): void
    {
        if (! $this->organization) {
            return;
        }

        $certification = OrganizationIsoCertification::firstOrCreate(
            [
                'organization_id' => $this->organization->id,
                'iso_standard_id' => $standardId,
            ],
            ['status' => OrganizationIsoCertification::STATUS_NOT_CERTIFIED]
        );

        $certification->status = $status;
        $certification->save();

        unset($this->isoCertifications, $this->isoStats);

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => __('carbex.compliance.status_updated'),
        ]);
    }

    // ==================== Task Methods ====================

    public function openTaskModal(?string $taskId = null): void
    {
        if ($taskId) {
            $task = ComplianceTask::find($taskId);
            if ($task && $task->organization_id === $this->organization?->id) {
                $this->editingTaskId = $taskId;
                $this->taskForm = [
                    'type' => $task->type,
                    'title' => $task->title,
                    'description' => $task->description,
                    'priority' => $task->priority,
                    'due_date' => $task->due_date?->format('Y-m-d'),
                    'assigned_to' => $task->assigned_to,
                ];
            }
        } else {
            $this->resetTaskForm();
        }
        $this->showTaskModal = true;
    }

    public function closeTaskModal(): void
    {
        $this->showTaskModal = false;
        $this->editingTaskId = null;
        $this->resetTaskForm();
    }

    public function saveTask(): void
    {
        $this->validate([
            'taskForm.type' => 'required|in:csrd,iso,internal',
            'taskForm.title' => 'required|string|max:255',
            'taskForm.description' => 'nullable|string',
            'taskForm.priority' => 'required|in:low,medium,high,critical',
            'taskForm.due_date' => 'nullable|date',
        ]);

        if (! $this->organization) {
            return;
        }

        $data = [
            'organization_id' => $this->organization->id,
            'type' => $this->taskForm['type'],
            'title' => $this->taskForm['title'],
            'description' => $this->taskForm['description'] ?? null,
            'priority' => $this->taskForm['priority'],
            'due_date' => $this->taskForm['due_date'] ?? null,
            'assigned_to' => $this->taskForm['assigned_to'] ?? null,
        ];

        if ($this->editingTaskId) {
            ComplianceTask::where('id', $this->editingTaskId)
                ->where('organization_id', $this->organization->id)
                ->update($data);
            $message = __('carbex.compliance.task_updated');
        } else {
            ComplianceTask::create($data);
            $message = __('carbex.compliance.task_created');
        }

        $this->closeTaskModal();
        unset($this->complianceTasks, $this->overdueTasks, $this->upcomingTasks);

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => $message,
        ]);
    }

    public function completeTask(string $taskId): void
    {
        $task = ComplianceTask::find($taskId);

        if ($task && $task->organization_id === $this->organization?->id) {
            $task->markAsCompleted(Auth::user());
            unset($this->complianceTasks, $this->overdueTasks, $this->upcomingTasks);

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => __('carbex.compliance.task_completed'),
            ]);
        }
    }

    public function deleteTask(string $taskId): void
    {
        $task = ComplianceTask::find($taskId);

        if ($task && $task->organization_id === $this->organization?->id) {
            $task->delete();
            unset($this->complianceTasks, $this->overdueTasks, $this->upcomingTasks);

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => __('carbex.compliance.task_deleted'),
            ]);
        }
    }

    protected function resetTaskForm(): void
    {
        $this->taskForm = [
            'type' => ComplianceTask::TYPE_CSRD,
            'title' => '',
            'description' => '',
            'priority' => ComplianceTask::PRIORITY_MEDIUM,
            'due_date' => '',
            'assigned_to' => null,
        ];
    }

    // ==================== Render ====================

    public function render()
    {
        return view('livewire.compliance.compliance-monitor');
    }
}
