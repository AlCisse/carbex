<?php

namespace App\Services\AI;

use App\Models\Assessment;
use App\Models\EmissionFactor;
use App\Models\EmissionRecord;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

/**
 * EmissionClassifier
 *
 * Service IA pour la classification et l'aide à la saisie des émissions.
 * Utilise le provider IA configuré dans l'admin pour les suggestions intelligentes.
 */
class EmissionClassifier
{
    protected AIManager $aiManager;

    protected PromptLibrary $prompts;

    /**
     * Category codes mapping.
     */
    protected array $categories = [
        '1.1' => 'Sources fixes de combustion',
        '1.2' => 'Sources mobiles de combustion',
        '1.4' => 'Émissions fugitives',
        '1.5' => 'Biomasse (sols et forêts)',
        '2.1' => 'Consommation d\'électricité',
        '3.1' => 'Transport de marchandise amont',
        '3.2' => 'Transport de marchandise aval',
        '3.3' => 'Déplacements domicile-travail',
        '3.5' => 'Déplacements professionnels',
        '4.1' => 'Achats de biens',
        '4.2' => 'Immobilisations de biens',
        '4.3' => 'Gestion des déchets',
        '4.4' => 'Actifs en leasing amont',
        '4.5' => 'Achats de services',
    ];

    public function __construct(AIManager $aiManager)
    {
        $this->aiManager = $aiManager;
    }

    /**
     * Suggest the most appropriate emission category for a description.
     *
     * @return array{category_code: string, category_name: string, confidence: float, reasoning: string}
     */
    public function suggestCategory(string $description): array
    {
        $cacheKey = 'emission_category_' . md5($description);

        return Cache::remember($cacheKey, 3600, function () use ($description) {
            $prompt = $this->buildCategoryPrompt($description);
            $result = $this->aiManager->json($prompt);

            if (!$result) {
                return $this->fallbackCategory($description);
            }

            return [
                'category_code' => $result['category_code'] ?? '4.5',
                'category_name' => $result['category_name'] ?? $this->categories['4.5'],
                'confidence' => (float) ($result['confidence'] ?? 0.5),
                'reasoning' => $result['reasoning'] ?? '',
            ];
        });
    }

    /**
     * Suggest an emission factor for a description within a category.
     */
    public function suggestFactor(string $description, string $categoryCode): ?EmissionFactor
    {
        // First try semantic search in ADEME factors
        $factors = $this->searchFactors($description, $categoryCode, 5);

        if ($factors->isEmpty()) {
            return null;
        }

        // If we have a clear match, return it
        $topFactor = $factors->first();

        // Use AI to pick the best one if multiple options
        if ($factors->count() > 1) {
            $bestFactorId = $this->aiPickBestFactor($description, $factors);
            if ($bestFactorId) {
                $topFactor = $factors->firstWhere('id', $bestFactorId) ?? $topFactor;
            }
        }

        return $topFactor;
    }

    /**
     * Detect anomalies in an assessment's emission data.
     *
     * @return array<int, array{type: string, message: string, severity: string, record_id?: string}>
     */
    public function detectAnomalies(Assessment $assessment): array
    {
        $anomalies = [];
        $records = $assessment->emissionRecords()->with('emissionFactor')->get();

        // Group by category for analysis
        $byCategory = $records->groupBy('ghg_category');

        foreach ($byCategory as $category => $categoryRecords) {
            // Check for duplicate entries
            $anomalies = array_merge($anomalies, $this->checkDuplicates($categoryRecords));

            // Check for outliers (values significantly different from average)
            $anomalies = array_merge($anomalies, $this->checkOutliers($categoryRecords));

            // Check for unit inconsistencies
            $anomalies = array_merge($anomalies, $this->checkUnitInconsistencies($categoryRecords));
        }

        // Check for missing common categories
        $anomalies = array_merge($anomalies, $this->checkMissingCategories($assessment, $byCategory->keys()->toArray()));

        // Use AI for deeper analysis if enabled
        if ($this->aiManager->isAvailable() && $records->count() > 0) {
            $aiAnomalies = $this->aiDetectAnomalies($assessment, $records);
            $anomalies = array_merge($anomalies, $aiAnomalies);
        }

        return $anomalies;
    }

    /**
     * Get suggestions for a specific category and sector.
     *
     * @return array<int, array{suggestion: string, description: string, typical_unit: string, typical_factor?: string}>
     */
    public function getCategorySuggestions(string $categoryCode, string $sector): array
    {
        $cacheKey = "category_suggestions_{$categoryCode}_{$sector}";

        return Cache::remember($cacheKey, 86400, function () use ($categoryCode, $sector) {
            if (!$this->aiManager->isAvailable()) {
                return $this->getDefaultSuggestions($categoryCode);
            }

            $prompt = $this->buildSuggestionsPrompt($categoryCode, $sector);
            $result = $this->aiManager->json($prompt);

            if (!$result || empty($result['suggestions'])) {
                return $this->getDefaultSuggestions($categoryCode);
            }

            return $result['suggestions'];
        });
    }

    /**
     * Explain why an emission entry might be incorrect.
     */
    public function explainIssue(EmissionRecord $record, string $issueType): string
    {
        if (!$this->aiManager->isAvailable()) {
            return $this->getDefaultIssueExplanation($issueType);
        }

        $prompt = $this->buildIssueExplanationPrompt($record, $issueType);

        return $this->aiManager->prompt($prompt) ?? $this->getDefaultIssueExplanation($issueType);
    }

    /**
     * Auto-complete a partial emission description.
     *
     * @return array<string>
     */
    public function autoComplete(string $partial, string $categoryCode): array
    {
        // Get matching factors from database
        $factors = EmissionFactor::where(function ($q) use ($categoryCode) {
            $scope = (int) substr($categoryCode, 0, 1);
            $q->where('scope', $scope);
        })
            ->where(function ($q) use ($partial) {
                $q->where('name', 'ILIKE', "%{$partial}%")
                    ->orWhere('name_en', 'ILIKE', "%{$partial}%")
                    ->orWhere('description', 'ILIKE', "%{$partial}%");
            })
            ->limit(10)
            ->pluck('name')
            ->toArray();

        return $factors;
    }

    /**
     * Search emission factors with semantic matching.
     */
    protected function searchFactors(string $query, string $categoryCode, int $limit = 5): Collection
    {
        $scope = (int) substr($categoryCode, 0, 1);

        // Basic keyword search (can be enhanced with embeddings/RAG)
        return EmissionFactor::where('scope', $scope)
            ->where(function ($q) use ($query) {
                $terms = explode(' ', strtolower($query));
                foreach ($terms as $term) {
                    if (strlen($term) >= 3) {
                        $q->where(function ($subQ) use ($term) {
                            $subQ->where('name', 'ILIKE', "%{$term}%")
                                ->orWhere('name_en', 'ILIKE', "%{$term}%")
                                ->orWhere('description', 'ILIKE', "%{$term}%");
                        });
                    }
                }
            })
            ->orderByDesc('factor_kg_co2e')
            ->limit($limit)
            ->get();
    }

    /**
     * Use AI to pick the best factor from candidates.
     */
    protected function aiPickBestFactor(string $description, Collection $factors): ?string
    {
        if (!$this->aiManager->isAvailable()) {
            return null;
        }

        $factorsList = $factors->map(fn ($f) => [
            'id' => $f->id,
            'name' => $f->translated_name,
            'unit' => $f->unit,
            'value' => $f->factor_kg_co2e,
        ])->toArray();

        $prompt = <<<PROMPT
Pour la description: "{$description}"

Choisis le facteur d'émission le plus approprié parmi:
{$this->formatFactorsForPrompt($factorsList)}

Réponds UNIQUEMENT avec le JSON:
{"best_factor_id": "uuid-du-facteur", "reasoning": "courte explication"}
PROMPT;

        $result = $this->aiManager->json($prompt);

        return $result['best_factor_id'] ?? null;
    }

    /**
     * Build the category suggestion prompt.
     */
    protected function buildCategoryPrompt(string $description): string
    {
        $categoriesJson = json_encode($this->categories, JSON_UNESCAPED_UNICODE);

        return <<<PROMPT
Tu es un expert en bilan carbone. Catégorise cette source d'émission.

Description: "{$description}"

Catégories disponibles (code => nom):
{$categoriesJson}

Réponds UNIQUEMENT avec le JSON suivant:
{
    "category_code": "X.X",
    "category_name": "Nom de la catégorie",
    "confidence": 0.0-1.0,
    "reasoning": "Explication courte de ton choix"
}

Règles:
- Si c'est un achat de service, utilise 4.5
- Si c'est un achat de bien physique, utilise 4.1
- L'électricité va toujours en 2.1
- Les carburants pour véhicules en 1.2
- Le gaz/fioul pour chauffage en 1.1
PROMPT;
    }

    /**
     * Build the suggestions prompt.
     */
    protected function buildSuggestionsPrompt(string $categoryCode, string $sector): string
    {
        $categoryName = $this->categories[$categoryCode] ?? 'Catégorie inconnue';

        return <<<PROMPT
Tu es un expert en bilan carbone pour PME françaises.

Donne 5 suggestions de sources d'émission typiques pour:
- Catégorie: {$categoryCode} - {$categoryName}
- Secteur d'activité: {$sector}

Réponds UNIQUEMENT avec le JSON:
{
    "suggestions": [
        {
            "suggestion": "Nom de la source",
            "description": "Description courte",
            "typical_unit": "unité (kWh, L, km, kg)",
            "typical_factor": "Nom du facteur ADEME recommandé"
        }
    ]
}
PROMPT;
    }

    /**
     * Build the issue explanation prompt.
     */
    protected function buildIssueExplanationPrompt(EmissionRecord $record, string $issueType): string
    {
        return <<<PROMPT
Explique brièvement pourquoi cette entrée d'émission peut être incorrecte.

Type de problème: {$issueType}
Source: {$record->notes}
Quantité: {$record->quantity} {$record->unit}
Émissions: {$record->co2e_kg} kgCO2e

Donne une explication courte (max 2 phrases) et une suggestion de correction.
PROMPT;
    }

    /**
     * AI-based anomaly detection.
     */
    protected function aiDetectAnomalies(Assessment $assessment, Collection $records): array
    {
        $summary = $records->groupBy('ghg_category')->map(function ($items) {
            return [
                'count' => $items->count(),
                'total_kg' => $items->sum('co2e_kg'),
                'items' => $items->take(3)->map(fn ($r) => [
                    'name' => $r->notes,
                    'quantity' => $r->quantity,
                    'unit' => $r->unit,
                    'co2e_kg' => $r->co2e_kg,
                ])->toArray(),
            ];
        })->toArray();

        $prompt = <<<PROMPT
Analyse ce bilan carbone et identifie les anomalies potentielles.

Données par catégorie:
```json
{$this->jsonEncode($summary)}
```

Réponds UNIQUEMENT avec le JSON:
{
    "anomalies": [
        {
            "type": "type_anomalie",
            "message": "Description du problème",
            "severity": "warning|error",
            "category": "code_categorie"
        }
    ]
}

Types d'anomalies à chercher:
- Valeurs anormalement élevées ou basses
- Catégories manquantes inhabituelles
- Incohérences entre catégories liées
PROMPT;

        $result = $this->aiManager->json($prompt);

        return $result['anomalies'] ?? [];
    }

    /**
     * Check for duplicate entries.
     */
    protected function checkDuplicates(Collection $records): array
    {
        $anomalies = [];
        $seen = [];

        foreach ($records as $record) {
            $key = strtolower($record->notes ?? '') . '_' . $record->quantity . '_' . $record->unit;

            if (isset($seen[$key])) {
                $anomalies[] = [
                    'type' => 'duplicate',
                    'message' => "Doublon potentiel détecté: {$record->notes}",
                    'severity' => 'warning',
                    'record_id' => $record->id,
                ];
            }

            $seen[$key] = true;
        }

        return $anomalies;
    }

    /**
     * Check for outlier values.
     */
    protected function checkOutliers(Collection $records): array
    {
        if ($records->count() < 3) {
            return [];
        }

        $anomalies = [];
        $values = $records->pluck('co2e_kg')->filter()->values();
        $mean = $values->avg();
        $stdDev = $this->standardDeviation($values);

        if ($stdDev == 0) {
            return [];
        }

        foreach ($records as $record) {
            $zScore = abs(($record->co2e_kg - $mean) / $stdDev);

            if ($zScore > 2.5) {
                $anomalies[] = [
                    'type' => 'outlier',
                    'message' => "Valeur inhabituelle: {$record->notes} ({$record->co2e_kg} kgCO2e)",
                    'severity' => $zScore > 3 ? 'error' : 'warning',
                    'record_id' => $record->id,
                ];
            }
        }

        return $anomalies;
    }

    /**
     * Check for unit inconsistencies.
     */
    protected function checkUnitInconsistencies(Collection $records): array
    {
        $anomalies = [];
        $unitGroups = $records->groupBy('unit');

        // If same factor has different units, flag it
        $factorUnits = $records->groupBy('emission_factor_id');

        foreach ($factorUnits as $factorId => $factorRecords) {
            if (!$factorId) {
                continue;
            }

            $units = $factorRecords->pluck('unit')->unique();

            if ($units->count() > 1) {
                $anomalies[] = [
                    'type' => 'unit_inconsistency',
                    'message' => "Unités différentes pour le même facteur: " . $units->implode(', '),
                    'severity' => 'warning',
                ];
            }
        }

        return $anomalies;
    }

    /**
     * Check for missing common categories.
     */
    protected function checkMissingCategories(Assessment $assessment, array $presentCategories): array
    {
        $anomalies = [];

        // Common categories that most companies should have
        $commonCategories = ['2.1', '3.3']; // Electricity and commuting

        foreach ($commonCategories as $category) {
            if (!in_array($category, $presentCategories)) {
                $anomalies[] = [
                    'type' => 'missing_category',
                    'message' => "Catégorie courante non renseignée: {$category} - {$this->categories[$category]}",
                    'severity' => 'warning',
                ];
            }
        }

        return $anomalies;
    }

    /**
     * Fallback category when AI is unavailable.
     */
    protected function fallbackCategory(string $description): array
    {
        $lower = strtolower($description);

        // Simple keyword matching
        $mappings = [
            'électricité' => '2.1',
            'electricity' => '2.1',
            'gaz' => '1.1',
            'fioul' => '1.1',
            'essence' => '1.2',
            'diesel' => '1.2',
            'carburant' => '1.2',
            'voiture' => '1.2',
            'véhicule' => '1.2',
            'avion' => '3.5',
            'train' => '3.5',
            'transport' => '3.1',
            'déchet' => '4.3',
            'achat' => '4.1',
            'service' => '4.5',
        ];

        foreach ($mappings as $keyword => $code) {
            if (str_contains($lower, $keyword)) {
                return [
                    'category_code' => $code,
                    'category_name' => $this->categories[$code],
                    'confidence' => 0.6,
                    'reasoning' => "Détection par mot-clé: {$keyword}",
                ];
            }
        }

        return [
            'category_code' => '4.5',
            'category_name' => $this->categories['4.5'],
            'confidence' => 0.3,
            'reasoning' => 'Catégorie par défaut (aucun mot-clé détecté)',
        ];
    }

    /**
     * Get default suggestions when AI is unavailable.
     */
    protected function getDefaultSuggestions(string $categoryCode): array
    {
        $defaults = [
            '1.1' => [
                ['suggestion' => 'Gaz naturel (chauffage)', 'description' => 'Consommation de gaz pour le chauffage des locaux', 'typical_unit' => 'kWh PCS'],
                ['suggestion' => 'Fioul domestique', 'description' => 'Consommation de fioul pour le chauffage', 'typical_unit' => 'L'],
            ],
            '1.2' => [
                ['suggestion' => 'Véhicules de société', 'description' => 'Carburant des véhicules de l\'entreprise', 'typical_unit' => 'L'],
                ['suggestion' => 'Diesel', 'description' => 'Consommation de diesel', 'typical_unit' => 'L'],
            ],
            '2.1' => [
                ['suggestion' => 'Électricité', 'description' => 'Consommation électrique des locaux', 'typical_unit' => 'kWh'],
            ],
            '3.3' => [
                ['suggestion' => 'Trajets domicile-travail voiture', 'description' => 'Déplacements des employés en voiture', 'typical_unit' => 'km'],
                ['suggestion' => 'Trajets domicile-travail transports', 'description' => 'Déplacements des employés en transports en commun', 'typical_unit' => 'km'],
            ],
            '3.5' => [
                ['suggestion' => 'Avion', 'description' => 'Voyages d\'affaires en avion', 'typical_unit' => 'km'],
                ['suggestion' => 'Train', 'description' => 'Voyages d\'affaires en train', 'typical_unit' => 'km'],
            ],
        ];

        return $defaults[$categoryCode] ?? [
            ['suggestion' => 'Ajouter une source', 'description' => 'Saisissez une source d\'émission', 'typical_unit' => 'unité'],
        ];
    }

    /**
     * Get default issue explanation.
     */
    protected function getDefaultIssueExplanation(string $issueType): string
    {
        $explanations = [
            'duplicate' => 'Cette entrée semble être un doublon d\'une autre entrée. Vérifiez que vous n\'avez pas saisi deux fois la même source.',
            'outlier' => 'Cette valeur est significativement différente des autres entrées similaires. Vérifiez la quantité et l\'unité.',
            'unit_inconsistency' => 'Des unités différentes sont utilisées pour le même type de source. Standardisez les unités pour faciliter la comparaison.',
            'missing_category' => 'Cette catégorie est généralement présente dans les bilans carbone. Si elle ne s\'applique pas à votre activité, vous pouvez l\'ignorer.',
        ];

        return $explanations[$issueType] ?? 'Vérifiez cette entrée pour vous assurer qu\'elle est correcte.';
    }

    /**
     * Format factors for prompt.
     */
    protected function formatFactorsForPrompt(array $factors): string
    {
        $lines = [];
        foreach ($factors as $f) {
            $lines[] = "- ID: {$f['id']} | {$f['name']} | {$f['value']} kgCO2e/{$f['unit']}";
        }

        return implode("\n", $lines);
    }

    /**
     * Calculate standard deviation.
     */
    protected function standardDeviation(Collection $values): float
    {
        $count = $values->count();

        if ($count < 2) {
            return 0;
        }

        $mean = $values->avg();
        $sumSquares = $values->sum(fn ($v) => pow($v - $mean, 2));

        return sqrt($sumSquares / ($count - 1));
    }

    /**
     * JSON encode helper.
     */
    protected function jsonEncode(array $data): string
    {
        return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
}
