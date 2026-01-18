<?php

namespace App\Livewire\TransitionPlan;

use App\Models\Action;
use App\Models\Category;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Component;

/**
 * ActionList - Actions de réduction du plan de transition
 *
 * Constitution LinsCarbon v3.0 - Section 2.8, T062-T063
 */
class ActionList extends Component
{
    // Filter state
    public string $filter = 'all';

    // Modal state
    public bool $showModal = false;
    public ?string $editingId = null;

    // Form fields
    public string $title = '';
    public string $description = '';
    public ?string $dueDate = null;
    public ?string $categoryId = null;
    public string $status = Action::STATUS_TODO;
    public int $co2ReductionPercent = 0;
    public ?string $estimatedCost = null;
    public string $difficulty = Action::DIFFICULTY_MEDIUM;

    protected function rules(): array
    {
        return [
            'title' => ['required', 'string', 'min:3', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'dueDate' => ['nullable', 'date'],
            'categoryId' => ['nullable', 'exists:categories,id'],
            'status' => ['required', 'in:todo,in_progress,completed'],
            'co2ReductionPercent' => ['required', 'integer', 'min:0', 'max:100'],
            'estimatedCost' => ['nullable', 'numeric', 'min:0'],
            'difficulty' => ['required', 'in:easy,medium,hard'],
        ];
    }

    protected array $messages = [
        'title.required' => 'Le titre est obligatoire.',
        'title.min' => 'Le titre doit contenir au moins 3 caractères.',
        'description.max' => 'La description ne peut pas dépasser 5000 caractères.',
        'dueDate.date' => 'La date limite doit être une date valide.',
        'co2ReductionPercent.min' => 'Le pourcentage doit être entre 0 et 100.',
        'co2ReductionPercent.max' => 'Le pourcentage doit être entre 0 et 100.',
        'estimatedCost.numeric' => 'Le coût doit être un nombre.',
    ];

    public function setFilter(string $filter): void
    {
        $this->filter = $filter;
    }

    public function getActionsProperty(): Collection
    {
        $query = Action::where('organization_id', auth()->user()->organization_id)
            ->with('category');

        if ($this->filter !== 'all') {
            $query->where('status', $this->filter);
        }

        return $query->orderByPriority()->orderByDueDate()->get();
    }

    public function getCategoriesProperty(): Collection
    {
        return Category::orderBy('code')->get();
    }

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function openEditModal(string $id): void
    {
        $action = Action::find($id);

        if ($action && $action->organization_id === auth()->user()->organization_id) {
            $this->editingId = $id;
            $this->title = $action->title;
            $this->description = $action->description ?? '';
            $this->dueDate = $action->due_date?->format('Y-m-d');
            $this->categoryId = $action->category_id;
            $this->status = $action->status;
            $this->co2ReductionPercent = (int) $action->co2_reduction_percent;
            $this->estimatedCost = $action->estimated_cost ? (string) $action->estimated_cost : null;
            $this->difficulty = $action->difficulty;
            $this->showModal = true;
        }
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function resetForm(): void
    {
        $this->editingId = null;
        $this->title = '';
        $this->description = '';
        $this->dueDate = null;
        $this->categoryId = null;
        $this->status = Action::STATUS_TODO;
        $this->co2ReductionPercent = 0;
        $this->estimatedCost = null;
        $this->difficulty = Action::DIFFICULTY_MEDIUM;
        $this->resetValidation();
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'organization_id' => auth()->user()->organization_id,
            'title' => $this->title,
            'description' => $this->description ?: null,
            'due_date' => $this->dueDate ?: null,
            'category_id' => $this->categoryId ?: null,
            'status' => $this->status,
            'co2_reduction_percent' => $this->co2ReductionPercent,
            'estimated_cost' => $this->estimatedCost ? (float) $this->estimatedCost : null,
            'difficulty' => $this->difficulty,
            'priority' => $this->calculatePriority(),
        ];

        if ($this->editingId) {
            $action = Action::find($this->editingId);
            if ($action && $action->organization_id === auth()->user()->organization_id) {
                $action->update($data);
                session()->flash('message', __('linscarbon.messages.updated'));
            }
        } else {
            Action::create($data);
            session()->flash('message', __('linscarbon.messages.created'));
        }

        $this->closeModal();
    }

    public function updateStatus(string $id, string $status): void
    {
        $action = Action::find($id);

        if ($action && $action->organization_id === auth()->user()->organization_id) {
            $action->status = $status;
            $action->save();

            session()->flash('message', __('linscarbon.actions.status_updated'));
        }
    }

    public function deleteAction(string $id): void
    {
        $action = Action::find($id);

        if ($action && $action->organization_id === auth()->user()->organization_id) {
            $action->delete();
            session()->flash('message', __('linscarbon.messages.deleted'));
        }
    }

    /**
     * Calculate priority based on difficulty and CO2 impact (quick wins first).
     */
    protected function calculatePriority(): int
    {
        $difficultyScore = match ($this->difficulty) {
            Action::DIFFICULTY_EASY => 3,
            Action::DIFFICULTY_MEDIUM => 2,
            Action::DIFFICULTY_HARD => 1,
        };

        // Higher CO2 reduction + easier = higher priority
        return $difficultyScore * 10 + $this->co2ReductionPercent;
    }

    public function getStatusCountsProperty(): array
    {
        $orgId = auth()->user()->organization_id;

        return [
            'all' => Action::where('organization_id', $orgId)->count(),
            'todo' => Action::where('organization_id', $orgId)->todo()->count(),
            'in_progress' => Action::where('organization_id', $orgId)->inProgress()->count(),
            'completed' => Action::where('organization_id', $orgId)->completed()->count(),
        ];
    }

    public function render(): View
    {
        return view('livewire.transition-plan.action-list', [
            'actions' => $this->actions,
            'categories' => $this->categories,
            'statusCounts' => $this->statusCounts,
        ]);
    }
}
