<?php

namespace App\Services\Trajectory;

use App\Models\Organization;
use Illuminate\Support\Collection;

/**
 * Recommendation Engine
 *
 * Provides actionable recommendations for emission reduction
 * based on organization's emission profile and best practices.
 */
class RecommendationEngine
{
    /**
     * Recommendation categories.
     */
    public const CATEGORY_ENERGY = 'energy';
    public const CATEGORY_TRANSPORT = 'transport';
    public const CATEGORY_PROCUREMENT = 'procurement';
    public const CATEGORY_WASTE = 'waste';
    public const CATEGORY_BUILDINGS = 'buildings';
    public const CATEGORY_OPERATIONS = 'operations';
    public const CATEGORY_ENGAGEMENT = 'engagement';

    /**
     * Impact levels.
     */
    public const IMPACT_HIGH = 'high';
    public const IMPACT_MEDIUM = 'medium';
    public const IMPACT_LOW = 'low';

    /**
     * Effort levels.
     */
    public const EFFORT_LOW = 'low';
    public const EFFORT_MEDIUM = 'medium';
    public const EFFORT_HIGH = 'high';

    /**
     * Generate recommendations based on emission profile.
     */
    public function generateRecommendations(
        Organization $organization,
        array $emissionProfile
    ): Collection {
        $recommendations = collect();

        // Analyze each scope and category
        $recommendations = $recommendations->merge($this->analyzeScope1($emissionProfile['scope_1'] ?? []));
        $recommendations = $recommendations->merge($this->analyzeScope2($emissionProfile['scope_2'] ?? []));
        $recommendations = $recommendations->merge($this->analyzeScope3($emissionProfile['scope_3'] ?? []));

        // Add generic best practices
        $recommendations = $recommendations->merge($this->getGeneralRecommendations($emissionProfile));

        // Score and rank recommendations
        return $recommendations
            ->map(fn ($r) => $this->scoreRecommendation($r, $emissionProfile))
            ->sortByDesc('priority_score')
            ->values();
    }

    /**
     * Analyze Scope 1 emissions and generate recommendations.
     */
    protected function analyzeScope1(array $scope1Data): Collection
    {
        $recommendations = collect();
        $total = $scope1Data['total'] ?? 0;

        if ($total <= 0) {
            return $recommendations;
        }

        // Stationary combustion (heating, boilers)
        $stationary = $scope1Data['stationary_combustion'] ?? 0;
        if ($stationary > $total * 0.3) {
            $recommendations->push([
                'id' => 'scope1_heating_efficiency',
                'title' => 'Améliorer l\'efficacité du chauffage',
                'description' => 'Remplacer les anciens systèmes de chauffage par des pompes à chaleur ou des chaudières à condensation haute efficacité.',
                'category' => self::CATEGORY_BUILDINGS,
                'scope' => 1,
                'impact' => self::IMPACT_HIGH,
                'effort' => self::EFFORT_HIGH,
                'estimated_reduction' => '20-40%',
                'payback_years' => '3-7',
                'co_benefits' => ['Réduction des coûts énergétiques', 'Amélioration du confort'],
                'actions' => [
                    'Réaliser un audit énergétique des systèmes de chauffage',
                    'Étudier la faisabilité des pompes à chaleur',
                    'Planifier le remplacement progressif des équipements',
                ],
            ]);

            $recommendations->push([
                'id' => 'scope1_renewable_heat',
                'title' => 'Passer au chauffage renouvelable',
                'description' => 'Installer des solutions de chauffage biomasse, géothermie ou solaire thermique.',
                'category' => self::CATEGORY_ENERGY,
                'scope' => 1,
                'impact' => self::IMPACT_HIGH,
                'effort' => self::EFFORT_HIGH,
                'estimated_reduction' => '80-100%',
                'payback_years' => '5-10',
                'co_benefits' => ['Indépendance énergétique', 'Image verte'],
            ]);
        }

        // Mobile combustion (fleet)
        $mobile = $scope1Data['mobile_combustion'] ?? 0;
        if ($mobile > $total * 0.2) {
            $recommendations->push([
                'id' => 'scope1_fleet_electrification',
                'title' => 'Électrifier la flotte de véhicules',
                'description' => 'Remplacer progressivement les véhicules thermiques par des véhicules électriques ou hybrides.',
                'category' => self::CATEGORY_TRANSPORT,
                'scope' => 1,
                'impact' => self::IMPACT_HIGH,
                'effort' => self::EFFORT_MEDIUM,
                'estimated_reduction' => '50-80%',
                'payback_years' => '4-8',
                'co_benefits' => ['Réduction du bruit', 'Coûts de maintenance réduits'],
                'actions' => [
                    'Analyser les trajets et autonomies nécessaires',
                    'Identifier les véhicules prioritaires à remplacer',
                    'Installer des bornes de recharge',
                    'Former les conducteurs à l\'éco-conduite',
                ],
            ]);

            $recommendations->push([
                'id' => 'scope1_eco_driving',
                'title' => 'Former à l\'éco-conduite',
                'description' => 'Former les conducteurs aux techniques d\'éco-conduite pour réduire la consommation de carburant.',
                'category' => self::CATEGORY_TRANSPORT,
                'scope' => 1,
                'impact' => self::IMPACT_MEDIUM,
                'effort' => self::EFFORT_LOW,
                'estimated_reduction' => '10-15%',
                'payback_years' => '<1',
                'co_benefits' => ['Réduction des accidents', 'Économies de carburant'],
            ]);
        }

        // Refrigerants
        $fugitive = $scope1Data['fugitive_emissions'] ?? 0;
        if ($fugitive > $total * 0.1) {
            $recommendations->push([
                'id' => 'scope1_refrigerant_management',
                'title' => 'Optimiser la gestion des fluides frigorigènes',
                'description' => 'Améliorer la maintenance des équipements de réfrigération et passer à des fluides à faible GWP.',
                'category' => self::CATEGORY_OPERATIONS,
                'scope' => 1,
                'impact' => self::IMPACT_MEDIUM,
                'effort' => self::EFFORT_MEDIUM,
                'estimated_reduction' => '30-70%',
                'payback_years' => '2-5',
                'actions' => [
                    'Inventorier tous les équipements frigorifiques',
                    'Mettre en place un programme de détection des fuites',
                    'Planifier le remplacement par des fluides naturels (CO2, ammoniac)',
                ],
            ]);
        }

        return $recommendations;
    }

    /**
     * Analyze Scope 2 emissions and generate recommendations.
     */
    protected function analyzeScope2(array $scope2Data): Collection
    {
        $recommendations = collect();
        $electricity = $scope2Data['electricity'] ?? 0;
        $heat = $scope2Data['heat'] ?? 0;

        if ($electricity > 0) {
            $recommendations->push([
                'id' => 'scope2_green_electricity',
                'title' => 'Souscrire à un contrat d\'électricité verte',
                'description' => 'Acheter de l\'électricité 100% renouvelable avec garanties d\'origine.',
                'category' => self::CATEGORY_ENERGY,
                'scope' => 2,
                'impact' => self::IMPACT_HIGH,
                'effort' => self::EFFORT_LOW,
                'estimated_reduction' => '100%',
                'payback_years' => '0',
                'co_benefits' => ['Soutien aux énergies renouvelables', 'Communication positive'],
                'actions' => [
                    'Comparer les offres d\'électricité verte',
                    'Privilégier les garanties d\'origine françaises',
                    'Négocier un PPA (Power Purchase Agreement) si consommation importante',
                ],
            ]);

            $recommendations->push([
                'id' => 'scope2_solar_panels',
                'title' => 'Installer des panneaux solaires',
                'description' => 'Produire votre propre électricité renouvelable avec des installations photovoltaïques.',
                'category' => self::CATEGORY_ENERGY,
                'scope' => 2,
                'impact' => self::IMPACT_HIGH,
                'effort' => self::EFFORT_HIGH,
                'estimated_reduction' => '30-70%',
                'payback_years' => '6-10',
                'co_benefits' => ['Réduction des coûts à long terme', 'Résilience énergétique'],
            ]);

            $recommendations->push([
                'id' => 'scope2_energy_efficiency',
                'title' => 'Améliorer l\'efficacité énergétique',
                'description' => 'Réduire la consommation d\'électricité par des mesures d\'efficacité (LED, équipements A+++, etc.).',
                'category' => self::CATEGORY_BUILDINGS,
                'scope' => 2,
                'impact' => self::IMPACT_MEDIUM,
                'effort' => self::EFFORT_MEDIUM,
                'estimated_reduction' => '15-30%',
                'payback_years' => '2-5',
                'actions' => [
                    'Passer à l\'éclairage LED',
                    'Installer des détecteurs de présence',
                    'Optimiser les systèmes de climatisation',
                    'Remplacer les équipements énergivores',
                ],
            ]);
        }

        if ($heat > 0) {
            $recommendations->push([
                'id' => 'scope2_district_heating',
                'title' => 'Se raccorder au réseau de chaleur',
                'description' => 'Se connecter à un réseau de chaleur urbain alimenté par des sources renouvelables.',
                'category' => self::CATEGORY_ENERGY,
                'scope' => 2,
                'impact' => self::IMPACT_MEDIUM,
                'effort' => self::EFFORT_MEDIUM,
                'estimated_reduction' => '40-80%',
                'payback_years' => '5-10',
            ]);
        }

        return $recommendations;
    }

    /**
     * Analyze Scope 3 emissions and generate recommendations.
     */
    protected function analyzeScope3(array $scope3Data): Collection
    {
        $recommendations = collect();

        // Category 1: Purchased goods and services
        $purchasedGoods = $scope3Data['purchased_goods'] ?? 0;
        if ($purchasedGoods > 0) {
            $recommendations->push([
                'id' => 'scope3_sustainable_procurement',
                'title' => 'Mettre en place des achats responsables',
                'description' => 'Intégrer des critères environnementaux dans la sélection des fournisseurs.',
                'category' => self::CATEGORY_PROCUREMENT,
                'scope' => 3,
                'impact' => self::IMPACT_HIGH,
                'effort' => self::EFFORT_MEDIUM,
                'estimated_reduction' => '10-30%',
                'payback_years' => '1-3',
                'actions' => [
                    'Évaluer l\'empreinte carbone des fournisseurs clés',
                    'Ajouter des critères carbone dans les appels d\'offres',
                    'Privilégier les fournisseurs locaux',
                    'Encourager les fournisseurs à réduire leurs émissions',
                ],
            ]);

            $recommendations->push([
                'id' => 'scope3_supplier_engagement',
                'title' => 'Engager les fournisseurs sur le climat',
                'description' => 'Accompagner les fournisseurs clés dans leur démarche de réduction des émissions.',
                'category' => self::CATEGORY_ENGAGEMENT,
                'scope' => 3,
                'impact' => self::IMPACT_HIGH,
                'effort' => self::EFFORT_HIGH,
                'estimated_reduction' => '15-40%',
                'payback_years' => '2-5',
            ]);
        }

        // Category 6: Business travel
        $travel = $scope3Data['business_travel'] ?? 0;
        if ($travel > 0) {
            $recommendations->push([
                'id' => 'scope3_travel_policy',
                'title' => 'Réviser la politique de déplacements',
                'description' => 'Favoriser les alternatives au transport aérien et le télétravail.',
                'category' => self::CATEGORY_TRANSPORT,
                'scope' => 3,
                'impact' => self::IMPACT_MEDIUM,
                'effort' => self::EFFORT_LOW,
                'estimated_reduction' => '30-50%',
                'payback_years' => '<1',
                'actions' => [
                    'Privilégier le train pour les trajets < 4h',
                    'Encourager la visioconférence',
                    'Fixer des objectifs de réduction des vols',
                    'Compenser les émissions résiduelles',
                ],
            ]);
        }

        // Category 7: Employee commuting
        $commuting = $scope3Data['employee_commuting'] ?? 0;
        if ($commuting > 0) {
            $recommendations->push([
                'id' => 'scope3_mobility_plan',
                'title' => 'Mettre en place un plan de mobilité',
                'description' => 'Encourager les modes de transport durables pour les trajets domicile-travail.',
                'category' => self::CATEGORY_TRANSPORT,
                'scope' => 3,
                'impact' => self::IMPACT_MEDIUM,
                'effort' => self::EFFORT_MEDIUM,
                'estimated_reduction' => '20-40%',
                'payback_years' => '1-3',
                'actions' => [
                    'Subventionner les transports en commun',
                    'Installer des parkings vélos sécurisés',
                    'Proposer des bornes de recharge électrique',
                    'Mettre en place le forfait mobilités durables',
                    'Favoriser le télétravail',
                ],
            ]);
        }

        // Category 5: Waste
        $waste = $scope3Data['waste'] ?? 0;
        if ($waste > 0) {
            $recommendations->push([
                'id' => 'scope3_waste_reduction',
                'title' => 'Réduire et valoriser les déchets',
                'description' => 'Mettre en place le tri sélectif et réduire les déchets à la source.',
                'category' => self::CATEGORY_WASTE,
                'scope' => 3,
                'impact' => self::IMPACT_LOW,
                'effort' => self::EFFORT_LOW,
                'estimated_reduction' => '30-60%',
                'payback_years' => '<1',
                'actions' => [
                    'Installer des points de tri',
                    'Supprimer le plastique à usage unique',
                    'Mettre en place le compostage',
                    'Sensibiliser les collaborateurs',
                ],
            ]);
        }

        return $recommendations;
    }

    /**
     * Get general recommendations.
     */
    protected function getGeneralRecommendations(array $emissionProfile): Collection
    {
        return collect([
            [
                'id' => 'general_carbon_strategy',
                'title' => 'Définir une stratégie carbone',
                'description' => 'Formaliser une stratégie climat avec des objectifs SBTi alignés.',
                'category' => self::CATEGORY_OPERATIONS,
                'scope' => 0,
                'impact' => self::IMPACT_HIGH,
                'effort' => self::EFFORT_MEDIUM,
                'estimated_reduction' => 'Variable',
                'payback_years' => 'N/A',
                'actions' => [
                    'Nommer un responsable climat',
                    'Fixer des objectifs de réduction alignés SBTi',
                    'Communiquer les engagements',
                    'Mettre en place un reporting régulier',
                ],
            ],
            [
                'id' => 'general_employee_engagement',
                'title' => 'Sensibiliser les collaborateurs',
                'description' => 'Former et impliquer les équipes dans la démarche de réduction.',
                'category' => self::CATEGORY_ENGAGEMENT,
                'scope' => 0,
                'impact' => self::IMPACT_MEDIUM,
                'effort' => self::EFFORT_LOW,
                'estimated_reduction' => '5-15%',
                'payback_years' => '<1',
            ],
        ]);
    }

    /**
     * Score and prioritize a recommendation.
     */
    protected function scoreRecommendation(array $recommendation, array $emissionProfile): array
    {
        $impactScores = [
            self::IMPACT_HIGH => 3,
            self::IMPACT_MEDIUM => 2,
            self::IMPACT_LOW => 1,
        ];

        $effortScores = [
            self::EFFORT_LOW => 3,
            self::EFFORT_MEDIUM => 2,
            self::EFFORT_HIGH => 1,
        ];

        $impactScore = $impactScores[$recommendation['impact']] ?? 0;
        $effortScore = $effortScores[$recommendation['effort']] ?? 0;

        // Priority = Impact × Ease of implementation
        $priorityScore = $impactScore * $effortScore;

        $recommendation['priority_score'] = $priorityScore;
        $recommendation['priority'] = match (true) {
            $priorityScore >= 6 => 'quick_win',
            $priorityScore >= 4 => 'priority',
            default => 'long_term',
        };

        return $recommendation;
    }

    /**
     * Get recommendations by priority.
     */
    public function getByPriority(Collection $recommendations): array
    {
        return [
            'quick_wins' => $recommendations->where('priority', 'quick_win')->values(),
            'priorities' => $recommendations->where('priority', 'priority')->values(),
            'long_term' => $recommendations->where('priority', 'long_term')->values(),
        ];
    }

    /**
     * Get recommendations by category.
     */
    public function getByCategory(Collection $recommendations): array
    {
        return $recommendations->groupBy('category')->toArray();
    }

    /**
     * Get recommendations by scope.
     */
    public function getByScope(Collection $recommendations): array
    {
        return $recommendations->groupBy('scope')->toArray();
    }
}
