<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Emission Categories Seeder
 *
 * Seeds emission categories based on GHG Protocol and Constitution v3.0 Section 2.1
 * Categories structure: Scope 1, 2, 3 with subcategories
 */
class EmissionCategorySeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding emission categories...');

        // Scope 1 - Émissions directes
        $scope1 = $this->createCategory(null, [
            'name' => 'Scope 1 - Émissions directes',
            'code' => 'scope_1',
            'scope' => 1,
            'ghg_category' => 'direct_emissions',
            'description' => 'Émissions provenant de sources détenues ou contrôlées par l\'entreprise',
            'icon' => 'heroicon-o-fire',
            'sort_order' => 1,
            'translations' => [
                'en' => ['name' => 'Scope 1 - Direct emissions'],
                'de' => ['name' => 'Scope 1 - Direkte Emissionen'],
            ],
        ]);

        $this->createCategory($scope1->id, [
            'name' => 'Sources fixes de combustion',
            'code' => '1.1',
            'scope' => 1,
            'ghg_category' => 'stationary_combustion',
            'description' => 'Chaudières, fours, turbines, chauffages',
            'default_unit' => 'kWh',
            'calculation_method' => 'activity_based',
            'sort_order' => 1,
            'translations' => ['en' => ['name' => 'Stationary combustion']],
        ]);

        $this->createCategory($scope1->id, [
            'name' => 'Sources mobiles de combustion',
            'code' => '1.2',
            'scope' => 1,
            'ghg_category' => 'mobile_combustion',
            'description' => 'Véhicules de l\'entreprise, engins',
            'default_unit' => 'liter',
            'calculation_method' => 'activity_based',
            'sort_order' => 2,
            'translations' => ['en' => ['name' => 'Mobile combustion']],
        ]);

        $this->createCategory($scope1->id, [
            'name' => 'Émissions fugitives',
            'code' => '1.4',
            'scope' => 1,
            'ghg_category' => 'fugitive_emissions',
            'description' => 'Fuites de fluides frigorigènes, climatisation',
            'default_unit' => 'kg',
            'calculation_method' => 'activity_based',
            'sort_order' => 3,
            'translations' => ['en' => ['name' => 'Fugitive emissions']],
        ]);

        $this->createCategory($scope1->id, [
            'name' => 'Biomasse (sols et forêts)',
            'code' => '1.5',
            'scope' => 1,
            'ghg_category' => 'biomass',
            'description' => 'Changement d\'affectation des sols',
            'default_unit' => 'ha',
            'calculation_method' => 'activity_based',
            'sort_order' => 4,
            'translations' => ['en' => ['name' => 'Biomass (land and forests)']],
        ]);

        // Fuel category (pour AdemeFactorSeeder)
        $this->createCategory($scope1->id, [
            'name' => 'Combustibles',
            'code' => 'fuel',
            'scope' => 1,
            'ghg_category' => 'fuel_combustion',
            'description' => 'Tous types de combustibles (carburants, chauffage)',
            'default_unit' => 'liter',
            'calculation_method' => 'activity_based',
            'sort_order' => 5,
            'keywords' => ['diesel', 'essence', 'gazole', 'fioul', 'gaz naturel'],
            'translations' => ['en' => ['name' => 'Fuels']],
        ]);

        $this->command->info('  - Scope 1: 6 categories');

        // Scope 2 - Émissions indirectes liées à l'énergie
        $scope2 = $this->createCategory(null, [
            'name' => 'Scope 2 - Émissions indirectes liées à l\'énergie',
            'code' => 'scope_2',
            'scope' => 2,
            'ghg_category' => 'indirect_energy',
            'description' => 'Émissions liées à la consommation d\'électricité, chaleur et froid',
            'icon' => 'heroicon-o-bolt',
            'sort_order' => 2,
            'translations' => [
                'en' => ['name' => 'Scope 2 - Indirect energy emissions'],
                'de' => ['name' => 'Scope 2 - Indirekte energiebezogene Emissionen'],
            ],
        ]);

        $this->createCategory($scope2->id, [
            'name' => 'Consommation d\'électricité',
            'code' => '2.1',
            'scope' => 2,
            'ghg_category' => 'purchased_electricity',
            'description' => 'Électricité achetée et consommée',
            'default_unit' => 'kWh',
            'calculation_method' => 'activity_based',
            'sort_order' => 1,
            'translations' => ['en' => ['name' => 'Electricity consumption']],
        ]);

        // Electricity category (pour AdemeFactorSeeder)
        $this->createCategory($scope2->id, [
            'name' => 'Électricité et chaleur',
            'code' => 'electricity',
            'scope' => 2,
            'ghg_category' => 'purchased_electricity',
            'description' => 'Électricité, réseaux de chaleur et de froid',
            'default_unit' => 'kWh',
            'calculation_method' => 'activity_based',
            'sort_order' => 2,
            'keywords' => ['électricité', 'kwh', 'réseau chaleur', 'chauffage urbain'],
            'translations' => ['en' => ['name' => 'Electricity and heat']],
        ]);

        $this->command->info('  - Scope 2: 3 categories');

        // Scope 3 - Autres émissions indirectes
        $scope3 = $this->createCategory(null, [
            'name' => 'Scope 3 - Autres émissions indirectes',
            'code' => 'scope_3',
            'scope' => 3,
            'ghg_category' => 'other_indirect',
            'description' => 'Toutes les autres émissions indirectes de la chaîne de valeur',
            'icon' => 'heroicon-o-globe-alt',
            'sort_order' => 3,
            'translations' => [
                'en' => ['name' => 'Scope 3 - Other indirect emissions'],
                'de' => ['name' => 'Scope 3 - Sonstige indirekte Emissionen'],
            ],
        ]);

        // Transport amont
        $this->createCategory($scope3->id, [
            'name' => 'Transport de marchandise amont',
            'code' => '3.1',
            'scope' => 3,
            'scope_3_category' => 4,
            'ghg_category' => 'upstream_transportation',
            'description' => 'Transport des achats vers l\'entreprise',
            'default_unit' => 'tkm',
            'calculation_method' => 'activity_based',
            'sort_order' => 1,
            'translations' => ['en' => ['name' => 'Upstream transportation']],
        ]);

        // upstream_transport (pour AdemeFactorSeeder)
        $this->createCategory($scope3->id, [
            'name' => 'Transport amont (fret)',
            'code' => 'upstream_transport',
            'scope' => 3,
            'scope_3_category' => 4,
            'ghg_category' => 'upstream_transportation',
            'description' => 'Transport et distribution amont de marchandises',
            'default_unit' => 'tkm',
            'calculation_method' => 'activity_based',
            'sort_order' => 2,
            'keywords' => ['fret', 'transport', 'camion', 'bateau', 'avion', 'train'],
            'translations' => ['en' => ['name' => 'Upstream freight transport']],
        ]);

        // Transport aval
        $this->createCategory($scope3->id, [
            'name' => 'Transport de marchandise aval',
            'code' => '3.2',
            'scope' => 3,
            'scope_3_category' => 9,
            'ghg_category' => 'downstream_transportation',
            'description' => 'Transport des produits vers les clients',
            'default_unit' => 'tkm',
            'calculation_method' => 'activity_based',
            'sort_order' => 3,
            'translations' => ['en' => ['name' => 'Downstream transportation']],
        ]);

        // downstream_transport (pour AdemeFactorSeeder)
        $this->createCategory($scope3->id, [
            'name' => 'Transport aval (fret)',
            'code' => 'downstream_transport',
            'scope' => 3,
            'scope_3_category' => 9,
            'ghg_category' => 'downstream_transportation',
            'description' => 'Transport et distribution aval de marchandises',
            'default_unit' => 'tkm',
            'calculation_method' => 'activity_based',
            'sort_order' => 4,
            'keywords' => ['livraison', 'distribution', 'expédition'],
            'translations' => ['en' => ['name' => 'Downstream freight transport']],
        ]);

        // Déplacements domicile-travail
        $this->createCategory($scope3->id, [
            'name' => 'Déplacements domicile-travail',
            'code' => '3.3',
            'scope' => 3,
            'scope_3_category' => 7,
            'ghg_category' => 'employee_commuting',
            'description' => 'Trajets quotidiens des employés',
            'default_unit' => 'km',
            'calculation_method' => 'activity_based',
            'sort_order' => 5,
            'translations' => ['en' => ['name' => 'Employee commuting']],
        ]);

        // employee_commuting (pour AdemeFactorSeeder)
        $this->createCategory($scope3->id, [
            'name' => 'Trajets domicile-travail',
            'code' => 'employee_commuting',
            'scope' => 3,
            'scope_3_category' => 7,
            'ghg_category' => 'employee_commuting',
            'description' => 'Déplacements des employés entre domicile et lieu de travail',
            'default_unit' => 'km',
            'calculation_method' => 'activity_based',
            'sort_order' => 6,
            'keywords' => ['trajet', 'domicile', 'travail', 'voiture', 'bus', 'métro'],
            'translations' => ['en' => ['name' => 'Employee commuting']],
        ]);

        // Déplacements professionnels
        $this->createCategory($scope3->id, [
            'name' => 'Déplacements professionnels',
            'code' => '3.5',
            'scope' => 3,
            'scope_3_category' => 6,
            'ghg_category' => 'business_travel',
            'description' => 'Voyages d\'affaires (avion, train, voiture)',
            'default_unit' => 'pkm',
            'calculation_method' => 'activity_based',
            'sort_order' => 7,
            'translations' => ['en' => ['name' => 'Business travel']],
        ]);

        // business_travel (pour AdemeFactorSeeder)
        $this->createCategory($scope3->id, [
            'name' => 'Voyages d\'affaires',
            'code' => 'business_travel',
            'scope' => 3,
            'scope_3_category' => 6,
            'ghg_category' => 'business_travel',
            'description' => 'Déplacements professionnels des employés',
            'default_unit' => 'pkm',
            'calculation_method' => 'activity_based',
            'sort_order' => 8,
            'keywords' => ['voyage', 'avion', 'train', 'hôtel', 'mission'],
            'translations' => ['en' => ['name' => 'Business travel']],
        ]);

        // Achats de biens
        $this->createCategory($scope3->id, [
            'name' => 'Achats de biens',
            'code' => '4.1',
            'scope' => 3,
            'scope_3_category' => 1,
            'ghg_category' => 'purchased_goods',
            'description' => 'Matières premières, produits finis achetés',
            'default_unit' => 'EUR',
            'calculation_method' => 'spend_based',
            'sort_order' => 9,
            'translations' => ['en' => ['name' => 'Purchased goods']],
        ]);

        // purchased_goods (pour AdemeFactorSeeder)
        $this->createCategory($scope3->id, [
            'name' => 'Biens et services achetés',
            'code' => 'purchased_goods',
            'scope' => 3,
            'scope_3_category' => 1,
            'ghg_category' => 'purchased_goods',
            'description' => 'Achats de biens et services (scope 3.1)',
            'default_unit' => 'EUR',
            'calculation_method' => 'spend_based',
            'sort_order' => 10,
            'keywords' => ['achat', 'fourniture', 'équipement', 'matériel', 'service'],
            'mcc_codes' => ['5311', '5411', '5611', '5651', '5691', '5999'],
            'translations' => ['en' => ['name' => 'Purchased goods and services']],
        ]);

        // Immobilisations
        $this->createCategory($scope3->id, [
            'name' => 'Immobilisations de biens',
            'code' => '4.2',
            'scope' => 3,
            'scope_3_category' => 2,
            'ghg_category' => 'capital_goods',
            'description' => 'Équipements, machines, bâtiments',
            'default_unit' => 'EUR',
            'calculation_method' => 'spend_based',
            'sort_order' => 11,
            'translations' => ['en' => ['name' => 'Capital goods']],
        ]);

        // Déchets
        $this->createCategory($scope3->id, [
            'name' => 'Gestion des déchets',
            'code' => '4.3',
            'scope' => 3,
            'scope_3_category' => 5,
            'ghg_category' => 'waste',
            'description' => 'Traitement et élimination des déchets',
            'default_unit' => 'kg',
            'calculation_method' => 'activity_based',
            'sort_order' => 12,
            'translations' => ['en' => ['name' => 'Waste management']],
        ]);

        // waste (pour AdemeFactorSeeder)
        $this->createCategory($scope3->id, [
            'name' => 'Déchets générés',
            'code' => 'waste',
            'scope' => 3,
            'scope_3_category' => 5,
            'ghg_category' => 'waste',
            'description' => 'Déchets générés par les opérations',
            'default_unit' => 'kg',
            'calculation_method' => 'activity_based',
            'sort_order' => 13,
            'keywords' => ['déchet', 'recyclage', 'incinération', 'enfouissement'],
            'translations' => ['en' => ['name' => 'Waste generated']],
        ]);

        // Leasing amont
        $this->createCategory($scope3->id, [
            'name' => 'Actifs en leasing amont',
            'code' => '4.4',
            'scope' => 3,
            'scope_3_category' => 8,
            'ghg_category' => 'upstream_leased_assets',
            'description' => 'Équipements loués par l\'entreprise',
            'default_unit' => 'EUR',
            'calculation_method' => 'spend_based',
            'sort_order' => 14,
            'translations' => ['en' => ['name' => 'Upstream leased assets']],
        ]);

        // Achats de services
        $this->createCategory($scope3->id, [
            'name' => 'Achats de services',
            'code' => '4.5',
            'scope' => 3,
            'scope_3_category' => 1,
            'ghg_category' => 'purchased_services',
            'description' => 'Services externalisés (conseil, IT, etc.)',
            'default_unit' => 'EUR',
            'calculation_method' => 'spend_based',
            'sort_order' => 15,
            'keywords' => ['conseil', 'informatique', 'comptabilité', 'juridique'],
            'mcc_codes' => ['7311', '7372', '7392', '8111', '8911'],
            'translations' => ['en' => ['name' => 'Purchased services']],
        ]);

        $this->command->info('  - Scope 3: 16 categories');
        $this->command->info('Emission categories seeded successfully. Total: 25 categories');
    }

    /**
     * Create or update a category.
     */
    private function createCategory(?string $parentId, array $data): Category
    {
        return Category::updateOrCreate(
            ['code' => $data['code']],
            [
                'id' => Str::uuid()->toString(),
                'parent_id' => $parentId,
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'scope' => $data['scope'],
                'ghg_category' => $data['ghg_category'],
                'scope_3_category' => $data['scope_3_category'] ?? null,
                'default_unit' => $data['default_unit'] ?? 'EUR',
                'calculation_method' => $data['calculation_method'] ?? 'spend_based',
                'icon' => $data['icon'] ?? null,
                'color' => $data['color'] ?? null,
                'sort_order' => $data['sort_order'] ?? 0,
                'is_active' => true,
                'keywords' => $data['keywords'] ?? null,
                'mcc_codes' => $data['mcc_codes'] ?? null,
                'translations' => $data['translations'] ?? null,
            ]
        );
    }
}
