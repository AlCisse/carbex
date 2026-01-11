<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\EmissionFactor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * UBA Emission Factors Seeder
 *
 * Populates emission factors from UBA (Umweltbundesamt - German Federal Environment Agency)
 * Source: https://www.umweltbundesamt.de/
 *
 * Categories covered:
 * - Scope 1: Brennstoffe, Fahrzeuge
 * - Scope 2: Strom, Wärme
 * - Scope 3: Dienstreisen, Einkäufe, Abfall
 */
class UbaFactorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding UBA emission factors (Germany)...');

        // Scope 1: Brennstoffe
        $this->seedFuelFactors();

        // Scope 2: Strom
        $this->seedElectricityFactors();

        // Scope 3: Diverse
        $this->seedTravelFactors();
        $this->seedSpendBasedFactors();
        $this->seedWasteFactors();
        $this->seedFreightFactors();

        $this->command->info('UBA emission factors seeded successfully.');
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
            // Kraftstoffe
            [
                'name' => 'Benzin (Super E10)',
                'name_en' => 'Petrol (Super E10)',
                'name_de' => 'Benzin (Super E10)',
                'source_id' => 'UBA_FUEL_001',
                'factor_kg_co2e' => 2.37,
                'factor_kg_co2' => 2.33,
                'factor_kg_ch4' => 0.00050,
                'factor_kg_n2o' => 0.00055,
                'unit' => 'liters',
                'uncertainty_percent' => 5,
            ],
            [
                'name' => 'Diesel',
                'name_en' => 'Diesel',
                'name_de' => 'Diesel',
                'source_id' => 'UBA_FUEL_002',
                'factor_kg_co2e' => 2.65,
                'factor_kg_co2' => 2.63,
                'factor_kg_ch4' => 0.00002,
                'factor_kg_n2o' => 0.00042,
                'unit' => 'liters',
                'uncertainty_percent' => 5,
            ],
            [
                'name' => 'Autogas (LPG)',
                'name_en' => 'LPG (Autogas)',
                'name_de' => 'Autogas (LPG)',
                'source_id' => 'UBA_FUEL_003',
                'factor_kg_co2e' => 1.64,
                'factor_kg_co2' => 1.63,
                'factor_kg_ch4' => 0.00014,
                'factor_kg_n2o' => 0.00006,
                'unit' => 'liters',
                'uncertainty_percent' => 5,
            ],
            [
                'name' => 'Erdgas (CNG)',
                'name_en' => 'Natural gas (CNG)',
                'name_de' => 'Erdgas (CNG)',
                'source_id' => 'UBA_FUEL_004',
                'factor_kg_co2e' => 2.79,
                'factor_kg_co2' => 2.75,
                'factor_kg_ch4' => 0.0008,
                'factor_kg_n2o' => 0.00002,
                'unit' => 'kg',
                'uncertainty_percent' => 5,
            ],
            // Heizstoffe
            [
                'name' => 'Heizöl EL',
                'name_en' => 'Light heating oil',
                'name_de' => 'Heizöl EL',
                'source_id' => 'UBA_FUEL_005',
                'factor_kg_co2e' => 2.68,
                'factor_kg_co2' => 2.66,
                'factor_kg_ch4' => 0.00006,
                'factor_kg_n2o' => 0.00006,
                'unit' => 'liters',
                'uncertainty_percent' => 5,
            ],
            [
                'name' => 'Erdgas H (kWh)',
                'name_en' => 'Natural gas H (kWh)',
                'name_de' => 'Erdgas H (kWh)',
                'source_id' => 'UBA_FUEL_006',
                'factor_kg_co2e' => 0.201,
                'factor_kg_co2' => 0.199,
                'factor_kg_ch4' => 0.000055,
                'factor_kg_n2o' => 0.000001,
                'unit' => 'kWh',
                'uncertainty_percent' => 5,
            ],
            [
                'name' => 'Erdgas H (m³)',
                'name_en' => 'Natural gas H (m³)',
                'name_de' => 'Erdgas H (m³)',
                'source_id' => 'UBA_FUEL_007',
                'factor_kg_co2e' => 2.00,
                'factor_kg_co2' => 1.98,
                'factor_kg_ch4' => 0.00055,
                'factor_kg_n2o' => 0.00001,
                'unit' => 'm3',
                'uncertainty_percent' => 5,
            ],
            [
                'name' => 'Flüssiggas (Propan)',
                'name_en' => 'LPG (Propane)',
                'name_de' => 'Flüssiggas (Propan)',
                'source_id' => 'UBA_FUEL_008',
                'factor_kg_co2e' => 2.98,
                'factor_kg_co2' => 2.94,
                'factor_kg_ch4' => 0.0001,
                'factor_kg_n2o' => 0.0001,
                'unit' => 'kg',
                'uncertainty_percent' => 5,
            ],
            [
                'name' => 'Holzpellets',
                'name_en' => 'Wood pellets',
                'name_de' => 'Holzpellets',
                'source_id' => 'UBA_FUEL_009',
                'factor_kg_co2e' => 0.023,
                'factor_kg_co2' => 0.0,
                'factor_kg_ch4' => 0.008,
                'factor_kg_n2o' => 0.0004,
                'unit' => 'kg',
                'uncertainty_percent' => 15,
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
            // Strommix Deutschland
            [
                'name' => 'Strommix Deutschland',
                'name_en' => 'German electricity grid mix',
                'name_de' => 'Strommix Deutschland',
                'source_id' => 'UBA_ELEC_001',
                'factor_kg_co2e' => 0.366,
                'factor_kg_co2' => 0.350,
                'factor_kg_ch4' => 0.00045,
                'factor_kg_n2o' => 0.0000045,
                'unit' => 'kWh',
                'uncertainty_percent' => 10,
                'methodology' => 'location-based',
            ],
            // Market-based (Residualmix)
            [
                'name' => 'Residualmix Deutschland',
                'name_en' => 'German residual mix',
                'name_de' => 'Residualmix Deutschland',
                'source_id' => 'UBA_ELEC_002',
                'factor_kg_co2e' => 0.498,
                'factor_kg_co2' => 0.480,
                'factor_kg_ch4' => 0.0006,
                'factor_kg_n2o' => 0.000006,
                'unit' => 'kWh',
                'uncertainty_percent' => 15,
                'methodology' => 'market-based',
            ],
            // Ökostrom
            [
                'name' => 'Ökostrom (zertifiziert)',
                'name_en' => 'Green electricity (certified)',
                'name_de' => 'Ökostrom (zertifiziert)',
                'source_id' => 'UBA_ELEC_003',
                'factor_kg_co2e' => 0.0,
                'factor_kg_co2' => 0.0,
                'unit' => 'kWh',
                'uncertainty_percent' => 0,
                'methodology' => 'market-based',
            ],
            // Fernwärme
            [
                'name' => 'Fernwärme Deutschland (Durchschnitt)',
                'name_en' => 'District heating Germany (average)',
                'name_de' => 'Fernwärme Deutschland (Durchschnitt)',
                'source_id' => 'UBA_HEAT_001',
                'factor_kg_co2e' => 0.182,
                'factor_kg_co2' => 0.175,
                'factor_kg_ch4' => 0.0001,
                'factor_kg_n2o' => 0.00002,
                'unit' => 'kWh',
                'uncertainty_percent' => 20,
            ],
            [
                'name' => 'Fernwärme aus KWK',
                'name_en' => 'District heating from CHP',
                'name_de' => 'Fernwärme aus KWK',
                'source_id' => 'UBA_HEAT_002',
                'factor_kg_co2e' => 0.150,
                'factor_kg_co2' => 0.145,
                'factor_kg_ch4' => 0.00008,
                'factor_kg_n2o' => 0.000015,
                'unit' => 'kWh',
                'uncertainty_percent' => 20,
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
            // Flugzeug
            [
                'name' => 'Kurzstreckenflug (<1500 km)',
                'name_en' => 'Short-haul flight (<1500 km)',
                'name_de' => 'Kurzstreckenflug (<1500 km)',
                'source_id' => 'UBA_FLIGHT_001',
                'factor_kg_co2e' => 0.284,
                'factor_kg_co2' => 0.274,
                'factor_kg_ch4' => 0.00001,
                'factor_kg_n2o' => 0.00001,
                'unit' => 'passenger-km',
                'uncertainty_percent' => 20,
                'category' => 'business_travel',
            ],
            [
                'name' => 'Mittelstreckenflug (1500-4000 km)',
                'name_en' => 'Medium-haul flight (1500-4000 km)',
                'name_de' => 'Mittelstreckenflug (1500-4000 km)',
                'source_id' => 'UBA_FLIGHT_002',
                'factor_kg_co2e' => 0.178,
                'factor_kg_co2' => 0.171,
                'factor_kg_ch4' => 0.00001,
                'factor_kg_n2o' => 0.00001,
                'unit' => 'passenger-km',
                'uncertainty_percent' => 20,
                'category' => 'business_travel',
            ],
            [
                'name' => 'Langstreckenflug (>4000 km)',
                'name_en' => 'Long-haul flight (>4000 km)',
                'name_de' => 'Langstreckenflug (>4000 km)',
                'source_id' => 'UBA_FLIGHT_003',
                'factor_kg_co2e' => 0.147,
                'factor_kg_co2' => 0.142,
                'factor_kg_ch4' => 0.00001,
                'factor_kg_n2o' => 0.00001,
                'unit' => 'passenger-km',
                'uncertainty_percent' => 20,
                'category' => 'business_travel',
            ],
            // Bahn
            [
                'name' => 'ICE/IC (Fernverkehr)',
                'name_en' => 'ICE/IC (Long-distance)',
                'name_de' => 'ICE/IC (Fernverkehr)',
                'source_id' => 'UBA_TRAIN_001',
                'factor_kg_co2e' => 0.029,
                'factor_kg_co2' => 0.028,
                'factor_kg_ch4' => 0.000003,
                'factor_kg_n2o' => 0.0000003,
                'unit' => 'passenger-km',
                'uncertainty_percent' => 15,
                'category' => 'business_travel',
            ],
            [
                'name' => 'Regionalzug',
                'name_en' => 'Regional train',
                'name_de' => 'Regionalzug',
                'source_id' => 'UBA_TRAIN_002',
                'factor_kg_co2e' => 0.057,
                'factor_kg_co2' => 0.055,
                'factor_kg_ch4' => 0.000005,
                'factor_kg_n2o' => 0.0000005,
                'unit' => 'passenger-km',
                'uncertainty_percent' => 15,
                'category' => 'business_travel',
            ],
            [
                'name' => 'S-Bahn',
                'name_en' => 'S-Bahn (suburban train)',
                'name_de' => 'S-Bahn',
                'source_id' => 'UBA_TRAIN_003',
                'factor_kg_co2e' => 0.056,
                'factor_kg_co2' => 0.054,
                'factor_kg_ch4' => 0.000005,
                'factor_kg_n2o' => 0.0000005,
                'unit' => 'passenger-km',
                'uncertainty_percent' => 15,
                'category' => 'employee_commuting',
            ],
            // PKW
            [
                'name' => 'PKW Benzin (Durchschnitt)',
                'name_en' => 'Car petrol (average)',
                'name_de' => 'PKW Benzin (Durchschnitt)',
                'source_id' => 'UBA_CAR_001',
                'factor_kg_co2e' => 0.214,
                'factor_kg_co2' => 0.210,
                'factor_kg_ch4' => 0.00022,
                'factor_kg_n2o' => 0.00006,
                'unit' => 'km',
                'uncertainty_percent' => 15,
                'category' => 'employee_commuting',
            ],
            [
                'name' => 'PKW Diesel (Durchschnitt)',
                'name_en' => 'Car diesel (average)',
                'name_de' => 'PKW Diesel (Durchschnitt)',
                'source_id' => 'UBA_CAR_002',
                'factor_kg_co2e' => 0.185,
                'factor_kg_co2' => 0.182,
                'factor_kg_ch4' => 0.00003,
                'factor_kg_n2o' => 0.00007,
                'unit' => 'km',
                'uncertainty_percent' => 15,
                'category' => 'employee_commuting',
            ],
            [
                'name' => 'Elektroauto - Deutschland',
                'name_en' => 'Electric car - Germany',
                'name_de' => 'Elektroauto - Deutschland',
                'source_id' => 'UBA_CAR_003',
                'factor_kg_co2e' => 0.073,
                'factor_kg_co2' => 0.070,
                'factor_kg_ch4' => 0.00003,
                'factor_kg_n2o' => 0.000007,
                'unit' => 'km',
                'uncertainty_percent' => 20,
                'category' => 'employee_commuting',
            ],
            [
                'name' => 'Plug-in Hybrid',
                'name_en' => 'Plug-in hybrid',
                'name_de' => 'Plug-in Hybrid',
                'source_id' => 'UBA_CAR_004',
                'factor_kg_co2e' => 0.119,
                'factor_kg_co2' => 0.116,
                'factor_kg_ch4' => 0.00012,
                'factor_kg_n2o' => 0.00003,
                'unit' => 'km',
                'uncertainty_percent' => 20,
                'category' => 'employee_commuting',
            ],
            // ÖPNV
            [
                'name' => 'U-Bahn',
                'name_en' => 'Underground/Metro',
                'name_de' => 'U-Bahn',
                'source_id' => 'UBA_METRO_001',
                'factor_kg_co2e' => 0.068,
                'factor_kg_co2' => 0.065,
                'factor_kg_ch4' => 0.000006,
                'factor_kg_n2o' => 0.0000006,
                'unit' => 'passenger-km',
                'uncertainty_percent' => 15,
                'category' => 'employee_commuting',
            ],
            [
                'name' => 'Straßenbahn',
                'name_en' => 'Tram',
                'name_de' => 'Straßenbahn',
                'source_id' => 'UBA_TRAM_001',
                'factor_kg_co2e' => 0.053,
                'factor_kg_co2' => 0.051,
                'factor_kg_ch4' => 0.000005,
                'factor_kg_n2o' => 0.0000005,
                'unit' => 'passenger-km',
                'uncertainty_percent' => 15,
                'category' => 'employee_commuting',
            ],
            [
                'name' => 'Linienbus',
                'name_en' => 'City bus',
                'name_de' => 'Linienbus',
                'source_id' => 'UBA_BUS_001',
                'factor_kg_co2e' => 0.075,
                'factor_kg_co2' => 0.072,
                'factor_kg_ch4' => 0.00002,
                'factor_kg_n2o' => 0.000007,
                'unit' => 'passenger-km',
                'uncertainty_percent' => 20,
                'category' => 'employee_commuting',
            ],
            [
                'name' => 'Reisebus (Fernbus)',
                'name_en' => 'Coach/Long-distance bus',
                'name_de' => 'Reisebus (Fernbus)',
                'source_id' => 'UBA_BUS_002',
                'factor_kg_co2e' => 0.029,
                'factor_kg_co2' => 0.028,
                'factor_kg_ch4' => 0.000008,
                'factor_kg_n2o' => 0.000003,
                'unit' => 'passenger-km',
                'uncertainty_percent' => 15,
                'category' => 'business_travel',
            ],
            // Taxi
            [
                'name' => 'Taxi',
                'name_en' => 'Taxi',
                'name_de' => 'Taxi',
                'source_id' => 'UBA_TAXI_001',
                'factor_kg_co2e' => 0.200,
                'factor_kg_co2' => 0.195,
                'factor_kg_ch4' => 0.0002,
                'factor_kg_n2o' => 0.00005,
                'unit' => 'km',
                'uncertainty_percent' => 20,
                'category' => 'business_travel',
            ],
            // Hotel
            [
                'name' => 'Hotelübernachtung (Standard)',
                'name_en' => 'Hotel night (standard)',
                'name_de' => 'Hotelübernachtung (Standard)',
                'source_id' => 'UBA_HOTEL_001',
                'factor_kg_co2e' => 32.0,
                'factor_kg_co2' => 30.5,
                'factor_kg_ch4' => 0.03,
                'factor_kg_n2o' => 0.007,
                'unit' => 'nights',
                'uncertainty_percent' => 30,
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
            // Dienstleistungen
            [
                'name' => 'IT-Dienstleistungen / Cloud',
                'name_en' => 'IT services / Cloud',
                'name_de' => 'IT-Dienstleistungen / Cloud',
                'source_id' => 'UBA_SPEND_001',
                'factor_kg_co2e' => 0.18,
                'unit' => 'EUR',
                'uncertainty_percent' => 40,
            ],
            [
                'name' => 'Beratungsdienstleistungen',
                'name_en' => 'Consulting services',
                'name_de' => 'Beratungsdienstleistungen',
                'source_id' => 'UBA_SPEND_002',
                'factor_kg_co2e' => 0.14,
                'unit' => 'EUR',
                'uncertainty_percent' => 40,
            ],
            [
                'name' => 'Finanzdienstleistungen',
                'name_en' => 'Financial services',
                'name_de' => 'Finanzdienstleistungen',
                'source_id' => 'UBA_SPEND_003',
                'factor_kg_co2e' => 0.09,
                'unit' => 'EUR',
                'uncertainty_percent' => 45,
            ],
            // Ausstattung
            [
                'name' => 'IT-Hardware',
                'name_en' => 'IT hardware',
                'name_de' => 'IT-Hardware',
                'source_id' => 'UBA_SPEND_004',
                'factor_kg_co2e' => 0.40,
                'unit' => 'EUR',
                'uncertainty_percent' => 35,
            ],
            [
                'name' => 'Büromöbel',
                'name_en' => 'Office furniture',
                'name_de' => 'Büromöbel',
                'source_id' => 'UBA_SPEND_005',
                'factor_kg_co2e' => 0.32,
                'unit' => 'EUR',
                'uncertainty_percent' => 40,
            ],
            [
                'name' => 'Bürobedarf',
                'name_en' => 'Office supplies',
                'name_de' => 'Bürobedarf',
                'source_id' => 'UBA_SPEND_006',
                'factor_kg_co2e' => 0.22,
                'unit' => 'EUR',
                'uncertainty_percent' => 40,
            ],
            // Verpflegung
            [
                'name' => 'Catering / Restaurant',
                'name_en' => 'Catering / Restaurant',
                'name_de' => 'Catering / Restaurant',
                'source_id' => 'UBA_SPEND_007',
                'factor_kg_co2e' => 0.48,
                'unit' => 'EUR',
                'uncertainty_percent' => 35,
            ],
            [
                'name' => 'Lebensmittel allgemein',
                'name_en' => 'General food products',
                'name_de' => 'Lebensmittel allgemein',
                'source_id' => 'UBA_SPEND_008',
                'factor_kg_co2e' => 0.55,
                'unit' => 'EUR',
                'uncertainty_percent' => 35,
            ],
            // Marketing
            [
                'name' => 'Marketing und Werbung',
                'name_en' => 'Marketing and advertising',
                'name_de' => 'Marketing und Werbung',
                'source_id' => 'UBA_SPEND_009',
                'factor_kg_co2e' => 0.20,
                'unit' => 'EUR',
                'uncertainty_percent' => 45,
            ],
            // Miete
            [
                'name' => 'Gewerbemiete',
                'name_en' => 'Commercial rent',
                'name_de' => 'Gewerbemiete',
                'source_id' => 'UBA_SPEND_010',
                'factor_kg_co2e' => 0.12,
                'unit' => 'EUR',
                'uncertainty_percent' => 50,
            ],
            // Durchschnitt
            [
                'name' => 'Durchschnittlicher Einkauf',
                'name_en' => 'Average purchase',
                'name_de' => 'Durchschnittlicher Einkauf',
                'source_id' => 'UBA_SPEND_DEFAULT',
                'factor_kg_co2e' => 0.28,
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
            // Abfall nach Behandlung
            [
                'name' => 'Hausmüll - Deponie',
                'name_en' => 'Household waste - Landfill',
                'name_de' => 'Hausmüll - Deponie',
                'source_id' => 'UBA_WASTE_001',
                'factor_kg_co2e' => 0.590,
                'factor_kg_co2' => 0.022,
                'factor_kg_ch4' => 0.0205,
                'unit' => 'kg',
                'uncertainty_percent' => 25,
            ],
            [
                'name' => 'Hausmüll - MVA',
                'name_en' => 'Household waste - Incineration',
                'name_de' => 'Hausmüll - MVA',
                'source_id' => 'UBA_WASTE_002',
                'factor_kg_co2e' => 0.320,
                'factor_kg_co2' => 0.315,
                'factor_kg_ch4' => 0.0002,
                'unit' => 'kg',
                'uncertainty_percent' => 20,
            ],
            [
                'name' => 'Altpapier - Recycling',
                'name_en' => 'Waste paper - Recycling',
                'name_de' => 'Altpapier - Recycling',
                'source_id' => 'UBA_WASTE_003',
                'factor_kg_co2e' => 0.021,
                'factor_kg_co2' => 0.020,
                'unit' => 'kg',
                'uncertainty_percent' => 30,
            ],
            [
                'name' => 'Kunststoff - Recycling',
                'name_en' => 'Plastic - Recycling',
                'name_de' => 'Kunststoff - Recycling',
                'source_id' => 'UBA_WASTE_004',
                'factor_kg_co2e' => 0.050,
                'factor_kg_co2' => 0.048,
                'unit' => 'kg',
                'uncertainty_percent' => 30,
            ],
            [
                'name' => 'Glas - Recycling',
                'name_en' => 'Glass - Recycling',
                'name_de' => 'Glas - Recycling',
                'source_id' => 'UBA_WASTE_005',
                'factor_kg_co2e' => 0.015,
                'factor_kg_co2' => 0.014,
                'unit' => 'kg',
                'uncertainty_percent' => 25,
            ],
            [
                'name' => 'Metall - Recycling',
                'name_en' => 'Metal - Recycling',
                'name_de' => 'Metall - Recycling',
                'source_id' => 'UBA_WASTE_006',
                'factor_kg_co2e' => 0.035,
                'factor_kg_co2' => 0.033,
                'unit' => 'kg',
                'uncertainty_percent' => 30,
            ],
            [
                'name' => 'Elektroschrott',
                'name_en' => 'E-waste',
                'name_de' => 'Elektroschrott',
                'source_id' => 'UBA_WASTE_007',
                'factor_kg_co2e' => 0.110,
                'factor_kg_co2' => 0.105,
                'unit' => 'kg',
                'uncertainty_percent' => 35,
            ],
            [
                'name' => 'Bioabfall - Kompostierung',
                'name_en' => 'Organic waste - Composting',
                'name_de' => 'Bioabfall - Kompostierung',
                'source_id' => 'UBA_WASTE_008',
                'factor_kg_co2e' => 0.027,
                'factor_kg_co2' => 0.009,
                'factor_kg_ch4' => 0.0007,
                'unit' => 'kg',
                'uncertainty_percent' => 35,
            ],
            [
                'name' => 'Bioabfall - Biogasanlage',
                'name_en' => 'Organic waste - Biogas plant',
                'name_de' => 'Bioabfall - Biogasanlage',
                'source_id' => 'UBA_WASTE_009',
                'factor_kg_co2e' => -0.050,
                'factor_kg_co2' => -0.048,
                'unit' => 'kg',
                'uncertainty_percent' => 40,
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
            // Straßengüterverkehr
            [
                'name' => 'Sattelzug >32t',
                'name_en' => 'Articulated truck >32t',
                'name_de' => 'Sattelzug >32t',
                'source_id' => 'UBA_FREIGHT_001',
                'factor_kg_co2e' => 0.062,
                'factor_kg_co2' => 0.061,
                'unit' => 'tonne-km',
                'uncertainty_percent' => 15,
            ],
            [
                'name' => 'LKW 12-24t',
                'name_en' => 'Truck 12-24t',
                'name_de' => 'LKW 12-24t',
                'source_id' => 'UBA_FREIGHT_002',
                'factor_kg_co2e' => 0.119,
                'factor_kg_co2' => 0.117,
                'unit' => 'tonne-km',
                'uncertainty_percent' => 15,
            ],
            [
                'name' => 'Transporter <3.5t',
                'name_en' => 'Van <3.5t',
                'name_de' => 'Transporter <3.5t',
                'source_id' => 'UBA_FREIGHT_003',
                'factor_kg_co2e' => 0.428,
                'factor_kg_co2' => 0.422,
                'unit' => 'tonne-km',
                'uncertainty_percent' => 20,
            ],
            // Schienengüterverkehr
            [
                'name' => 'Schienengüterverkehr',
                'name_en' => 'Rail freight',
                'name_de' => 'Schienengüterverkehr',
                'source_id' => 'UBA_FREIGHT_004',
                'factor_kg_co2e' => 0.018,
                'factor_kg_co2' => 0.017,
                'unit' => 'tonne-km',
                'uncertainty_percent' => 15,
            ],
            // Binnenschifffahrt
            [
                'name' => 'Binnenschiff',
                'name_en' => 'Inland waterway ship',
                'name_de' => 'Binnenschiff',
                'source_id' => 'UBA_FREIGHT_005',
                'factor_kg_co2e' => 0.033,
                'factor_kg_co2' => 0.032,
                'unit' => 'tonne-km',
                'uncertainty_percent' => 20,
            ],
            // Seeschifffahrt
            [
                'name' => 'Containerschiff (Durchschnitt)',
                'name_en' => 'Container ship (average)',
                'name_de' => 'Containerschiff (Durchschnitt)',
                'source_id' => 'UBA_FREIGHT_006',
                'factor_kg_co2e' => 0.016,
                'factor_kg_co2' => 0.016,
                'unit' => 'tonne-km',
                'uncertainty_percent' => 20,
            ],
            // Luftfracht
            [
                'name' => 'Luftfracht Kurzstrecke',
                'name_en' => 'Air freight short-haul',
                'name_de' => 'Luftfracht Kurzstrecke',
                'source_id' => 'UBA_FREIGHT_007',
                'factor_kg_co2e' => 2.10,
                'factor_kg_co2' => 2.05,
                'unit' => 'tonne-km',
                'uncertainty_percent' => 25,
            ],
            [
                'name' => 'Luftfracht Langstrecke',
                'name_en' => 'Air freight long-haul',
                'name_de' => 'Luftfracht Langstrecke',
                'source_id' => 'UBA_FREIGHT_008',
                'factor_kg_co2e' => 0.67,
                'factor_kg_co2' => 0.65,
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
                'source' => 'uba',
                'source_id' => $data['source_id'],
            ],
            [
                'id' => Str::uuid()->toString(),
                'category_id' => $categoryId,
                'scope' => $scope,
                'country' => 'DE',
                'name' => $data['name'],
                'name_en' => $data['name_en'] ?? $data['name'],
                'name_de' => $data['name_de'] ?? $data['name'],
                'factor_kg_co2e' => $data['factor_kg_co2e'],
                'factor_kg_co2' => $data['factor_kg_co2'] ?? null,
                'factor_kg_ch4' => $data['factor_kg_ch4'] ?? null,
                'factor_kg_n2o' => $data['factor_kg_n2o'] ?? null,
                'unit' => $data['unit'],
                'uncertainty_percent' => $data['uncertainty_percent'] ?? null,
                'methodology' => $data['methodology'] ?? null,
                'source' => 'uba',
                'source_url' => 'https://www.umweltbundesamt.de/',
                'source_id' => $data['source_id'],
                'valid_from' => now()->startOfYear(),
                'valid_until' => now()->addYears(2)->endOfYear(),
                'is_active' => true,
            ]
        );
    }
}
