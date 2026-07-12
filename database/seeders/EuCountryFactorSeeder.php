<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\EmissionFactor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * EU Country Emission Factors Seeder
 *
 * Populates emission factors for EU countries:
 * - Belgium (BE) - Source: VITO
 * - Netherlands (NL) - Source: CE Delft
 * - Austria (AT) - Source: Umweltbundesamt AT
 * - Switzerland (CH) - Source: BAFU
 * - Spain (ES) - Source: MITECO
 * - Italy (IT) - Source: ISPRA
 *
 * Data sources and methodology based on national environmental agencies
 * and EU reference values (EEA, Eurostat).
 */
class EuCountryFactorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding EU country emission factors...');

        $this->seedBelgiumFactors();
        $this->seedNetherlandsFactors();
        $this->seedAustriaFactors();
        $this->seedSwitzerlandFactors();
        $this->seedSpainFactors();
        $this->seedItalyFactors();

        $this->command->info('EU country emission factors seeded successfully.');
    }

    /**
     * Seed Belgium (BE) emission factors.
     * Source: VITO (Flemish Institute for Technological Research)
     */
    private function seedBelgiumFactors(): void
    {
        $this->command->info('  Seeding Belgium (BE) factors...');

        $electricityCategory = Category::where('code', 'electricity')->first();
        $fuelCategory = Category::where('code', 'fuel')->first();

        // Electricity grid mix
        if ($electricityCategory) {
            $this->createFactor($electricityCategory->id, 2, 'BE', 'vito', [
                'name' => 'Mix \u00e9lectrique Belgique',
                'name_en' => 'Belgian electricity grid mix',
                'name_fr' => 'Mix \u00e9lectrique Belgique',
                'name_nl' => 'Belgische elektriciteitsmix',
                'source_id' => 'VITO_ELEC_001',
                'factor_kg_co2e' => 0.167,
                'factor_kg_co2' => 0.162,
                'unit' => 'kWh',
                'uncertainty_percent' => 10,
                'methodology' => 'location-based',
            ]);

            $this->createFactor($electricityCategory->id, 2, 'BE', 'vito', [
                'name' => 'Residualmix Belgique',
                'name_en' => 'Belgian residual mix',
                'name_fr' => 'Residualmix Belgique',
                'source_id' => 'VITO_ELEC_002',
                'factor_kg_co2e' => 0.312,
                'factor_kg_co2' => 0.305,
                'unit' => 'kWh',
                'uncertainty_percent' => 15,
                'methodology' => 'market-based',
            ]);

            $this->createFactor($electricityCategory->id, 2, 'BE', 'vito', [
                'name' => 'Gaz naturel Belgique',
                'name_en' => 'Natural gas Belgium',
                'source_id' => 'VITO_GAS_001',
                'factor_kg_co2e' => 0.202,
                'factor_kg_co2' => 0.200,
                'unit' => 'kWh',
                'uncertainty_percent' => 5,
            ]);
        }

        // Fuels
        if ($fuelCategory) {
            $this->createFactor($fuelCategory->id, 1, 'BE', 'vito', [
                'name' => 'Essence (E10)',
                'name_en' => 'Petrol (E10)',
                'source_id' => 'VITO_FUEL_001',
                'factor_kg_co2e' => 2.38,
                'factor_kg_co2' => 2.34,
                'unit' => 'liters',
                'uncertainty_percent' => 5,
            ]);

            $this->createFactor($fuelCategory->id, 1, 'BE', 'vito', [
                'name' => 'Diesel (B7)',
                'name_en' => 'Diesel (B7)',
                'source_id' => 'VITO_FUEL_002',
                'factor_kg_co2e' => 2.64,
                'factor_kg_co2' => 2.61,
                'unit' => 'liters',
                'uncertainty_percent' => 5,
            ]);
        }

        $this->command->info('    - Belgium: 6 factors');
    }

    /**
     * Seed Netherlands (NL) emission factors.
     * Source: CE Delft
     */
    private function seedNetherlandsFactors(): void
    {
        $this->command->info('  Seeding Netherlands (NL) factors...');

        $electricityCategory = Category::where('code', 'electricity')->first();
        $fuelCategory = Category::where('code', 'fuel')->first();

        if ($electricityCategory) {
            $this->createFactor($electricityCategory->id, 2, 'NL', 'ce_delft', [
                'name' => 'Nederlandse elektriciteitsmix',
                'name_en' => 'Dutch electricity grid mix',
                'name_nl' => 'Nederlandse elektriciteitsmix',
                'source_id' => 'CE_ELEC_001',
                'factor_kg_co2e' => 0.328,
                'factor_kg_co2' => 0.316,
                'unit' => 'kWh',
                'uncertainty_percent' => 10,
                'methodology' => 'location-based',
            ]);

            $this->createFactor($electricityCategory->id, 2, 'NL', 'ce_delft', [
                'name' => 'Residualmix Nederland',
                'name_en' => 'Dutch residual mix',
                'source_id' => 'CE_ELEC_002',
                'factor_kg_co2e' => 0.476,
                'factor_kg_co2' => 0.462,
                'unit' => 'kWh',
                'uncertainty_percent' => 15,
                'methodology' => 'market-based',
            ]);

            $this->createFactor($electricityCategory->id, 2, 'NL', 'ce_delft', [
                'name' => 'Aardgas Nederland',
                'name_en' => 'Natural gas Netherlands',
                'source_id' => 'CE_GAS_001',
                'factor_kg_co2e' => 0.201,
                'factor_kg_co2' => 0.199,
                'unit' => 'kWh',
                'uncertainty_percent' => 5,
            ]);

            $this->createFactor($electricityCategory->id, 2, 'NL', 'ce_delft', [
                'name' => 'Stadswarmte Nederland',
                'name_en' => 'District heating Netherlands',
                'source_id' => 'CE_HEAT_001',
                'factor_kg_co2e' => 0.035,
                'factor_kg_co2' => 0.034,
                'unit' => 'kWh',
                'uncertainty_percent' => 20,
            ]);
        }

        if ($fuelCategory) {
            $this->createFactor($fuelCategory->id, 1, 'NL', 'ce_delft', [
                'name' => 'Benzine (Euro95-E10)',
                'name_en' => 'Petrol (Euro95-E10)',
                'source_id' => 'CE_FUEL_001',
                'factor_kg_co2e' => 2.37,
                'factor_kg_co2' => 2.33,
                'unit' => 'liters',
                'uncertainty_percent' => 5,
            ]);

            $this->createFactor($fuelCategory->id, 1, 'NL', 'ce_delft', [
                'name' => 'Diesel (B7)',
                'name_en' => 'Diesel (B7)',
                'source_id' => 'CE_FUEL_002',
                'factor_kg_co2e' => 2.65,
                'factor_kg_co2' => 2.62,
                'unit' => 'liters',
                'uncertainty_percent' => 5,
            ]);
        }

        $this->command->info('    - Netherlands: 6 factors');
    }

    /**
     * Seed Austria (AT) emission factors.
     * Source: Umweltbundesamt Austria
     */
    private function seedAustriaFactors(): void
    {
        $this->command->info('  Seeding Austria (AT) factors...');

        $electricityCategory = Category::where('code', 'electricity')->first();
        $fuelCategory = Category::where('code', 'fuel')->first();

        if ($electricityCategory) {
            // Austria has high renewable share (hydro)
            $this->createFactor($electricityCategory->id, 2, 'AT', 'umweltbundesamt_at', [
                'name' => '\u00d6sterreichischer Strommix',
                'name_en' => 'Austrian electricity grid mix',
                'name_de' => '\u00d6sterreichischer Strommix',
                'source_id' => 'UBA_AT_ELEC_001',
                'factor_kg_co2e' => 0.089,
                'factor_kg_co2' => 0.085,
                'unit' => 'kWh',
                'uncertainty_percent' => 10,
                'methodology' => 'location-based',
            ]);

            $this->createFactor($electricityCategory->id, 2, 'AT', 'umweltbundesamt_at', [
                'name' => 'Residualmix \u00d6sterreich',
                'name_en' => 'Austrian residual mix',
                'source_id' => 'UBA_AT_ELEC_002',
                'factor_kg_co2e' => 0.267,
                'factor_kg_co2' => 0.258,
                'unit' => 'kWh',
                'uncertainty_percent' => 15,
                'methodology' => 'market-based',
            ]);

            $this->createFactor($electricityCategory->id, 2, 'AT', 'umweltbundesamt_at', [
                'name' => 'Erdgas \u00d6sterreich',
                'name_en' => 'Natural gas Austria',
                'source_id' => 'UBA_AT_GAS_001',
                'factor_kg_co2e' => 0.201,
                'factor_kg_co2' => 0.199,
                'unit' => 'kWh',
                'uncertainty_percent' => 5,
            ]);

            $this->createFactor($electricityCategory->id, 2, 'AT', 'umweltbundesamt_at', [
                'name' => 'Fernw\u00e4rme \u00d6sterreich',
                'name_en' => 'District heating Austria',
                'source_id' => 'UBA_AT_HEAT_001',
                'factor_kg_co2e' => 0.145,
                'factor_kg_co2' => 0.140,
                'unit' => 'kWh',
                'uncertainty_percent' => 20,
            ]);
        }

        if ($fuelCategory) {
            $this->createFactor($fuelCategory->id, 1, 'AT', 'umweltbundesamt_at', [
                'name' => 'Benzin Super (E10)',
                'name_en' => 'Petrol Super (E10)',
                'source_id' => 'UBA_AT_FUEL_001',
                'factor_kg_co2e' => 2.37,
                'factor_kg_co2' => 2.33,
                'unit' => 'liters',
                'uncertainty_percent' => 5,
            ]);

            $this->createFactor($fuelCategory->id, 1, 'AT', 'umweltbundesamt_at', [
                'name' => 'Diesel',
                'name_en' => 'Diesel',
                'source_id' => 'UBA_AT_FUEL_002',
                'factor_kg_co2e' => 2.65,
                'factor_kg_co2' => 2.63,
                'unit' => 'liters',
                'uncertainty_percent' => 5,
            ]);
        }

        $this->command->info('    - Austria: 6 factors');
    }

    /**
     * Seed Switzerland (CH) emission factors.
     * Source: BAFU (Bundesamt f\u00fcr Umwelt)
     */
    private function seedSwitzerlandFactors(): void
    {
        $this->command->info('  Seeding Switzerland (CH) factors...');

        $electricityCategory = Category::where('code', 'electricity')->first();
        $fuelCategory = Category::where('code', 'fuel')->first();

        if ($electricityCategory) {
            // Switzerland has very low carbon electricity (hydro + nuclear)
            $this->createFactor($electricityCategory->id, 2, 'CH', 'bafu', [
                'name' => 'Schweizer Strommix',
                'name_en' => 'Swiss electricity grid mix',
                'name_de' => 'Schweizer Strommix',
                'name_fr' => 'Mix \u00e9lectrique suisse',
                'source_id' => 'BAFU_ELEC_001',
                'factor_kg_co2e' => 0.012,
                'factor_kg_co2' => 0.011,
                'unit' => 'kWh',
                'uncertainty_percent' => 15,
                'methodology' => 'location-based',
            ]);

            $this->createFactor($electricityCategory->id, 2, 'CH', 'bafu', [
                'name' => 'Schweizer Residualmix',
                'name_en' => 'Swiss residual mix',
                'source_id' => 'BAFU_ELEC_002',
                'factor_kg_co2e' => 0.128,
                'factor_kg_co2' => 0.124,
                'unit' => 'kWh',
                'uncertainty_percent' => 20,
                'methodology' => 'market-based',
            ]);

            $this->createFactor($electricityCategory->id, 2, 'CH', 'bafu', [
                'name' => 'Erdgas Schweiz',
                'name_en' => 'Natural gas Switzerland',
                'source_id' => 'BAFU_GAS_001',
                'factor_kg_co2e' => 0.203,
                'factor_kg_co2' => 0.201,
                'unit' => 'kWh',
                'uncertainty_percent' => 5,
            ]);

            $this->createFactor($electricityCategory->id, 2, 'CH', 'bafu', [
                'name' => 'Fernw\u00e4rme Schweiz',
                'name_en' => 'District heating Switzerland',
                'source_id' => 'BAFU_HEAT_001',
                'factor_kg_co2e' => 0.120,
                'factor_kg_co2' => 0.115,
                'unit' => 'kWh',
                'uncertainty_percent' => 25,
            ]);
        }

        if ($fuelCategory) {
            $this->createFactor($fuelCategory->id, 1, 'CH', 'bafu', [
                'name' => 'Benzin Bleifrei 95',
                'name_en' => 'Unleaded petrol 95',
                'source_id' => 'BAFU_FUEL_001',
                'factor_kg_co2e' => 2.38,
                'factor_kg_co2' => 2.34,
                'unit' => 'liters',
                'uncertainty_percent' => 5,
            ]);

            $this->createFactor($fuelCategory->id, 1, 'CH', 'bafu', [
                'name' => 'Diesel',
                'name_en' => 'Diesel',
                'source_id' => 'BAFU_FUEL_002',
                'factor_kg_co2e' => 2.66,
                'factor_kg_co2' => 2.64,
                'unit' => 'liters',
                'uncertainty_percent' => 5,
            ]);

            $this->createFactor($fuelCategory->id, 1, 'CH', 'bafu', [
                'name' => 'Heiz\u00f6l extra-leicht',
                'name_en' => 'Light heating oil',
                'source_id' => 'BAFU_FUEL_003',
                'factor_kg_co2e' => 2.68,
                'factor_kg_co2' => 2.66,
                'unit' => 'liters',
                'uncertainty_percent' => 5,
            ]);
        }

        $this->command->info('    - Switzerland: 7 factors');
    }

    /**
     * Seed Spain (ES) emission factors.
     * Source: MITECO (Ministerio para la Transici\u00f3n Ecol\u00f3gica)
     */
    private function seedSpainFactors(): void
    {
        $this->command->info('  Seeding Spain (ES) factors...');

        $electricityCategory = Category::where('code', 'electricity')->first();
        $fuelCategory = Category::where('code', 'fuel')->first();

        if ($electricityCategory) {
            $this->createFactor($electricityCategory->id, 2, 'ES', 'miteco', [
                'name' => 'Mix el\u00e9ctrico Espa\u00f1a',
                'name_en' => 'Spanish electricity grid mix',
                'name_es' => 'Mix el\u00e9ctrico Espa\u00f1a',
                'source_id' => 'MITECO_ELEC_001',
                'factor_kg_co2e' => 0.138,
                'factor_kg_co2' => 0.133,
                'unit' => 'kWh',
                'uncertainty_percent' => 10,
                'methodology' => 'location-based',
            ]);

            $this->createFactor($electricityCategory->id, 2, 'ES', 'miteco', [
                'name' => 'Residualmix Espa\u00f1a',
                'name_en' => 'Spanish residual mix',
                'source_id' => 'MITECO_ELEC_002',
                'factor_kg_co2e' => 0.285,
                'factor_kg_co2' => 0.278,
                'unit' => 'kWh',
                'uncertainty_percent' => 15,
                'methodology' => 'market-based',
            ]);

            $this->createFactor($electricityCategory->id, 2, 'ES', 'miteco', [
                'name' => 'Gas natural Espa\u00f1a',
                'name_en' => 'Natural gas Spain',
                'source_id' => 'MITECO_GAS_001',
                'factor_kg_co2e' => 0.202,
                'factor_kg_co2' => 0.200,
                'unit' => 'kWh',
                'uncertainty_percent' => 5,
            ]);
        }

        if ($fuelCategory) {
            $this->createFactor($fuelCategory->id, 1, 'ES', 'miteco', [
                'name' => 'Gasolina 95 (E10)',
                'name_en' => 'Petrol 95 (E10)',
                'source_id' => 'MITECO_FUEL_001',
                'factor_kg_co2e' => 2.37,
                'factor_kg_co2' => 2.33,
                'unit' => 'liters',
                'uncertainty_percent' => 5,
            ]);

            $this->createFactor($fuelCategory->id, 1, 'ES', 'miteco', [
                'name' => 'Gasoil A',
                'name_en' => 'Diesel A',
                'source_id' => 'MITECO_FUEL_002',
                'factor_kg_co2e' => 2.65,
                'factor_kg_co2' => 2.63,
                'unit' => 'liters',
                'uncertainty_percent' => 5,
            ]);

            $this->createFactor($fuelCategory->id, 1, 'ES', 'miteco', [
                'name' => 'Gasoil C (calefacci\u00f3n)',
                'name_en' => 'Heating oil C',
                'source_id' => 'MITECO_FUEL_003',
                'factor_kg_co2e' => 2.68,
                'factor_kg_co2' => 2.66,
                'unit' => 'liters',
                'uncertainty_percent' => 5,
            ]);
        }

        $this->command->info('    - Spain: 6 factors');
    }

    /**
     * Seed Italy (IT) emission factors.
     * Source: ISPRA (Istituto Superiore per la Protezione e la Ricerca Ambientale)
     */
    private function seedItalyFactors(): void
    {
        $this->command->info('  Seeding Italy (IT) factors...');

        $electricityCategory = Category::where('code', 'electricity')->first();
        $fuelCategory = Category::where('code', 'fuel')->first();

        if ($electricityCategory) {
            $this->createFactor($electricityCategory->id, 2, 'IT', 'ispra', [
                'name' => 'Mix elettrico Italia',
                'name_en' => 'Italian electricity grid mix',
                'name_it' => 'Mix elettrico Italia',
                'source_id' => 'ISPRA_ELEC_001',
                'factor_kg_co2e' => 0.256,
                'factor_kg_co2' => 0.248,
                'unit' => 'kWh',
                'uncertainty_percent' => 10,
                'methodology' => 'location-based',
            ]);

            $this->createFactor($electricityCategory->id, 2, 'IT', 'ispra', [
                'name' => 'Residualmix Italia',
                'name_en' => 'Italian residual mix',
                'source_id' => 'ISPRA_ELEC_002',
                'factor_kg_co2e' => 0.457,
                'factor_kg_co2' => 0.445,
                'unit' => 'kWh',
                'uncertainty_percent' => 15,
                'methodology' => 'market-based',
            ]);

            $this->createFactor($electricityCategory->id, 2, 'IT', 'ispra', [
                'name' => 'Gas naturale Italia',
                'name_en' => 'Natural gas Italy',
                'source_id' => 'ISPRA_GAS_001',
                'factor_kg_co2e' => 0.202,
                'factor_kg_co2' => 0.200,
                'unit' => 'kWh',
                'uncertainty_percent' => 5,
            ]);

            $this->createFactor($electricityCategory->id, 2, 'IT', 'ispra', [
                'name' => 'Teleriscaldamento Italia',
                'name_en' => 'District heating Italy',
                'source_id' => 'ISPRA_HEAT_001',
                'factor_kg_co2e' => 0.168,
                'factor_kg_co2' => 0.162,
                'unit' => 'kWh',
                'uncertainty_percent' => 20,
            ]);
        }

        if ($fuelCategory) {
            $this->createFactor($fuelCategory->id, 1, 'IT', 'ispra', [
                'name' => 'Benzina (E10)',
                'name_en' => 'Petrol (E10)',
                'source_id' => 'ISPRA_FUEL_001',
                'factor_kg_co2e' => 2.37,
                'factor_kg_co2' => 2.33,
                'unit' => 'liters',
                'uncertainty_percent' => 5,
            ]);

            $this->createFactor($fuelCategory->id, 1, 'IT', 'ispra', [
                'name' => 'Gasolio autotrazione',
                'name_en' => 'Automotive diesel',
                'source_id' => 'ISPRA_FUEL_002',
                'factor_kg_co2e' => 2.65,
                'factor_kg_co2' => 2.63,
                'unit' => 'liters',
                'uncertainty_percent' => 5,
            ]);

            $this->createFactor($fuelCategory->id, 1, 'IT', 'ispra', [
                'name' => 'Gasolio riscaldamento',
                'name_en' => 'Heating oil',
                'source_id' => 'ISPRA_FUEL_003',
                'factor_kg_co2e' => 2.68,
                'factor_kg_co2' => 2.66,
                'unit' => 'liters',
                'uncertainty_percent' => 5,
            ]);

            $this->createFactor($fuelCategory->id, 1, 'IT', 'ispra', [
                'name' => 'GPL',
                'name_en' => 'LPG',
                'source_id' => 'ISPRA_FUEL_004',
                'factor_kg_co2e' => 1.65,
                'factor_kg_co2' => 1.63,
                'unit' => 'liters',
                'uncertainty_percent' => 5,
            ]);
        }

        $this->command->info('    - Italy: 8 factors');
    }

    /**
     * Create or update an emission factor.
     */
    private function createFactor(
        string $categoryId,
        int $scope,
        string $country,
        string $source,
        array $data
    ): EmissionFactor {
        $sourceUrls = [
            'vito' => 'https://vito.be/',
            'ce_delft' => 'https://ce.nl/',
            'umweltbundesamt_at' => 'https://www.umweltbundesamt.at/',
            'bafu' => 'https://www.bafu.admin.ch/',
            'miteco' => 'https://www.miteco.gob.es/',
            'ispra' => 'https://www.isprambiente.gov.it/',
        ];

        // Build translations metadata for languages not in main columns
        $translations = [];
        foreach (['name_fr', 'name_nl', 'name_es', 'name_it'] as $key) {
            if (isset($data[$key])) {
                $lang = str_replace('name_', '', $key);
                $translations[$lang] = ['name' => $data[$key]];
            }
        }

        return EmissionFactor::updateOrCreate(
            [
                'source' => $source,
                'source_id' => $data['source_id'],
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
                'methodology' => $data['methodology'] ?? null,
                'source_url' => $sourceUrls[$source] ?? null,
                'valid_from' => now()->startOfYear(),
                'valid_until' => now()->addYears(2)->endOfYear(),
                'is_active' => true,
                'metadata' => ! empty($translations) ? ['translations' => $translations] : null,
            ]
        );
    }
}
