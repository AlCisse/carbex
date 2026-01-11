<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\GenerateReport;
use App\Models\Report;
use App\Services\Reporting\PdfGenerator;
use App\Services\Reporting\ReportBuilder;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Report Controller
 *
 * Manages carbon footprint reports:
 * - List reports
 * - Generate new reports
 * - Download reports
 * - Delete reports
 */
class ReportController extends Controller
{
    public function __construct(
        private ReportBuilder $reportBuilder,
        private PdfGenerator $pdfGenerator
    ) {}

    /**
     * List all reports for the organization.
     *
     * GET /api/reports
     */
    public function index(Request $request): JsonResponse
    {
        $reports = Report::where('organization_id', auth()->user()->organization_id)
            ->orderByDesc('created_at')
            ->paginate($request->integer('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $reports,
        ]);
    }

    /**
     * Generate a new report.
     *
     * POST /api/reports
     */
    public function generate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'type' => 'required|in:summary,detailed,methodology',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'site_id' => 'nullable|uuid|exists:sites,id',
            'async' => 'nullable|boolean',
        ]);

        $organizationId = auth()->user()->organization_id;

        // If async, dispatch job
        if ($validated['async'] ?? true) {
            GenerateReport::dispatch(
                $organizationId,
                auth()->id(),
                $validated['type'],
                $validated['start_date'],
                $validated['end_date'],
                $validated['site_id'] ?? null
            );

            return response()->json([
                'success' => true,
                'message' => __('Report generation started. You will be notified when it\'s ready.'),
            ], 202);
        }

        // Synchronous generation
        $reportData = $this->reportBuilder->build(
            $organizationId,
            Carbon::parse($validated['start_date']),
            Carbon::parse($validated['end_date']),
            $validated['type'],
            $validated['site_id'] ?? null
        );

        $filePath = $this->pdfGenerator->generate($reportData, $validated['type']);

        $report = Report::create([
            'organization_id' => $organizationId,
            'site_id' => $validated['site_id'] ?? null,
            'type' => $validated['type'],
            'title' => $this->generateTitle($validated['type'], $reportData),
            'period_start' => $validated['start_date'],
            'period_end' => $validated['end_date'],
            'file_path' => $filePath,
            'file_size' => Storage::size($filePath),
            'status' => 'completed',
            'data' => $reportData,
            'generated_by' => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'data' => $report,
        ], 201);
    }

    /**
     * Show a specific report.
     *
     * GET /api/reports/{report}
     */
    public function show(Report $report): JsonResponse
    {
        $this->authorize('view', $report);

        return response()->json([
            'success' => true,
            'data' => $report,
        ]);
    }

    /**
     * Download a report.
     *
     * GET /api/reports/{report}/download
     */
    public function download(Report $report): StreamedResponse
    {
        $this->authorize('view', $report);

        if (! $report->file_path || ! Storage::exists($report->file_path)) {
            abort(404, 'Report file not found');
        }

        $filename = sprintf(
            '%s_%s_%s.pdf',
            \Str::slug($report->title),
            $report->period_start->format('Y-m-d'),
            $report->period_end->format('Y-m-d')
        );

        return Storage::download($report->file_path, $filename);
    }

    /**
     * Stream a report preview.
     *
     * GET /api/reports/{report}/preview
     */
    public function preview(Report $report): StreamedResponse
    {
        $this->authorize('view', $report);

        if (! $report->file_path || ! Storage::exists($report->file_path)) {
            abort(404, 'Report file not found');
        }

        return Storage::response($report->file_path, null, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    /**
     * Delete a report.
     *
     * DELETE /api/reports/{report}
     */
    public function destroy(Report $report): JsonResponse
    {
        $this->authorize('delete', $report);

        // Delete file
        if ($report->file_path && Storage::exists($report->file_path)) {
            Storage::delete($report->file_path);
        }

        $report->delete();

        return response()->json([
            'success' => true,
            'message' => __('Report deleted successfully.'),
        ]);
    }

    /**
     * Quick generate and download (no storage).
     *
     * POST /api/reports/quick
     */
    public function quick(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:summary,detailed,methodology',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'site_id' => 'nullable|uuid|exists:sites,id',
        ]);

        $reportData = $this->reportBuilder->build(
            auth()->user()->organization_id,
            Carbon::parse($validated['start_date']),
            Carbon::parse($validated['end_date']),
            $validated['type'],
            $validated['site_id'] ?? null
        );

        return $this->pdfGenerator->download($reportData, $validated['type']);
    }

    private function generateTitle(string $type, array $reportData): string
    {
        $typeLabel = match ($type) {
            'detailed' => __('Detailed Report'),
            'methodology' => __('Methodology Report'),
            default => __('Carbon Footprint Summary'),
        };

        return sprintf('%s - %s', $typeLabel, $reportData['report']['period']['label']);
    }
}
