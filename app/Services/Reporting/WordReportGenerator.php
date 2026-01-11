<?php

namespace App\Services\Reporting;

use App\Models\Assessment;
use App\Models\Organization;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Shared\Converter;
use PhpOffice\PhpWord\Style\Font;

/**
 * Word Report Generator - Generates complete carbon footprint reports in DOCX format
 *
 * Constitution Carbex v3.0 - Section 5.2, T073
 *
 * Report structure:
 * - Cover page
 * - Table of contents
 * - Introduction and scope
 * - Methodology (ISO 14064, ISO 14067, GHG Protocol)
 * - Results by scope
 * - Charts
 * - Action plan
 * - Appendices
 */
class WordReportGenerator
{
    private PhpWord $phpWord;

    private ReportBuilder $reportBuilder;

    private array $styles = [
        'title' => [
            'name' => 'Arial',
            'size' => 28,
            'bold' => true,
            'color' => '1F4E79',
        ],
        'heading1' => [
            'name' => 'Arial',
            'size' => 18,
            'bold' => true,
            'color' => '2E75B6',
        ],
        'heading2' => [
            'name' => 'Arial',
            'size' => 14,
            'bold' => true,
            'color' => '404040',
        ],
        'heading3' => [
            'name' => 'Arial',
            'size' => 12,
            'bold' => true,
            'color' => '595959',
        ],
        'body' => [
            'name' => 'Arial',
            'size' => 11,
            'color' => '000000',
        ],
        'caption' => [
            'name' => 'Arial',
            'size' => 10,
            'italic' => true,
            'color' => '666666',
        ],
    ];

    public function __construct(ReportBuilder $reportBuilder)
    {
        $this->reportBuilder = $reportBuilder;
    }

    /**
     * Generate a complete carbon footprint report.
     */
    public function generate(
        string $organizationId,
        int $year,
        ?string $siteId = null
    ): string {
        $this->phpWord = new PhpWord();
        $this->setupDocument();

        $startDate = Carbon::create($year, 1, 1);
        $endDate = Carbon::create($year, 12, 31);

        // Build report data
        $data = $this->reportBuilder->build(
            $organizationId,
            $startDate,
            $endDate,
            'detailed',
            $siteId
        );

        $organization = Organization::findOrFail($organizationId);

        // Generate sections
        $this->addCoverPage($organization, $year);
        $this->addTableOfContents();
        $this->addIntroduction($organization, $year);
        $this->addMethodology($data['methodology']);
        $this->addResultsSummary($data['summary']);
        $this->addScopeBreakdown($data['scope_breakdown']);
        $this->addCategoryAnalysis($data['category_breakdown']);
        $this->addTrends($data['monthly_trend']);
        $this->addActionPlan($organization);
        $this->addAppendices($data);

        // Save document
        $filename = $this->saveDocument($organization, $year);

        return $filename;
    }

    /**
     * Setup document properties and styles.
     */
    private function setupDocument(): void
    {
        // Document properties
        $properties = $this->phpWord->getDocInfo();
        $properties->setCreator('Carbex');
        $properties->setCompany('Carbex SAS');
        $properties->setTitle('Bilan Carbone');
        $properties->setDescription('Rapport de bilan carbone généré par Carbex');
        $properties->setCategory('Environnement');
        $properties->setLastModifiedBy('Carbex');

        // Define styles
        foreach ($this->styles as $name => $style) {
            $this->phpWord->addFontStyle($name, $style);
        }

        // Paragraph styles
        $this->phpWord->addParagraphStyle('pTitle', [
            'alignment' => 'center',
            'spaceAfter' => Converter::pointToTwip(24),
        ]);

        $this->phpWord->addParagraphStyle('pHeading1', [
            'spaceBefore' => Converter::pointToTwip(24),
            'spaceAfter' => Converter::pointToTwip(12),
        ]);

        $this->phpWord->addParagraphStyle('pHeading2', [
            'spaceBefore' => Converter::pointToTwip(18),
            'spaceAfter' => Converter::pointToTwip(6),
        ]);

        $this->phpWord->addParagraphStyle('pBody', [
            'alignment' => 'both',
            'spaceAfter' => Converter::pointToTwip(6),
            'lineHeight' => 1.5,
        ]);

        // Table styles
        $this->phpWord->addTableStyle('dataTable', [
            'borderSize' => 1,
            'borderColor' => 'CCCCCC',
            'cellMargin' => Converter::pointToTwip(5),
        ], [
            'bgColor' => '2E75B6',
            'color' => 'FFFFFF',
            'bold' => true,
        ]);
    }

    /**
     * Add cover page.
     */
    private function addCoverPage(Organization $organization, int $year): void
    {
        $section = $this->phpWord->addSection();

        // Add vertical space
        $section->addTextBreak(8);

        // Title
        $section->addText(
            'BILAN CARBONE',
            'title',
            'pTitle'
        );

        $section->addText(
            $organization->name,
            ['name' => 'Arial', 'size' => 20, 'color' => '404040'],
            'pTitle'
        );

        $section->addText(
            "Année {$year}",
            ['name' => 'Arial', 'size' => 16, 'color' => '666666'],
            'pTitle'
        );

        $section->addTextBreak(4);

        // Organization info box
        $table = $section->addTable();
        $table->addRow();
        $cell = $table->addCell(Converter::cmToTwip(16));
        $cell->getStyle()->setBgColor('F5F5F5');
        $cell->getStyle()->setBorderSize(1);
        $cell->getStyle()->setBorderColor('DDDDDD');

        $textRun = $cell->addTextRun(['alignment' => 'center']);
        $textRun->addText("Secteur : ", 'caption');
        $textRun->addText($organization->sector ?? 'Non spécifié', 'body');
        $textRun->addTextBreak();
        $textRun->addText("Pays : ", 'caption');
        $textRun->addText($organization->country ?? 'France', 'body');
        $textRun->addTextBreak();
        $textRun->addText("Effectif : ", 'caption');
        $textRun->addText($organization->employee_count ?? '-', 'body');

        $section->addTextBreak(6);

        // Footer with generation date
        $section->addText(
            'Rapport généré le ' . now()->format('d/m/Y à H:i'),
            'caption',
            'pTitle'
        );

        $section->addText(
            'Généré par Carbex - www.carbex.fr',
            ['name' => 'Arial', 'size' => 9, 'color' => '999999'],
            'pTitle'
        );
    }

    /**
     * Add table of contents.
     */
    private function addTableOfContents(): void
    {
        $section = $this->phpWord->addSection();
        $section->addText('Sommaire', 'heading1', 'pHeading1');
        $section->addTOC(['tabLeader' => 'dot']);
    }

    /**
     * Add introduction section.
     */
    private function addIntroduction(Organization $organization, int $year): void
    {
        $section = $this->phpWord->addSection();

        $section->addTitle('1. Introduction', 1);

        $section->addText('1.1 Contexte', 'heading2', 'pHeading2');
        $section->addText(
            "Ce document présente le bilan des émissions de gaz à effet de serre (GES) de {$organization->name} pour l'année {$year}. " .
            "Ce bilan a été réalisé conformément aux normes internationales ISO 14064 et au GHG Protocol.",
            'body',
            'pBody'
        );

        $section->addText('1.2 Périmètre', 'heading2', 'pHeading2');
        $section->addText(
            "Le périmètre de ce bilan couvre l'ensemble des activités de l'organisation, incluant :",
            'body',
            'pBody'
        );

        $textRun = $section->addTextRun('pBody');
        $textRun->addText("• Scope 1 : ", ['bold' => true]);
        $textRun->addText("Émissions directes (combustion, véhicules de société, fuites de réfrigérants)");

        $section->addTextBreak(0);
        $textRun = $section->addTextRun('pBody');
        $textRun->addText("• Scope 2 : ", ['bold' => true]);
        $textRun->addText("Émissions indirectes liées à l'énergie (électricité, chaleur)");

        $section->addTextBreak(0);
        $textRun = $section->addTextRun('pBody');
        $textRun->addText("• Scope 3 : ", ['bold' => true]);
        $textRun->addText("Autres émissions indirectes (achats, déplacements, déchets)");

        $section->addText('1.3 Objectifs', 'heading2', 'pHeading2');
        $section->addText(
            "Les objectifs de ce bilan carbone sont :" . PHP_EOL .
            "• Quantifier les émissions de GES de l'organisation" . PHP_EOL .
            "• Identifier les principales sources d'émissions" . PHP_EOL .
            "• Définir des axes de réduction prioritaires" . PHP_EOL .
            "• Suivre l'évolution des émissions dans le temps",
            'body',
            'pBody'
        );
    }

    /**
     * Add methodology section.
     */
    private function addMethodology(array $methodology): void
    {
        $section = $this->phpWord->addSection();

        $section->addTitle('2. Méthodologie', 1);

        $section->addText('2.1 Référentiels', 'heading2', 'pHeading2');
        $section->addText(
            "Ce bilan carbone a été réalisé en conformité avec les référentiels suivants :" . PHP_EOL .
            "• ISO 14064-1 : Spécifications pour la quantification des émissions de GES" . PHP_EOL .
            "• ISO 14067 : Empreinte carbone des produits" . PHP_EOL .
            "• GHG Protocol Corporate Standard" . PHP_EOL .
            "• Méthode Bilan Carbone® de l'ADEME",
            'body',
            'pBody'
        );

        $section->addText('2.2 Sources des facteurs d\'émission', 'heading2', 'pHeading2');
        $section->addText(
            "Les facteurs d'émission utilisés proviennent de : " .
            $methodology['emission_source']['name'] . " (version " . $methodology['emission_source']['version'] . ").",
            'body',
            'pBody'
        );

        $section->addText('2.3 Méthodes de calcul', 'heading2', 'pHeading2');

        $table = $section->addTable('dataTable');
        $table->addRow();
        $table->addCell(Converter::cmToTwip(5))->addText('Méthode', null, ['alignment' => 'center']);
        $table->addCell(Converter::cmToTwip(10))->addText('Description', null, ['alignment' => 'center']);

        foreach ($methodology['calculation_methods'] as $method => $description) {
            $table->addRow();
            $table->addCell(Converter::cmToTwip(5))->addText(ucfirst(str_replace('_', ' ', $method)), 'body');
            $table->addCell(Converter::cmToTwip(10))->addText($description, 'body');
        }

        $section->addTextBreak();
        $section->addText(
            "Note : " . $methodology['uncertainty'],
            'caption',
            'pBody'
        );
    }

    /**
     * Add results summary section.
     */
    private function addResultsSummary(array $summary): void
    {
        $section = $this->phpWord->addSection();

        $section->addTitle('3. Résultats', 1);

        $section->addText('3.1 Synthèse des émissions', 'heading2', 'pHeading2');

        // Summary table
        $table = $section->addTable('dataTable');

        $table->addRow();
        $table->addCell(Converter::cmToTwip(6))->addText('Indicateur', null, ['alignment' => 'center']);
        $table->addCell(Converter::cmToTwip(5))->addText('Valeur', null, ['alignment' => 'center']);
        $table->addCell(Converter::cmToTwip(4))->addText('Unité', null, ['alignment' => 'center']);

        $rows = [
            ['Émissions totales', number_format($summary['total_emissions']['tonnes'], 2, ',', ' '), 'tCO₂e'],
            ['Scope 1', number_format($summary['scope_1']['tonnes'], 2, ',', ' '), 'tCO₂e'],
            ['Scope 2', number_format($summary['scope_2']['tonnes'], 2, ',', ' '), 'tCO₂e'],
            ['Scope 3', number_format($summary['scope_3']['tonnes'], 2, ',', ' '), 'tCO₂e'],
        ];

        foreach ($rows as $row) {
            $table->addRow();
            $table->addCell(Converter::cmToTwip(6))->addText($row[0], 'body');
            $table->addCell(Converter::cmToTwip(5))->addText($row[1], ['bold' => true], ['alignment' => 'right']);
            $table->addCell(Converter::cmToTwip(4))->addText($row[2], 'body', ['alignment' => 'center']);
        }

        $section->addTextBreak();

        // Percentages
        $total = $summary['total_emissions']['tonnes'];
        if ($total > 0) {
            $scope1Pct = round(($summary['scope_1']['tonnes'] / $total) * 100, 1);
            $scope2Pct = round(($summary['scope_2']['tonnes'] / $total) * 100, 1);
            $scope3Pct = round(($summary['scope_3']['tonnes'] / $total) * 100, 1);

            $section->addText(
                "Répartition : Scope 1 ({$scope1Pct}%) | Scope 2 ({$scope2Pct}%) | Scope 3 ({$scope3Pct}%)",
                'body',
                'pBody'
            );
        }
    }

    /**
     * Add scope breakdown section.
     */
    private function addScopeBreakdown(array $scopeBreakdown): void
    {
        $section = $this->phpWord->addSection();

        $section->addText('3.2 Détail par scope', 'heading2', 'pHeading2');

        foreach ([1, 2, 3] as $scope) {
            $scopeData = collect($scopeBreakdown)->firstWhere('scope', $scope);
            if (! $scopeData) {
                continue;
            }

            $scopeName = match ($scope) {
                1 => 'Scope 1 - Émissions directes',
                2 => 'Scope 2 - Émissions indirectes liées à l\'énergie',
                3 => 'Scope 3 - Autres émissions indirectes',
            };

            $section->addText($scopeName, 'heading3', 'pHeading2');
            $section->addText(
                "Total : " . number_format($scopeData['emissions_tonnes'] ?? 0, 2, ',', ' ') . " tCO₂e " .
                "(" . ($scopeData['percent'] ?? 0) . "% du total)",
                'body',
                'pBody'
            );
        }
    }

    /**
     * Add category analysis section.
     */
    private function addCategoryAnalysis(array $categories): void
    {
        $section = $this->phpWord->addSection();

        $section->addTitle('4. Analyse par catégorie', 1);

        if (empty($categories)) {
            $section->addText('Aucune donnée disponible pour cette période.', 'body', 'pBody');

            return;
        }

        $table = $section->addTable('dataTable');

        $table->addRow();
        $table->addCell(Converter::cmToTwip(6))->addText('Catégorie', null, ['alignment' => 'center']);
        $table->addCell(Converter::cmToTwip(2))->addText('Scope', null, ['alignment' => 'center']);
        $table->addCell(Converter::cmToTwip(4))->addText('Émissions (tCO₂e)', null, ['alignment' => 'center']);
        $table->addCell(Converter::cmToTwip(3))->addText('Part (%)', null, ['alignment' => 'center']);

        foreach (array_slice($categories, 0, 15) as $category) {
            $table->addRow();
            $table->addCell(Converter::cmToTwip(6))->addText($category['name'] ?? '-', 'body');
            $table->addCell(Converter::cmToTwip(2))->addText($category['scope'] ?? '-', 'body', ['alignment' => 'center']);
            $table->addCell(Converter::cmToTwip(4))->addText(
                number_format($category['emissions_tonnes'] ?? 0, 2, ',', ' '),
                'body',
                ['alignment' => 'right']
            );
            $table->addCell(Converter::cmToTwip(3))->addText(
                ($category['percent'] ?? 0) . '%',
                'body',
                ['alignment' => 'center']
            );
        }
    }

    /**
     * Add trends section.
     */
    private function addTrends(array $trends): void
    {
        $section = $this->phpWord->addSection();

        $section->addTitle('5. Évolution mensuelle', 1);

        $section->addText(
            'Le graphique ci-dessous présente l\'évolution mensuelle des émissions par scope.',
            'body',
            'pBody'
        );

        // Note: Actual chart generation would require additional libraries
        // For now, we add a placeholder text
        $section->addText(
            '[Graphique d\'évolution mensuelle - voir rapport PDF pour visualisation]',
            'caption',
            ['alignment' => 'center']
        );

        // Add monthly data table as alternative
        if (! empty($trends['categories']) && ! empty($trends['series'])) {
            $section->addTextBreak();

            $table = $section->addTable('dataTable');

            // Header row
            $table->addRow();
            $table->addCell(Converter::cmToTwip(3))->addText('Mois', null, ['alignment' => 'center']);
            foreach ($trends['series'] as $series) {
                $table->addCell(Converter::cmToTwip(3))->addText($series['name'], null, ['alignment' => 'center']);
            }

            // Data rows
            foreach ($trends['categories'] as $index => $month) {
                $table->addRow();
                $table->addCell(Converter::cmToTwip(3))->addText($month, 'body');
                foreach ($trends['series'] as $series) {
                    $value = $series['data'][$index] ?? 0;
                    $table->addCell(Converter::cmToTwip(3))->addText(
                        number_format($value, 1, ',', ' '),
                        'body',
                        ['alignment' => 'right']
                    );
                }
            }
        }
    }

    /**
     * Add action plan section.
     */
    private function addActionPlan(Organization $organization): void
    {
        $section = $this->phpWord->addSection();

        $section->addTitle('6. Plan d\'action', 1);

        $section->addText(
            'Sur la base de ce bilan, les axes de réduction prioritaires identifiés sont :',
            'body',
            'pBody'
        );

        // Get actions from database if available
        $actions = \App\Models\Action::where('organization_id', $organization->id)
            ->where('status', '!=', 'completed')
            ->orderByDesc('co2_reduction_percent')
            ->limit(10)
            ->get();

        if ($actions->count() > 0) {
            $table = $section->addTable('dataTable');

            $table->addRow();
            $table->addCell(Converter::cmToTwip(7))->addText('Action', null, ['alignment' => 'center']);
            $table->addCell(Converter::cmToTwip(3))->addText('Réduction estimée', null, ['alignment' => 'center']);
            $table->addCell(Converter::cmToTwip(3))->addText('Difficulté', null, ['alignment' => 'center']);
            $table->addCell(Converter::cmToTwip(2))->addText('Statut', null, ['alignment' => 'center']);

            foreach ($actions as $action) {
                $table->addRow();
                $table->addCell(Converter::cmToTwip(7))->addText($action->title, 'body');
                $table->addCell(Converter::cmToTwip(3))->addText(
                    ($action->co2_reduction_percent ?? '-') . '%',
                    'body',
                    ['alignment' => 'center']
                );
                $table->addCell(Converter::cmToTwip(3))->addText(
                    $action->difficulty_label ?? '-',
                    'body',
                    ['alignment' => 'center']
                );
                $table->addCell(Converter::cmToTwip(2))->addText(
                    $action->status_label ?? '-',
                    'body',
                    ['alignment' => 'center']
                );
            }
        } else {
            $section->addText(
                'Aucune action de réduction n\'a encore été définie. ' .
                'Utilisez la plateforme Carbex pour créer votre plan de transition.',
                'body',
                'pBody'
            );
        }

        $section->addTextBreak();
        $section->addText('6.1 Recommandations générales', 'heading2', 'pHeading2');
        $section->addText(
            "• Privilégier les énergies renouvelables pour réduire les émissions Scope 2" . PHP_EOL .
            "• Optimiser les déplacements professionnels (visioconférence, covoiturage)" . PHP_EOL .
            "• Sensibiliser les collaborateurs aux éco-gestes" . PHP_EOL .
            "• Travailler avec les fournisseurs sur la réduction de leur empreinte carbone" . PHP_EOL .
            "• Mettre en place un suivi régulier des indicateurs clés",
            'body',
            'pBody'
        );
    }

    /**
     * Add appendices.
     */
    private function addAppendices(array $data): void
    {
        $section = $this->phpWord->addSection();

        $section->addTitle('7. Annexes', 1);

        $section->addText('7.1 Glossaire', 'heading2', 'pHeading2');

        $glossary = [
            'GES' => 'Gaz à Effet de Serre',
            'tCO₂e' => 'Tonnes d\'équivalent CO₂',
            'Scope 1' => 'Émissions directes de l\'organisation',
            'Scope 2' => 'Émissions indirectes liées à l\'énergie',
            'Scope 3' => 'Autres émissions indirectes dans la chaîne de valeur',
            'SBTi' => 'Science Based Targets initiative',
            'GHG Protocol' => 'Greenhouse Gas Protocol',
        ];

        foreach ($glossary as $term => $definition) {
            $textRun = $section->addTextRun('pBody');
            $textRun->addText($term . ' : ', ['bold' => true]);
            $textRun->addText($definition);
        }

        $section->addText('7.2 Sources et références', 'heading2', 'pHeading2');
        $section->addText(
            "• " . $data['methodology']['emission_source']['name'] . " - " .
            $data['methodology']['emission_source']['url'] . PHP_EOL .
            "• GHG Protocol - https://ghgprotocol.org/" . PHP_EOL .
            "• ADEME - https://www.ademe.fr/" . PHP_EOL .
            "• Science Based Targets - https://sciencebasedtargets.org/",
            'body',
            'pBody'
        );
    }

    /**
     * Save document to storage.
     */
    private function saveDocument(Organization $organization, int $year): string
    {
        $filename = sprintf(
            'reports/%s/bilan-carbone_%d_%s.docx',
            $organization->id,
            $year,
            now()->format('Ymd_His')
        );

        $tempPath = storage_path('app/temp_' . uniqid() . '.docx');

        $writer = IOFactory::createWriter($this->phpWord, 'Word2007');
        $writer->save($tempPath);

        // Move to final storage
        Storage::put($filename, file_get_contents($tempPath));

        // Clean up temp file
        unlink($tempPath);

        return $filename;
    }

    /**
     * Get file size of generated document.
     */
    public function getFileSize(string $filename): int
    {
        return Storage::size($filename);
    }
}
