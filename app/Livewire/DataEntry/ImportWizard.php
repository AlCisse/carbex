<?php

namespace App\Livewire\DataEntry;

use App\Jobs\ProcessImportFile;
use App\Models\Site;
use App\Services\Import\CsvImportService;
use App\Services\Import\ExcelImportService;
use App\Services\Import\FecParser;
use App\Services\Import\ImportValidationRules;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

/**
 * Import Wizard Component
 *
 * Multi-step wizard for importing data:
 * - Step 1: Upload file
 * - Step 2: Map columns
 * - Step 3: Preview & validate
 * - Step 4: Import & confirm
 */
class ImportWizard extends Component
{
    use WithFileUploads;

    // Wizard state
    public int $step = 1;

    public string $importType = 'transactions'; // transactions, activities, fec

    #[Validate('required|file|max:10240|mimes:csv,txt,xlsx,xls')]
    public $file;

    public string $siteId = '';

    // File analysis
    public array $headers = [];

    public array $sampleRows = [];

    public array $columnMapping = [];

    public int $totalRows = 0;

    // Validation results
    public array $validationErrors = [];

    public array $validationWarnings = [];

    public int $validRowCount = 0;

    public int $invalidRowCount = 0;

    // Import results
    public bool $importStarted = false;

    public ?string $importJobId = null;

    public ?string $errorMessage = null;

    public function mount(): void
    {
        // Default site if only one
        $sites = $this->sites;
        if ($sites->count() === 1) {
            $this->siteId = $sites->first()->id;
        }
    }

    #[Computed]
    public function sites(): Collection
    {
        return Site::where('organization_id', auth()->user()->organization_id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'city']);
    }

    #[Computed]
    public function requiredColumns(): array
    {
        return match ($this->importType) {
            'transactions' => [
                'date' => ['required' => true, 'type' => 'date', 'label' => __('Date')],
                'description' => ['required' => true, 'type' => 'string', 'label' => __('Description')],
                'amount' => ['required' => true, 'type' => 'number', 'label' => __('Amount')],
                'currency' => ['required' => false, 'type' => 'string', 'label' => __('Currency')],
                'category' => ['required' => false, 'type' => 'string', 'label' => __('Category')],
                'mcc_code' => ['required' => false, 'type' => 'string', 'label' => __('MCC Code')],
            ],
            'activities' => [
                'date' => ['required' => true, 'type' => 'date', 'label' => __('Date')],
                'description' => ['required' => true, 'type' => 'string', 'label' => __('Description')],
                'category' => ['required' => true, 'type' => 'string', 'label' => __('Category')],
                'quantity' => ['required' => true, 'type' => 'number', 'label' => __('Quantity')],
                'unit' => ['required' => true, 'type' => 'string', 'label' => __('Unit')],
                'amount' => ['required' => false, 'type' => 'number', 'label' => __('Amount')],
            ],
            'fec' => [
                'JournalCode' => ['required' => true, 'type' => 'string', 'label' => __('Journal Code')],
                'EcritureDate' => ['required' => true, 'type' => 'date', 'label' => __('Entry Date')],
                'CompteNum' => ['required' => true, 'type' => 'string', 'label' => __('Account Number')],
                'EcritureLib' => ['required' => true, 'type' => 'string', 'label' => __('Entry Label')],
                'Debit' => ['required' => true, 'type' => 'number', 'label' => __('Debit')],
                'Credit' => ['required' => true, 'type' => 'number', 'label' => __('Credit')],
            ],
            default => [],
        };
    }

    public function updatedFile(): void
    {
        $this->validate(['file' => 'required|file|max:10240|mimes:csv,txt,xlsx,xls']);
        $this->errorMessage = null;
    }

    public function analyzeFile(): void
    {
        if (! $this->file) {
            return;
        }

        $this->errorMessage = null;

        try {
            $extension = strtolower($this->file->getClientOriginalExtension());
            $path = $this->file->store('imports', 'local');

            if (in_array($extension, ['xlsx', 'xls'])) {
                $service = app(ExcelImportService::class);
            } else {
                $service = app(CsvImportService::class);
            }

            $analysis = $service->analyze(Storage::disk('local')->path($path));

            $this->headers = $analysis['headers'];
            $this->sampleRows = $analysis['sample_rows'];
            $this->totalRows = $analysis['total_rows'];

            // Auto-map columns
            $this->columnMapping = $this->autoMapColumns($this->headers);

            $this->step = 2;
        } catch (\Exception $e) {
            $this->errorMessage = __('Error analyzing file: :message', ['message' => $e->getMessage()]);
        }
    }

    public function validateMapping(): void
    {
        $this->errorMessage = null;

        // Check required columns are mapped
        $missingRequired = [];
        foreach ($this->requiredColumns as $field => $config) {
            if ($config['required'] && empty($this->columnMapping[$field])) {
                $missingRequired[] = $config['label'];
            }
        }

        if (! empty($missingRequired)) {
            $this->errorMessage = __('Missing required columns: :columns', [
                'columns' => implode(', ', $missingRequired),
            ]);

            return;
        }

        // Validate sample data
        $validator = app(ImportValidationRules::class);
        $result = $validator->validateSample(
            $this->sampleRows,
            $this->columnMapping,
            $this->requiredColumns
        );

        $this->validationErrors = $result['errors'];
        $this->validationWarnings = $result['warnings'];
        $this->validRowCount = $result['valid_count'];
        $this->invalidRowCount = $result['invalid_count'];

        $this->step = 3;
    }

    public function startImport(): void
    {
        $this->validate([
            'siteId' => 'required|uuid|exists:sites,id',
        ]);

        $this->errorMessage = null;

        try {
            $path = $this->file->store('imports', 'local');

            // Dispatch import job
            $job = new ProcessImportFile(
                filePath: Storage::disk('local')->path($path),
                importType: $this->importType,
                organizationId: auth()->user()->organization_id,
                siteId: $this->siteId,
                columnMapping: $this->columnMapping,
                userId: auth()->id()
            );

            dispatch($job);

            $this->importStarted = true;
            $this->step = 4;

            $this->dispatch('import-started');
        } catch (\Exception $e) {
            $this->errorMessage = __('Error starting import: :message', ['message' => $e->getMessage()]);
        }
    }

    public function previousStep(): void
    {
        if ($this->step > 1) {
            $this->step--;
        }
    }

    public function resetWizard(): void
    {
        $this->step = 1;
        $this->file = null;
        $this->headers = [];
        $this->sampleRows = [];
        $this->columnMapping = [];
        $this->totalRows = 0;
        $this->validationErrors = [];
        $this->validationWarnings = [];
        $this->validRowCount = 0;
        $this->invalidRowCount = 0;
        $this->importStarted = false;
        $this->importJobId = null;
        $this->errorMessage = null;
    }

    private function autoMapColumns(array $headers): array
    {
        $mapping = [];
        $normalizedHeaders = array_map(fn ($h) => strtolower(trim($h)), $headers);

        $fieldAliases = [
            'date' => ['date', 'datum', 'transaction_date', 'ecrituredate', 'datecompta'],
            'description' => ['description', 'beschreibung', 'libelle', 'ecriturelib', 'label', 'bezeichnung'],
            'amount' => ['amount', 'betrag', 'montant', 'value', 'debit', 'credit'],
            'currency' => ['currency', 'wahrung', 'devise', 'curr'],
            'category' => ['category', 'kategorie', 'categorie', 'cat'],
            'quantity' => ['quantity', 'menge', 'quantite', 'qty'],
            'unit' => ['unit', 'einheit', 'unite'],
            'mcc_code' => ['mcc', 'mcc_code', 'merchant_category'],
        ];

        foreach ($this->requiredColumns as $field => $config) {
            $aliases = $fieldAliases[$field] ?? [$field];

            foreach ($aliases as $alias) {
                $index = array_search($alias, $normalizedHeaders);
                if ($index !== false) {
                    $mapping[$field] = $headers[$index];
                    break;
                }
            }
        }

        return $mapping;
    }

    public function render()
    {
        return view('livewire.data-entry.import-wizard');
    }
}
