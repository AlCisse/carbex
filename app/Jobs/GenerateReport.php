<?php

namespace App\Jobs;

use App\Models\Report;
use App\Models\User;
use App\Notifications\ReportReady;
use App\Services\Reporting\PdfGenerator;
use App\Services\Reporting\ReportBuilder;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Generate Report Job
 *
 * Background job for generating reports:
 * - Build report data
 * - Generate PDF
 * - Store report record
 * - Notify user
 */
class GenerateReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $timeout = 300; // 5 minutes

    public function __construct(
        public string $organizationId,
        public string $userId,
        public string $reportType,
        public string $startDate,
        public string $endDate,
        public ?string $siteId = null
    ) {
        $this->onQueue('reports');
    }

    public function handle(
        ReportBuilder $builder,
        PdfGenerator $pdfGenerator
    ): void {
        try {
            // Build report data
            $reportData = $builder->build(
                $this->organizationId,
                Carbon::parse($this->startDate),
                Carbon::parse($this->endDate),
                $this->reportType,
                $this->siteId
            );

            // Generate PDF
            $filePath = $pdfGenerator->generate($reportData, $this->reportType);

            // Create report record
            $report = Report::create([
                'organization_id' => $this->organizationId,
                'site_id' => $this->siteId,
                'type' => $this->reportType,
                'title' => $this->generateTitle($reportData),
                'period_start' => $this->startDate,
                'period_end' => $this->endDate,
                'file_path' => $filePath,
                'file_size' => \Storage::size($filePath),
                'status' => 'completed',
                'data' => $reportData,
                'generated_by' => $this->userId,
            ]);

            // Notify user
            $user = User::find($this->userId);
            if ($user) {
                $user->notify(new ReportReady($report));
            }
        } catch (Throwable $e) {
            Log::error('Report generation failed', [
                'organization' => $this->organizationId,
                'type' => $this->reportType,
                'error' => $e->getMessage(),
            ]);

            // Create failed report record
            Report::create([
                'organization_id' => $this->organizationId,
                'site_id' => $this->siteId,
                'type' => $this->reportType,
                'title' => 'Failed Report',
                'period_start' => $this->startDate,
                'period_end' => $this->endDate,
                'status' => 'failed',
                'error_message' => $e->getMessage(),
                'generated_by' => $this->userId,
            ]);

            throw $e;
        }
    }

    private function generateTitle(array $reportData): string
    {
        $type = match ($this->reportType) {
            'detailed' => __('Detailed Report'),
            'methodology' => __('Methodology Report'),
            default => __('Carbon Footprint Summary'),
        };

        return sprintf(
            '%s - %s',
            $type,
            $reportData['report']['period']['label']
        );
    }

    public function failed(Throwable $exception): void
    {
        Log::error('Report job failed', [
            'organization' => $this->organizationId,
            'error' => $exception->getMessage(),
        ]);
    }
}
