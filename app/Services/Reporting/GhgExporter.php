<?php

namespace App\Services\Reporting;

use App\Models\EmissionRecord;
use App\Models\Organization;
use App\Models\Report;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

/**
 * GHG Protocol Exporter - Export in WBCSD/WRI format
 *
 * Constitution LinsCarbon v3.0 - Section 5.4, T075
 *
 * Generates Excel file conforming to GHG Protocol Corporate Standard:
 * - Organizational boundary
 * - Operational boundary
 * - Emissions by scope and category
 * - Base year comparison
 * - Verification statement
 */
class GhgExporter
{
    private Spreadsheet $spreadsheet;

    private ReportBuilder $reportBuilder;

    /**
     * GHG Protocol Scope 3 categories.
     */
    private array $scope3Categories = [
        1 => 'Purchased Goods and Services',
        2 => 'Capital Goods',
        3 => 'Fuel and Energy Related Activities',
        4 => 'Upstream Transportation and Distribution',
        5 => 'Waste Generated in Operations',
        6 => 'Business Travel',
        7 => 'Employee Commuting',
        8 => 'Upstream Leased Assets',
        9 => 'Downstream Transportation and Distribution',
        10 => 'Processing of Sold Products',
        11 => 'Use of Sold Products',
        12 => 'End-of-Life Treatment of Sold Products',
        13 => 'Downstream Leased Assets',
        14 => 'Franchises',
        15 => 'Investments',
    ];

    public function __construct(ReportBuilder $reportBuilder)
    {
        $this->reportBuilder = $reportBuilder;
    }

    /**
     * Generate GHG Protocol export.
     */
    public function export(
        string $organizationId,
        int $year,
        ?string $siteId = null
    ): string {
        $this->spreadsheet = new Spreadsheet();
        $this->spreadsheet->removeSheetByIndex(0);

        $startDate = Carbon::create($year, 1, 1);
        $endDate = Carbon::create($year, 12, 31);

        $organization = Organization::findOrFail($organizationId);

        // Build report data
        $data = $this->reportBuilder->build(
            $organizationId,
            $startDate,
            $endDate,
            'detailed',
            $siteId
        );

        // Create sheets
        $this->createSummarySheet($organization, $year, $data['summary']);
        $this->createScope1Sheet($organizationId, $siteId, $startDate, $endDate);
        $this->createScope2Sheet($organizationId, $siteId, $startDate, $endDate);
        $this->createScope3Sheet($organizationId, $siteId, $startDate, $endDate);
        $this->createMethodologySheet($organization);
        $this->createHistorySheet($organizationId);

        // Save and return filename
        return $this->saveSpreadsheet($organization, $year);
    }

    /**
     * Create summary sheet.
     */
    private function createSummarySheet(Organization $organization, int $year, array $summary): void
    {
        $sheet = $this->spreadsheet->createSheet();
        $sheet->setTitle('Summary');

        // Header
        $sheet->setCellValue('A1', 'GHG EMISSIONS INVENTORY - SUMMARY');
        $sheet->mergeCells('A1:E1');
        $this->applyHeaderStyle($sheet, 'A1:E1');

        // Organization info
        $sheet->setCellValue('A3', 'Organization:');
        $sheet->setCellValue('B3', $organization->name);
        $sheet->setCellValue('A4', 'Reporting Year:');
        $sheet->setCellValue('B4', $year);
        $sheet->setCellValue('A5', 'Reporting Period:');
        $sheet->setCellValue('B5', "January 1, {$year} - December 31, {$year}");
        $sheet->setCellValue('A6', 'Consolidation Approach:');
        $sheet->setCellValue('B6', 'Operational Control');

        $sheet->getStyle('A3:A6')->getFont()->setBold(true);

        // Emissions summary
        $sheet->setCellValue('A8', 'EMISSIONS SUMMARY');
        $sheet->getStyle('A8')->getFont()->setBold(true)->setSize(12);

        $headers = ['Scope', 'Description', 'Emissions (tCO₂e)', '% of Total'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '10', $header);
            $col++;
        }
        $this->applyTableHeaderStyle($sheet, 'A10:D10');

        $total = $summary['total_emissions']['tonnes'];
        $rows = [
            ['Scope 1', 'Direct GHG emissions', $summary['scope_1']['tonnes'], $total > 0 ? round(($summary['scope_1']['tonnes'] / $total) * 100, 1) : 0],
            ['Scope 2', 'Indirect GHG emissions from electricity', $summary['scope_2']['tonnes'], $total > 0 ? round(($summary['scope_2']['tonnes'] / $total) * 100, 1) : 0],
            ['Scope 3', 'Other indirect GHG emissions', $summary['scope_3']['tonnes'], $total > 0 ? round(($summary['scope_3']['tonnes'] / $total) * 100, 1) : 0],
        ];

        $row = 11;
        foreach ($rows as $data) {
            $sheet->setCellValue('A' . $row, $data[0]);
            $sheet->setCellValue('B' . $row, $data[1]);
            $sheet->setCellValue('C' . $row, $data[2]);
            $sheet->setCellValue('D' . $row, $data[3] . '%');
            $row++;
        }

        // Total row
        $sheet->setCellValue('A' . $row, 'TOTAL');
        $sheet->setCellValue('B' . $row, 'All scopes');
        $sheet->setCellValue('C' . $row, $total);
        $sheet->setCellValue('D' . $row, '100%');
        $sheet->getStyle('A' . $row . ':D' . $row)->getFont()->setBold(true);
        $sheet->getStyle('A' . $row . ':D' . $row)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('E2EFDA');

        // Number format
        $sheet->getStyle('C11:C' . $row)->getNumberFormat()
            ->setFormatCode('#,##0.00');

        // Column widths
        $sheet->getColumnDimension('A')->setWidth(15);
        $sheet->getColumnDimension('B')->setWidth(40);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(15);

        // GHG Protocol info
        $row += 3;
        $sheet->setCellValue('A' . $row, 'Prepared in accordance with:');
        $sheet->setCellValue('B' . $row, 'GHG Protocol Corporate Accounting and Reporting Standard');
        $row++;
        $sheet->setCellValue('A' . $row, 'Reference:');
        $sheet->setCellValue('B' . $row, 'https://ghgprotocol.org/corporate-standard');
        $sheet->getStyle('A' . ($row - 1) . ':A' . $row)->getFont()->setBold(true);
    }

    /**
     * Create Scope 1 sheet.
     */
    private function createScope1Sheet(
        string $organizationId,
        ?string $siteId,
        Carbon $startDate,
        Carbon $endDate
    ): void {
        $sheet = $this->spreadsheet->createSheet();
        $sheet->setTitle('Scope 1');

        $sheet->setCellValue('A1', 'SCOPE 1 - DIRECT GHG EMISSIONS');
        $sheet->mergeCells('A1:F1');
        $this->applyHeaderStyle($sheet, 'A1:F1');

        $sheet->setCellValue('A3', 'Definition: Direct GHG emissions from sources owned or controlled by the company');
        $sheet->mergeCells('A3:F3');

        // Get emissions data
        $emissions = $this->getEmissionsByScope($organizationId, $siteId, $startDate, $endDate, 1);

        // Categories
        $categories = [
            '1.1' => 'Stationary Combustion',
            '1.2' => 'Mobile Combustion',
            '1.4' => 'Fugitive Emissions',
            '1.5' => 'Biomass',
        ];

        $headers = ['Category', 'Source', 'Activity Data', 'Unit', 'Emission Factor', 'Emissions (tCO₂e)'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '5', $header);
            $col++;
        }
        $this->applyTableHeaderStyle($sheet, 'A5:F5');

        $row = 6;
        $total = 0;
        foreach ($categories as $code => $name) {
            $categoryEmissions = $emissions->where('category_code', $code);

            if ($categoryEmissions->isEmpty()) {
                $sheet->setCellValue('A' . $row, $code . ' - ' . $name);
                $sheet->setCellValue('B' . $row, 'No data');
                $sheet->setCellValue('F' . $row, 0);
                $row++;
            } else {
                foreach ($categoryEmissions as $emission) {
                    $sheet->setCellValue('A' . $row, $code . ' - ' . $name);
                    $sheet->setCellValue('B' . $row, $emission->source_name ?? '-');
                    $sheet->setCellValue('C' . $row, $emission->quantity ?? '-');
                    $sheet->setCellValue('D' . $row, $emission->unit ?? '-');
                    $sheet->setCellValue('E' . $row, $emission->emission_factor ?? '-');
                    $sheet->setCellValue('F' . $row, round(($emission->co2e_kg ?? 0) / 1000, 2));
                    $total += ($emission->co2e_kg ?? 0) / 1000;
                    $row++;
                }
            }
        }

        // Total
        $row++;
        $sheet->setCellValue('E' . $row, 'TOTAL SCOPE 1:');
        $sheet->setCellValue('F' . $row, round($total, 2));
        $sheet->getStyle('E' . $row . ':F' . $row)->getFont()->setBold(true);
        $sheet->getStyle('A' . $row . ':F' . $row)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('FDE9D9');

        // Column widths
        $sheet->getColumnDimension('A')->setWidth(25);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(12);
        $sheet->getColumnDimension('E')->setWidth(15);
        $sheet->getColumnDimension('F')->setWidth(18);
    }

    /**
     * Create Scope 2 sheet.
     */
    private function createScope2Sheet(
        string $organizationId,
        ?string $siteId,
        Carbon $startDate,
        Carbon $endDate
    ): void {
        $sheet = $this->spreadsheet->createSheet();
        $sheet->setTitle('Scope 2');

        $sheet->setCellValue('A1', 'SCOPE 2 - INDIRECT GHG EMISSIONS FROM ELECTRICITY');
        $sheet->mergeCells('A1:F1');
        $this->applyHeaderStyle($sheet, 'A1:F1');

        $sheet->setCellValue('A3', 'Definition: Indirect GHG emissions from purchased electricity, steam, heating and cooling');
        $sheet->mergeCells('A3:F3');

        // Get emissions data
        $emissions = $this->getEmissionsByScope($organizationId, $siteId, $startDate, $endDate, 2);

        $headers = ['Category', 'Source', 'Consumption (kWh)', 'Method', 'Emission Factor', 'Emissions (tCO₂e)'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '5', $header);
            $col++;
        }
        $this->applyTableHeaderStyle($sheet, 'A5:F5');

        $row = 6;
        $locationTotal = 0;
        $marketTotal = 0;

        // Location-based
        $sheet->setCellValue('A' . $row, '2.1 - Electricity (Location-based)');
        $sheet->mergeCells('A' . $row . ':F' . $row);
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);
        $row++;

        $locationEmissions = $emissions->where('calculation_method', 'location-based');
        if ($locationEmissions->isEmpty()) {
            $sheet->setCellValue('A' . $row, 'Electricity');
            $sheet->setCellValue('B' . $row, 'No data');
            $sheet->setCellValue('F' . $row, 0);
            $row++;
        } else {
            foreach ($locationEmissions as $emission) {
                $sheet->setCellValue('A' . $row, 'Electricity');
                $sheet->setCellValue('B' . $row, $emission->source_name ?? 'Grid');
                $sheet->setCellValue('C' . $row, $emission->quantity ?? '-');
                $sheet->setCellValue('D' . $row, 'Location-based');
                $sheet->setCellValue('E' . $row, $emission->emission_factor ?? '-');
                $sheet->setCellValue('F' . $row, round(($emission->co2e_kg ?? 0) / 1000, 2));
                $locationTotal += ($emission->co2e_kg ?? 0) / 1000;
                $row++;
            }
        }

        $row++;
        // Market-based
        $sheet->setCellValue('A' . $row, '2.1 - Electricity (Market-based)');
        $sheet->mergeCells('A' . $row . ':F' . $row);
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);
        $row++;

        $marketEmissions = $emissions->where('calculation_method', 'market-based');
        if ($marketEmissions->isEmpty()) {
            $sheet->setCellValue('A' . $row, 'Electricity');
            $sheet->setCellValue('B' . $row, 'No data');
            $sheet->setCellValue('F' . $row, 0);
            $row++;
        } else {
            foreach ($marketEmissions as $emission) {
                $sheet->setCellValue('A' . $row, 'Electricity');
                $sheet->setCellValue('B' . $row, $emission->source_name ?? 'Contract');
                $sheet->setCellValue('C' . $row, $emission->quantity ?? '-');
                $sheet->setCellValue('D' . $row, 'Market-based');
                $sheet->setCellValue('E' . $row, $emission->emission_factor ?? '-');
                $sheet->setCellValue('F' . $row, round(($emission->co2e_kg ?? 0) / 1000, 2));
                $marketTotal += ($emission->co2e_kg ?? 0) / 1000;
                $row++;
            }
        }

        // Totals
        $row++;
        $sheet->setCellValue('D' . $row, 'Location-based total:');
        $sheet->setCellValue('F' . $row, round($locationTotal, 2));
        $row++;
        $sheet->setCellValue('D' . $row, 'Market-based total:');
        $sheet->setCellValue('F' . $row, round($marketTotal, 2));

        $row++;
        $sheet->setCellValue('E' . $row, 'TOTAL SCOPE 2:');
        $sheet->setCellValue('F' . $row, round(max($locationTotal, $marketTotal), 2));
        $sheet->getStyle('E' . $row . ':F' . $row)->getFont()->setBold(true);
        $sheet->getStyle('A' . $row . ':F' . $row)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('FCE4D6');

        // Column widths
        $sheet->getColumnDimension('A')->setWidth(35);
        $sheet->getColumnDimension('B')->setWidth(25);
        $sheet->getColumnDimension('C')->setWidth(18);
        $sheet->getColumnDimension('D')->setWidth(15);
        $sheet->getColumnDimension('E')->setWidth(15);
        $sheet->getColumnDimension('F')->setWidth(18);
    }

    /**
     * Create Scope 3 sheet.
     */
    private function createScope3Sheet(
        string $organizationId,
        ?string $siteId,
        Carbon $startDate,
        Carbon $endDate
    ): void {
        $sheet = $this->spreadsheet->createSheet();
        $sheet->setTitle('Scope 3');

        $sheet->setCellValue('A1', 'SCOPE 3 - OTHER INDIRECT GHG EMISSIONS');
        $sheet->mergeCells('A1:E1');
        $this->applyHeaderStyle($sheet, 'A1:E1');

        $sheet->setCellValue('A3', 'Definition: All other indirect emissions in the value chain');
        $sheet->mergeCells('A3:E3');

        $headers = ['Category', 'Description', 'Included', 'Emissions (tCO₂e)', 'Data Quality'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '5', $header);
            $col++;
        }
        $this->applyTableHeaderStyle($sheet, 'A5:E5');

        // Get emissions data grouped by GHG category
        $emissions = EmissionRecord::where('emission_records.organization_id', $organizationId)
            ->when($siteId, fn($q) => $q->where('emission_records.site_id', $siteId))
            ->where('emission_records.date', '>=', $startDate)
            ->where('emission_records.date', '<=', $endDate)
            ->where('emission_records.scope', 3)
            ->join('categories', 'emission_records.category_id', '=', 'categories.id')
            ->selectRaw('categories.ghg_category, SUM(co2e_kg) as total_kg')
            ->groupBy('categories.ghg_category')
            ->pluck('total_kg', 'ghg_category');

        $row = 6;
        $total = 0;
        foreach ($this->scope3Categories as $num => $name) {
            $categoryTotal = ($emissions[$num] ?? 0) / 1000;
            $included = $categoryTotal > 0 ? 'Yes' : 'Not relevant';
            $dataQuality = $categoryTotal > 0 ? 'Estimated' : '-';

            $sheet->setCellValue('A' . $row, "Category $num");
            $sheet->setCellValue('B' . $row, $name);
            $sheet->setCellValue('C' . $row, $included);
            $sheet->setCellValue('D' . $row, round($categoryTotal, 2));
            $sheet->setCellValue('E' . $row, $dataQuality);

            $total += $categoryTotal;
            $row++;
        }

        // Total
        $row++;
        $sheet->setCellValue('C' . $row, 'TOTAL SCOPE 3:');
        $sheet->setCellValue('D' . $row, round($total, 2));
        $sheet->getStyle('C' . $row . ':D' . $row)->getFont()->setBold(true);
        $sheet->getStyle('A' . $row . ':E' . $row)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('DDEBF7');

        // Number format
        $sheet->getStyle('D6:D' . $row)->getNumberFormat()
            ->setFormatCode('#,##0.00');

        // Column widths
        $sheet->getColumnDimension('A')->setWidth(15);
        $sheet->getColumnDimension('B')->setWidth(45);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(15);
    }

    /**
     * Create methodology sheet.
     */
    private function createMethodologySheet(Organization $organization): void
    {
        $sheet = $this->spreadsheet->createSheet();
        $sheet->setTitle('Methodology');

        $sheet->setCellValue('A1', 'METHODOLOGY AND VERIFICATION');
        $sheet->mergeCells('A1:C1');
        $this->applyHeaderStyle($sheet, 'A1:C1');

        $content = [
            ['ORGANIZATIONAL BOUNDARY', ''],
            ['Consolidation Approach', 'Operational Control'],
            ['Legal Entities Included', $organization->name],
            ['Geographic Coverage', $organization->country ?? 'France'],
            ['', ''],
            ['OPERATIONAL BOUNDARY', ''],
            ['Scope 1', 'All direct emissions from owned/controlled sources'],
            ['Scope 2', 'Purchased electricity (location and market-based)'],
            ['Scope 3', 'Relevant upstream and downstream categories'],
            ['', ''],
            ['EMISSION FACTORS', ''],
            ['Primary Source', 'ADEME Base Empreinte (France)'],
            ['Secondary Sources', 'IEA, DEFRA, GHG Protocol'],
            ['GWP Values', 'IPCC AR6'],
            ['', ''],
            ['DATA QUALITY', ''],
            ['Scope 1 Uncertainty', '±10%'],
            ['Scope 2 Uncertainty', '±15%'],
            ['Scope 3 Uncertainty', '±30%'],
            ['', ''],
            ['VERIFICATION', ''],
            ['Verification Status', 'Not externally verified'],
            ['Verification Body', 'N/A'],
            ['', ''],
            ['EXCLUSIONS', ''],
            ['Excluded Sources', 'None material'],
            ['Justification', 'All material sources included'],
        ];

        $row = 3;
        foreach ($content as $item) {
            $sheet->setCellValue('A' . $row, $item[0]);
            $sheet->setCellValue('B' . $row, $item[1]);
            if (str_contains($item[0], 'BOUNDARY') || str_contains($item[0], 'FACTORS') ||
                str_contains($item[0], 'QUALITY') || str_contains($item[0], 'VERIFICATION') ||
                str_contains($item[0], 'EXCLUSIONS')) {
                $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(11);
                $sheet->getStyle('A' . $row)->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('D9E1F2');
            } elseif ($item[0]) {
                $sheet->getStyle('A' . $row)->getFont()->setBold(true);
            }
            $row++;
        }

        $sheet->getColumnDimension('A')->setWidth(30);
        $sheet->getColumnDimension('B')->setWidth(50);
    }

    /**
     * Create history sheet for year-over-year comparison.
     */
    private function createHistorySheet(string $organizationId): void
    {
        $sheet = $this->spreadsheet->createSheet();
        $sheet->setTitle('Historical Data');

        $sheet->setCellValue('A1', 'HISTORICAL EMISSIONS DATA');
        $sheet->mergeCells('A1:E1');
        $this->applyHeaderStyle($sheet, 'A1:E1');

        $headers = ['Year', 'Scope 1 (tCO₂e)', 'Scope 2 (tCO₂e)', 'Scope 3 (tCO₂e)', 'Total (tCO₂e)'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '3', $header);
            $col++;
        }
        $this->applyTableHeaderStyle($sheet, 'A3:E3');

        // Get historical data from reports
        $reports = Report::where('organization_id', $organizationId)
            ->where('status', 'completed')
            ->orderBy('year', 'desc')
            ->limit(5)
            ->get();

        $row = 4;
        if ($reports->count() > 0) {
            foreach ($reports as $report) {
                // Historical summary not available, show year only
                $sheet->setCellValue('A' . $row, $report->year);
                $sheet->setCellValue('B' . $row, '-');
                $sheet->setCellValue('C' . $row, '-');
                $sheet->setCellValue('D' . $row, '-');
                $sheet->setCellValue('E' . $row, '-');
                $row++;
            }
        } else {
            $sheet->setCellValue('A' . $row, 'No historical data available');
            $sheet->mergeCells('A' . $row . ':E' . $row);
        }

        // Number format
        $sheet->getStyle('B4:E' . ($row - 1))->getNumberFormat()
            ->setFormatCode('#,##0.00');

        // Column widths
        $sheet->getColumnDimension('A')->setWidth(10);
        $sheet->getColumnDimension('B')->setWidth(18);
        $sheet->getColumnDimension('C')->setWidth(18);
        $sheet->getColumnDimension('D')->setWidth(18);
        $sheet->getColumnDimension('E')->setWidth(18);
    }

    /**
     * Get emissions by scope.
     */
    private function getEmissionsByScope(
        string $organizationId,
        ?string $siteId,
        Carbon $startDate,
        Carbon $endDate,
        int $scope
    ) {
        return EmissionRecord::where('emission_records.organization_id', $organizationId)
            ->when($siteId, fn($q) => $q->where('emission_records.site_id', $siteId))
            ->where('emission_records.date', '>=', $startDate)
            ->where('emission_records.date', '<=', $endDate)
            ->where('emission_records.scope', $scope)
            ->join('categories', 'emission_records.category_id', '=', 'categories.id')
            ->select(
                'emission_records.*',
                'categories.code as category_code',
                'categories.name as category_name'
            )
            ->get();
    }

    /**
     * Apply header style.
     */
    private function applyHeaderStyle($sheet, string $range): void
    {
        $sheet->getStyle($range)->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 14,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1F4E79'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(30);
    }

    /**
     * Apply table header style.
     */
    private function applyTableHeaderStyle($sheet, string $range): void
    {
        $sheet->getStyle($range)->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '2E75B6'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);
    }

    /**
     * Save spreadsheet to storage.
     */
    private function saveSpreadsheet(Organization $organization, int $year): string
    {
        $filename = sprintf(
            'reports/%s/ghg-protocol-report_%d_%s.xlsx',
            $organization->id,
            $year,
            now()->format('Ymd_His')
        );

        $tempPath = storage_path('app/temp_' . uniqid() . '.xlsx');

        $writer = new Xlsx($this->spreadsheet);
        $writer->save($tempPath);

        // Move to final storage
        Storage::put($filename, file_get_contents($tempPath));

        // Clean up
        unlink($tempPath);

        return $filename;
    }

    /**
     * Get file size.
     */
    public function getFileSize(string $filename): int
    {
        return Storage::size($filename);
    }
}
