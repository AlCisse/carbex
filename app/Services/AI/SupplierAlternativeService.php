<?php

namespace App\Services\AI;

use App\Models\Supplier;
use Illuminate\Support\Collection;

/**
 * SupplierAlternativeService
 *
 * Service IA pour suggérer des alternatives fournisseurs plus vertes.
 * Analyse les fournisseurs existants et recommande des options à moindre impact carbone.
 *
 * Constitution LinsCarbon v3.0 - Section 9.6 (Module Fournisseurs Scope 3)
 */
class SupplierAlternativeService
{
    public function __construct(
        protected AIManager $aiManager,
    ) {}

    /**
     * Suggère des alternatives pour un fournisseur donné.
     *
     * @return array{suggestions: array, potential_reduction: float, reasoning: string}
     */
    public function suggestAlternatives(Supplier $supplier): array
    {
        if (! $this->aiManager->isAvailable()) {
            return $this->getFallbackSuggestions($supplier);
        }

        $prompt = $this->buildPrompt($supplier);

        try {
            $response = $this->aiManager->json($prompt);

            if (! $response) {
                return $this->getFallbackSuggestions($supplier);
            }

            return [
                'suggestions' => $response['alternatives'] ?? [],
                'potential_reduction' => $response['potential_reduction_percent'] ?? 0,
                'reasoning' => $response['reasoning'] ?? '',
                'criteria' => $response['criteria'] ?? [],
            ];
        } catch (\Exception $e) {
            report($e);

            return $this->getFallbackSuggestions($supplier);
        }
    }

    /**
     * Compare l'intensité carbone de plusieurs fournisseurs.
     *
     * @param  Collection<int, Supplier>  $suppliers
     * @return array<string, array>
     */
    public function compareEmissionIntensity(Collection $suppliers): array
    {
        $comparison = [];

        foreach ($suppliers as $supplier) {
            $intensity = $this->calculateEmissionIntensity($supplier);

            $comparison[$supplier->id] = [
                'name' => $supplier->name,
                'sector' => $supplier->sector,
                'intensity' => $intensity,
                'rating' => $this->getRating($intensity, $supplier->sector),
                'annual_spend' => $supplier->annual_spend,
                'estimated_emissions' => $supplier->annual_spend ? $supplier->annual_spend * $intensity : null,
            ];
        }

        // Trier par intensité croissante
        uasort($comparison, fn ($a, $b) => $a['intensity'] <=> $b['intensity']);

        return $comparison;
    }

    /**
     * Calcule l'impact potentiel d'un changement de fournisseur.
     *
     * @return array{current_emissions: float, potential_emissions: float, reduction_kg: float, reduction_percent: float}
     */
    public function calculateSwitchingImpact(
        Supplier $currentSupplier,
        float $alternativeIntensity,
        ?int $year = null
    ): array {
        $year = $year ?? now()->year;

        $currentEmissions = $currentSupplier->getAllocatedEmissions($year);

        if (! $currentSupplier->annual_spend) {
            return [
                'current_emissions' => $currentEmissions,
                'potential_emissions' => 0,
                'reduction_kg' => 0,
                'reduction_percent' => 0,
            ];
        }

        $potentialEmissions = $currentSupplier->annual_spend * $alternativeIntensity;
        $reductionKg = max(0, $currentEmissions - $potentialEmissions);
        $reductionPercent = $currentEmissions > 0
            ? ($reductionKg / $currentEmissions) * 100
            : 0;

        return [
            'current_emissions' => round($currentEmissions, 2),
            'potential_emissions' => round($potentialEmissions, 2),
            'reduction_kg' => round($reductionKg, 2),
            'reduction_percent' => round($reductionPercent, 1),
        ];
    }

    /**
     * Analyse les fournisseurs d'une organisation et identifie les opportunités.
     *
     * @param  Collection<int, Supplier>  $suppliers
     * @return array{opportunities: array, total_potential_reduction: float}
     */
    public function identifyOpportunities(Collection $suppliers): array
    {
        $opportunities = [];
        $totalPotentialReduction = 0;

        // Grouper par secteur pour identifier les outliers
        $bySector = $suppliers->groupBy('sector');

        foreach ($bySector as $sector => $sectorSuppliers) {
            if ($sectorSuppliers->count() < 2) {
                continue;
            }

            $intensities = $sectorSuppliers->map(fn ($s) => [
                'supplier' => $s,
                'intensity' => $this->calculateEmissionIntensity($s),
            ])->sortBy('intensity');

            $avgIntensity = $intensities->avg('intensity');
            $bestIntensity = $intensities->first()['intensity'];

            // Identifier les fournisseurs au-dessus de la moyenne
            foreach ($intensities as $item) {
                if ($item['intensity'] > $avgIntensity * 1.2) {
                    $supplier = $item['supplier'];
                    $impact = $this->calculateSwitchingImpact($supplier, $bestIntensity);

                    if ($impact['reduction_percent'] > 5) {
                        $opportunities[] = [
                            'supplier_id' => $supplier->id,
                            'supplier_name' => $supplier->name,
                            'sector' => $sector,
                            'current_intensity' => $item['intensity'],
                            'best_intensity' => $bestIntensity,
                            'potential_reduction_kg' => $impact['reduction_kg'],
                            'potential_reduction_percent' => $impact['reduction_percent'],
                            'priority' => $impact['reduction_kg'] > 1000 ? 'high' : ($impact['reduction_kg'] > 100 ? 'medium' : 'low'),
                        ];

                        $totalPotentialReduction += $impact['reduction_kg'];
                    }
                }
            }
        }

        // Trier par potentiel de réduction
        usort($opportunities, fn ($a, $b) => $b['potential_reduction_kg'] <=> $a['potential_reduction_kg']);

        return [
            'opportunities' => $opportunities,
            'total_potential_reduction' => round($totalPotentialReduction, 2),
        ];
    }

    /**
     * Obtient des critères de sélection verts pour un secteur.
     */
    public function getGreenCriteria(string $sector): array
    {
        $baseCriteria = [
            'certifications' => ['ISO 14001', 'ISO 50001', 'B Corp', 'EcoVadis'],
            'transparency' => ['Publication bilan carbone', 'Objectifs SBTi', 'Rapport RSE'],
            'logistics' => ['Optimisation transport', 'Proximité géographique', 'Emballages durables'],
        ];

        $sectorSpecific = match ($sector) {
            'C' => [
                'manufacturing' => ['Économie circulaire', 'Énergies renouvelables', 'Efficacité énergétique'],
            ],
            'H' => [
                'transport' => ['Flotte électrique', 'Optimisation tournées', 'Compensation carbone'],
            ],
            'G' => [
                'retail' => ['Approvisionnement local', 'Réduction emballages', 'Logistique verte'],
            ],
            default => [],
        };

        return array_merge($baseCriteria, $sectorSpecific);
    }

    /**
     * Construit le prompt pour l'IA.
     */
    protected function buildPrompt(Supplier $supplier): string
    {
        $sector = $supplier->sector ?? 'inconnu';
        $country = $supplier->country ?? 'FR';
        $spend = $supplier->annual_spend ? number_format($supplier->annual_spend, 0, ',', ' ') . ' €' : 'non renseigné';

        return <<<PROMPT
Tu es un expert en approvisionnement durable et en réduction carbone Scope 3.

**Fournisseur actuel à analyser:**
- Nom: {$supplier->name}
- Secteur NACE: {$sector}
- Pays: {$country}
- Dépense annuelle: {$spend}

**Ta mission:**
Suggère des alternatives fournisseurs plus vertes et explique les critères de sélection.

**Réponds UNIQUEMENT en JSON avec cette structure:**
```json
{
    "alternatives": [
        {
            "type": "local",
            "description": "Fournisseur local pour réduire le transport",
            "potential_reduction": 15,
            "criteria": ["Proximité géographique", "Réduction emballages"]
        },
        {
            "type": "certified",
            "description": "Fournisseur certifié ISO 14001 ou EcoVadis",
            "potential_reduction": 20,
            "criteria": ["Certification environnementale", "Transparence carbone"]
        }
    ],
    "potential_reduction_percent": 20,
    "reasoning": "Explication de la stratégie de réduction",
    "criteria": ["Critère 1", "Critère 2", "Critère 3"]
}
```

**Règles:**
- Propose 2-4 alternatives réalistes
- Estime le potentiel de réduction en %
- Adapte au secteur et au pays
- Sois concret et actionnable
PROMPT;
    }

    /**
     * Calcule l'intensité carbone d'un fournisseur.
     */
    protected function calculateEmissionIntensity(Supplier $supplier): float
    {
        $latestEmission = $supplier->latestEmission;

        if ($latestEmission && $latestEmission->emission_intensity) {
            return (float) $latestEmission->emission_intensity;
        }

        // Utiliser les facteurs sectoriels par défaut
        return $this->getDefaultSectorIntensity($supplier->sector);
    }

    /**
     * Obtient l'intensité carbone par défaut selon le secteur.
     */
    protected function getDefaultSectorIntensity(?string $sector): float
    {
        $sectorIntensities = [
            'A' => 0.85, // Agriculture
            'B' => 0.95, // Extraction
            'C' => 0.45, // Industrie manufacturière
            'D' => 0.65, // Électricité/Gaz
            'E' => 0.35, // Eau/Déchets
            'F' => 0.38, // Construction
            'G' => 0.22, // Commerce
            'H' => 0.55, // Transport
            'I' => 0.32, // Hébergement/Restauration
            'J' => 0.18, // Information/Communication
            'K' => 0.09, // Finance/Assurance
            'L' => 0.12, // Immobilier
            'M' => 0.14, // Services professionnels
            'N' => 0.16, // Services administratifs
        ];

        return $sectorIntensities[$sector] ?? 0.28;
    }

    /**
     * Obtient une note basée sur l'intensité carbone.
     */
    protected function getRating(float $intensity, ?string $sector): string
    {
        $benchmark = $this->getDefaultSectorIntensity($sector);
        $ratio = $intensity / max(0.01, $benchmark);

        return match (true) {
            $ratio < 0.7 => 'A',
            $ratio < 0.9 => 'B',
            $ratio < 1.1 => 'C',
            $ratio < 1.3 => 'D',
            default => 'E',
        };
    }

    /**
     * Retourne des suggestions par défaut si l'IA n'est pas disponible.
     */
    protected function getFallbackSuggestions(Supplier $supplier): array
    {
        $criteria = $this->getGreenCriteria($supplier->sector ?? '');

        return [
            'suggestions' => [
                [
                    'type' => 'local',
                    'description' => __('linscarbon.suppliers.alternative.local'),
                    'potential_reduction' => 15,
                    'criteria' => [__('linscarbon.suppliers.criteria.proximity'), __('linscarbon.suppliers.criteria.reduced_transport')],
                ],
                [
                    'type' => 'certified',
                    'description' => __('linscarbon.suppliers.alternative.certified'),
                    'potential_reduction' => 20,
                    'criteria' => [__('linscarbon.suppliers.criteria.iso14001'), __('linscarbon.suppliers.criteria.transparency')],
                ],
                [
                    'type' => 'collaborative',
                    'description' => __('linscarbon.suppliers.alternative.collaborative'),
                    'potential_reduction' => 10,
                    'criteria' => [__('linscarbon.suppliers.criteria.sbti'), __('linscarbon.suppliers.criteria.partnership')],
                ],
            ],
            'potential_reduction' => 15,
            'reasoning' => __('linscarbon.suppliers.alternative.reasoning'),
            'criteria' => $criteria['certifications'] ?? [],
        ];
    }
}
