<?php

namespace Database\Seeders;

use App\Models\EmissionFactor;
use Illuminate\Database\Seeder;

/**
 * Market-Based Emission Factors Seeder
 *
 * Seeds emission factors for market-based Scope 2 accounting.
 * These factors are based on:
 * - Renewable Energy Certificates (RECs/GOs)
 * - Power Purchase Agreements (PPAs)
 * - Supplier-specific emission factors
 * - Residual mix factors
 *
 * @see https://ghgprotocol.org/scope_2_guidance
 */
class MarketBasedFactorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding market-based emission factors...');

        $factors = [
            // France - Residual Mix
            [
                'name' => 'Électricité - Mix résiduel France',
                'name_en' => 'Electricity - France Residual Mix',
                'scope' => 2,
                'factor_kg_co2e' => 0.0544,
                'unit' => 'kWh',
                'source' => 'aib_residual_mix',
                'source_url' => 'https://www.aib-net.org/facts/european-residual-mix',
                'country' => 'FR',
                'methodology' => 'market_based',
                'sector' => 'electricity',
                'uncertainty_percent' => 5.0,
            ],

            // Germany - Residual Mix
            [
                'name' => 'Strom - Residualmix Deutschland',
                'name_en' => 'Electricity - Germany Residual Mix',
                'scope' => 2,
                'factor_kg_co2e' => 0.6590,
                'unit' => 'kWh',
                'source' => 'aib_residual_mix',
                'source_url' => 'https://www.aib-net.org/facts/european-residual-mix',
                'country' => 'DE',
                'methodology' => 'market_based',
                'sector' => 'electricity',
                'uncertainty_percent' => 5.0,
            ],

            // Belgium - Residual Mix
            [
                'name' => 'Électricité - Mix résiduel Belgique',
                'name_en' => 'Electricity - Belgium Residual Mix',
                'scope' => 2,
                'factor_kg_co2e' => 0.3290,
                'unit' => 'kWh',
                'source' => 'aib_residual_mix',
                'source_url' => 'https://www.aib-net.org/facts/european-residual-mix',
                'country' => 'BE',
                'methodology' => 'market_based',
                'sector' => 'electricity',
                'uncertainty_percent' => 5.0,
            ],

            // Netherlands - Residual Mix
            [
                'name' => 'Elektriciteit - Residuele mix Nederland',
                'name_en' => 'Electricity - Netherlands Residual Mix',
                'scope' => 2,
                'factor_kg_co2e' => 0.5350,
                'unit' => 'kWh',
                'source' => 'aib_residual_mix',
                'source_url' => 'https://www.aib-net.org/facts/european-residual-mix',
                'country' => 'NL',
                'methodology' => 'market_based',
                'sector' => 'electricity',
                'uncertainty_percent' => 5.0,
            ],

            // Austria - Residual Mix
            [
                'name' => 'Strom - Residualmix Österreich',
                'name_en' => 'Electricity - Austria Residual Mix',
                'scope' => 2,
                'factor_kg_co2e' => 0.3680,
                'unit' => 'kWh',
                'source' => 'aib_residual_mix',
                'source_url' => 'https://www.aib-net.org/facts/european-residual-mix',
                'country' => 'AT',
                'methodology' => 'market_based',
                'sector' => 'electricity',
                'uncertainty_percent' => 5.0,
            ],

            // Switzerland - Residual Mix
            [
                'name' => 'Strom - Residualmix Schweiz',
                'name_en' => 'Electricity - Switzerland Residual Mix',
                'scope' => 2,
                'factor_kg_co2e' => 0.0890,
                'unit' => 'kWh',
                'source' => 'aib_residual_mix',
                'source_url' => 'https://www.aib-net.org/facts/european-residual-mix',
                'country' => 'CH',
                'methodology' => 'market_based',
                'sector' => 'electricity',
                'uncertainty_percent' => 5.0,
            ],

            // Spain - Residual Mix
            [
                'name' => 'Electricidad - Mix residual España',
                'name_en' => 'Electricity - Spain Residual Mix',
                'scope' => 2,
                'factor_kg_co2e' => 0.2860,
                'unit' => 'kWh',
                'source' => 'aib_residual_mix',
                'source_url' => 'https://www.aib-net.org/facts/european-residual-mix',
                'country' => 'ES',
                'methodology' => 'market_based',
                'sector' => 'electricity',
                'uncertainty_percent' => 5.0,
            ],

            // Italy - Residual Mix
            [
                'name' => 'Elettricità - Mix residuo Italia',
                'name_en' => 'Electricity - Italy Residual Mix',
                'scope' => 2,
                'factor_kg_co2e' => 0.4570,
                'unit' => 'kWh',
                'source' => 'aib_residual_mix',
                'source_url' => 'https://www.aib-net.org/facts/european-residual-mix',
                'country' => 'IT',
                'methodology' => 'market_based',
                'sector' => 'electricity',
                'uncertainty_percent' => 5.0,
            ],

            // UK - Residual Mix
            [
                'name' => 'Electricity - UK Residual Mix',
                'name_en' => 'Electricity - UK Residual Mix',
                'scope' => 2,
                'factor_kg_co2e' => 0.3820,
                'unit' => 'kWh',
                'source' => 'defra',
                'source_url' => 'https://www.gov.uk/government/publications/greenhouse-gas-reporting-conversion-factors-2023',
                'country' => 'GB',
                'methodology' => 'market_based',
                'sector' => 'electricity',
                'uncertainty_percent' => 5.0,
            ],

            // Renewable Energy (100% Green Contracts)
            [
                'name' => 'Électricité verte certifiée (GO)',
                'name_en' => 'Certified Green Electricity (GO)',
                'scope' => 2,
                'factor_kg_co2e' => 0.0,
                'unit' => 'kWh',
                'source' => 'ghg_protocol',
                'source_url' => 'https://ghgprotocol.org/scope_2_guidance',
                'country' => null,
                'methodology' => 'market_based',
                'sector' => 'electricity_renewable',
                'uncertainty_percent' => 0.0,
                'description' => 'Requires valid Guarantee of Origin (GO) or equivalent certificate',
            ],

            // Solar PPA
            [
                'name' => 'PPA Solaire',
                'name_en' => 'Solar PPA',
                'scope' => 2,
                'factor_kg_co2e' => 0.041,
                'unit' => 'kWh',
                'source' => 'ipcc_ar5',
                'source_url' => 'https://www.ipcc.ch/report/ar5/',
                'country' => null,
                'methodology' => 'market_based',
                'sector' => 'electricity_ppa',
                'uncertainty_percent' => 20.0,
                'description' => 'Lifecycle emissions for solar PV',
            ],

            // Wind PPA
            [
                'name' => 'PPA Éolien',
                'name_en' => 'Wind PPA',
                'scope' => 2,
                'factor_kg_co2e' => 0.011,
                'unit' => 'kWh',
                'source' => 'ipcc_ar5',
                'source_url' => 'https://www.ipcc.ch/report/ar5/',
                'country' => null,
                'methodology' => 'market_based',
                'sector' => 'electricity_ppa',
                'uncertainty_percent' => 20.0,
                'description' => 'Lifecycle emissions for onshore wind',
            ],

            // Hydropower PPA
            [
                'name' => 'PPA Hydraulique',
                'name_en' => 'Hydropower PPA',
                'scope' => 2,
                'factor_kg_co2e' => 0.024,
                'unit' => 'kWh',
                'source' => 'ipcc_ar5',
                'source_url' => 'https://www.ipcc.ch/report/ar5/',
                'country' => null,
                'methodology' => 'market_based',
                'sector' => 'electricity_ppa',
                'uncertainty_percent' => 30.0,
                'description' => 'Lifecycle emissions for hydropower',
            ],

            // District Heating - Market Based
            [
                'name' => 'Réseau de chaleur urbain - Vert',
                'name_en' => 'District Heating - Green',
                'scope' => 2,
                'factor_kg_co2e' => 0.05,
                'unit' => 'kWh',
                'source' => 'ademe',
                'source_url' => 'https://base-empreinte.ademe.fr/',
                'country' => 'FR',
                'methodology' => 'market_based',
                'sector' => 'district_heating',
                'uncertainty_percent' => 15.0,
                'description' => 'Networks with >50% renewable energy',
            ],

            // Major French Green Suppliers
            [
                'name' => 'Enercoop - 100% Renouvelable',
                'name_en' => 'Enercoop - 100% Renewable',
                'scope' => 2,
                'factor_kg_co2e' => 0.0,
                'unit' => 'kWh',
                'source' => 'supplier_enercoop',
                'source_url' => 'https://www.enercoop.fr/',
                'country' => 'FR',
                'methodology' => 'market_based',
                'sector' => 'electricity_supplier',
                'uncertainty_percent' => 0.0,
                'description' => 'Supplier 100% renewable with GOs',
            ],

            [
                'name' => 'EDF Vert Électrique',
                'name_en' => 'EDF Green Electric',
                'scope' => 2,
                'factor_kg_co2e' => 0.0,
                'unit' => 'kWh',
                'source' => 'supplier_edf',
                'source_url' => 'https://www.edf.fr/',
                'country' => 'FR',
                'methodology' => 'market_based',
                'sector' => 'electricity_supplier',
                'uncertainty_percent' => 0.0,
                'description' => 'EDF 100% renewable offer with GOs',
            ],
        ];

        $count = 0;
        foreach ($factors as $factor) {
            EmissionFactor::updateOrCreate(
                [
                    'name' => $factor['name'],
                    'country' => $factor['country'],
                    'methodology' => $factor['methodology'],
                ],
                [
                    'name_en' => $factor['name_en'],
                    'scope' => $factor['scope'],
                    'factor_kg_co2e' => $factor['factor_kg_co2e'],
                    'unit' => $factor['unit'],
                    'source' => $factor['source'],
                    'source_url' => $factor['source_url'],
                    'sector' => $factor['sector'],
                    'uncertainty_percent' => $factor['uncertainty_percent'],
                    'description' => $factor['description'] ?? null,
                    'is_active' => true,
                ]
            );
            $count++;
        }

        $this->command->info("  - Market-based factors: {$count}");
        $this->command->info('Market-based emission factors seeded successfully.');
    }
}
