<?php

namespace App\Livewire\Sites;

use App\Models\Organization;
use App\Models\Site;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithFileUploads;

/**
 * SiteImport Livewire Component
 *
 * Import multiple sites from CSV file.
 *
 * Tasks T176 - Phase 10 (TrackZero Features)
 */
class SiteImport extends Component
{
    use WithFileUploads;

    public $csvFile;

    public bool $hasHeader = true;

    public array $columnMapping = [];

    public array $previewData = [];

    public array $importErrors = [];

    public int $importedCount = 0;

    public int $skippedCount = 0;

    public bool $showMapping = false;

    public bool $showResults = false;

    public bool $isProcessing = false;

    protected array $availableColumns = [
        'name' => ['name', 'nom', 'site_name', 'sitename'],
        'code' => ['code', 'site_code', 'reference', 'ref'],
        'type' => ['type', 'site_type', 'building_type'],
        'address_line_1' => ['address', 'address_line_1', 'adresse', 'street'],
        'city' => ['city', 'ville', 'town'],
        'postal_code' => ['postal_code', 'zip', 'code_postal', 'postcode'],
        'country' => ['country', 'pays', 'country_code'],
        'floor_area_m2' => ['floor_area', 'surface', 'area', 'sqm', 'm2'],
        'employee_count' => ['employees', 'employee_count', 'effectif', 'headcount'],
        'energy_rating' => ['energy_rating', 'dpe', 'rating'],
        'construction_year' => ['construction_year', 'year_built', 'annee_construction'],
        'heating_type' => ['heating', 'heating_type', 'chauffage'],
        'renewable_energy' => ['renewable', 'green_energy', 'energie_verte'],
    ];

    #[Computed]
    public function organization(): ?Organization
    {
        return Auth::user()?->organization;
    }

    #[Computed]
    public function existingSiteCodes(): Collection
    {
        if (! $this->organization) {
            return collect();
        }

        return Site::where('organization_id', $this->organization->id)
            ->whereNotNull('code')
            ->pluck('code');
    }

    public function updatedCsvFile(): void
    {
        $this->validate([
            'csvFile' => 'required|file|mimes:csv,txt|max:5120', // 5MB max
        ]);

        $this->parseCSV();
    }

    protected function parseCSV(): void
    {
        if (! $this->csvFile) {
            return;
        }

        $path = $this->csvFile->getRealPath();
        $handle = fopen($path, 'r');

        if (! $handle) {
            $this->addError('csvFile', __('carbex.sites.import.file_read_error'));

            return;
        }

        $this->previewData = [];
        $this->columnMapping = [];
        $headers = [];
        $rowNumber = 0;

        while (($row = fgetcsv($handle, 0, ';')) !== false) {
            // Try comma delimiter if semicolon gives single column
            if (count($row) === 1) {
                rewind($handle);
                $row = fgetcsv($handle, 0, ',');
            }

            if ($rowNumber === 0) {
                if ($this->hasHeader) {
                    $headers = array_map('strtolower', array_map('trim', $row));
                    $this->autoMapColumns($headers);
                } else {
                    $headers = range(0, count($row) - 1);
                    $this->previewData[] = $row;
                }
            } else {
                $this->previewData[] = $row;
                if (count($this->previewData) >= 10) {
                    break; // Preview only first 10 rows
                }
            }
            $rowNumber++;
        }

        fclose($handle);
        $this->showMapping = true;
    }

    protected function autoMapColumns(array $headers): void
    {
        foreach ($this->availableColumns as $field => $aliases) {
            foreach ($headers as $index => $header) {
                if (in_array($header, $aliases)) {
                    $this->columnMapping[$field] = $index;
                    break;
                }
            }
        }
    }

    public function setColumnMapping(string $field, $columnIndex): void
    {
        if ($columnIndex === '' || $columnIndex === null) {
            unset($this->columnMapping[$field]);
        } else {
            $this->columnMapping[$field] = (int) $columnIndex;
        }
    }

    public function import(): void
    {
        if (! $this->organization || ! $this->csvFile) {
            return;
        }

        if (! isset($this->columnMapping['name'])) {
            $this->addError('mapping', __('carbex.sites.import.name_required'));

            return;
        }

        $this->isProcessing = true;
        $this->importErrors = [];
        $this->importedCount = 0;
        $this->skippedCount = 0;

        $path = $this->csvFile->getRealPath();
        $handle = fopen($path, 'r');

        if (! $handle) {
            $this->addError('csvFile', __('carbex.sites.import.file_read_error'));
            $this->isProcessing = false;

            return;
        }

        DB::beginTransaction();

        try {
            $rowNumber = 0;
            $delimiter = ';';

            // Detect delimiter
            $firstLine = fgets($handle);
            rewind($handle);
            if (substr_count($firstLine, ',') > substr_count($firstLine, ';')) {
                $delimiter = ',';
            }

            while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
                $rowNumber++;

                // Skip header
                if ($rowNumber === 1 && $this->hasHeader) {
                    continue;
                }

                $result = $this->importRow($row, $rowNumber);

                if ($result === true) {
                    $this->importedCount++;
                } elseif ($result === false) {
                    $this->skippedCount++;
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Site import failed', ['error' => $e->getMessage()]);
            $this->addError('import', __('carbex.sites.import.import_failed'));
        }

        fclose($handle);
        $this->isProcessing = false;
        $this->showResults = true;

        $this->dispatch('notify', [
            'type' => $this->importedCount > 0 ? 'success' : 'warning',
            'message' => __('carbex.sites.import.result', [
                'imported' => $this->importedCount,
                'skipped' => $this->skippedCount,
            ]),
        ]);
    }

    protected function importRow(array $row, int $rowNumber): ?bool
    {
        $data = [];

        // Extract data based on mapping
        foreach ($this->columnMapping as $field => $columnIndex) {
            if (isset($row[$columnIndex])) {
                $data[$field] = trim($row[$columnIndex]);
            }
        }

        // Skip empty rows
        if (empty($data['name'])) {
            return null;
        }

        // Check for duplicate code
        if (! empty($data['code']) && $this->existingSiteCodes->contains($data['code'])) {
            $this->importErrors[] = [
                'row' => $rowNumber,
                'message' => __('carbex.sites.import.duplicate_code', ['code' => $data['code']]),
            ];

            return false;
        }

        // Validate data
        $validator = Validator::make($data, [
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'type' => 'nullable|in:headquarters,office,warehouse,factory,retail,remote,other',
            'address_line_1' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:2',
            'floor_area_m2' => 'nullable|numeric|min:0',
            'employee_count' => 'nullable|integer|min:0',
            'energy_rating' => 'nullable|in:A,B,C,D,E,F,G',
            'construction_year' => 'nullable|integer|min:1800|max:' . (now()->year + 5),
            'heating_type' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            $this->importErrors[] = [
                'row' => $rowNumber,
                'message' => implode(', ', $validator->errors()->all()),
            ];

            return false;
        }

        // Transform boolean fields
        if (isset($data['renewable_energy'])) {
            $data['renewable_energy'] = in_array(strtolower($data['renewable_energy']), ['yes', 'oui', '1', 'true', 'y']);
        }

        // Set defaults
        $data['organization_id'] = $this->organization->id;
        $data['country'] = strtoupper($data['country'] ?? 'FR');
        $data['type'] = $data['type'] ?? 'office';

        // Create site
        try {
            Site::create($data);

            return true;
        } catch (\Exception $e) {
            $this->importErrors[] = [
                'row' => $rowNumber,
                'message' => __('carbex.sites.import.create_failed'),
            ];
            Log::error('Site creation failed', ['row' => $rowNumber, 'error' => $e->getMessage()]);

            return false;
        }
    }

    public function downloadTemplate(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $headers = [
            'name',
            'code',
            'type',
            'address_line_1',
            'city',
            'postal_code',
            'country',
            'floor_area_m2',
            'employee_count',
            'energy_rating',
            'construction_year',
            'heating_type',
            'renewable_energy',
        ];

        $example = [
            'Siège social Paris',
            'HQ-001',
            'headquarters',
            '1 rue de la Paix',
            'Paris',
            '75001',
            'FR',
            '500',
            '50',
            'B',
            '2010',
            'gas',
            'no',
        ];

        return response()->streamDownload(function () use ($headers, $example) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $headers, ';');
            fputcsv($handle, $example, ';');
            fclose($handle);
        }, 'carbex-sites-template.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function resetImport(): void
    {
        $this->reset([
            'csvFile',
            'previewData',
            'columnMapping',
            'importErrors',
            'importedCount',
            'skippedCount',
            'showMapping',
            'showResults',
        ]);
    }

    public function render()
    {
        return view('livewire.sites.site-import');
    }
}
