<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\EmissionFactor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Scope 3 Emission Factors Seeder
 *
 * Populates comprehensive emission factors for all 15 GHG Protocol Scope 3 categories
 * across EU countries: FR, DE, BE, NL, AT, CH, ES, IT
 *
 * Categories:
 * Upstream (1-8):
 *   1. Purchased goods and services
 *   2. Capital goods
 *   3. Fuel and energy related activities
 *   4. Upstream transportation and distribution
 *   5. Waste generated in operations
 *   6. Business travel
 *   7. Employee commuting
 *   8. Upstream leased assets
 *
 * Downstream (9-15):
 *   9. Downstream transportation and distribution
 *   10. Processing of sold products
 *   11. Use of sold products
 *   12. End-of-life treatment of sold products
 *   13. Downstream leased assets
 *   14. Franchises
 *   15. Investments
 */
class Scope3FactorSeeder extends Seeder
{
    private const COUNTRIES = ['FR', 'DE', 'BE', 'NL', 'AT', 'CH', 'ES', 'IT'];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding Scope 3 emission factors for EU countries...');

        // Category 1: Purchased goods and services
        $this->seedPurchasedGoodsFactors();

        // Category 2: Capital goods
        $this->seedCapitalGoodsFactors();

        // Category 3: Fuel and energy related activities
        $this->seedFuelEnergyFactors();

        // Category 4: Upstream transportation
        $this->seedUpstreamTransportFactors();

        // Category 5: Waste
        $this->seedWasteFactors();

        // Category 6: Business travel
        $this->seedBusinessTravelFactors();

        // Category 7: Employee commuting
        $this->seedCommutingFactors();

        // Category 9: Downstream transportation
        $this->seedDownstreamTransportFactors();

        // Category 11: Use of sold products
        $this->seedProductUseFactors();

        // Category 12: End-of-life treatment
        $this->seedEndOfLifeFactors();

        // Category 15: Investments
        $this->seedInvestmentFactors();

        $this->command->info('Scope 3 emission factors seeded successfully.');
    }

    /**
     * Seed purchased goods and services factors (Category 1).
     */
    private function seedPurchasedGoodsFactors(): void
    {
        $category = Category::where('code', 'purchased_goods')->first();

        if (! $category) {
            $this->command->warn('Category "purchased_goods" not found, skipping.');
            return;
        }

        // Spend-based factors by industry (kg CO2e per EUR)
        $factors = [
            [
                'name' => 'Agriculture, sylviculture et pêche',
                'name_en' => 'Agriculture, forestry and fishing',
                'name_de' => 'Land- und Forstwirtschaft, Fischerei',
                'source_id' => 'SCOPE3_GOODS_001',
                'factor_kg_co2e' => 0.65,
                'unit' => 'EUR',
                'uncertainty_percent' => 35,
            ],
            [
                'name' => 'Industries extractives',
                'name_en' => 'Mining and quarrying',
                'name_de' => 'Bergbau und Gewinnung von Steinen und Erden',
                'source_id' => 'SCOPE3_GOODS_002',
                'factor_kg_co2e' => 0.55,
                'unit' => 'EUR',
                'uncertainty_percent' => 35,
            ],
            [
                'name' => 'Industries alimentaires',
                'name_en' => 'Food products',
                'name_de' => 'Nahrungsmittel',
                'source_id' => 'SCOPE3_GOODS_003',
                'factor_kg_co2e' => 0.48,
                'unit' => 'EUR',
                'uncertainty_percent' => 30,
            ],
            [
                'name' => 'Textile et habillement',
                'name_en' => 'Textiles and clothing',
                'name_de' => 'Textilien und Bekleidung',
                'source_id' => 'SCOPE3_GOODS_004',
                'factor_kg_co2e' => 0.42,
                'unit' => 'EUR',
                'uncertainty_percent' => 35,
            ],
            [
                'name' => 'Papier et imprimerie',
                'name_en' => 'Paper and printing',
                'name_de' => 'Papier und Druckerzeugnisse',
                'source_id' => 'SCOPE3_GOODS_005',
                'factor_kg_co2e' => 0.38,
                'unit' => 'EUR',
                'uncertainty_percent' => 30,
            ],
            [
                'name' => 'Industrie chimique',
                'name_en' => 'Chemical industry',
                'name_de' => 'Chemische Industrie',
                'source_id' => 'SCOPE3_GOODS_006',
                'factor_kg_co2e' => 0.72,
                'unit' => 'EUR',
                'uncertainty_percent' => 35,
            ],
            [
                'name' => 'Pharmacie',
                'name_en' => 'Pharmaceuticals',
                'name_de' => 'Pharmazeutische Erzeugnisse',
                'source_id' => 'SCOPE3_GOODS_007',
                'factor_kg_co2e' => 0.28,
                'unit' => 'EUR',
                'uncertainty_percent' => 40,
            ],
            [
                'name' => 'Plastiques et caoutchouc',
                'name_en' => 'Rubber and plastics',
                'name_de' => 'Gummi- und Kunststoffwaren',
                'source_id' => 'SCOPE3_GOODS_008',
                'factor_kg_co2e' => 0.58,
                'unit' => 'EUR',
                'uncertainty_percent' => 30,
            ],
            [
                'name' => 'Métaux et produits métalliques',
                'name_en' => 'Metals and metal products',
                'name_de' => 'Metalle und Metallerzeugnisse',
                'source_id' => 'SCOPE3_GOODS_009',
                'factor_kg_co2e' => 0.68,
                'unit' => 'EUR',
                'uncertainty_percent' => 30,
            ],
            [
                'name' => 'Équipements électroniques',
                'name_en' => 'Electronic equipment',
                'name_de' => 'Elektronische Ausrüstung',
                'source_id' => 'SCOPE3_GOODS_010',
                'factor_kg_co2e' => 0.35,
                'unit' => 'EUR',
                'uncertainty_percent' => 35,
            ],
            [
                'name' => 'Machines et équipements',
                'name_en' => 'Machinery and equipment',
                'name_de' => 'Maschinen und Ausrüstungen',
                'source_id' => 'SCOPE3_GOODS_011',
                'factor_kg_co2e' => 0.45,
                'unit' => 'EUR',
                'uncertainty_percent' => 35,
            ],
            [
                'name' => 'Véhicules et matériel de transport',
                'name_en' => 'Vehicles and transport equipment',
                'name_de' => 'Fahrzeuge und Transportausrüstung',
                'source_id' => 'SCOPE3_GOODS_012',
                'factor_kg_co2e' => 0.52,
                'unit' => 'EUR',
                'uncertainty_percent' => 35,
            ],
            [
                'name' => 'Construction',
                'name_en' => 'Construction',
                'name_de' => 'Baugewerbe',
                'source_id' => 'SCOPE3_GOODS_013',
                'factor_kg_co2e' => 0.48,
                'unit' => 'EUR',
                'uncertainty_percent' => 40,
            ],
            [
                'name' => 'Services informatiques',
                'name_en' => 'IT services',
                'name_de' => 'IT-Dienstleistungen',
                'source_id' => 'SCOPE3_GOODS_014',
                'factor_kg_co2e' => 0.15,
                'unit' => 'EUR',
                'uncertainty_percent' => 45,
            ],
            [
                'name' => 'Services professionnels',
                'name_en' => 'Professional services',
                'name_de' => 'Freiberufliche Dienstleistungen',
                'source_id' => 'SCOPE3_GOODS_015',
                'factor_kg_co2e' => 0.12,
                'unit' => 'EUR',
                'uncertainty_percent' => 45,
            ],
            [
                'name' => 'Services financiers',
                'name_en' => 'Financial services',
                'name_de' => 'Finanzdienstleistungen',
                'source_id' => 'SCOPE3_GOODS_016',
                'factor_kg_co2e' => 0.08,
                'unit' => 'EUR',
                'uncertainty_percent' => 50,
            ],
            [
                'name' => 'Télécommunications',
                'name_en' => 'Telecommunications',
                'name_de' => 'Telekommunikation',
                'source_id' => 'SCOPE3_GOODS_017',
                'factor_kg_co2e' => 0.14,
                'unit' => 'EUR',
                'uncertainty_percent' => 45,
            ],
            [
                'name' => 'Hébergement et restauration',
                'name_en' => 'Accommodation and food services',
                'name_de' => 'Beherbergung und Gastronomie',
                'source_id' => 'SCOPE3_GOODS_018',
                'factor_kg_co2e' => 0.42,
                'unit' => 'EUR',
                'uncertainty_percent' => 35,
            ],
        ];

        foreach (self::COUNTRIES as $country) {
            foreach ($factors as $factor) {
                $this->createFactor($category->id, 3, $country, $factor, 'ecoinvent');
            }
        }

        $this->command->info('  - Purchased goods factors: ' . count($factors) . ' x ' . count(self::COUNTRIES) . ' countries');
    }

    /**
     * Seed capital goods factors (Category 2).
     */
    private function seedCapitalGoodsFactors(): void
    {
        $category = Category::where('code', 'capital_goods')->first();

        if (! $category) {
            $this->command->warn('Category "capital_goods" not found, skipping.');
            return;
        }

        $factors = [
            [
                'name' => 'Bâtiment commercial neuf',
                'name_en' => 'New commercial building',
                'name_de' => 'Neues Gewerbegebäude',
                'source_id' => 'SCOPE3_CAPITAL_001',
                'factor_kg_co2e' => 500,
                'unit' => 'm2',
                'uncertainty_percent' => 30,
            ],
            [
                'name' => 'Bâtiment industriel neuf',
                'name_en' => 'New industrial building',
                'name_de' => 'Neues Industriegebäude',
                'source_id' => 'SCOPE3_CAPITAL_002',
                'factor_kg_co2e' => 350,
                'unit' => 'm2',
                'uncertainty_percent' => 30,
            ],
            [
                'name' => 'Serveur informatique',
                'name_en' => 'Computer server',
                'name_de' => 'Server',
                'source_id' => 'SCOPE3_CAPITAL_003',
                'factor_kg_co2e' => 800,
                'unit' => 'units',
                'uncertainty_percent' => 25,
            ],
            [
                'name' => 'Ordinateur portable',
                'name_en' => 'Laptop computer',
                'name_de' => 'Laptop',
                'source_id' => 'SCOPE3_CAPITAL_004',
                'factor_kg_co2e' => 350,
                'unit' => 'units',
                'uncertainty_percent' => 20,
            ],
            [
                'name' => 'Écran informatique',
                'name_en' => 'Computer monitor',
                'name_de' => 'Computermonitor',
                'source_id' => 'SCOPE3_CAPITAL_005',
                'factor_kg_co2e' => 200,
                'unit' => 'units',
                'uncertainty_percent' => 25,
            ],
            [
                'name' => 'Véhicule utilitaire léger',
                'name_en' => 'Light commercial vehicle',
                'name_de' => 'Leichtes Nutzfahrzeug',
                'source_id' => 'SCOPE3_CAPITAL_006',
                'factor_kg_co2e' => 8500,
                'unit' => 'units',
                'uncertainty_percent' => 25,
            ],
            [
                'name' => 'Véhicule poids lourd',
                'name_en' => 'Heavy goods vehicle',
                'name_de' => 'Schweres Nutzfahrzeug',
                'source_id' => 'SCOPE3_CAPITAL_007',
                'factor_kg_co2e' => 35000,
                'unit' => 'units',
                'uncertainty_percent' => 30,
            ],
            [
                'name' => 'Machine industrielle (moyenne)',
                'name_en' => 'Industrial machinery (average)',
                'name_de' => 'Industriemaschine (Durchschnitt)',
                'source_id' => 'SCOPE3_CAPITAL_008',
                'factor_kg_co2e' => 2.5,
                'unit' => 'kg',
                'uncertainty_percent' => 35,
            ],
            [
                'name' => 'Mobilier de bureau',
                'name_en' => 'Office furniture',
                'name_de' => 'Büromöbel',
                'source_id' => 'SCOPE3_CAPITAL_009',
                'factor_kg_co2e' => 0.30,
                'unit' => 'EUR',
                'uncertainty_percent' => 40,
            ],
            [
                'name' => 'Installation photovoltaïque',
                'name_en' => 'Photovoltaic installation',
                'name_de' => 'Photovoltaikanlage',
                'source_id' => 'SCOPE3_CAPITAL_010',
                'factor_kg_co2e' => 1100,
                'unit' => 'kWp',
                'uncertainty_percent' => 25,
            ],
        ];

        foreach (self::COUNTRIES as $country) {
            foreach ($factors as $factor) {
                $this->createFactor($category->id, 3, $country, $factor, 'ecoinvent');
            }
        }

        $this->command->info('  - Capital goods factors: ' . count($factors) . ' x ' . count(self::COUNTRIES) . ' countries');
    }

    /**
     * Seed fuel and energy related activities factors (Category 3).
     */
    private function seedFuelEnergyFactors(): void
    {
        $category = Category::where('code', 'fuel_energy')->first();

        if (! $category) {
            $this->command->warn('Category "fuel_energy" not found, skipping.');
            return;
        }

        // Well-to-tank (WTT) factors
        $factors = [
            [
                'name' => 'Diesel (WTT)',
                'name_en' => 'Diesel (WTT)',
                'name_de' => 'Diesel (WTT)',
                'source_id' => 'SCOPE3_FUELWTT_001',
                'factor_kg_co2e' => 0.625,
                'unit' => 'liters',
                'uncertainty_percent' => 20,
            ],
            [
                'name' => 'Essence (WTT)',
                'name_en' => 'Petrol (WTT)',
                'name_de' => 'Benzin (WTT)',
                'source_id' => 'SCOPE3_FUELWTT_002',
                'factor_kg_co2e' => 0.584,
                'unit' => 'liters',
                'uncertainty_percent' => 20,
            ],
            [
                'name' => 'Gaz naturel (WTT)',
                'name_en' => 'Natural gas (WTT)',
                'name_de' => 'Erdgas (WTT)',
                'source_id' => 'SCOPE3_FUELWTT_003',
                'factor_kg_co2e' => 0.412,
                'unit' => 'm3',
                'uncertainty_percent' => 25,
            ],
            [
                'name' => 'GPL (WTT)',
                'name_en' => 'LPG (WTT)',
                'name_de' => 'Flüssiggas (WTT)',
                'source_id' => 'SCOPE3_FUELWTT_004',
                'factor_kg_co2e' => 0.324,
                'unit' => 'liters',
                'uncertainty_percent' => 20,
            ],
            [
                'name' => 'Fioul lourd (WTT)',
                'name_en' => 'Heavy fuel oil (WTT)',
                'name_de' => 'Schweröl (WTT)',
                'source_id' => 'SCOPE3_FUELWTT_005',
                'factor_kg_co2e' => 0.520,
                'unit' => 'liters',
                'uncertainty_percent' => 25,
            ],
        ];

        // T&D losses vary by country
        $electricityTdLosses = [
            'FR' => 0.00426, // Low losses (nuclear)
            'DE' => 0.0274,  // Higher due to coal
            'BE' => 0.0123,
            'NL' => 0.0246,
            'AT' => 0.00855,
            'CH' => 0.00180,
            'ES' => 0.0144,
            'IT' => 0.0206,
        ];

        foreach (self::COUNTRIES as $country) {
            foreach ($factors as $factor) {
                $this->createFactor($category->id, 3, $country, $factor, 'ecoinvent');
            }

            // Add country-specific T&D loss factor
            $this->createFactor($category->id, 3, $country, [
                'name' => 'Pertes réseau électrique (T&D)',
                'name_en' => 'Electricity T&D losses',
                'name_de' => 'Strom Übertragungs- und Verteilungsverluste',
                'source_id' => 'SCOPE3_ELEC_TD_' . $country,
                'factor_kg_co2e' => $electricityTdLosses[$country],
                'unit' => 'kWh',
                'uncertainty_percent' => 30,
            ], 'national_inventory');
        }

        $this->command->info('  - Fuel/energy WTT factors: ' . (count($factors) + 1) . ' x ' . count(self::COUNTRIES) . ' countries');
    }

    /**
     * Seed upstream transportation factors (Category 4).
     */
    private function seedUpstreamTransportFactors(): void
    {
        $category = Category::where('code', 'upstream_transport')->first();

        if (! $category) {
            $this->command->warn('Category "upstream_transport" not found, skipping.');
            return;
        }

        $factors = [
            // Road freight
            [
                'name' => 'Camion articulé >32t',
                'name_en' => 'Articulated truck >32t',
                'name_de' => 'Sattelzug >32t',
                'source_id' => 'SCOPE3_FREIGHT_001',
                'factor_kg_co2e' => 0.0623,
                'unit' => 'tonne-km',
                'uncertainty_percent' => 15,
            ],
            [
                'name' => 'Camion rigide 7.5-12t',
                'name_en' => 'Rigid truck 7.5-12t',
                'name_de' => 'Starrer LKW 7,5-12t',
                'source_id' => 'SCOPE3_FREIGHT_002',
                'factor_kg_co2e' => 0.185,
                'unit' => 'tonne-km',
                'uncertainty_percent' => 15,
            ],
            [
                'name' => 'Camionnette <3.5t',
                'name_en' => 'Van <3.5t',
                'name_de' => 'Transporter <3,5t',
                'source_id' => 'SCOPE3_FREIGHT_003',
                'factor_kg_co2e' => 0.456,
                'unit' => 'tonne-km',
                'uncertainty_percent' => 20,
            ],
            // Rail freight
            [
                'name' => 'Fret ferroviaire électrique',
                'name_en' => 'Electric rail freight',
                'name_de' => 'Elektrischer Schienengüterverkehr',
                'source_id' => 'SCOPE3_FREIGHT_004',
                'factor_kg_co2e' => 0.0049,
                'unit' => 'tonne-km',
                'uncertainty_percent' => 20,
            ],
            [
                'name' => 'Fret ferroviaire diesel',
                'name_en' => 'Diesel rail freight',
                'name_de' => 'Diesel-Schienengüterverkehr',
                'source_id' => 'SCOPE3_FREIGHT_005',
                'factor_kg_co2e' => 0.0298,
                'unit' => 'tonne-km',
                'uncertainty_percent' => 20,
            ],
            // Sea freight
            [
                'name' => 'Porte-conteneurs (8000+ TEU)',
                'name_en' => 'Container ship (8000+ TEU)',
                'name_de' => 'Containerschiff (8000+ TEU)',
                'source_id' => 'SCOPE3_FREIGHT_006',
                'factor_kg_co2e' => 0.0082,
                'unit' => 'tonne-km',
                'uncertainty_percent' => 25,
            ],
            [
                'name' => 'Porte-conteneurs (1000-5000 TEU)',
                'name_en' => 'Container ship (1000-5000 TEU)',
                'name_de' => 'Containerschiff (1000-5000 TEU)',
                'source_id' => 'SCOPE3_FREIGHT_007',
                'factor_kg_co2e' => 0.0158,
                'unit' => 'tonne-km',
                'uncertainty_percent' => 25,
            ],
            [
                'name' => 'Vraquier',
                'name_en' => 'Bulk carrier',
                'name_de' => 'Massengutfrachter',
                'source_id' => 'SCOPE3_FREIGHT_008',
                'factor_kg_co2e' => 0.0048,
                'unit' => 'tonne-km',
                'uncertainty_percent' => 25,
            ],
            // Air freight
            [
                'name' => 'Fret aérien long-courrier',
                'name_en' => 'Long-haul air freight',
                'name_de' => 'Langstrecken-Luftfracht',
                'source_id' => 'SCOPE3_FREIGHT_009',
                'factor_kg_co2e' => 0.602,
                'unit' => 'tonne-km',
                'uncertainty_percent' => 20,
            ],
            [
                'name' => 'Fret aérien court-courrier',
                'name_en' => 'Short-haul air freight',
                'name_de' => 'Kurzstrecken-Luftfracht',
                'source_id' => 'SCOPE3_FREIGHT_010',
                'factor_kg_co2e' => 2.10,
                'unit' => 'tonne-km',
                'uncertainty_percent' => 20,
            ],
        ];

        foreach (self::COUNTRIES as $country) {
            foreach ($factors as $factor) {
                $this->createFactor($category->id, 3, $country, $factor, 'ecoinvent');
            }
        }

        $this->command->info('  - Upstream transport factors: ' . count($factors) . ' x ' . count(self::COUNTRIES) . ' countries');
    }

    /**
     * Seed waste factors (Category 5).
     */
    private function seedWasteFactors(): void
    {
        $category = Category::where('code', 'waste')->first();

        if (! $category) {
            $this->command->warn('Category "waste" not found, skipping.');
            return;
        }

        $factors = [
            // By waste type and treatment
            [
                'name' => 'Déchets résiduels - Incinération',
                'name_en' => 'Residual waste - Incineration',
                'name_de' => 'Restmüll - Verbrennung',
                'source_id' => 'SCOPE3_WASTE_001',
                'factor_kg_co2e' => 0.377,
                'unit' => 'kg',
                'uncertainty_percent' => 25,
            ],
            [
                'name' => 'Déchets résiduels - Enfouissement',
                'name_en' => 'Residual waste - Landfill',
                'name_de' => 'Restmüll - Deponie',
                'source_id' => 'SCOPE3_WASTE_002',
                'factor_kg_co2e' => 0.578,
                'unit' => 'kg',
                'uncertainty_percent' => 30,
            ],
            [
                'name' => 'Papier/Carton - Recyclage',
                'name_en' => 'Paper/Cardboard - Recycling',
                'name_de' => 'Papier/Karton - Recycling',
                'source_id' => 'SCOPE3_WASTE_003',
                'factor_kg_co2e' => 0.021,
                'unit' => 'kg',
                'uncertainty_percent' => 30,
            ],
            [
                'name' => 'Plastique PET - Recyclage',
                'name_en' => 'PET plastic - Recycling',
                'name_de' => 'PET-Kunststoff - Recycling',
                'source_id' => 'SCOPE3_WASTE_004',
                'factor_kg_co2e' => 0.021,
                'unit' => 'kg',
                'uncertainty_percent' => 30,
            ],
            [
                'name' => 'Plastique mixte - Recyclage',
                'name_en' => 'Mixed plastic - Recycling',
                'name_de' => 'Mischkunststoff - Recycling',
                'source_id' => 'SCOPE3_WASTE_005',
                'factor_kg_co2e' => 0.046,
                'unit' => 'kg',
                'uncertainty_percent' => 35,
            ],
            [
                'name' => 'Verre - Recyclage',
                'name_en' => 'Glass - Recycling',
                'name_de' => 'Glas - Recycling',
                'source_id' => 'SCOPE3_WASTE_006',
                'factor_kg_co2e' => 0.018,
                'unit' => 'kg',
                'uncertainty_percent' => 25,
            ],
            [
                'name' => 'Aluminium - Recyclage',
                'name_en' => 'Aluminium - Recycling',
                'name_de' => 'Aluminium - Recycling',
                'source_id' => 'SCOPE3_WASTE_007',
                'factor_kg_co2e' => 0.014,
                'unit' => 'kg',
                'uncertainty_percent' => 30,
            ],
            [
                'name' => 'Acier - Recyclage',
                'name_en' => 'Steel - Recycling',
                'name_de' => 'Stahl - Recycling',
                'source_id' => 'SCOPE3_WASTE_008',
                'factor_kg_co2e' => 0.038,
                'unit' => 'kg',
                'uncertainty_percent' => 30,
            ],
            [
                'name' => 'Biodéchets - Compostage',
                'name_en' => 'Organic waste - Composting',
                'name_de' => 'Bioabfall - Kompostierung',
                'source_id' => 'SCOPE3_WASTE_009',
                'factor_kg_co2e' => 0.024,
                'unit' => 'kg',
                'uncertainty_percent' => 35,
            ],
            [
                'name' => 'Biodéchets - Méthanisation',
                'name_en' => 'Organic waste - Anaerobic digestion',
                'name_de' => 'Bioabfall - Vergärung',
                'source_id' => 'SCOPE3_WASTE_010',
                'factor_kg_co2e' => -0.082,
                'unit' => 'kg',
                'uncertainty_percent' => 40,
            ],
            [
                'name' => 'DEEE - Recyclage',
                'name_en' => 'WEEE - Recycling',
                'name_de' => 'Elektroschrott - Recycling',
                'source_id' => 'SCOPE3_WASTE_011',
                'factor_kg_co2e' => 0.100,
                'unit' => 'kg',
                'uncertainty_percent' => 35,
            ],
            [
                'name' => 'Déchets dangereux - Traitement',
                'name_en' => 'Hazardous waste - Treatment',
                'name_de' => 'Sonderabfall - Behandlung',
                'source_id' => 'SCOPE3_WASTE_012',
                'factor_kg_co2e' => 0.250,
                'unit' => 'kg',
                'uncertainty_percent' => 40,
            ],
            [
                'name' => 'Déchets de construction - Recyclage',
                'name_en' => 'Construction waste - Recycling',
                'name_de' => 'Bauabfall - Recycling',
                'source_id' => 'SCOPE3_WASTE_013',
                'factor_kg_co2e' => 0.015,
                'unit' => 'kg',
                'uncertainty_percent' => 35,
            ],
        ];

        foreach (self::COUNTRIES as $country) {
            foreach ($factors as $factor) {
                $this->createFactor($category->id, 3, $country, $factor, 'ecoinvent');
            }
        }

        $this->command->info('  - Waste factors: ' . count($factors) . ' x ' . count(self::COUNTRIES) . ' countries');
    }

    /**
     * Seed business travel factors (Category 6).
     */
    private function seedBusinessTravelFactors(): void
    {
        $category = Category::where('code', 'business_travel')->first();

        if (! $category) {
            $this->command->warn('Category "business_travel" not found, skipping.');
            return;
        }

        $factors = [
            // Flights (with radiative forcing multiplier of 1.9)
            [
                'name' => 'Vol domestique économique',
                'name_en' => 'Domestic flight economy',
                'name_de' => 'Inlandsflug Economy',
                'source_id' => 'SCOPE3_TRAVEL_001',
                'factor_kg_co2e' => 0.246,
                'unit' => 'passenger-km',
                'uncertainty_percent' => 20,
            ],
            [
                'name' => 'Vol court-courrier économique',
                'name_en' => 'Short-haul flight economy',
                'name_de' => 'Kurzstreckenflug Economy',
                'source_id' => 'SCOPE3_TRAVEL_002',
                'factor_kg_co2e' => 0.156,
                'unit' => 'passenger-km',
                'uncertainty_percent' => 20,
            ],
            [
                'name' => 'Vol court-courrier affaires',
                'name_en' => 'Short-haul flight business',
                'name_de' => 'Kurzstreckenflug Business',
                'source_id' => 'SCOPE3_TRAVEL_003',
                'factor_kg_co2e' => 0.234,
                'unit' => 'passenger-km',
                'uncertainty_percent' => 20,
            ],
            [
                'name' => 'Vol long-courrier économique',
                'name_en' => 'Long-haul flight economy',
                'name_de' => 'Langstreckenflug Economy',
                'source_id' => 'SCOPE3_TRAVEL_004',
                'factor_kg_co2e' => 0.148,
                'unit' => 'passenger-km',
                'uncertainty_percent' => 20,
            ],
            [
                'name' => 'Vol long-courrier affaires',
                'name_en' => 'Long-haul flight business',
                'name_de' => 'Langstreckenflug Business',
                'source_id' => 'SCOPE3_TRAVEL_005',
                'factor_kg_co2e' => 0.429,
                'unit' => 'passenger-km',
                'uncertainty_percent' => 20,
            ],
            [
                'name' => 'Vol long-courrier première',
                'name_en' => 'Long-haul flight first class',
                'name_de' => 'Langstreckenflug First Class',
                'source_id' => 'SCOPE3_TRAVEL_006',
                'factor_kg_co2e' => 0.592,
                'unit' => 'passenger-km',
                'uncertainty_percent' => 25,
            ],
            // Rail
            [
                'name' => 'Train grande vitesse',
                'name_en' => 'High-speed train',
                'name_de' => 'Hochgeschwindigkeitszug',
                'source_id' => 'SCOPE3_TRAVEL_007',
                'factor_kg_co2e' => 0.0041,
                'unit' => 'passenger-km',
                'uncertainty_percent' => 15,
            ],
            [
                'name' => 'Train régional électrique',
                'name_en' => 'Electric regional train',
                'name_de' => 'Elektrischer Regionalzug',
                'source_id' => 'SCOPE3_TRAVEL_008',
                'factor_kg_co2e' => 0.0356,
                'unit' => 'passenger-km',
                'uncertainty_percent' => 20,
            ],
            // Road
            [
                'name' => 'Voiture de location moyenne',
                'name_en' => 'Average rental car',
                'name_de' => 'Durchschnittlicher Mietwagen',
                'source_id' => 'SCOPE3_TRAVEL_009',
                'factor_kg_co2e' => 0.171,
                'unit' => 'km',
                'uncertainty_percent' => 15,
            ],
            [
                'name' => 'Taxi/VTC',
                'name_en' => 'Taxi/Ride-hailing',
                'name_de' => 'Taxi/Fahrdienst',
                'source_id' => 'SCOPE3_TRAVEL_010',
                'factor_kg_co2e' => 0.210,
                'unit' => 'km',
                'uncertainty_percent' => 20,
            ],
            // Accommodation
            [
                'name' => 'Hôtel 1-2 étoiles',
                'name_en' => 'Hotel 1-2 stars',
                'name_de' => 'Hotel 1-2 Sterne',
                'source_id' => 'SCOPE3_TRAVEL_011',
                'factor_kg_co2e' => 15.0,
                'unit' => 'nights',
                'uncertainty_percent' => 30,
            ],
            [
                'name' => 'Hôtel 3 étoiles',
                'name_en' => 'Hotel 3 stars',
                'name_de' => 'Hotel 3 Sterne',
                'source_id' => 'SCOPE3_TRAVEL_012',
                'factor_kg_co2e' => 25.0,
                'unit' => 'nights',
                'uncertainty_percent' => 30,
            ],
            [
                'name' => 'Hôtel 4-5 étoiles',
                'name_en' => 'Hotel 4-5 stars',
                'name_de' => 'Hotel 4-5 Sterne',
                'source_id' => 'SCOPE3_TRAVEL_013',
                'factor_kg_co2e' => 45.0,
                'unit' => 'nights',
                'uncertainty_percent' => 35,
            ],
        ];

        foreach (self::COUNTRIES as $country) {
            foreach ($factors as $factor) {
                $this->createFactor($category->id, 3, $country, $factor, 'ecoinvent');
            }
        }

        $this->command->info('  - Business travel factors: ' . count($factors) . ' x ' . count(self::COUNTRIES) . ' countries');
    }

    /**
     * Seed commuting factors (Category 7).
     */
    private function seedCommutingFactors(): void
    {
        $category = Category::where('code', 'employee_commuting')->first();

        if (! $category) {
            $this->command->warn('Category "employee_commuting" not found, skipping.');
            return;
        }

        $factors = [
            [
                'name' => 'Voiture essence',
                'name_en' => 'Petrol car',
                'name_de' => 'Benzin-PKW',
                'source_id' => 'SCOPE3_COMMUTE_001',
                'factor_kg_co2e' => 0.192,
                'unit' => 'km',
                'uncertainty_percent' => 15,
            ],
            [
                'name' => 'Voiture diesel',
                'name_en' => 'Diesel car',
                'name_de' => 'Diesel-PKW',
                'source_id' => 'SCOPE3_COMMUTE_002',
                'factor_kg_co2e' => 0.170,
                'unit' => 'km',
                'uncertainty_percent' => 15,
            ],
            [
                'name' => 'Voiture hybride',
                'name_en' => 'Hybrid car',
                'name_de' => 'Hybrid-PKW',
                'source_id' => 'SCOPE3_COMMUTE_003',
                'factor_kg_co2e' => 0.110,
                'unit' => 'km',
                'uncertainty_percent' => 15,
            ],
            [
                'name' => 'Voiture électrique',
                'name_en' => 'Electric car',
                'name_de' => 'Elektro-PKW',
                'source_id' => 'SCOPE3_COMMUTE_004',
                'factor_kg_co2e' => 0.053,
                'unit' => 'km',
                'uncertainty_percent' => 25,
            ],
            [
                'name' => 'Moto/Scooter',
                'name_en' => 'Motorcycle/Scooter',
                'name_de' => 'Motorrad/Roller',
                'source_id' => 'SCOPE3_COMMUTE_005',
                'factor_kg_co2e' => 0.103,
                'unit' => 'km',
                'uncertainty_percent' => 20,
            ],
            [
                'name' => 'Bus urbain',
                'name_en' => 'Urban bus',
                'name_de' => 'Stadtbus',
                'source_id' => 'SCOPE3_COMMUTE_006',
                'factor_kg_co2e' => 0.089,
                'unit' => 'passenger-km',
                'uncertainty_percent' => 20,
            ],
            [
                'name' => 'Métro/Tramway',
                'name_en' => 'Metro/Tram',
                'name_de' => 'U-Bahn/Straßenbahn',
                'source_id' => 'SCOPE3_COMMUTE_007',
                'factor_kg_co2e' => 0.0041,
                'unit' => 'passenger-km',
                'uncertainty_percent' => 20,
            ],
            [
                'name' => 'Train de banlieue',
                'name_en' => 'Suburban train',
                'name_de' => 'S-Bahn',
                'source_id' => 'SCOPE3_COMMUTE_008',
                'factor_kg_co2e' => 0.0356,
                'unit' => 'passenger-km',
                'uncertainty_percent' => 20,
            ],
            [
                'name' => 'Vélo électrique',
                'name_en' => 'Electric bike',
                'name_de' => 'E-Bike',
                'source_id' => 'SCOPE3_COMMUTE_009',
                'factor_kg_co2e' => 0.006,
                'unit' => 'km',
                'uncertainty_percent' => 30,
            ],
            [
                'name' => 'Trottinette électrique',
                'name_en' => 'Electric scooter',
                'name_de' => 'E-Scooter',
                'source_id' => 'SCOPE3_COMMUTE_010',
                'factor_kg_co2e' => 0.035,
                'unit' => 'km',
                'uncertainty_percent' => 35,
            ],
            [
                'name' => 'Covoiturage (conducteur)',
                'name_en' => 'Carpooling (driver)',
                'name_de' => 'Fahrgemeinschaft (Fahrer)',
                'source_id' => 'SCOPE3_COMMUTE_011',
                'factor_kg_co2e' => 0.085,
                'unit' => 'km',
                'uncertainty_percent' => 20,
            ],
            [
                'name' => 'Télétravail',
                'name_en' => 'Remote work',
                'name_de' => 'Homeoffice',
                'source_id' => 'SCOPE3_COMMUTE_012',
                'factor_kg_co2e' => 0.8,
                'unit' => 'days',
                'uncertainty_percent' => 40,
            ],
        ];

        foreach (self::COUNTRIES as $country) {
            foreach ($factors as $factor) {
                $this->createFactor($category->id, 3, $country, $factor, 'ecoinvent');
            }
        }

        $this->command->info('  - Commuting factors: ' . count($factors) . ' x ' . count(self::COUNTRIES) . ' countries');
    }

    /**
     * Seed downstream transportation factors (Category 9).
     */
    private function seedDownstreamTransportFactors(): void
    {
        $category = Category::where('code', 'downstream_transport')->first();

        if (! $category) {
            $this->command->warn('Category "downstream_transport" not found, skipping.');
            return;
        }

        // Same factors as upstream but with downstream context
        $factors = [
            [
                'name' => 'Livraison dernier kilomètre - camionnette',
                'name_en' => 'Last-mile delivery - van',
                'name_de' => 'Letzte-Meile-Lieferung - Transporter',
                'source_id' => 'SCOPE3_DOWN_001',
                'factor_kg_co2e' => 0.890,
                'unit' => 'tonne-km',
                'uncertainty_percent' => 25,
            ],
            [
                'name' => 'Livraison dernier kilomètre - vélo cargo',
                'name_en' => 'Last-mile delivery - cargo bike',
                'name_de' => 'Letzte-Meile-Lieferung - Lastenrad',
                'source_id' => 'SCOPE3_DOWN_002',
                'factor_kg_co2e' => 0.021,
                'unit' => 'tonne-km',
                'uncertainty_percent' => 35,
            ],
            [
                'name' => 'Distribution régionale - camion',
                'name_en' => 'Regional distribution - truck',
                'name_de' => 'Regionale Distribution - LKW',
                'source_id' => 'SCOPE3_DOWN_003',
                'factor_kg_co2e' => 0.0857,
                'unit' => 'tonne-km',
                'uncertainty_percent' => 15,
            ],
            [
                'name' => 'Client se rend en magasin - voiture',
                'name_en' => 'Customer travel to store - car',
                'name_de' => 'Kundenfahrt zum Geschäft - PKW',
                'source_id' => 'SCOPE3_DOWN_004',
                'factor_kg_co2e' => 0.171,
                'unit' => 'km',
                'uncertainty_percent' => 20,
            ],
        ];

        foreach (self::COUNTRIES as $country) {
            foreach ($factors as $factor) {
                $this->createFactor($category->id, 3, $country, $factor, 'ecoinvent');
            }
        }

        $this->command->info('  - Downstream transport factors: ' . count($factors) . ' x ' . count(self::COUNTRIES) . ' countries');
    }

    /**
     * Seed product use factors (Category 11).
     */
    private function seedProductUseFactors(): void
    {
        $category = Category::where('code', 'product_use')->first();

        if (! $category) {
            $this->command->warn('Category "product_use" not found, skipping.');
            return;
        }

        $factors = [
            [
                'name' => 'Utilisation appareil électrique (durée de vie)',
                'name_en' => 'Electric appliance use (lifetime)',
                'name_de' => 'Nutzung Elektrogerät (Lebensdauer)',
                'source_id' => 'SCOPE3_USE_001',
                'factor_kg_co2e' => 0.10,
                'unit' => 'kWh',
                'uncertainty_percent' => 25,
            ],
            [
                'name' => 'Véhicule vendu - utilisation (diesel)',
                'name_en' => 'Sold vehicle - use (diesel)',
                'name_de' => 'Verkauftes Fahrzeug - Nutzung (Diesel)',
                'source_id' => 'SCOPE3_USE_002',
                'factor_kg_co2e' => 2.51,
                'unit' => 'liters',
                'uncertainty_percent' => 10,
            ],
            [
                'name' => 'Véhicule vendu - utilisation (essence)',
                'name_en' => 'Sold vehicle - use (petrol)',
                'name_de' => 'Verkauftes Fahrzeug - Nutzung (Benzin)',
                'source_id' => 'SCOPE3_USE_003',
                'factor_kg_co2e' => 2.28,
                'unit' => 'liters',
                'uncertainty_percent' => 10,
            ],
            [
                'name' => 'Consommables vendus - utilisation',
                'name_en' => 'Sold consumables - use',
                'name_de' => 'Verkaufte Verbrauchsmaterialien - Nutzung',
                'source_id' => 'SCOPE3_USE_004',
                'factor_kg_co2e' => 0.5,
                'unit' => 'kg',
                'uncertainty_percent' => 50,
            ],
        ];

        foreach (self::COUNTRIES as $country) {
            foreach ($factors as $factor) {
                $this->createFactor($category->id, 3, $country, $factor, 'ecoinvent');
            }
        }

        $this->command->info('  - Product use factors: ' . count($factors) . ' x ' . count(self::COUNTRIES) . ' countries');
    }

    /**
     * Seed end-of-life treatment factors (Category 12).
     */
    private function seedEndOfLifeFactors(): void
    {
        $category = Category::where('code', 'end_of_life')->first();

        if (! $category) {
            $this->command->warn('Category "end_of_life" not found, skipping.');
            return;
        }

        $factors = [
            [
                'name' => 'Produit vendu - fin de vie (incinération)',
                'name_en' => 'Sold product - end of life (incineration)',
                'name_de' => 'Verkauftes Produkt - Ende der Lebensdauer (Verbrennung)',
                'source_id' => 'SCOPE3_EOL_001',
                'factor_kg_co2e' => 0.377,
                'unit' => 'kg',
                'uncertainty_percent' => 25,
            ],
            [
                'name' => 'Produit vendu - fin de vie (recyclage)',
                'name_en' => 'Sold product - end of life (recycling)',
                'name_de' => 'Verkauftes Produkt - Ende der Lebensdauer (Recycling)',
                'source_id' => 'SCOPE3_EOL_002',
                'factor_kg_co2e' => 0.021,
                'unit' => 'kg',
                'uncertainty_percent' => 30,
            ],
            [
                'name' => 'Produit vendu - fin de vie (enfouissement)',
                'name_en' => 'Sold product - end of life (landfill)',
                'name_de' => 'Verkauftes Produkt - Ende der Lebensdauer (Deponie)',
                'source_id' => 'SCOPE3_EOL_003',
                'factor_kg_co2e' => 0.578,
                'unit' => 'kg',
                'uncertainty_percent' => 30,
            ],
            [
                'name' => 'Emballage vendu - fin de vie (recyclage)',
                'name_en' => 'Sold packaging - end of life (recycling)',
                'name_de' => 'Verkaufte Verpackung - Ende der Lebensdauer (Recycling)',
                'source_id' => 'SCOPE3_EOL_004',
                'factor_kg_co2e' => 0.035,
                'unit' => 'kg',
                'uncertainty_percent' => 35,
            ],
        ];

        foreach (self::COUNTRIES as $country) {
            foreach ($factors as $factor) {
                $this->createFactor($category->id, 3, $country, $factor, 'ecoinvent');
            }
        }

        $this->command->info('  - End-of-life factors: ' . count($factors) . ' x ' . count(self::COUNTRIES) . ' countries');
    }

    /**
     * Seed investment factors (Category 15).
     */
    private function seedInvestmentFactors(): void
    {
        $category = Category::where('code', 'investments')->first();

        if (! $category) {
            $this->command->warn('Category "investments" not found, skipping.');
            return;
        }

        // Investment intensity factors by sector (kg CO2e per EUR invested)
        $factors = [
            [
                'name' => 'Investissement - Énergie fossile',
                'name_en' => 'Investment - Fossil fuels',
                'name_de' => 'Investition - Fossile Brennstoffe',
                'source_id' => 'SCOPE3_INV_001',
                'factor_kg_co2e' => 1.20,
                'unit' => 'EUR',
                'uncertainty_percent' => 40,
            ],
            [
                'name' => 'Investissement - Industrie lourde',
                'name_en' => 'Investment - Heavy industry',
                'name_de' => 'Investition - Schwerindustrie',
                'source_id' => 'SCOPE3_INV_002',
                'factor_kg_co2e' => 0.85,
                'unit' => 'EUR',
                'uncertainty_percent' => 40,
            ],
            [
                'name' => 'Investissement - Automobile',
                'name_en' => 'Investment - Automotive',
                'name_de' => 'Investition - Automobilindustrie',
                'source_id' => 'SCOPE3_INV_003',
                'factor_kg_co2e' => 0.52,
                'unit' => 'EUR',
                'uncertainty_percent' => 40,
            ],
            [
                'name' => 'Investissement - Immobilier',
                'name_en' => 'Investment - Real estate',
                'name_de' => 'Investition - Immobilien',
                'source_id' => 'SCOPE3_INV_004',
                'factor_kg_co2e' => 0.25,
                'unit' => 'EUR',
                'uncertainty_percent' => 45,
            ],
            [
                'name' => 'Investissement - Technologies',
                'name_en' => 'Investment - Technology',
                'name_de' => 'Investition - Technologie',
                'source_id' => 'SCOPE3_INV_005',
                'factor_kg_co2e' => 0.15,
                'unit' => 'EUR',
                'uncertainty_percent' => 45,
            ],
            [
                'name' => 'Investissement - Services financiers',
                'name_en' => 'Investment - Financial services',
                'name_de' => 'Investition - Finanzdienstleistungen',
                'source_id' => 'SCOPE3_INV_006',
                'factor_kg_co2e' => 0.08,
                'unit' => 'EUR',
                'uncertainty_percent' => 50,
            ],
            [
                'name' => 'Investissement - Énergies renouvelables',
                'name_en' => 'Investment - Renewable energy',
                'name_de' => 'Investition - Erneuerbare Energien',
                'source_id' => 'SCOPE3_INV_007',
                'factor_kg_co2e' => 0.05,
                'unit' => 'EUR',
                'uncertainty_percent' => 45,
            ],
            [
                'name' => 'Investissement - Moyenne tous secteurs',
                'name_en' => 'Investment - All sectors average',
                'name_de' => 'Investition - Durchschnitt alle Sektoren',
                'source_id' => 'SCOPE3_INV_DEFAULT',
                'factor_kg_co2e' => 0.35,
                'unit' => 'EUR',
                'uncertainty_percent' => 50,
            ],
        ];

        foreach (self::COUNTRIES as $country) {
            foreach ($factors as $factor) {
                $this->createFactor($category->id, 3, $country, $factor, 'ecoinvent');
            }
        }

        $this->command->info('  - Investment factors: ' . count($factors) . ' x ' . count(self::COUNTRIES) . ' countries');
    }

    /**
     * Create or update an emission factor.
     */
    private function createFactor(
        string $categoryId,
        int $scope,
        string $country,
        array $data,
        string $source = 'ecoinvent'
    ): EmissionFactor {
        return EmissionFactor::updateOrCreate(
            [
                'source' => $source,
                'source_id' => $data['source_id'] . '_' . $country,
                'country' => $country,
            ],
            [
                'id' => Str::uuid()->toString(),
                'category_id' => $categoryId,
                'scope' => $scope,
                'country' => $country,
                'name' => $data['name'],
                'name_en' => $data['name_en'] ?? $data['name'],
                'name_de' => $data['name_de'] ?? null,
                'factor_kg_co2e' => $data['factor_kg_co2e'],
                'factor_kg_co2' => $data['factor_kg_co2'] ?? null,
                'factor_kg_ch4' => $data['factor_kg_ch4'] ?? null,
                'factor_kg_n2o' => $data['factor_kg_n2o'] ?? null,
                'unit' => $data['unit'],
                'uncertainty_percent' => $data['uncertainty_percent'] ?? null,
                'methodology' => $data['methodology'] ?? 'spend-based',
                'source' => $source,
                'source_url' => match ($source) {
                    'ademe' => 'https://base-empreinte.ademe.fr/',
                    'uba' => 'https://www.umweltbundesamt.de/',
                    'ecoinvent' => 'https://ecoinvent.org/',
                    default => null,
                },
                'source_id' => $data['source_id'] . '_' . $country,
                'valid_from' => now()->startOfYear(),
                'valid_until' => now()->addYears(2)->endOfYear(),
                'is_active' => true,
            ]
        );
    }
}
