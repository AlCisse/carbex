<?php

namespace App\Livewire\Settings;

use App\Models\Site;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
#[Title('Gestion des sites - LinsCarbon')]
class SiteManagement extends Component
{
    use WithFileUploads;

    public $sites = [];

    // Form fields
    public bool $showForm = false;
    public ?string $editingSiteId = null;
    public string $name = '';
    public ?string $code = null;
    public ?string $description = null;
    public string $type = 'office';
    public ?string $address_line_1 = null;
    public ?string $address_line_2 = null;
    public ?string $city = null;
    public ?string $postal_code = null;
    public ?string $country = null;
    public ?float $floor_area_m2 = null;
    public ?int $employee_count = null;
    public ?string $electricity_provider = null;
    public bool $renewable_energy = false;
    public ?float $renewable_percentage = null;
    public bool $is_primary = false;

    // Delete confirmation
    public bool $showDeleteModal = false;
    public ?string $deletingSiteId = null;

    // CSV Import
    public bool $showImportModal = false;
    public $csvFile = null;
    public array $importPreview = [];
    public array $importErrors = [];
    public int $importedCount = 0;

    public function mount(): void
    {
        $this->loadSites();
        $this->country = auth()->user()->organization->country;
    }

    public function loadSites(): void
    {
        $this->sites = Site::orderBy('is_primary', 'desc')
            ->orderBy('name')
            ->get()
            ->toArray();
    }

    public function rules(): array
    {
        $uniqueRule = $this->editingSiteId
            ? "unique:sites,code,{$this->editingSiteId}"
            : 'unique:sites,code';

        return [
            'name' => 'required|string|max:255',
            'code' => "nullable|string|max:50|{$uniqueRule}",
            'description' => 'nullable|string|max:1000',
            'type' => 'required|string|in:office,warehouse,factory,store,datacenter,other',
            'address_line_1' => 'nullable|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|size:2',
            'floor_area_m2' => 'nullable|numeric|min:0',
            'employee_count' => 'nullable|integer|min:0',
            'electricity_provider' => 'nullable|string|max:255',
            'renewable_energy' => 'boolean',
            'renewable_percentage' => 'nullable|numeric|min:0|max:100',
            'is_primary' => 'boolean',
        ];
    }

    public function openForm(?string $siteId = null): void
    {
        if ($siteId) {
            $site = Site::findOrFail($siteId);
            Gate::authorize('update', $site);

            $this->editingSiteId = $siteId;
            $this->name = $site->name;
            $this->code = $site->code;
            $this->description = $site->description;
            $this->type = $site->type ?? 'office';
            $this->address_line_1 = $site->address_line_1;
            $this->address_line_2 = $site->address_line_2;
            $this->city = $site->city;
            $this->postal_code = $site->postal_code;
            $this->country = $site->country;
            $this->floor_area_m2 = $site->floor_area_m2;
            $this->employee_count = $site->employee_count;
            $this->electricity_provider = $site->electricity_provider;
            $this->renewable_energy = $site->renewable_energy ?? false;
            $this->renewable_percentage = $site->renewable_percentage;
            $this->is_primary = $site->is_primary;
        } else {
            Gate::authorize('create', Site::class);
            $this->resetForm();
        }

        $this->showForm = true;
    }

    public function closeForm(): void
    {
        $this->showForm = false;
        $this->resetForm();
    }

    public function resetForm(): void
    {
        $this->editingSiteId = null;
        $this->name = '';
        $this->code = null;
        $this->description = null;
        $this->type = 'office';
        $this->address_line_1 = null;
        $this->address_line_2 = null;
        $this->city = null;
        $this->postal_code = null;
        $this->country = auth()->user()->organization->country;
        $this->floor_area_m2 = null;
        $this->employee_count = null;
        $this->electricity_provider = null;
        $this->renewable_energy = false;
        $this->renewable_percentage = null;
        $this->is_primary = false;
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'code' => $this->code ?: strtoupper(substr(preg_replace('/[^a-zA-Z0-9]/', '', $this->name), 0, 8)),
            'description' => $this->description,
            'type' => $this->type,
            'address_line_1' => $this->address_line_1,
            'address_line_2' => $this->address_line_2,
            'city' => $this->city,
            'postal_code' => $this->postal_code,
            'country' => $this->country,
            'floor_area_m2' => $this->floor_area_m2,
            'employee_count' => $this->employee_count,
            'electricity_provider' => $this->electricity_provider,
            'renewable_energy' => $this->renewable_energy,
            'renewable_percentage' => $this->renewable_percentage,
        ];

        if ($this->editingSiteId) {
            $site = Site::findOrFail($this->editingSiteId);
            Gate::authorize('update', $site);
            $site->update($data);
            $message = __('linscarbon.sites.updated');
        } else {
            Gate::authorize('create', Site::class);
            $data['is_primary'] = $this->is_primary;
            $site = Site::create($data);

            // Update subscription usage
            $subscription = auth()->user()->organization->subscription;
            if ($subscription) {
                $subscription->increment('sites_used');
            }

            $message = __('linscarbon.sites.created');
        }

        // Handle primary site
        if ($this->is_primary && ! $site->is_primary) {
            Site::where('id', '!=', $site->id)->update(['is_primary' => false]);
            $site->update(['is_primary' => true]);
        }

        $this->closeForm();
        $this->loadSites();
        session()->flash('success', $message);
    }

    public function confirmDelete(string $siteId): void
    {
        $site = Site::findOrFail($siteId);
        Gate::authorize('delete', $site);

        $this->deletingSiteId = $siteId;
        $this->showDeleteModal = true;
    }

    public function cancelDelete(): void
    {
        $this->showDeleteModal = false;
        $this->deletingSiteId = null;
    }

    public function delete(): void
    {
        if (! $this->deletingSiteId) {
            return;
        }

        $site = Site::findOrFail($this->deletingSiteId);
        Gate::authorize('delete', $site);

        if ($site->is_primary) {
            session()->flash('error', __('linscarbon.sites.cannot_delete_primary'));
            $this->cancelDelete();

            return;
        }

        $site->delete();

        // Update subscription usage
        $subscription = auth()->user()->organization->subscription;
        if ($subscription && $subscription->sites_used > 0) {
            $subscription->decrement('sites_used');
        }

        $this->cancelDelete();
        $this->loadSites();
        session()->flash('success', __('linscarbon.sites.deleted'));
    }

    public function setPrimary(string $siteId): void
    {
        $site = Site::findOrFail($siteId);
        Gate::authorize('setPrimary', $site);

        Site::where('is_primary', true)->update(['is_primary' => false]);
        $site->update(['is_primary' => true]);

        $this->loadSites();
        session()->flash('success', __('linscarbon.sites.set_as_primary'));
    }

    // CSV Import Methods

    public function openImportModal(): void
    {
        Gate::authorize('create', Site::class);
        $this->resetImport();
        $this->showImportModal = true;
    }

    public function closeImportModal(): void
    {
        $this->showImportModal = false;
        $this->resetImport();
    }

    public function resetImport(): void
    {
        $this->csvFile = null;
        $this->importPreview = [];
        $this->importErrors = [];
        $this->importedCount = 0;
    }

    public function updatedCsvFile(): void
    {
        $this->validate([
            'csvFile' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $this->parseCSV();
    }

    protected function parseCSV(): void
    {
        if (! $this->csvFile) {
            return;
        }

        $this->importPreview = [];
        $this->importErrors = [];

        $path = $this->csvFile->getRealPath();
        $handle = fopen($path, 'r');

        if ($handle === false) {
            $this->importErrors[] = __('linscarbon.sites.import.file_read_error');

            return;
        }

        // Read header row
        $header = fgetcsv($handle, 0, ';');
        if (! $header) {
            $this->importErrors[] = __('linscarbon.sites.import.empty_file');
            fclose($handle);

            return;
        }

        // Normalize headers (lowercase, trim)
        $header = array_map(fn ($h) => strtolower(trim($h)), $header);

        // Required columns
        $requiredColumns = ['name'];
        $missingColumns = array_diff($requiredColumns, $header);

        if (! empty($missingColumns)) {
            $this->importErrors[] = __('linscarbon.sites.import.missing_columns', [
                'columns' => implode(', ', $missingColumns),
            ]);
            fclose($handle);

            return;
        }

        // Parse rows (max 100 for preview)
        $rowNumber = 1;
        while (($row = fgetcsv($handle, 0, ';')) !== false && $rowNumber <= 100) {
            $rowNumber++;

            if (count($row) < count($header)) {
                $row = array_pad($row, count($header), null);
            }

            $data = array_combine($header, $row);

            // Skip empty rows
            if (empty(trim($data['name'] ?? ''))) {
                continue;
            }

            $this->importPreview[] = [
                'row' => $rowNumber,
                'name' => $data['name'] ?? '',
                'code' => $data['code'] ?? null,
                'type' => $this->mapSiteType($data['type'] ?? 'office'),
                'city' => $data['city'] ?? null,
                'country' => $data['country'] ?? auth()->user()->organization->country,
                'floor_area_m2' => $this->parseNumber($data['floor_area_m2'] ?? null),
                'employee_count' => $this->parseInteger($data['employee_count'] ?? null),
                'address_line_1' => $data['address'] ?? $data['address_line_1'] ?? null,
                'postal_code' => $data['postal_code'] ?? $data['zip'] ?? null,
                'building_type' => $data['building_type'] ?? null,
                'energy_rating' => $data['energy_rating'] ?? null,
            ];
        }

        fclose($handle);

        if (empty($this->importPreview)) {
            $this->importErrors[] = __('linscarbon.sites.import.no_valid_rows');
        }
    }

    protected function mapSiteType(?string $type): string
    {
        if (! $type) {
            return 'office';
        }

        $type = strtolower(trim($type));

        return match ($type) {
            'office', 'bureau' => 'office',
            'warehouse', 'entrepôt', 'entrepot' => 'warehouse',
            'factory', 'usine' => 'factory',
            'store', 'magasin', 'retail' => 'store',
            'datacenter', 'data center' => 'datacenter',
            default => 'other',
        };
    }

    protected function parseNumber(?string $value): ?float
    {
        if (! $value || trim($value) === '') {
            return null;
        }

        // Replace comma with dot for French decimals
        $value = str_replace(',', '.', $value);

        return is_numeric($value) ? (float) $value : null;
    }

    protected function parseInteger(?string $value): ?int
    {
        if (! $value || trim($value) === '') {
            return null;
        }

        return is_numeric($value) ? (int) $value : null;
    }

    public function confirmImport(): void
    {
        Gate::authorize('create', Site::class);

        if (empty($this->importPreview)) {
            return;
        }

        $this->importedCount = 0;
        $organization = auth()->user()->organization;

        foreach ($this->importPreview as $row) {
            // Check if site with same name already exists
            $exists = Site::where('organization_id', $organization->id)
                ->where('name', $row['name'])
                ->exists();

            if ($exists) {
                $this->importErrors[] = __('linscarbon.sites.import.duplicate', ['name' => $row['name']]);

                continue;
            }

            // Generate code if not provided
            $code = $row['code'] ?: strtoupper(substr(preg_replace('/[^a-zA-Z0-9]/', '', $row['name']), 0, 8));

            Site::create([
                'organization_id' => $organization->id,
                'name' => $row['name'],
                'code' => $code,
                'type' => $row['type'],
                'city' => $row['city'],
                'country' => $row['country'],
                'floor_area_m2' => $row['floor_area_m2'],
                'employee_count' => $row['employee_count'],
                'address_line_1' => $row['address_line_1'],
                'postal_code' => $row['postal_code'],
                'building_type' => $row['building_type'],
                'energy_rating' => $row['energy_rating'],
                'is_active' => true,
            ]);

            $this->importedCount++;
        }

        // Update subscription usage
        if ($this->importedCount > 0) {
            $subscription = $organization->subscription;
            if ($subscription) {
                $subscription->increment('sites_used', $this->importedCount);
            }
        }

        $this->loadSites();
        $this->closeImportModal();

        if ($this->importedCount > 0) {
            session()->flash('success', __('linscarbon.sites.import.success', ['count' => $this->importedCount]));
        }

        if (! empty($this->importErrors)) {
            session()->flash('warning', implode(' | ', $this->importErrors));
        }
    }

    public function downloadTemplate(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="linscarbon_sites_template.csv"',
        ];

        $columns = [
            'name',
            'code',
            'type',
            'address',
            'city',
            'postal_code',
            'country',
            'floor_area_m2',
            'employee_count',
            'building_type',
            'energy_rating',
        ];

        $exampleRow = [
            'Siège Paris',
            'SIEGE-PAR',
            'office',
            '12 rue de la Paix',
            'Paris',
            '75002',
            'FR',
            '2500',
            '120',
            'office_modern',
            'B',
        ];

        return response()->streamDownload(function () use ($columns, $exampleRow) {
            $output = fopen('php://output', 'w');
            fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF)); // UTF-8 BOM
            fputcsv($output, $columns, ';');
            fputcsv($output, $exampleRow, ';');
            fclose($output);
        }, 'linscarbon_sites_template.csv', $headers);
    }

    public function render()
    {
        return view('livewire.settings.site-management');
    }
}
