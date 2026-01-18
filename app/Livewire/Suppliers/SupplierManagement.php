<?php

namespace App\Livewire\Suppliers;

use App\Models\Supplier;
use App\Models\SupplierInvitation;
use App\Services\Suppliers\SupplierInvitationService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

/**
 * SupplierManagement
 *
 * Composant Livewire pour la gestion des fournisseurs Scope 3.
 * Permet de lister, inviter, et suivre la collecte des données fournisseurs.
 *
 * Constitution LinsCarbon v3.0 - Section 9.6 (Module Fournisseurs)
 */
class SupplierManagement extends Component
{
    use WithFileUploads, WithPagination;

    // Filters
    public string $search = '';

    public string $statusFilter = '';

    public string $dataQualityFilter = '';

    public string $sectorFilter = '';

    // Modal state
    public bool $showModal = false;

    public bool $showInviteModal = false;

    public bool $showImportModal = false;

    public ?string $editingId = null;

    // Form data
    public array $form = [
        'name' => '',
        'email' => '',
        'contact_name' => '',
        'contact_email' => '',
        'phone' => '',
        'country' => 'FR',
        'sector' => '',
        'address' => '',
        'city' => '',
        'postal_code' => '',
        'annual_spend' => '',
        'notes' => '',
    ];

    // Invite form
    public array $inviteForm = [
        'supplier_id' => '',
        'message' => '',
        'due_date' => '',
    ];

    // CSV Import
    public $csvFile;

    // Stats
    public array $stats = [];

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->loadStats();
    }

    /**
     * Load supplier statistics.
     */
    protected function loadStats(): void
    {
        $organization = auth()->user()?->organization;

        if (! $organization) {
            return;
        }

        $suppliers = Supplier::where('organization_id', $organization->id);

        $this->stats = [
            'total' => $suppliers->count(),
            'active' => (clone $suppliers)->active()->count(),
            'pending_data' => (clone $suppliers)->pendingData(now()->year)->count(),
            'with_data' => (clone $suppliers)->withEmissionData(now()->year)->count(),
            'total_spend' => (clone $suppliers)->sum('annual_spend'),
        ];
    }

    /**
     * Get paginated suppliers.
     */
    #[Computed]
    public function suppliers(): LengthAwarePaginator
    {
        $organization = auth()->user()?->organization;

        if (! $organization) {
            return new LengthAwarePaginator([], 0, 15);
        }

        $query = Supplier::where('organization_id', $organization->id)
            ->with(['latestInvitation', 'latestEmission']);

        // Apply search
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('email', 'like', "%{$this->search}%")
                    ->orWhere('contact_name', 'like', "%{$this->search}%");
            });
        }

        // Apply status filter
        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        // Apply data quality filter
        if ($this->dataQualityFilter) {
            $query->where('data_quality', $this->dataQualityFilter);
        }

        // Apply sector filter
        if ($this->sectorFilter) {
            $query->where('sector', $this->sectorFilter);
        }

        return $query->orderBy('name')->paginate(15);
    }

    /**
     * Get available sectors.
     */
    #[Computed]
    public function sectors(): array
    {
        return [
            'A' => __('linscarbon.sectors.agriculture'),
            'B' => __('linscarbon.sectors.mining'),
            'C' => __('linscarbon.sectors.manufacturing'),
            'D' => __('linscarbon.sectors.electricity'),
            'E' => __('linscarbon.sectors.water'),
            'F' => __('linscarbon.sectors.construction'),
            'G' => __('linscarbon.sectors.wholesale'),
            'H' => __('linscarbon.sectors.transport'),
            'I' => __('linscarbon.sectors.accommodation'),
            'J' => __('linscarbon.sectors.information'),
            'K' => __('linscarbon.sectors.finance'),
            'L' => __('linscarbon.sectors.real_estate'),
            'M' => __('linscarbon.sectors.professional'),
            'N' => __('linscarbon.sectors.administrative'),
            'O' => __('linscarbon.sectors.public'),
            'P' => __('linscarbon.sectors.education'),
            'Q' => __('linscarbon.sectors.health'),
            'R' => __('linscarbon.sectors.arts'),
            'S' => __('linscarbon.sectors.other'),
        ];
    }

    /**
     * Open create modal.
     */
    public function create(): void
    {
        $this->resetForm();
        $this->editingId = null;
        $this->showModal = true;
    }

    /**
     * Open edit modal.
     */
    public function edit(string $id): void
    {
        $supplier = Supplier::findOrFail($id);

        $this->editingId = $id;
        $this->form = [
            'name' => $supplier->name,
            'email' => $supplier->email ?? '',
            'contact_name' => $supplier->contact_name ?? '',
            'contact_email' => $supplier->contact_email ?? '',
            'phone' => $supplier->phone ?? '',
            'country' => $supplier->country ?? 'FR',
            'sector' => $supplier->sector ?? '',
            'address' => $supplier->address ?? '',
            'city' => $supplier->city ?? '',
            'postal_code' => $supplier->postal_code ?? '',
            'annual_spend' => $supplier->annual_spend ?? '',
            'notes' => $supplier->notes ?? '',
        ];

        $this->showModal = true;
    }

    /**
     * Save supplier (create or update).
     */
    public function save(): void
    {
        $this->validate([
            'form.name' => 'required|string|max:255',
            'form.email' => 'nullable|email|max:255',
            'form.contact_name' => 'nullable|string|max:255',
            'form.contact_email' => 'nullable|email|max:255',
            'form.phone' => 'nullable|string|max:50',
            'form.country' => 'required|string|size:2',
            'form.sector' => 'nullable|string|size:1',
            'form.address' => 'nullable|string|max:500',
            'form.city' => 'nullable|string|max:255',
            'form.postal_code' => 'nullable|string|max:20',
            'form.annual_spend' => 'nullable|numeric|min:0',
            'form.notes' => 'nullable|string|max:1000',
        ]);

        $organization = auth()->user()->organization;

        $data = array_merge($this->form, [
            'organization_id' => $organization->id,
            'annual_spend' => $this->form['annual_spend'] ?: null,
        ]);

        if ($this->editingId) {
            Supplier::where('id', $this->editingId)->update($data);
            session()->flash('success', __('linscarbon.suppliers.updated'));
        } else {
            $data['status'] = Supplier::STATUS_PENDING;
            $data['data_quality'] = Supplier::QUALITY_NONE;
            Supplier::create($data);
            session()->flash('success', __('linscarbon.suppliers.created'));
        }

        $this->closeModal();
        $this->loadStats();
    }

    /**
     * Delete a supplier.
     */
    public function delete(string $id): void
    {
        Supplier::where('id', $id)
            ->where('organization_id', auth()->user()->organization_id)
            ->delete();

        session()->flash('success', __('linscarbon.suppliers.deleted'));
        $this->loadStats();
    }

    /**
     * Open invite modal.
     */
    public function openInviteModal(string $supplierId): void
    {
        $this->inviteForm = [
            'supplier_id' => $supplierId,
            'message' => __('linscarbon.suppliers.default_invite_message'),
            'due_date' => now()->addDays(30)->format('Y-m-d'),
        ];
        $this->showInviteModal = true;
    }

    /**
     * Send invitation to supplier.
     */
    public function sendInvitation(): void
    {
        $this->validate([
            'inviteForm.supplier_id' => 'required|exists:suppliers,id',
            'inviteForm.message' => 'nullable|string|max:2000',
            'inviteForm.due_date' => 'required|date|after:today',
        ]);

        $supplier = Supplier::findOrFail($this->inviteForm['supplier_id']);

        $service = app(SupplierInvitationService::class);
        $invitation = $service->createInvitation(
            $supplier,
            auth()->user(),
            $this->inviteForm['message'],
            $this->inviteForm['due_date']
        );

        $service->sendInvitation($invitation);

        $supplier->update(['status' => Supplier::STATUS_INVITED]);

        session()->flash('success', __('linscarbon.suppliers.invitation_sent', ['name' => $supplier->name]));

        $this->showInviteModal = false;
        $this->loadStats();
    }

    /**
     * Open import modal.
     */
    public function openImportModal(): void
    {
        $this->csvFile = null;
        $this->showImportModal = true;
    }

    /**
     * Import suppliers from CSV.
     */
    public function importCsv(): void
    {
        $this->validate([
            'csvFile' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $organization = auth()->user()->organization;
        $path = $this->csvFile->getRealPath();
        $imported = 0;
        $errors = [];

        if (($handle = fopen($path, 'r')) !== false) {
            $header = fgetcsv($handle, 0, ';');

            while (($row = fgetcsv($handle, 0, ';')) !== false) {
                if (count($row) < 2) {
                    continue;
                }

                try {
                    $data = array_combine($header, $row);

                    Supplier::create([
                        'organization_id' => $organization->id,
                        'name' => $data['name'] ?? $data['Nom'] ?? '',
                        'email' => $data['email'] ?? $data['Email'] ?? null,
                        'contact_name' => $data['contact_name'] ?? $data['Contact'] ?? null,
                        'country' => $data['country'] ?? $data['Pays'] ?? 'FR',
                        'sector' => $data['sector'] ?? $data['Secteur'] ?? null,
                        'annual_spend' => isset($data['annual_spend']) ? (float) str_replace([' ', ','], ['', '.'], $data['annual_spend']) : null,
                        'status' => Supplier::STATUS_PENDING,
                        'data_quality' => Supplier::QUALITY_NONE,
                    ]);

                    $imported++;
                } catch (\Exception $e) {
                    $errors[] = $row[0] ?? 'Unknown';
                }
            }

            fclose($handle);
        }

        $this->showImportModal = false;
        $this->loadStats();

        if ($imported > 0) {
            session()->flash('success', trans_choice('linscarbon.suppliers.imported', $imported, ['count' => $imported]));
        }

        if (count($errors) > 0) {
            session()->flash('warning', __('linscarbon.suppliers.import_errors', ['count' => count($errors)]));
        }
    }

    /**
     * Download CSV template.
     */
    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="suppliers_template.csv"',
        ];

        $columns = ['name', 'email', 'contact_name', 'country', 'sector', 'annual_spend'];

        return response()->streamDownload(function () use ($columns) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $columns, ';');
            fputcsv($handle, ['Exemple SARL', 'contact@exemple.fr', 'Jean Dupont', 'FR', 'C', '50000'], ';');
            fclose($handle);
        }, 'suppliers_template.csv', $headers);
    }

    /**
     * Close modal and reset form.
     */
    public function closeModal(): void
    {
        $this->showModal = false;
        $this->showInviteModal = false;
        $this->showImportModal = false;
        $this->resetForm();
    }

    /**
     * Reset form.
     */
    protected function resetForm(): void
    {
        $this->form = [
            'name' => '',
            'email' => '',
            'contact_name' => '',
            'contact_email' => '',
            'phone' => '',
            'country' => 'FR',
            'sector' => '',
            'address' => '',
            'city' => '',
            'postal_code' => '',
            'annual_spend' => '',
            'notes' => '',
        ];
        $this->editingId = null;
    }

    /**
     * Reset filters.
     */
    public function resetFilters(): void
    {
        $this->search = '';
        $this->statusFilter = '';
        $this->dataQualityFilter = '';
        $this->sectorFilter = '';
        $this->resetPage();
    }

    /**
     * Get status badge class.
     */
    public function getStatusClass(string $status): string
    {
        return match ($status) {
            Supplier::STATUS_ACTIVE => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
            Supplier::STATUS_INVITED => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
            Supplier::STATUS_PENDING => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
            Supplier::STATUS_INACTIVE => 'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-400',
            default => 'bg-gray-100 text-gray-600',
        };
    }

    /**
     * Get data quality badge class.
     */
    public function getQualityClass(string $quality): string
    {
        return match ($quality) {
            Supplier::QUALITY_VERIFIED => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
            Supplier::QUALITY_SUPPLIER_SPECIFIC => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
            Supplier::QUALITY_ESTIMATED => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
            Supplier::QUALITY_NONE => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
            default => 'bg-gray-100 text-gray-600',
        };
    }

    /**
     * Format currency.
     */
    public function formatCurrency(?float $amount): string
    {
        if (! $amount) {
            return '-';
        }

        return number_format($amount, 0, ',', ' ') . ' €';
    }

    /**
     * Render the component.
     */
    public function render()
    {
        return view('livewire.suppliers.supplier-management');
    }
}
