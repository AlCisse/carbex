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
        $factors = [
            // France - Residual Mix
            [
                'name' => 'Électricité - Mix résiduel France',
                'name_en' => 'Electricity - France Residual Mix',
                'category' => 'electricity',
                'subcategory' => 'residual_mix',
                'scope' => 2,
                'value' => 0.0544,
                'unit' => 'kgCO2e/kWh',
                'source' => 'AIB European Residual Mix',
                'source_url' => 'https://www.aib-net.org/facts/european-residual-mix',
                'country' => 'FR',
                'year' => 2023,
                'uncertainty' => 5.0,
                'ghg_category' => null,
                'calculation_method' => 'market_based',
            ],

            // Germany - Residual Mix
            [
                'name' => 'Strom - Residualmix Deutschland',
                'name_en' => 'Electricity - Germany Residual Mix',
                'category' => 'electricity',
                'subcategory' => 'residual_mix',
                'scope' => 2,
                'value' => 0.6590,
                'unit' => 'kgCO2e/kWh',
                'source' => 'AIB European Residual Mix',
                'source_url' => 'https://www.aib-net.org/facts/european-residual-mix',
                'country' => 'DE',
                'year' => 2023,
                'uncertainty' => 5.0,
                'ghg_category' => null,
                'calculation_method' => 'market_based',
            ],

            // Belgium - Residual Mix
            [
                'name' => 'Électricité - Mix résiduel Belgique',
                'name_en' => 'Electricity - Belgium Residual Mix',
                'category' => 'electricity',
                'subcategory' => 'residual_mix',
                'scope' => 2,
                'value' => 0.3290,
                'unit' => 'kgCO2e/kWh',
                'source' => 'AIB European Residual Mix',
                'source_url' => 'https://www.aib-net.org/facts/european-residual-mix',
                'country' => 'BE',
                'year' => 2023,
                'uncertainty' => 5.0,
                'ghg_category' => null,
                'calculation_method' => 'market_based',
            ],

            // Netherlands - Residual Mix
            [
                'name' => 'Elektriciteit - Residuele mix Nederland',
                'name_en' => 'Electricity - Netherlands Residual Mix',
                'category' => 'electricity',
                'subcategory' => 'residual_mix',
                'scope' => 2,
                'value' => 0.5350,
                'unit' => 'kgCO2e/kWh',
                'source' => 'AIB European Residual Mix',
                'source_url' => 'https://www.aib-net.org/facts/european-residual-mix',
                'country' => 'NL',
                'year' => 2023,
                'uncertainty' => 5.0,
                'ghg_category' => null,
                'calculation_method' => 'market_based',
            ],

            // Austria - Residual Mix
            [
                'name' => 'Strom - Residualmix Österreich',
                'name_en' => 'Electricity - Austria Residual Mix',
                'category' => 'electricity',
                'subcategory' => 'residual_mix',
                'scope' => 2,
                'value' => 0.3680,
                'unit' => 'kgCO2e/kWh',
                'source' => 'AIB European Residual Mix',
                'source_url' => 'https://www.aib-net.org/facts/european-residual-mix',
                'country' => 'AT',
                'year' => 2023,
                'uncertainty' => 5.0,
                'ghg_category' => null,
                'calculation_method' => 'market_based',
            ],

            // Switzerland - Residual Mix
            [
                'name' => 'Strom - Residualmix Schweiz',
                'name_en' => 'Electricity - Switzerland Residual Mix',
                'category' => 'electricity',
                'subcategory' => 'residual_mix',
                'scope' => 2,
                'value' => 0.0890,
                'unit' => 'kgCO2e/kWh',
                'source' => 'AIB European Residual Mix',
                'source_url' => 'https://www.aib-net.org/facts/european-residual-mix',
                'country' => 'CH',
                'year' => 2023,
                'uncertainty' => 5.0,
                'ghg_category' => null,
                'calculation_method' => 'market_based',
            ],

            // Spain - Residual Mix
            [
                'name' => 'Electricidad - Mix residual España',
                'name_en' => 'Electricity - Spain Residual Mix',
                'category' => 'electricity',
                'subcategory' => 'residual_mix',
                'scope' => 2,
                'value' => 0.2860,
                'unit' => 'kgCO2e/kWh',
                'source' => 'AIB European Residual Mix',
                'source_url' => 'https://www.aib-net.org/facts/european-residual-mix',
                'country' => 'ES',
                'year' => 2023,
                'uncertainty' => 5.0,
                'ghg_category' => null,
                'calculation_method' => 'market_based',
            ],

            // Italy - Residual Mix
            [
                'name' => 'Elettricità - Mix residuo Italia',
                'name_en' => 'Electricity - Italy Residual Mix',
                'category' => 'electricity',
                'subcategory' => 'residual_mix',
                'scope' => 2,
                'value' => 0.4570,
                'unit' => 'kgCO2e/kWh',
                'source' => 'AIB European Residual Mix',
                'source_url' => 'https://www.aib-net.org/facts/european-residual-mix',
                'country' => 'IT',
                'year' => 2023,
                'uncertainty' => 5.0,
                'ghg_category' => null,
                'calculation_method' => 'market_based',
            ],

            // UK - Residual Mix
            [
                'name' => 'Electricity - UK Residual Mix',
                'name_en' => 'Electricity - UK Residual Mix',
                'category' => 'electricity',
                'subcategory' => 'residual_mix',
                'scope' => 2,
                'value' => 0.3820,
                'unit' => 'kgCO2e/kWh',
                'source' => 'DEFRA',
                'source_url' => 'https://www.gov.uk/government/publications/greenhouse-gas-reporting-conversion-factors-2023',
                'country' => 'GB',
                'year' => 2023,
                'uncertainty' => 5.0,
                'ghg_category' => null,
                'calculation_method' => 'market_based',
            ],

            // Renewable Energy (100% Green Contracts)
            [
                'name' => 'Électricité verte certifiée (GO)',
                'name_en' => 'Certified Green Electricity (GO)',
                'category' => 'electricity',
                'subcategory' => 'renewable_certified',
                'scope' => 2,
                'value' => 0.0,
                'unit' => 'kgCO2e/kWh',
                'source' => 'GHG Protocol',
                'source_url' => 'https://ghgprotocol.org/scope_2_guidance',
                'country' => null,
                'year' => 2023,
                'uncertainty' => 0.0,
                'ghg_category' => null,
                'calculation_method' => 'market_based',
                'notes' => 'Requires valid Guarantee of Origin (GO) or equivalent certificate',
            ],

            // Solar PPA
            [
                'name' => 'PPA Solaire',
                'name_en' => 'Solar PPA',
                'category' => 'electricity',
                'subcategory' => 'ppa_solar',
                'scope' => 2,
                'value' => 0.041,
                'unit' => 'kgCO2e/kWh',
                'source' => 'IPCC AR5',
                'source_url' => 'https://www.ipcc.ch/report/ar5/',
                'country' => null,
                'year' => 2023,
                'uncertainty' => 20.0,
                'ghg_category' => null,
                'calculation_method' => 'market_based',
                'notes' => 'Lifecycle emissions for solar PV',
            ],

            // Wind PPA
            [
                'name' => 'PPA Éolien',
                'name_en' => 'Wind PPA',
                'category' => 'electricity',
                'subcategory' => 'ppa_wind',
                'scope' => 2,
                'value' => 0.011,
                'unit' => 'kgCO2e/kWh',
                'source' => 'IPCC AR5',
                'source_url' => 'https://www.ipcc.ch/report/ar5/',
                'country' => null,
                'year' => 2023,
                'uncertainty' => 20.0,
                'ghg_category' => null,
                'calculation_method' => 'market_based',
                'notes' => 'Lifecycle emissions for onshore wind',
            ],

            // Hydropower PPA
            [
                'name' => 'PPA Hydraulique',
                'name_en' => 'Hydropower PPA',
                'category' => 'electricity',
                'subcategory' => 'ppa_hydro',
                'scope' => 2,
                'value' => 0.024,
                'unit' => 'kgCO2e/kWh',
                'source' => 'IPCC AR5',
                'source_url' => 'https://www.ipcc.ch/report/ar5/',
                'country' => null,
                'year' => 2023,
                'uncertainty' => 30.0,
                'ghg_category' => null,
                'calculation_method' => 'market_based',
                'notes' => 'Lifecycle emissions for hydropower',
            ],

            // District Heating - Market Based
            [
                'name' => 'Réseau de chaleur urbain - Vert',
                'name_en' => 'District Heating - Green',
                'category' => 'district_heating',
                'subcategory' => 'green_certified',
                'scope' => 2,
                'value' => 0.05,
                'unit' => 'kgCO2e/kWh',
                'source' => 'ADEME',
                'source_url' => 'https://base-empreinte.ademe.fr/',
                'country' => 'FR',
                'year' => 2023,
                'uncertainty' => 15.0,
                'ghg_category' => null,
                'calculation_method' => 'market_based',
                'notes' => 'Networks with >50% renewable energy',
            ],

            // Major French Green Suppliers
            [
                'name' => 'Enercoop - 100% Renouvelable',
                'name_en' => 'Enercoop - 100% Renewable',
                'category' => 'electricity',
                'subcategory' => 'supplier_enercoop',
                'scope' => 2,
                'value' => 0.0,
                'unit' => 'kgCO2e/kWh',
                'source' => 'Enercoop',
                'source_url' => 'https://www.enercoop.fr/',
                'country' => 'FR',
                'year' => 2023,
                'uncertainty' => 0.0,
                'ghg_category' => null,
                'calculation_method' => 'market_based',
                'notes' => 'Supplier 100% renewable with GOs',
            ],

            [
                'name' => 'EDF Vert Électrique',
                'name_en' => 'EDF Green Electric',
                'category' => 'electricity',
                'subcategory' => 'supplier_edf_green',
                'scope' => 2,
                'value' => 0.0,
                'unit' => 'kgCO2e/kWh',
                'source' => 'EDF',
                'source_url' => 'https://www.edf.fr/',
                'country' => 'FR',
                'year' => 2023,
                'uncertainty' => 0.0,
                'ghg_category' => null,
                'calculation_method' => 'market_based',
                'notes' => 'EDF 100% renewable offer with GOs',
            ],
        ];

        foreach ($factors as $factor) {
            EmissionFactor::updateOrCreate(
                [
                    'category' => $factor['category'],
                    'subcategory' => $factor['subcategory'],
                    'country' => $factor['country'],
                    'calculation_method' => $factor['calculation_method'],
                    'year' => $factor['year'],
                ],
                [
                    'name' => $factor['name'],
                    'name_en' => $factor['name_en'],
                    'scope' => $factor['scope'],
                    'value' => $factor['value'],
                    'unit' => $factor['unit'],
                    'source' => $factor['source'],
                    'source_url' => $factor['source_url'],
                    'uncertainty' => $factor['uncertainty'],
                    'ghg_category' => $factor['ghg_category'] ?? null,
                    'notes' => $factor['notes'] ?? null,
                    'is_active' => true,
                ]
            );
        }

        $this->command->info('Market-based emission factors seeded successfully.');
    }
}
