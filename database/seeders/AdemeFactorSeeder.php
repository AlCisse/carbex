<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\EmissionFactor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * ADEME Emission Factors Seeder
 *
 * Populates emission factors from ADEME Base Empreinte (France)
 * Source: https://base-empreinte.ademe.fr/
 *
 * Categories covered:
 * - Scope 1: Combustibles, véhicules
 * - Scope 2: Électricité, chaleur
 * - Scope 3: Achats, déplacements, déchets, etc.
 */
class AdemeFactorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding ADEME emission factors...');

        // Scope 1: Combustibles
        $this->seedFuelFactors();

        // Scope 2: Électricité
        $this->seedElectricityFactors();

        // Scope 3: Catégories diverses
        $this->seedTravelFactors();
        $this->seedSpendBasedFactors();
        $this->seedWasteFactors();
        $this->seedFreightFactors();

        $this->command->info('ADEME emission factors seeded successfully.');
    }

    /**
     * Seed fuel combustion factors (Scope 1).
     */
    private function seedFuelFactors(): void
    {
        $category = Category::where('code', 'fuel')->first();

        if (! $category) {
            $this->command->warn('Category "fuel" not found, skipping fuel factors.');
            return;
        }

        $factors = [
            // Carburants routiers
            [
                'name' => 'Essence sans plomb (E10)',
                'name_en' => 'Unleaded petrol (E10)',
                'source_id' => 'ADEME_FUEL_001',
                'factor_kg_co2e' => 2.28,
                'factor_kg_co2' => 2.24,
                'factor_kg_ch4' => 0.00048,
                'factor_kg_n2o' => 0.00052,
                'unit' => 'liters',
                'uncertainty_percent' => 5,
            ],
            [
                'name' => 'Gazole routier (B7)',
                'name_en' => 'Diesel (B7)',
                'source_id' => 'ADEME_FUEL_002',
                'factor_kg_co2e' => 2.51,
                'factor_kg_co2' => 2.49,
                'factor_kg_ch4' => 0.00002,
                'factor_kg_n2o' => 0.00041,
                'unit' => 'liters',
                'uncertainty_percent' => 5,
            ],
            [
                'name' => 'GPL carburant',
                'name_en' => 'LPG fuel',
                'source_id' => 'ADEME_FUEL_003',
                'factor_kg_co2e' => 1.66,
                'factor_kg_co2' => 1.65,
                'factor_kg_ch4' => 0.00015,
                'factor_kg_n2o' => 0.00006,
                'unit' => 'liters',
                'uncertainty_percent' => 5,
            ],
            [
                'name' => 'E85 (Superéthanol)',
                'name_en' => 'E85 (Flex fuel)',
                'source_id' => 'ADEME_FUEL_004',
                'factor_kg_co2e' => 1.11,
                'factor_kg_co2' => 1.08,
                'factor_kg_ch4' => 0.0004,
                'factor_kg_n2o' => 0.0004,
                'unit' => 'liters',
                'uncertainty_percent' => 10,
            ],
            // Combustibles de chauffage
            [
                'name' => 'Fioul domestique',
                'name_en' => 'Heating oil',
                'source_id' => 'ADEME_FUEL_005',
                'factor_kg_co2e' => 2.69,
                'factor_kg_co2' => 2.67,
                'factor_kg_ch4' => 0.00006,
                'factor_kg_n2o' => 0.00006,
                'unit' => 'liters',
                'uncertainty_percent' => 5,
            ],
            [
                'name' => 'Gaz naturel',
                'name_en' => 'Natural gas',
                'source_id' => 'ADEME_FUEL_006',
                'factor_kg_co2e' => 2.04,
                'factor_kg_co2' => 2.02,
                'factor_kg_ch4' => 0.00058,
                'factor_kg_n2o' => 0.00001,
                'unit' => 'm3',
                'uncertainty_percent' => 5,
            ],
            [
                'name' => 'Gaz naturel (kWh PCI)',
                'name_en' => 'Natural gas (kWh LHV)',
                'source_id' => 'ADEME_FUEL_007',
                'factor_kg_co2e' => 0.205,
                'factor_kg_co2' => 0.203,
                'factor_kg_ch4' => 0.000058,
                'factor_kg_n2o' => 0.000001,
                'unit' => 'kWh',
                'uncertainty_percent' => 5,
            ],
            [
                'name' => 'Propane',
                'name_en' => 'Propane',
                'source_id' => 'ADEME_FUEL_008',
                'factor_kg_co2e' => 3.03,
                'factor_kg_co2' => 2.98,
                'factor_kg_ch4' => 0.0001,
                'factor_kg_n2o' => 0.0001,
                'unit' => 'kg',
                'uncertainty_percent' => 5,
            ],
            [
                'name' => 'Butane',
                'name_en' => 'Butane',
                'source_id' => 'ADEME_FUEL_009',
                'factor_kg_co2e' => 3.03,
                'factor_kg_co2' => 2.98,
                'factor_kg_ch4' => 0.0001,
                'factor_kg_n2o' => 0.0001,
                'unit' => 'kg',
                'uncertainty_percent' => 5,
            ],
        ];

        foreach ($factors as $factor) {
            $this->createFactor($category->id, 1, $factor);
        }

        $this->command->info('  - Fuel factors: ' . count($factors));
    }

    /**
     * Seed electricity factors (Scope 2).
     */
    private function seedElectricityFactors(): void
    {
        $category = Category::where('code', 'electricity')->first();

        if (! $category) {
            $this->command->warn('Category "electricity" not found, skipping electricity factors.');
            return;
        }

        $factors = [
            // Location-based
            [
                'name' => 'Électricité France - Mix moyen',
                'name_en' => 'Electricity France - Average grid mix',
                'source_id' => 'ADEME_ELEC_001',
                'factor_kg_co2e' => 0.0569,
                'factor_kg_co2' => 0.0520,
                'factor_kg_ch4' => 0.000044,
                'factor_kg_n2o' => 0.0000012,
                'unit' => 'kWh',
                'uncertainty_percent' => 10,
                'methodology' => 'location-based',
            ],
            // Market-based (résiduel)
            [
                'name' => 'Électricité France - Mix résiduel',
                'name_en' => 'Electricity France - Residual mix',
                'source_id' => 'ADEME_ELEC_002',
                'factor_kg_co2e' => 0.0876,
                'factor_kg_co2' => 0.0840,
                'factor_kg_ch4' => 0.00005,
                'factor_kg_n2o' => 0.0000015,
                'unit' => 'kWh',
                'uncertainty_percent' => 15,
                'methodology' => 'market-based',
            ],
            // Heures pleines/creuses
            [
                'name' => 'Électricité France - Heures pleines',
                'name_en' => 'Electricity France - Peak hours',
                'source_id' => 'ADEME_ELEC_003',
                'factor_kg_co2e' => 0.0700,
                'factor_kg_co2' => 0.0650,
                'factor_kg_ch4' => 0.00005,
                'factor_kg_n2o' => 0.0000015,
                'unit' => 'kWh',
                'uncertainty_percent' => 15,
                'methodology' => 'location-based',
            ],
            [
                'name' => 'Électricité France - Heures creuses',
                'name_en' => 'Electricity France - Off-peak hours',
                'source_id' => 'ADEME_ELEC_004',
                'factor_kg_co2e' => 0.0450,
                'factor_kg_co2' => 0.0420,
                'factor_kg_ch4' => 0.00003,
                'factor_kg_n2o' => 0.0000010,
                'unit' => 'kWh',
                'uncertainty_percent' => 15,
                'methodology' => 'location-based',
            ],
            // Chaleur
            [
                'name' => 'Réseau de chaleur urbain - France',
                'name_en' => 'District heating - France',
                'source_id' => 'ADEME_HEAT_001',
                'factor_kg_co2e' => 0.125,
                'factor_kg_co2' => 0.120,
                'factor_kg_ch4' => 0.00005,
                'factor_kg_n2o' => 0.00001,
                'unit' => 'kWh',
                'uncertainty_percent' => 20,
            ],
            [
                'name' => 'Réseau de froid urbain - France',
                'name_en' => 'District cooling - France',
                'source_id' => 'ADEME_COOL_001',
                'factor_kg_co2e' => 0.027,
                'factor_kg_co2' => 0.025,
                'factor_kg_ch4' => 0.00002,
                'factor_kg_n2o' => 0.000005,
                'unit' => 'kWh',
                'uncertainty_percent' => 25,
            ],
        ];

        foreach ($factors as $factor) {
            $this->createFactor($category->id, 2, $factor);
        }

        $this->command->info('  - Electricity/Heat factors: ' . count($factors));
    }

    /**
     * Seed travel factors (Scope 3).
     */
    private function seedTravelFactors(): void
    {
        $travelCategory = Category::where('code', 'business_travel')->first();
        $commutingCategory = Category::where('code', 'employee_commuting')->first();

        $factors = [
            // Avion
            [
                'name' => 'Avion court-courrier (<1000 km)',
                'name_en' => 'Short-haul flight (<1000 km)',
                'source_id' => 'ADEME_FLIGHT_001',
                'factor_kg_co2e' => 0.258,
                'factor_kg_co2' => 0.249,
                'factor_kg_ch4' => 0.00001,
                'factor_kg_n2o' => 0.00001,
                'unit' => 'passenger-km',
                'uncertainty_percent' => 20,
                'category' => 'business_travel',
            ],
            [
                'name' => 'Avion moyen-courrier (1000-3500 km)',
                'name_en' => 'Medium-haul flight (1000-3500 km)',
                'source_id' => 'ADEME_FLIGHT_002',
                'factor_kg_co2e' => 0.187,
                'factor_kg_co2' => 0.180,
                'factor_kg_ch4' => 0.00001,
                'factor_kg_n2o' => 0.00001,
                'unit' => 'passenger-km',
                'uncertainty_percent' => 20,
                'category' => 'business_travel',
            ],
            [
                'name' => 'Avion long-courrier (>3500 km)',
                'name_en' => 'Long-haul flight (>3500 km)',
                'source_id' => 'ADEME_FLIGHT_003',
                'factor_kg_co2e' => 0.152,
                'factor_kg_co2' => 0.146,
                'factor_kg_ch4' => 0.00001,
                'factor_kg_n2o' => 0.00001,
                'unit' => 'passenger-km',
                'uncertainty_percent' => 20,
                'category' => 'business_travel',
            ],
            // Train
            [
                'name' => 'TGV France',
                'name_en' => 'French high-speed train (TGV)',
                'source_id' => 'ADEME_TRAIN_001',
                'factor_kg_co2e' => 0.00293,
                'factor_kg_co2' => 0.00280,
                'factor_kg_ch4' => 0.000001,
                'factor_kg_n2o' => 0.0000001,
                'unit' => 'passenger-km',
                'uncertainty_percent' => 10,
                'category' => 'business_travel',
            ],
            [
                'name' => 'TER France',
                'name_en' => 'French regional train (TER)',
                'source_id' => 'ADEME_TRAIN_002',
                'factor_kg_co2e' => 0.0298,
                'factor_kg_co2' => 0.0285,
                'factor_kg_ch4' => 0.000003,
                'factor_kg_n2o' => 0.0000003,
                'unit' => 'passenger-km',
                'uncertainty_percent' => 15,
                'category' => 'business_travel',
            ],
            [
                'name' => 'Intercités France',
                'name_en' => 'French intercity train',
                'source_id' => 'ADEME_TRAIN_003',
                'factor_kg_co2e' => 0.00569,
                'factor_kg_co2' => 0.00545,
                'factor_kg_ch4' => 0.000002,
                'factor_kg_n2o' => 0.0000002,
                'unit' => 'passenger-km',
                'uncertainty_percent' => 15,
                'category' => 'business_travel',
            ],
            // Véhicules routiers
            [
                'name' => 'Voiture moyenne - essence',
                'name_en' => 'Average car - petrol',
                'source_id' => 'ADEME_CAR_001',
                'factor_kg_co2e' => 0.193,
                'factor_kg_co2' => 0.189,
                'factor_kg_ch4' => 0.0002,
                'factor_kg_n2o' => 0.00005,
                'unit' => 'km',
                'uncertainty_percent' => 15,
                'category' => 'employee_commuting',
            ],
            [
                'name' => 'Voiture moyenne - diesel',
                'name_en' => 'Average car - diesel',
                'source_id' => 'ADEME_CAR_002',
                'factor_kg_co2e' => 0.170,
                'factor_kg_co2' => 0.167,
                'factor_kg_ch4' => 0.00003,
                'factor_kg_n2o' => 0.00006,
                'unit' => 'km',
                'uncertainty_percent' => 15,
                'category' => 'employee_commuting',
            ],
            [
                'name' => 'Voiture électrique - France',
                'name_en' => 'Electric car - France',
                'source_id' => 'ADEME_CAR_003',
                'factor_kg_co2e' => 0.019,
                'factor_kg_co2' => 0.017,
                'factor_kg_ch4' => 0.00001,
                'factor_kg_n2o' => 0.000002,
                'unit' => 'km',
                'uncertainty_percent' => 20,
                'category' => 'employee_commuting',
            ],
            [
                'name' => 'Voiture hybride',
                'name_en' => 'Hybrid car',
                'source_id' => 'ADEME_CAR_004',
                'factor_kg_co2e' => 0.111,
                'factor_kg_co2' => 0.108,
                'factor_kg_ch4' => 0.0001,
                'factor_kg_n2o' => 0.00003,
                'unit' => 'km',
                'uncertainty_percent' => 15,
                'category' => 'employee_commuting',
            ],
            // Transports en commun
            [
                'name' => 'Métro - France',
                'name_en' => 'Metro - France',
                'source_id' => 'ADEME_METRO_001',
                'factor_kg_co2e' => 0.00369,
                'factor_kg_co2' => 0.00350,
                'factor_kg_ch4' => 0.000001,
                'factor_kg_n2o' => 0.0000001,
                'unit' => 'passenger-km',
                'uncertainty_percent' => 15,
                'category' => 'employee_commuting',
            ],
            [
                'name' => 'Bus urbain',
                'name_en' => 'Urban bus',
                'source_id' => 'ADEME_BUS_001',
                'factor_kg_co2e' => 0.104,
                'factor_kg_co2' => 0.100,
                'factor_kg_ch4' => 0.00003,
                'factor_kg_n2o' => 0.00001,
                'unit' => 'passenger-km',
                'uncertainty_percent' => 20,
                'category' => 'employee_commuting',
            ],
            [
                'name' => 'Tramway - France',
                'name_en' => 'Tramway - France',
                'source_id' => 'ADEME_TRAM_001',
                'factor_kg_co2e' => 0.00369,
                'factor_kg_co2' => 0.00350,
                'factor_kg_ch4' => 0.000001,
                'factor_kg_n2o' => 0.0000001,
                'unit' => 'passenger-km',
                'uncertainty_percent' => 15,
                'category' => 'employee_commuting',
            ],
            // Taxi/VTC
            [
                'name' => 'Taxi/VTC',
                'name_en' => 'Taxi/Ride-hailing',
                'source_id' => 'ADEME_TAXI_001',
                'factor_kg_co2e' => 0.210,
                'factor_kg_co2' => 0.205,
                'factor_kg_ch4' => 0.0002,
                'factor_kg_n2o' => 0.00005,
                'unit' => 'km',
                'uncertainty_percent' => 20,
                'category' => 'business_travel',
            ],
            // Hôtel
            [
                'name' => 'Nuit hôtel - standard',
                'name_en' => 'Hotel night - standard',
                'source_id' => 'ADEME_HOTEL_001',
                'factor_kg_co2e' => 25.0,
                'factor_kg_co2' => 24.0,
                'factor_kg_ch4' => 0.02,
                'factor_kg_n2o' => 0.005,
                'unit' => 'nights',
                'uncertainty_percent' => 30,
                'category' => 'business_travel',
            ],
            [
                'name' => 'Nuit hôtel - luxe',
                'name_en' => 'Hotel night - luxury',
                'source_id' => 'ADEME_HOTEL_002',
                'factor_kg_co2e' => 45.0,
                'factor_kg_co2' => 43.0,
                'factor_kg_ch4' => 0.04,
                'factor_kg_n2o' => 0.01,
                'unit' => 'nights',
                'uncertainty_percent' => 35,
                'category' => 'business_travel',
            ],
        ];

        foreach ($factors as $factor) {
            $categoryCode = $factor['category'] ?? 'business_travel';
            unset($factor['category']);

            $categoryModel = $categoryCode === 'business_travel' ? $travelCategory : $commutingCategory;

            if ($categoryModel) {
                $this->createFactor($categoryModel->id, 3, $factor);
            }
        }

        $this->command->info('  - Travel factors: ' . count($factors));
    }

    /**
     * Seed spend-based factors (Scope 3).
     */
    private function seedSpendBasedFactors(): void
    {
        $purchasesCategory = Category::where('code', 'purchased_goods')->first();

        if (! $purchasesCategory) {
            $this->command->warn('Category "purchased_goods" not found, skipping spend-based factors.');
            return;
        }

        $factors = [
            // Services
            [
                'name' => 'Services informatiques / Cloud',
                'name_en' => 'IT Services / Cloud',
                'source_id' => 'ADEME_SPEND_001',
                'factor_kg_co2e' => 0.15,
                'unit' => 'EUR',
                'uncertainty_percent' => 40,
            ],
            [
                'name' => 'Services professionnels (conseil, audit)',
                'name_en' => 'Professional services (consulting, audit)',
                'source_id' => 'ADEME_SPEND_002',
                'factor_kg_co2e' => 0.12,
                'unit' => 'EUR',
                'uncertainty_percent' => 40,
            ],
            [
                'name' => 'Services financiers et assurance',
                'name_en' => 'Financial and insurance services',
                'source_id' => 'ADEME_SPEND_003',
                'factor_kg_co2e' => 0.08,
                'unit' => 'EUR',
                'uncertainty_percent' => 45,
            ],
            // Équipements
            [
                'name' => 'Équipement informatique',
                'name_en' => 'IT equipment',
                'source_id' => 'ADEME_SPEND_004',
                'factor_kg_co2e' => 0.35,
                'unit' => 'EUR',
                'uncertainty_percent' => 35,
            ],
            [
                'name' => 'Mobilier de bureau',
                'name_en' => 'Office furniture',
                'source_id' => 'ADEME_SPEND_005',
                'factor_kg_co2e' => 0.30,
                'unit' => 'EUR',
                'uncertainty_percent' => 40,
            ],
            [
                'name' => 'Fournitures de bureau',
                'name_en' => 'Office supplies',
                'source_id' => 'ADEME_SPEND_006',
                'factor_kg_co2e' => 0.20,
                'unit' => 'EUR',
                'uncertainty_percent' => 40,
            ],
            // Alimentation
            [
                'name' => 'Restauration / Traiteur',
                'name_en' => 'Catering / Restaurant',
                'source_id' => 'ADEME_SPEND_007',
                'factor_kg_co2e' => 0.45,
                'unit' => 'EUR',
                'uncertainty_percent' => 35,
            ],
            [
                'name' => 'Alimentation générale',
                'name_en' => 'General food products',
                'source_id' => 'ADEME_SPEND_008',
                'factor_kg_co2e' => 0.50,
                'unit' => 'EUR',
                'uncertainty_percent' => 35,
            ],
            // Marketing
            [
                'name' => 'Services marketing et communication',
                'name_en' => 'Marketing and communication services',
                'source_id' => 'ADEME_SPEND_009',
                'factor_kg_co2e' => 0.18,
                'unit' => 'EUR',
                'uncertainty_percent' => 45,
            ],
            // Immobilier
            [
                'name' => 'Location immobilière',
                'name_en' => 'Real estate rental',
                'source_id' => 'ADEME_SPEND_010',
                'factor_kg_co2e' => 0.10,
                'unit' => 'EUR',
                'uncertainty_percent' => 50,
            ],
            // Moyenne générale
            [
                'name' => 'Achat moyen (toutes catégories)',
                'name_en' => 'Average purchase (all categories)',
                'source_id' => 'ADEME_SPEND_DEFAULT',
                'factor_kg_co2e' => 0.25,
                'unit' => 'EUR',
                'uncertainty_percent' => 50,
            ],
        ];

        foreach ($factors as $factor) {
            $this->createFactor($purchasesCategory->id, 3, $factor);
        }

        $this->command->info('  - Spend-based factors: ' . count($factors));
    }

    /**
     * Seed waste factors (Scope 3).
     */
    private function seedWasteFactors(): void
    {
        $wasteCategory = Category::where('code', 'waste')->first();

        if (! $wasteCategory) {
            $this->command->warn('Category "waste" not found, skipping waste factors.');
            return;
        }

        $factors = [
            // Déchets par type de traitement
            [
                'name' => 'Déchets ménagers - Enfouissement',
                'name_en' => 'Household waste - Landfill',
                'source_id' => 'ADEME_WASTE_001',
                'factor_kg_co2e' => 0.578,
                'factor_kg_co2' => 0.020,
                'factor_kg_ch4' => 0.0200,
                'unit' => 'kg',
                'uncertainty_percent' => 25,
            ],
            [
                'name' => 'Déchets ménagers - Incinération',
                'name_en' => 'Household waste - Incineration',
                'source_id' => 'ADEME_WASTE_002',
                'factor_kg_co2e' => 0.475,
                'factor_kg_co2' => 0.470,
                'factor_kg_ch4' => 0.0002,
                'unit' => 'kg',
                'uncertainty_percent' => 20,
            ],
            [
                'name' => 'Papier/Carton - Recyclage',
                'name_en' => 'Paper/Cardboard - Recycling',
                'source_id' => 'ADEME_WASTE_003',
                'factor_kg_co2e' => 0.047,
                'factor_kg_co2' => 0.045,
                'unit' => 'kg',
                'uncertainty_percent' => 30,
            ],
            [
                'name' => 'Plastique - Recyclage',
                'name_en' => 'Plastic - Recycling',
                'source_id' => 'ADEME_WASTE_004',
                'factor_kg_co2e' => 0.046,
                'factor_kg_co2' => 0.044,
                'unit' => 'kg',
                'uncertainty_percent' => 30,
            ],
            [
                'name' => 'Verre - Recyclage',
                'name_en' => 'Glass - Recycling',
                'source_id' => 'ADEME_WASTE_005',
                'factor_kg_co2e' => 0.018,
                'factor_kg_co2' => 0.017,
                'unit' => 'kg',
                'uncertainty_percent' => 25,
            ],
            [
                'name' => 'Métal - Recyclage',
                'name_en' => 'Metal - Recycling',
                'source_id' => 'ADEME_WASTE_006',
                'factor_kg_co2e' => 0.038,
                'factor_kg_co2' => 0.036,
                'unit' => 'kg',
                'uncertainty_percent' => 30,
            ],
            [
                'name' => 'Déchets électroniques (DEEE)',
                'name_en' => 'Electronic waste (WEEE)',
                'source_id' => 'ADEME_WASTE_007',
                'factor_kg_co2e' => 0.100,
                'factor_kg_co2' => 0.095,
                'unit' => 'kg',
                'uncertainty_percent' => 35,
            ],
            [
                'name' => 'Biodéchets - Compostage',
                'name_en' => 'Organic waste - Composting',
                'source_id' => 'ADEME_WASTE_008',
                'factor_kg_co2e' => 0.024,
                'factor_kg_co2' => 0.008,
                'factor_kg_ch4' => 0.0006,
                'unit' => 'kg',
                'uncertainty_percent' => 35,
            ],
        ];

        foreach ($factors as $factor) {
            $this->createFactor($wasteCategory->id, 3, $factor);
        }

        $this->command->info('  - Waste factors: ' . count($factors));
    }

    /**
     * Seed freight transport factors (Scope 3).
     */
    private function seedFreightFactors(): void
    {
        $upstreamCategory = Category::where('code', 'upstream_transport')->first();
        $downstreamCategory = Category::where('code', 'downstream_transport')->first();

        $factors = [
            // Transport routier
            [
                'name' => 'Camion articulé >32t',
                'name_en' => 'Articulated truck >32t',
                'source_id' => 'ADEME_FREIGHT_001',
                'factor_kg_co2e' => 0.0857,
                'factor_kg_co2' => 0.0850,
                'unit' => 'tonne-km',
                'uncertainty_percent' => 15,
            ],
            [
                'name' => 'Camion porteur 12-19t',
                'name_en' => 'Rigid truck 12-19t',
                'source_id' => 'ADEME_FREIGHT_002',
                'factor_kg_co2e' => 0.156,
                'factor_kg_co2' => 0.154,
                'unit' => 'tonne-km',
                'uncertainty_percent' => 15,
            ],
            [
                'name' => 'Utilitaire <3.5t',
                'name_en' => 'Van <3.5t',
                'source_id' => 'ADEME_FREIGHT_003',
                'factor_kg_co2e' => 0.483,
                'factor_kg_co2' => 0.478,
                'unit' => 'tonne-km',
                'uncertainty_percent' => 20,
            ],
            // Transport ferroviaire
            [
                'name' => 'Fret ferroviaire - France',
                'name_en' => 'Rail freight - France',
                'source_id' => 'ADEME_FREIGHT_004',
                'factor_kg_co2e' => 0.00569,
                'factor_kg_co2' => 0.00540,
                'unit' => 'tonne-km',
                'uncertainty_percent' => 15,
            ],
            // Transport maritime
            [
                'name' => 'Porte-conteneurs (moyen)',
                'name_en' => 'Container ship (average)',
                'source_id' => 'ADEME_FREIGHT_005',
                'factor_kg_co2e' => 0.0158,
                'factor_kg_co2' => 0.0155,
                'unit' => 'tonne-km',
                'uncertainty_percent' => 20,
            ],
            // Transport aérien
            [
                'name' => 'Fret aérien continental',
                'name_en' => 'Air freight - Continental',
                'source_id' => 'ADEME_FREIGHT_006',
                'factor_kg_co2e' => 1.72,
                'factor_kg_co2' => 1.68,
                'unit' => 'tonne-km',
                'uncertainty_percent' => 25,
            ],
            [
                'name' => 'Fret aérien intercontinental',
                'name_en' => 'Air freight - Intercontinental',
                'source_id' => 'ADEME_FREIGHT_007',
                'factor_kg_co2e' => 0.616,
                'factor_kg_co2' => 0.600,
                'unit' => 'tonne-km',
                'uncertainty_percent' => 25,
            ],
        ];

        $categoryId = $upstreamCategory?->id ?? $downstreamCategory?->id;

        if (! $categoryId) {
            $this->command->warn('No transport category found, skipping freight factors.');
            return;
        }

        foreach ($factors as $factor) {
            $this->createFactor($categoryId, 3, $factor);
        }

        $this->command->info('  - Freight factors: ' . count($factors));
    }

    /**
     * Create or update an emission factor.
     */
    private function createFactor(string $categoryId, int $scope, array $data): EmissionFactor
    {
        return EmissionFactor::updateOrCreate(
            [
                'source' => 'ademe',
                'source_id' => $data['source_id'],
            ],
            [
                'id' => Str::uuid()->toString(),
                'category_id' => $categoryId,
                'scope' => $scope,
                'country' => 'FR',
                'name' => $data['name'],
                'name_en' => $data['name_en'] ?? $data['name'],
                'name_de' => null,
                'factor_kg_co2e' => $data['factor_kg_co2e'],
                'factor_kg_co2' => $data['factor_kg_co2'] ?? null,
                'factor_kg_ch4' => $data['factor_kg_ch4'] ?? null,
                'factor_kg_n2o' => $data['factor_kg_n2o'] ?? null,
                'unit' => $data['unit'],
                'uncertainty_percent' => $data['uncertainty_percent'] ?? null,
                'methodology' => $data['methodology'] ?? null,
                'source' => 'ademe',
                'source_url' => 'https://base-empreinte.ademe.fr/',
                'source_id' => $data['source_id'],
                'valid_from' => now()->startOfYear(),
                'valid_until' => now()->addYears(2)->endOfYear(),
                'is_active' => true,
            ]
        );
    }
}
