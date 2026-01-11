<?php

namespace App\Livewire\Reports;

use App\Models\Assessment;
use App\Models\Report;
use App\Services\Reporting\ReportBuilder;
use App\Services\Reporting\WordReportGenerator;
use App\Services\Reporting\AdemeExporter;
use App\Services\Reporting\GhgExporter;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

/**
 * ReportList - Reports dashboard with generation and history
 *
 * Constitution Carbex v3.0 - Section 5.1, T070-T071
 *
 * Features:
 * - 3 report types: Word, ADEME, GHG
 * - Report generation
 * - Download history
 */
class ReportList extends Component
{
    public ?string $selectedYear = null;

    public bool $showGenerateModal = false;

    public string $generateType = '';

    public bool $generating = false;

    public function mount(): void
    {
        $this->selectedYear = (string) date('Y');
    }

    /**
     * Available years from assessments.
     */
    #[Computed]
    public function availableYears(): array
    {
        return Assessment::where('organization_id', auth()->user()->organization_id)
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->unique()
            ->toArray() ?: [date('Y')];
    }

    /**
     * Report types available.
     */
    #[Computed]
    public function reportTypes(): array
    {
        return [
            [
                'id' => 'carbon_footprint',
                'name' => __('carbex.reports.carbon_footprint'),
                'description' => __('carbex.reports.carbon_footprint_desc'),
                'format' => 'docx',
                'icon' => 'document-text',
                'color' => 'blue',
            ],
            [
                'id' => 'ademe',
                'name' => __('carbex.reports.ademe'),
                'description' => __('carbex.reports.ademe_desc'),
                'format' => 'xlsx',
                'icon' => 'table-cells',
                'color' => 'green',
            ],
            [
                'id' => 'ghg',
                'name' => __('carbex.reports.ghg'),
                'description' => __('carbex.reports.ghg_desc'),
                'format' => 'xlsx',
                'icon' => 'globe-europe-africa',
                'color' => 'purple',
            ],
        ];
    }

    /**
     * Generated reports history.
     */
    #[Computed]
    public function reports(): Collection
    {
        return Report::where('organization_id', auth()->user()->organization_id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
    }

    /**
     * Current assessment for selected year.
     */
    #[Computed]
    public function currentAssessment(): ?Assessment
    {
        return Assessment::where('organization_id', auth()->user()->organization_id)
            ->where('year', $this->selectedYear)
            ->first();
    }

    /**
     * Open generate modal for a specific report type.
     */
    public function openGenerateModal(string $type): void
    {
        $this->generateType = $type;
        $this->showGenerateModal = true;
    }

    /**
     * Close generate modal.
     */
    public function closeGenerateModal(): void
    {
        $this->showGenerateModal = false;
        $this->generateType = '';
    }

    /**
     * Generate a report.
     */
    public function generate(): void
    {
        $this->generating = true;

        try {
            $organizationId = auth()->user()->organization_id;
            $year = (int) $this->selectedYear;
            $startDate = Carbon::create($year, 1, 1);
            $endDate = Carbon::create($year, 12, 31);

            // Create report record
            $report = Report::create([
                'organization_id' => $organizationId,
                'generated_by' => auth()->id(),
                'type' => $this->mapReportType($this->generateType),
                'name' => $this->getReportName($this->generateType, $year),
                'year' => $year,
                'period_start' => $startDate,
                'period_end' => $endDate,
                'file_format' => $this->getReportFormat($this->generateType),
                'status' => 'generating',
                'started_at' => now(),
            ]);

            // Generate the actual file based on type
            $filePath = $this->generateReportFile($this->generateType, $organizationId, $year);

            if ($filePath) {
                $report->update([
                    'status' => 'completed',
                    'file_path' => $filePath,
                    'file_size' => Storage::exists($filePath) ? Storage::size($filePath) : null,
                    'completed_at' => now(),
                ]);
            } else {
                $report->update([
                    'status' => 'failed',
                    'error_message' => 'Failed to generate report file',
                ]);
            }

            session()->flash('message', __('carbex.reports.generation_started'));

            $this->closeGenerateModal();
            unset($this->reports);
        } catch (\Exception $e) {
            \Log::error('Report generation failed: ' . $e->getMessage());
            session()->flash('error', __('carbex.reports.generation_failed') . ': ' . $e->getMessage());
        } finally {
            $this->generating = false;
        }
    }

    /**
     * Delete a report.
     */
    public function deleteReport(string $reportId): void
    {
        $report = Report::find($reportId);

        if ($report && $report->organization_id === auth()->user()->organization_id) {
            $report->deleteFile();
            $report->delete();
            session()->flash('message', __('carbex.messages.deleted'));
            unset($this->reports);
        }
    }

    /**
     * Get report name based on type.
     */
    private function getReportName(string $type, int $year): string
    {
        return match ($type) {
            'carbon_footprint' => "Bilan Carbone {$year}",
            'ademe' => "Déclaration ADEME {$year}",
            'ghg' => "GHG Protocol Report {$year}",
            default => "Report {$year}",
        };
    }

    /**
     * Get report format based on type.
     */
    private function getReportFormat(string $type): string
    {
        return match ($type) {
            'carbon_footprint' => 'docx',
            'ademe', 'ghg' => 'xlsx',
            default => 'pdf',
        };
    }

    /**
     * Map UI report type to database type.
     */
    private function mapReportType(string $type): string
    {
        return match ($type) {
            'carbon_footprint' => 'annual',
            'ademe' => 'beges',
            'ghg' => 'ghg_inventory',
            default => 'custom',
        };
    }

    /**
     * Generate the actual report file.
     */
    private function generateReportFile(string $type, string $organizationId, int $year): ?string
    {
        try {
            return match ($type) {
                'carbon_footprint' => app(WordReportGenerator::class)->generate($organizationId, $year),
                'ademe' => app(AdemeExporter::class)->export($organizationId, $year),
                'ghg' => app(GhgExporter::class)->export($organizationId, $year),
                default => null,
            };
        } catch (\Exception $e) {
            \Log::error("Report generation failed for type {$type}: " . $e->getMessage());
            return null;
        }
    }

    public function render(): View
    {
        return view('livewire.reports.report-list');
    }
}
