<?php

namespace App\Livewire\Transactions;

use App\Models\Category;
use App\Models\Transaction;
use App\Services\AI\CategorizationService;
use App\Services\AI\MerchantRuleService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Transaction List Component
 *
 * Displays and manages transactions:
 * - Paginated list with filters
 * - Validation queue (low confidence)
 * - Bulk categorization
 * - Search and filtering
 */
class TransactionList extends Component
{
    use WithPagination;

    // Filters
    #[Url]
    public string $filter = 'all'; // all, pending, validated, excluded

    #[Url]
    public string $search = '';

    #[Url]
    public ?string $categoryId = null;

    #[Url]
    public ?string $scope = null;

    #[Url]
    public string $dateFrom = '';

    #[Url]
    public string $dateTo = '';

    #[Url]
    public string $sortField = 'date';

    #[Url]
    public string $sortDirection = 'desc';

    // Bulk actions
    public array $selected = [];

    public bool $selectAll = false;

    // Inline editing
    public ?string $editingId = null;

    public ?string $editCategoryId = null;

    public function mount(): void
    {
        // Default date range (last 3 months)
        if (empty($this->dateFrom)) {
            $this->dateFrom = now()->subMonths(3)->startOfMonth()->toDateString();
        }
        if (empty($this->dateTo)) {
            $this->dateTo = now()->toDateString();
        }
    }

    #[Computed]
    public function transactions(): LengthAwarePaginator
    {
        $query = Transaction::where('organization_id', auth()->user()->organization_id)
            ->with(['category', 'emissionRecord']);

        // Apply filters
        $query = match ($this->filter) {
            'pending' => $query->whereNull('validated_at')->whereNull('category_id'),
            'low_confidence' => $query->whereNull('validated_at')
                ->whereNotNull('category_id')
                ->where('confidence', '<', 0.8),
            'validated' => $query->whereNotNull('validated_at'),
            'excluded' => $query->where('is_excluded', true),
            default => $query,
        };

        // Search
        if (! empty($this->search)) {
            $query->where(function ($q) {
                $q->where('description', 'like', "%{$this->search}%")
                    ->orWhere('clean_description', 'like', "%{$this->search}%")
                    ->orWhere('merchant_name', 'like', "%{$this->search}%");
            });
        }

        // Category filter
        if ($this->categoryId) {
            $query->where('category_id', $this->categoryId);
        }

        // Scope filter
        if ($this->scope) {
            $query->whereHas('category', fn ($q) => $q->where('scope', $this->scope));
        }

        // Date range
        if ($this->dateFrom) {
            $query->where('date', '>=', $this->dateFrom);
        }
        if ($this->dateTo) {
            $query->where('date', '<=', $this->dateTo);
        }

        // Sorting
        $query->orderBy($this->sortField, $this->sortDirection);

        return $query->paginate(25);
    }

    #[Computed]
    public function categories(): Collection
    {
        return Category::orderBy('scope')
            ->orderBy('name')
            ->get(['id', 'name', 'code', 'scope']);
    }

    #[Computed]
    public function stats(): array
    {
        $organizationId = auth()->user()->organization_id;

        return [
            'total' => Transaction::where('organization_id', $organizationId)->count(),
            'pending' => Transaction::where('organization_id', $organizationId)
                ->whereNull('validated_at')
                ->whereNull('category_id')
                ->count(),
            'low_confidence' => Transaction::where('organization_id', $organizationId)
                ->whereNull('validated_at')
                ->whereNotNull('category_id')
                ->where('confidence', '<', 0.8)
                ->count(),
            'validated' => Transaction::where('organization_id', $organizationId)
                ->whereNotNull('validated_at')
                ->count(),
        ];
    }

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function startEdit(string $transactionId): void
    {
        $transaction = Transaction::find($transactionId);
        $this->editingId = $transactionId;
        $this->editCategoryId = $transaction?->category_id;
    }

    public function cancelEdit(): void
    {
        $this->editingId = null;
        $this->editCategoryId = null;
    }

    public function saveCategory(string $transactionId): void
    {
        $transaction = Transaction::where('organization_id', auth()->user()->organization_id)
            ->findOrFail($transactionId);

        $transaction->update([
            'category_id' => $this->editCategoryId ?: null,
            'categorization_method' => 'manual',
            'validated_at' => now(),
            'validated_by' => auth()->id(),
        ]);

        // Recalculate emissions
        if ($this->editCategoryId) {
            dispatch(new \App\Jobs\ProcessNewTransactions(collect([$transaction->fresh()])));
        }

        $this->cancelEdit();
        $this->dispatch('transaction-updated');
    }

    public function validateTransaction(string $transactionId): void
    {
        $transaction = Transaction::where('organization_id', auth()->user()->organization_id)
            ->findOrFail($transactionId);

        $transaction->update([
            'validated_at' => now(),
            'validated_by' => auth()->id(),
        ]);

        $this->dispatch('transaction-validated');
    }

    public function excludeTransaction(string $transactionId): void
    {
        $transaction = Transaction::where('organization_id', auth()->user()->organization_id)
            ->findOrFail($transactionId);

        $transaction->update([
            'is_excluded' => true,
            'excluded_at' => now(),
            'excluded_by' => auth()->id(),
        ]);

        // Remove emission record if exists
        $transaction->emissionRecord?->delete();

        $this->dispatch('transaction-excluded');
    }

    public function includeTransaction(string $transactionId): void
    {
        $transaction = Transaction::where('organization_id', auth()->user()->organization_id)
            ->findOrFail($transactionId);

        $transaction->update([
            'is_excluded' => false,
            'excluded_at' => null,
            'excluded_by' => null,
        ]);

        // Recalculate emissions
        if ($transaction->category_id) {
            dispatch(new \App\Jobs\ProcessNewTransactions(collect([$transaction])));
        }

        $this->dispatch('transaction-included');
    }

    public function updatedSelectAll(): void
    {
        if ($this->selectAll) {
            $this->selected = $this->transactions->pluck('id')->toArray();
        } else {
            $this->selected = [];
        }
    }

    public function bulkValidate(): void
    {
        if (empty($this->selected)) {
            return;
        }

        Transaction::where('organization_id', auth()->user()->organization_id)
            ->whereIn('id', $this->selected)
            ->update([
                'validated_at' => now(),
                'validated_by' => auth()->id(),
            ]);

        $this->selected = [];
        $this->selectAll = false;
        $this->dispatch('transactions-validated', count: count($this->selected));
    }

    public function bulkCategorize(string $categoryId): void
    {
        if (empty($this->selected)) {
            return;
        }

        $transactions = Transaction::where('organization_id', auth()->user()->organization_id)
            ->whereIn('id', $this->selected)
            ->get();

        foreach ($transactions as $transaction) {
            $transaction->update([
                'category_id' => $categoryId,
                'categorization_method' => 'manual_bulk',
                'validated_at' => now(),
                'validated_by' => auth()->id(),
            ]);
        }

        // Recalculate emissions
        dispatch(new \App\Jobs\ProcessNewTransactions($transactions));

        $this->selected = [];
        $this->selectAll = false;
        $this->dispatch('transactions-categorized', count: count($transactions));
    }

    public function createMerchantRule(string $transactionId): void
    {
        $transaction = Transaction::where('organization_id', auth()->user()->organization_id)
            ->findOrFail($transactionId);

        if ($transaction->category_id && $transaction->merchant_name) {
            $ruleService = app(MerchantRuleService::class);
            $ruleService->createRule(
                auth()->user()->organization_id,
                $transaction->merchant_name,
                $transaction->category_id
            );

            $this->dispatch('merchant-rule-created', merchant: $transaction->merchant_name);
        }
    }

    public function resetFilters(): void
    {
        $this->filter = 'all';
        $this->search = '';
        $this->categoryId = null;
        $this->scope = null;
        $this->dateFrom = now()->subMonths(3)->startOfMonth()->toDateString();
        $this->dateTo = now()->toDateString();
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.transactions.transaction-list');
    }
}
