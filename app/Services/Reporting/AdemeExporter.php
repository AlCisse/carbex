<?php

namespace App\Services\Reporting;

use App\Models\Assessment;
use App\Models\EmissionRecord;
use App\Models\Organization;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

/**
 * ADEME Exporter - Export compatible with bilans-ges.ademe.fr
 *
 * Constitution Carbex v3.0 - Section 5.3, T074
 *
 * Generates Excel file in ADEME BEGES format:
 * - Organization identification
 * - Emissions by scope and category
 * - Methodology and uncertainty
 * - Reduction actions
 */
class AdemeExporter
{
    private Spreadsheet $spreadsheet;

    private ReportBuilder $reportBuilder;

    /**
     * ADEME category mapping.
     */
    private array $ademeCategories = [
        '1.1' => ['poste' => 1, 'name' => 'Émissions directes des sources fixes de combustion'],
        '1.2' => ['poste' => 2, 'name' => 'Émissions directes des sources mobiles à moteur thermique'],
        '1.4' => ['poste' => 3, 'name' => 'Émissions directes fugitives'],
        '1.5' => ['poste' => 4, 'name' => 'Émissions issues de la biomasse (sols et forêts)'],
        '2.1' => ['poste' => 5, 'name' => 'Émissions indirectes liées à la consommation d\'électricité'],
        '2.2' => ['poste' => 6, 'name' => 'Émissions indirectes liées à la consommation de vapeur, chaleur ou froid'],
        '3.1' => ['poste' => 7, 'name' => 'Achats de produits ou services'],
        '3.2' => ['poste' => 8, 'name' => 'Immobilisations de biens'],
        '3.3' => ['poste' => 9, 'name' => 'Déchets'],
        '3.5' => ['poste' => 10, 'name' => 'Transport de marchandise amont'],
        '4.1' => ['poste' => 11, 'name' => 'Transport de marchandise aval'],
        '4.2' => ['poste' => 12, 'name' => 'Déplacements professionnels'],
        '4.3' => ['poste' => 13, 'name' => 'Actifs en leasing amont'],
        '4.4' => ['poste' => 14, 'name' => 'Investissements'],
        '4.5' => ['poste' => 15, 'name' => 'Transport des visiteurs et des clients'],
        '5.1' => ['poste' => 16, 'name' => 'Transport de marchandise aval'],
        '5.2' => ['poste' => 17, 'name' => 'Utilisation des produits vendus'],
        '5.3' => ['poste' => 18, 'name' => 'Fin de vie des produits vendus'],
        '5.4' => ['poste' => 19, 'name' => 'Franchise aval'],
        '5.5' => ['poste' => 20, 'name' => 'Leasing aval'],
        '6.1' => ['poste' => 21, 'name' => 'Déplacements domicile travail'],
        '6.2' => ['poste' => 22, 'name' => 'Autres émissions indirectes'],
    ];

    public function __construct(ReportBuilder $reportBuilder)
    {
        $this->reportBuilder = $reportBuilder;
    }

    /**
     * Generate ADEME export.
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

        // Get emissions by category
        $emissions = $this->getEmissionsByCategory($organizationId, $siteId, $startDate, $endDate);

        // Create sheets
        $this->createIdentificationSheet($organization, $year);
        $this->createEmissionsSheet($emissions, $data['summary']);
        $this->createMethodologySheet();
        $this->createActionsSheet($organization);

        // Save and return filename
        return $this->saveSpreadsheet($organization, $year);
    }

    /**
     * Get emissions grouped by ADEME category.
     */
    private function getEmissionsByCategory(
        string $organizationId,
        ?string $siteId,
        Carbon $startDate,
        Carbon $endDate
    ): array {
        $emissions = EmissionRecord::where('organization_id', $organizationId)
            ->when($siteId, fn($q) => $q->where('site_id', $siteId))
            ->where('date', '>=', $startDate)
            ->where('date', '<=', $endDate)
            ->join('categories', 'emission_records.category_id', '=', 'categories.id')
            ->selectRaw('categories.code, categories.scope, SUM(co2e_kg) as total_kg')
            ->groupBy('categories.code', 'categories.scope')
            ->get();

        $result = [];
        foreach ($this->ademeCategories as $code => $info) {
            $emission = $emissions->firstWhere('code', $code);
            $result[$code] = [
                'poste' => $info['poste'],
                'name' => $info['name'],
                'code' => $code,
                'emissions_kg' => $emission ? (float) $emission->total_kg : 0,
                'emissions_tonnes' => $emission ? round((float) $emission->total_kg / 1000, 2) : 0,
            ];
        }

        return $result;
    }

    /**
     * Create identification sheet.
     */
    private function createIdentificationSheet(Organization $organization, int $year): void
    {
        $sheet = $this->spreadsheet->createSheet();
        $sheet->setTitle('Identification');

        // Title
        $sheet->setCellValue('A1', 'BILAN GES - IDENTIFICATION DE L\'ORGANISATION');
        $sheet->mergeCells('A1:D1');
        $this->applyHeaderStyle($sheet, 'A1:D1');

        // Organization info
        $data = [
            ['Raison sociale', $organization->name],
            ['SIREN/SIRET', $organization->siret ?? 'Non renseigné'],
            ['Code APE/NAF', $organization->naf_code ?? 'Non renseigné'],
            ['Secteur d\'activité', $organization->sector ?? 'Non renseigné'],
            ['', ''],
            ['Adresse', $organization->address ?? 'Non renseignée'],
            ['Code postal', $organization->postal_code ?? ''],
            ['Ville', $organization->city ?? ''],
            ['Pays', $organization->country ?? 'France'],
            ['', ''],
            ['Effectif', $organization->employee_count ?? 'Non renseigné'],
            ['Chiffre d\'affaires', 'Non renseigné'],
            ['', ''],
            ['Année de reporting', $year],
            ['Date de génération', now()->format('d/m/Y')],
            ['Outil utilisé', 'Carbex (www.carbex.fr)'],
        ];

        $row = 3;
        foreach ($data as $item) {
            $sheet->setCellValue('A' . $row, $item[0]);
            $sheet->setCellValue('B' . $row, $item[1]);
            if ($item[0]) {
                $sheet->getStyle('A' . $row)->getFont()->setBold(true);
            }
            $row++;
        }

        // Column widths
        $sheet->getColumnDimension('A')->setWidth(25);
        $sheet->getColumnDimension('B')->setWidth(40);
    }

    /**
     * Create emissions sheet.
     */
    private function createEmissionsSheet(array $emissions, array $summary): void
    {
        $sheet = $this->spreadsheet->createSheet();
        $sheet->setTitle('Émissions GES');

        // Header
        $sheet->setCellValue('A1', 'BILAN DES ÉMISSIONS DE GAZ À EFFET DE SERRE');
        $sheet->mergeCells('A1:E1');
        $this->applyHeaderStyle($sheet, 'A1:E1');

        // Summary
        $sheet->setCellValue('A3', 'SYNTHÈSE');
        $sheet->getStyle('A3')->getFont()->setBold(true);

        $sheet->setCellValue('A4', 'Total des émissions');
        $sheet->setCellValue('B4', number_format($summary['total_emissions']['tonnes'], 2, ',', ' '));
        $sheet->setCellValue('C4', 'tCO₂e');

        $sheet->setCellValue('A5', 'Scope 1');
        $sheet->setCellValue('B5', number_format($summary['scope_1']['tonnes'], 2, ',', ' '));
        $sheet->setCellValue('C5', 'tCO₂e');

        $sheet->setCellValue('A6', 'Scope 2');
        $sheet->setCellValue('B6', number_format($summary['scope_2']['tonnes'], 2, ',', ' '));
        $sheet->setCellValue('C6', 'tCO₂e');

        $sheet->setCellValue('A7', 'Scope 3');
        $sheet->setCellValue('B7', number_format($summary['scope_3']['tonnes'], 2, ',', ' '));
        $sheet->setCellValue('C7', 'tCO₂e');

        // Detailed table header
        $headerRow = 10;
        $headers = ['N° Poste', 'Catégorie d\'émission', 'Code', 'Émissions (tCO₂e)', 'Incertitude (%)'];

        foreach ($headers as $col => $header) {
            $cell = chr(65 + $col) . $headerRow;
            $sheet->setCellValue($cell, $header);
        }
        $this->applyTableHeaderStyle($sheet, 'A' . $headerRow . ':E' . $headerRow);

        // Data rows
        $row = $headerRow + 1;
        $scope1Total = 0;
        $scope2Total = 0;
        $scope3Total = 0;

        foreach ($emissions as $code => $emission) {
            $sheet->setCellValue('A' . $row, $emission['poste']);
            $sheet->setCellValue('B' . $row, $emission['name']);
            $sheet->setCellValue('C' . $row, $code);
            $sheet->setCellValue('D' . $row, $emission['emissions_tonnes']);
            $sheet->setCellValue('E' . $row, $emission['emissions_tonnes'] > 0 ? '20%' : '-');

            // Track scope totals
            $scopeNum = (int) substr($code, 0, 1);
            if ($scopeNum === 1) {
                $scope1Total += $emission['emissions_tonnes'];
            } elseif ($scopeNum === 2) {
                $scope2Total += $emission['emissions_tonnes'];
            } else {
                $scope3Total += $emission['emissions_tonnes'];
            }

            $row++;
        }

        // Scope subtotals
        $row++;
        $sheet->setCellValue('A' . $row, '');
        $sheet->setCellValue('B' . $row, 'TOTAL SCOPE 1');
        $sheet->setCellValue('D' . $row, $scope1Total);
        $sheet->getStyle('B' . $row . ':D' . $row)->getFont()->setBold(true);

        $row++;
        $sheet->setCellValue('B' . $row, 'TOTAL SCOPE 2');
        $sheet->setCellValue('D' . $row, $scope2Total);
        $sheet->getStyle('B' . $row . ':D' . $row)->getFont()->setBold(true);

        $row++;
        $sheet->setCellValue('B' . $row, 'TOTAL SCOPE 3');
        $sheet->setCellValue('D' . $row, $scope3Total);
        $sheet->getStyle('B' . $row . ':D' . $row)->getFont()->setBold(true);

        $row++;
        $sheet->setCellValue('B' . $row, 'TOTAL GÉNÉRAL');
        $sheet->setCellValue('D' . $row, $scope1Total + $scope2Total + $scope3Total);
        $sheet->getStyle('B' . $row . ':D' . $row)->getFont()->setBold(true);
        $sheet->getStyle('A' . $row . ':E' . $row)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('E2EFDA');

        // Column widths
        $sheet->getColumnDimension('A')->setWidth(12);
        $sheet->getColumnDimension('B')->setWidth(50);
        $sheet->getColumnDimension('C')->setWidth(10);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(15);

        // Number format for emissions
        $sheet->getStyle('D11:D' . $row)->getNumberFormat()
            ->setFormatCode('#,##0.00');
    }

    /**
     * Create methodology sheet.
     */
    private function createMethodologySheet(): void
    {
        $sheet = $this->spreadsheet->createSheet();
        $sheet->setTitle('Méthodologie');

        $sheet->setCellValue('A1', 'MÉTHODOLOGIE ET RÉFÉRENCES');
        $sheet->mergeCells('A1:C1');
        $this->applyHeaderStyle($sheet, 'A1:C1');

        $content = [
            ['Référentiel utilisé', 'Méthode Bilan Carbone® / GHG Protocol Corporate Standard'],
            ['Version', '2024'],
            ['', ''],
            ['Sources des facteurs d\'émission', ''],
            ['', 'Base Empreinte ADEME (https://base-empreinte.ademe.fr/)'],
            ['', 'DEFRA UK (pour certains facteurs transport)'],
            ['', 'IEA (facteurs d\'émission électricité par pays)'],
            ['', ''],
            ['Périmètre organisationnel', 'Approche contrôle opérationnel'],
            ['Périmètre opérationnel', 'Scopes 1, 2 et 3'],
            ['', ''],
            ['Incertitudes', ''],
            ['', 'Scope 1 : ±10% (données mesurées)'],
            ['', 'Scope 2 : ±15% (facteurs moyens)'],
            ['', 'Scope 3 : ±30% (estimations et facteurs monétaires)'],
            ['', ''],
            ['Exclusions', 'Aucune exclusion significative'],
            ['', ''],
            ['Vérification', 'Ce bilan n\'a pas fait l\'objet d\'une vérification externe'],
        ];

        $row = 3;
        foreach ($content as $item) {
            $sheet->setCellValue('A' . $row, $item[0]);
            $sheet->setCellValue('B' . $row, $item[1]);
            if ($item[0] && !empty($item[1])) {
                $sheet->getStyle('A' . $row)->getFont()->setBold(true);
            }
            $row++;
        }

        $sheet->getColumnDimension('A')->setWidth(30);
        $sheet->getColumnDimension('B')->setWidth(60);
    }

    /**
     * Create actions sheet.
     */
    private function createActionsSheet(Organization $organization): void
    {
        $sheet = $this->spreadsheet->createSheet();
        $sheet->setTitle('Actions de réduction');

        $sheet->setCellValue('A1', 'PLAN D\'ACTIONS DE RÉDUCTION');
        $sheet->mergeCells('A1:E1');
        $this->applyHeaderStyle($sheet, 'A1:E1');

        // Get actions
        $actions = \App\Models\Action::where('organization_id', $organization->id)
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        // Table header
        $headers = ['Action', 'Description', 'Réduction estimée (%)', 'Échéance', 'Statut'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '3', $header);
            $col++;
        }
        $this->applyTableHeaderStyle($sheet, 'A3:E3');

        // Data
        $row = 4;
        if ($actions->count() > 0) {
            foreach ($actions as $action) {
                $sheet->setCellValue('A' . $row, $action->title);
                $sheet->setCellValue('B' . $row, $action->description ?? '-');
                $sheet->setCellValue('C' . $row, $action->co2_reduction_percent ?? '-');
                $sheet->setCellValue('D' . $row, $action->due_date?->format('d/m/Y') ?? '-');
                $sheet->setCellValue('E' . $row, $action->status_label ?? '-');
                $row++;
            }
        } else {
            $sheet->setCellValue('A' . $row, 'Aucune action définie');
            $sheet->mergeCells('A' . $row . ':E' . $row);
        }

        // Column widths
        $sheet->getColumnDimension('A')->setWidth(30);
        $sheet->getColumnDimension('B')->setWidth(40);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(15);
        $sheet->getColumnDimension('E')->setWidth(15);
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
            'reports/%s/declaration-ademe_%d_%s.xlsx',
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
