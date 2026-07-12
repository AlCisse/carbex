<?php

namespace App\Services\Reporting;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

/**
 * PDF Generator Service
 *
 * Generates PDF reports:
 * - Carbon footprint summary
 * - Detailed breakdown
 * - Methodology documentation
 */
class PdfGenerator
{
    /**
     * Generate PDF from report data.
     */
    public function generate(array $reportData, string $template = 'summary'): string
    {
        $view = match ($template) {
            'detailed' => 'pdf.reports.detailed',
            'methodology' => 'pdf.reports.methodology',
            default => 'pdf.reports.summary',
        };

        $pdf = Pdf::loadView($view, [
            'report' => $reportData,
        ]);

        // Configure PDF settings
        $pdf->setPaper('a4', 'portrait');
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'defaultFont' => 'sans-serif',
        ]);

        // Generate unique filename
        $filename = sprintf(
            'reports/%s/%s_%s.pdf',
            $reportData['organization']['name'] ?? 'unknown',
            $template,
            now()->format('Y-m-d_His')
        );

        // Store PDF
        Storage::put($filename, $pdf->output());

        return $filename;
    }

    /**
     * Generate and return PDF as download response.
     */
    public function download(array $reportData, string $template = 'summary'): \Illuminate\Http\Response
    {
        $view = match ($template) {
            'detailed' => 'pdf.reports.detailed',
            'methodology' => 'pdf.reports.methodology',
            default => 'pdf.reports.summary',
        };

        $pdf = Pdf::loadView($view, [
            'report' => $reportData,
        ]);

        $pdf->setPaper('a4', 'portrait');

        $filename = sprintf(
            'bilan-carbone_%s_%s.pdf',
            \Str::slug($reportData['organization']['name'] ?? 'report'),
            now()->format('Y-m-d')
        );

        return $pdf->download($filename);
    }

    /**
     * Generate and return PDF as stream response.
     */
    public function stream(array $reportData, string $template = 'summary'): \Illuminate\Http\Response
    {
        $view = match ($template) {
            'detailed' => 'pdf.reports.detailed',
            'methodology' => 'pdf.reports.methodology',
            default => 'pdf.reports.summary',
        };

        $pdf = Pdf::loadView($view, [
            'report' => $reportData,
        ]);

        $pdf->setPaper('a4', 'portrait');

        return $pdf->stream('bilan-carbone.pdf');
    }
}
