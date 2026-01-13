<?php

namespace Database\Seeders;

use App\Models\Assessment;
use App\Models\Category;
use App\Models\EmissionFactor;
use App\Models\EmissionRecord;
use App\Models\Organization;
use App\Models\Site;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * EmissionRecordSeeder - Creates realistic emission data for a German SME
 *
 * Scenario: Tech company "Test Company" based in Germany
 * - 50 employees
 * - 1 office building (~800m²)
 * - Small vehicle fleet (5 company cars)
 * - Typical office operations
 *
 * Data based on UBA (Umweltbundesamt) emission factors
 * Reference year: Current year assessment
 */
class EmissionRecordSeeder extends Seeder
{
    private Organization $organization;
    private Assessment $assessment;
    private ?Site $site = null;
    private int $year;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding realistic emission records for German SME...');

        // Get test organization
        $this->organization = Organization::where('slug', 'test-company')->first();

        if (! $this->organization) {
            $this->command->error('Test organization not found. Run TestDataSeeder first.');
            return;
        }

        // Get or create site
        $this->site = Site::where('organization_id', $this->organization->id)->first();
        if (! $this->site) {
            $this->site = new Site([
                'organization_id' => $this->organization->id,
                'name' => 'Hauptsitz Berlin',
                'address_line_1' => 'Friedrichstraße 123',
                'city' => 'Berlin',
                'postal_code' => '10117',
                'country' => 'DE',
                'floor_area_m2' => 800,
                'employee_count' => 50,
                'is_primary' => true,
                'is_active' => true,
            ]);
            $this->site->id = Str::uuid()->toString();
            $this->site->save();
            $this->command->info('  Created site: Hauptsitz Berlin');
        }

        // Get active assessment (current year)
        $this->year = (int) date('Y');
        $this->assessment = Assessment::where('organization_id', $this->organization->id)
            ->where('year', $this->year)
            ->first();

        if (! $this->assessment) {
            $this->command->error("No assessment found for year {$this->year}. Run AssessmentSeeder first.");
            return;
        }

        // Clear existing emission records for this assessment
        EmissionRecord::where('assessment_id', $this->assessment->id)->delete();
        $this->command->info("  Cleared existing records for assessment {$this->year}");

        // Seed emissions by scope
        $this->seedScope1Emissions();
        $this->seedScope2Emissions();
        $this->seedScope3Emissions();

        // Summary
        $totalRecords = EmissionRecord::where('assessment_id', $this->assessment->id)->count();
        $totalEmissions = EmissionRecord::where('assessment_id', $this->assessment->id)->sum('co2e_kg');

        $this->command->newLine();
        $this->command->info('=================================');
        $this->command->info('Emission records seeding complete!');
        $this->command->info('=================================');
        $this->command->info("  Total records: {$totalRecords}");
        $this->command->info('  Total emissions: ' . number_format($totalEmissions / 1000, 2) . ' tCO2e');
        $this->command->newLine();
    }

    /**
     * Seed Scope 1: Direct emissions (vehicles, heating fuels)
     */
    private function seedScope1Emissions(): void
    {
        $this->command->info('  Seeding Scope 1 (Direct emissions)...');
        $count = 0;

        // 1.1 - Stationary combustion (heating)
        // Natural gas heating: ~50,000 kWh/year for 800m² office
        $gasFactorKwh = $this->findFactor('UBA_FUEL_006'); // Erdgas H (kWh)
        if ($gasFactorKwh) {
            $this->createEmissionRecord([
                'scope' => 1,
                'ghg_category' => '1.1',
                'emission_factor_id' => $gasFactorKwh->id,
                'quantity' => 52000, // kWh - slightly more for German climate
                'unit' => 'kWh',
                'factor_value' => $gasFactorKwh->factor_kg_co2e,
                'factor_unit' => 'kg CO2e/kWh',
                'factor_source' => 'UBA',
                'notes' => 'Erdgasverbrauch Büroheizung - Jahresverbrauch',
                'source_type' => 'manual',
                'data_quality' => 'measured',
            ]);
            $count++;
        }

        // 1.2 - Mobile combustion (company vehicles)
        // Fleet of 5 cars: 3 diesel, 2 petrol
        // Average 18,000 km/year per vehicle

        // Diesel vehicles (3 cars × 18,000 km × 6L/100km = 3,240L each)
        $dieselFactor = $this->findFactor('UBA_FUEL_002');
        if ($dieselFactor) {
            // Car 1: Diesel Kombi (Sales team)
            $this->createEmissionRecord([
                'scope' => 1,
                'ghg_category' => '1.2',
                'emission_factor_id' => $dieselFactor->id,
                'quantity' => 1296, // 21,600 km × 6L/100km
                'unit' => 'liters',
                'factor_value' => $dieselFactor->factor_kg_co2e,
                'factor_unit' => 'kg CO2e/L',
                'factor_source' => 'UBA',
                'notes' => 'Firmenwagen 1 (Diesel Kombi) - Vertriebsteam',
                'source_type' => 'manual',
                'data_quality' => 'measured',
            ]);
            $count++;

            // Car 2: Diesel SUV (Management)
            $this->createEmissionRecord([
                'scope' => 1,
                'ghg_category' => '1.2',
                'emission_factor_id' => $dieselFactor->id,
                'quantity' => 1440, // 18,000 km × 8L/100km
                'unit' => 'liters',
                'factor_value' => $dieselFactor->factor_kg_co2e,
                'factor_unit' => 'kg CO2e/L',
                'factor_source' => 'UBA',
                'notes' => 'Firmenwagen 2 (Diesel SUV) - Geschäftsführung',
                'source_type' => 'manual',
                'data_quality' => 'measured',
            ]);
            $count++;

            // Car 3: Diesel compact (Tech support)
            $this->createEmissionRecord([
                'scope' => 1,
                'ghg_category' => '1.2',
                'emission_factor_id' => $dieselFactor->id,
                'quantity' => 900, // 15,000 km × 6L/100km
                'unit' => 'liters',
                'factor_value' => $dieselFactor->factor_kg_co2e,
                'factor_unit' => 'kg CO2e/L',
                'factor_source' => 'UBA',
                'notes' => 'Firmenwagen 3 (Diesel Kompakt) - Technischer Support',
                'source_type' => 'manual',
                'data_quality' => 'measured',
            ]);
            $count++;
        }

        // Petrol vehicles (2 cars)
        $petrolFactor = $this->findFactor('UBA_FUEL_001');
        if ($petrolFactor) {
            // Car 4: Petrol compact (HR/Admin)
            $this->createEmissionRecord([
                'scope' => 1,
                'ghg_category' => '1.2',
                'emission_factor_id' => $petrolFactor->id,
                'quantity' => 720, // 12,000 km × 6L/100km
                'unit' => 'liters',
                'factor_value' => $petrolFactor->factor_kg_co2e,
                'factor_unit' => 'kg CO2e/L',
                'factor_source' => 'UBA',
                'notes' => 'Firmenwagen 4 (Benzin Kompakt) - Personal/Verwaltung',
                'source_type' => 'manual',
                'data_quality' => 'measured',
            ]);
            $count++;

            // Car 5: Hybrid (Marketing)
            $this->createEmissionRecord([
                'scope' => 1,
                'ghg_category' => '1.2',
                'emission_factor_id' => $petrolFactor->id,
                'quantity' => 480, // 12,000 km × 4L/100km (hybrid efficiency)
                'unit' => 'liters',
                'factor_value' => $petrolFactor->factor_kg_co2e,
                'factor_unit' => 'kg CO2e/L',
                'factor_source' => 'UBA',
                'notes' => 'Firmenwagen 5 (Plug-in Hybrid) - Marketing',
                'source_type' => 'manual',
                'data_quality' => 'measured',
            ]);
            $count++;
        }

        $this->command->info("    - Created {$count} Scope 1 records");
    }

    /**
     * Seed Scope 2: Indirect emissions (purchased electricity, heat)
     */
    private function seedScope2Emissions(): void
    {
        $this->command->info('  Seeding Scope 2 (Indirect energy emissions)...');
        $count = 0;

        // 2.1 - Purchased electricity
        // Tech company: ~100 kWh/m²/year + IT infrastructure
        // 800m² × 100 kWh = 80,000 kWh base + 25,000 kWh IT = 105,000 kWh

        // Location-based method (German grid mix)
        $gridMixFactor = $this->findFactor('UBA_ELEC_001');
        if ($gridMixFactor) {
            $this->createEmissionRecord([
                'scope' => 2,
                'ghg_category' => '2.1',
                'emission_factor_id' => $gridMixFactor->id,
                'quantity' => 105000, // kWh total
                'unit' => 'kWh',
                'factor_value' => $gridMixFactor->factor_kg_co2e,
                'factor_unit' => 'kg CO2e/kWh',
                'factor_source' => 'UBA',
                'calculation_method' => 'location-based',
                'notes' => 'Stromverbrauch Büro + IT-Infrastruktur (Location-based)',
                'source_type' => 'manual',
                'data_quality' => 'measured',
            ]);
            $count++;
        }

        // Also add 30% green electricity (certified)
        $greenFactor = $this->findFactor('UBA_ELEC_003');
        if ($greenFactor) {
            $this->createEmissionRecord([
                'scope' => 2,
                'ghg_category' => '2.1',
                'emission_factor_id' => $greenFactor->id,
                'quantity' => 31500, // 30% of total as certified green
                'unit' => 'kWh',
                'factor_value' => $greenFactor->factor_kg_co2e,
                'factor_unit' => 'kg CO2e/kWh',
                'factor_source' => 'UBA',
                'calculation_method' => 'market-based',
                'notes' => 'Ökostrom-Anteil (30%) - zertifiziert',
                'source_type' => 'manual',
                'data_quality' => 'measured',
            ]);
            $count++;
        }

        // District heating (Fernwärme) - 20,000 kWh/year
        $heatFactor = $this->findFactor('UBA_HEAT_001');
        if ($heatFactor) {
            $this->createEmissionRecord([
                'scope' => 2,
                'ghg_category' => '2.1',
                'emission_factor_id' => $heatFactor->id,
                'quantity' => 18000,
                'unit' => 'kWh',
                'factor_value' => $heatFactor->factor_kg_co2e,
                'factor_unit' => 'kg CO2e/kWh',
                'factor_source' => 'UBA',
                'notes' => 'Fernwärme für Warmwasser und Zusatzheizung',
                'source_type' => 'manual',
                'data_quality' => 'measured',
            ]);
            $count++;
        }

        $this->command->info("    - Created {$count} Scope 2 records");
    }

    /**
     * Seed Scope 3: Other indirect emissions
     */
    private function seedScope3Emissions(): void
    {
        $this->command->info('  Seeding Scope 3 (Other indirect emissions)...');
        $count = 0;

        // 3.1 - Purchased goods and services
        $count += $this->seedPurchasedGoods();

        // 3.3 - Fuel and energy-related activities (not in Scope 1 or 2)
        // Upstream emissions are typically ~10-15% of Scope 1+2
        // We'll add this implicitly through purchased goods

        // 3.4 - Upstream transportation
        $count += $this->seedUpstreamTransport();

        // 3.5 - Waste generated in operations
        $count += $this->seedWaste();

        // 3.6 - Business travel
        $count += $this->seedBusinessTravel();

        // 3.7 - Employee commuting
        $count += $this->seedEmployeeCommuting();

        $this->command->info("    - Created {$count} Scope 3 records total");
    }

    /**
     * Seed 3.1 - Purchased goods and services
     */
    private function seedPurchasedGoods(): int
    {
        $count = 0;

        // IT Services / Cloud - €48,000/year
        $itServicesFactor = $this->findFactor('UBA_SPEND_001');
        if ($itServicesFactor) {
            $this->createEmissionRecord([
                'scope' => 3,
                'ghg_category' => '3.1',
                'scope_3_category' => 1,
                'emission_factor_id' => $itServicesFactor->id,
                'quantity' => 48000,
                'unit' => 'EUR',
                'factor_value' => $itServicesFactor->factor_kg_co2e,
                'factor_unit' => 'kg CO2e/EUR',
                'factor_source' => 'UBA',
                'notes' => 'Cloud-Services (AWS, Azure), SaaS-Abonnements',
                'source_type' => 'manual',
                'data_quality' => 'financial',
            ]);
            $count++;
        }

        // IT Hardware - €35,000/year (laptops, monitors, servers)
        $itHardwareFactor = $this->findFactor('UBA_SPEND_004');
        if ($itHardwareFactor) {
            $this->createEmissionRecord([
                'scope' => 3,
                'ghg_category' => '3.1',
                'scope_3_category' => 1,
                'emission_factor_id' => $itHardwareFactor->id,
                'quantity' => 35000,
                'unit' => 'EUR',
                'factor_value' => $itHardwareFactor->factor_kg_co2e,
                'factor_unit' => 'kg CO2e/EUR',
                'factor_source' => 'UBA',
                'notes' => 'IT-Hardware: Laptops, Monitore, Server-Komponenten',
                'source_type' => 'manual',
                'data_quality' => 'financial',
            ]);
            $count++;
        }

        // Consulting services - €25,000/year
        $consultingFactor = $this->findFactor('UBA_SPEND_002');
        if ($consultingFactor) {
            $this->createEmissionRecord([
                'scope' => 3,
                'ghg_category' => '3.1',
                'scope_3_category' => 1,
                'emission_factor_id' => $consultingFactor->id,
                'quantity' => 25000,
                'unit' => 'EUR',
                'factor_value' => $consultingFactor->factor_kg_co2e,
                'factor_unit' => 'kg CO2e/EUR',
                'factor_source' => 'UBA',
                'notes' => 'Externe Beratung: Rechts-, Steuer-, Managementberatung',
                'source_type' => 'manual',
                'data_quality' => 'financial',
            ]);
            $count++;
        }

        // Office supplies - €8,000/year
        $officeSuppliesFactor = $this->findFactor('UBA_SPEND_006');
        if ($officeSuppliesFactor) {
            $this->createEmissionRecord([
                'scope' => 3,
                'ghg_category' => '3.1',
                'scope_3_category' => 1,
                'emission_factor_id' => $officeSuppliesFactor->id,
                'quantity' => 8000,
                'unit' => 'EUR',
                'factor_value' => $officeSuppliesFactor->factor_kg_co2e,
                'factor_unit' => 'kg CO2e/EUR',
                'factor_source' => 'UBA',
                'notes' => 'Bürobedarf: Papier, Schreibwaren, Kleinmaterial',
                'source_type' => 'manual',
                'data_quality' => 'financial',
            ]);
            $count++;
        }

        // Office furniture - €12,000/year
        $furnitureFactor = $this->findFactor('UBA_SPEND_005');
        if ($furnitureFactor) {
            $this->createEmissionRecord([
                'scope' => 3,
                'ghg_category' => '3.1',
                'scope_3_category' => 1,
                'emission_factor_id' => $furnitureFactor->id,
                'quantity' => 12000,
                'unit' => 'EUR',
                'factor_value' => $furnitureFactor->factor_kg_co2e,
                'factor_unit' => 'kg CO2e/EUR',
                'factor_source' => 'UBA',
                'notes' => 'Büromöbel: Schreibtische, Stühle, Regale',
                'source_type' => 'manual',
                'data_quality' => 'financial',
            ]);
            $count++;
        }

        // Catering/Food - €24,000/year (50 employees × €40/month)
        $cateringFactor = $this->findFactor('UBA_SPEND_007');
        if ($cateringFactor) {
            $this->createEmissionRecord([
                'scope' => 3,
                'ghg_category' => '3.1',
                'scope_3_category' => 1,
                'emission_factor_id' => $cateringFactor->id,
                'quantity' => 24000,
                'unit' => 'EUR',
                'factor_value' => $cateringFactor->factor_kg_co2e,
                'factor_unit' => 'kg CO2e/EUR',
                'factor_source' => 'UBA',
                'notes' => 'Kantine, Catering bei Events, Getränke',
                'source_type' => 'manual',
                'data_quality' => 'financial',
            ]);
            $count++;
        }

        // Marketing - €18,000/year
        $marketingFactor = $this->findFactor('UBA_SPEND_009');
        if ($marketingFactor) {
            $this->createEmissionRecord([
                'scope' => 3,
                'ghg_category' => '3.1',
                'scope_3_category' => 1,
                'emission_factor_id' => $marketingFactor->id,
                'quantity' => 18000,
                'unit' => 'EUR',
                'factor_value' => $marketingFactor->factor_kg_co2e,
                'factor_unit' => 'kg CO2e/EUR',
                'factor_source' => 'UBA',
                'notes' => 'Marketing: Werbematerial, Online-Werbung, Events',
                'source_type' => 'manual',
                'data_quality' => 'financial',
            ]);
            $count++;
        }

        // Commercial rent - €96,000/year (800m² × €10/m²/month)
        $rentFactor = $this->findFactor('UBA_SPEND_010');
        if ($rentFactor) {
            $this->createEmissionRecord([
                'scope' => 3,
                'ghg_category' => '3.1',
                'scope_3_category' => 1,
                'emission_factor_id' => $rentFactor->id,
                'quantity' => 96000,
                'unit' => 'EUR',
                'factor_value' => $rentFactor->factor_kg_co2e,
                'factor_unit' => 'kg CO2e/EUR',
                'factor_source' => 'UBA',
                'notes' => 'Gewerbemiete Bürogebäude Berlin',
                'source_type' => 'manual',
                'data_quality' => 'financial',
            ]);
            $count++;
        }

        // Financial services - €15,000/year
        $financeFactor = $this->findFactor('UBA_SPEND_003');
        if ($financeFactor) {
            $this->createEmissionRecord([
                'scope' => 3,
                'ghg_category' => '3.1',
                'scope_3_category' => 1,
                'emission_factor_id' => $financeFactor->id,
                'quantity' => 15000,
                'unit' => 'EUR',
                'factor_value' => $financeFactor->factor_kg_co2e,
                'factor_unit' => 'kg CO2e/EUR',
                'factor_source' => 'UBA',
                'notes' => 'Bankgebühren, Versicherungen, Finanzdienstleistungen',
                'source_type' => 'manual',
                'data_quality' => 'financial',
            ]);
            $count++;
        }

        return $count;
    }

    /**
     * Seed 3.4 - Upstream transportation (deliveries to company)
     */
    private function seedUpstreamTransport(): int
    {
        $count = 0;

        // Van deliveries - ~500 deliveries/year × 20 km average
        $vanFactor = $this->findFactor('UBA_FREIGHT_003');
        if ($vanFactor) {
            // Assume 50 kg average per delivery
            $this->createEmissionRecord([
                'scope' => 3,
                'ghg_category' => '3.4',
                'scope_3_category' => 4,
                'emission_factor_id' => $vanFactor->id,
                'quantity' => 500, // 500 deliveries × 50kg × 20km = 500 tkm
                'unit' => 'tonne-km',
                'factor_value' => $vanFactor->factor_kg_co2e,
                'factor_unit' => 'kg CO2e/tkm',
                'factor_source' => 'UBA',
                'notes' => 'Paketlieferungen und Kurierdienste',
                'source_type' => 'manual',
                'data_quality' => 'estimated',
            ]);
            $count++;
        }

        return $count;
    }

    /**
     * Seed 3.5 - Waste generated in operations
     */
    private function seedWaste(): int
    {
        $count = 0;

        // Paper recycling - 2,000 kg/year
        $paperFactor = $this->findFactor('UBA_WASTE_003');
        if ($paperFactor) {
            $this->createEmissionRecord([
                'scope' => 3,
                'ghg_category' => '3.5',
                'scope_3_category' => 5,
                'emission_factor_id' => $paperFactor->id,
                'quantity' => 2000,
                'unit' => 'kg',
                'factor_value' => $paperFactor->factor_kg_co2e,
                'factor_unit' => 'kg CO2e/kg',
                'factor_source' => 'UBA',
                'notes' => 'Altpapier-Recycling (Dokumente, Verpackungen)',
                'source_type' => 'manual',
                'data_quality' => 'measured',
            ]);
            $count++;
        }

        // Plastic recycling - 300 kg/year
        $plasticFactor = $this->findFactor('UBA_WASTE_004');
        if ($plasticFactor) {
            $this->createEmissionRecord([
                'scope' => 3,
                'ghg_category' => '3.5',
                'scope_3_category' => 5,
                'emission_factor_id' => $plasticFactor->id,
                'quantity' => 300,
                'unit' => 'kg',
                'factor_value' => $plasticFactor->factor_kg_co2e,
                'factor_unit' => 'kg CO2e/kg',
                'factor_source' => 'UBA',
                'notes' => 'Kunststoff-Recycling (Verpackungen, Folien)',
                'source_type' => 'manual',
                'data_quality' => 'estimated',
            ]);
            $count++;
        }

        // General waste (incineration) - 1,500 kg/year
        $wasteFactor = $this->findFactor('UBA_WASTE_002');
        if ($wasteFactor) {
            $this->createEmissionRecord([
                'scope' => 3,
                'ghg_category' => '3.5',
                'scope_3_category' => 5,
                'emission_factor_id' => $wasteFactor->id,
                'quantity' => 1500,
                'unit' => 'kg',
                'factor_value' => $wasteFactor->factor_kg_co2e,
                'factor_unit' => 'kg CO2e/kg',
                'factor_source' => 'UBA',
                'notes' => 'Restmüll - thermische Verwertung',
                'source_type' => 'manual',
                'data_quality' => 'estimated',
            ]);
            $count++;
        }

        // E-waste - 150 kg/year
        $ewasteFactor = $this->findFactor('UBA_WASTE_007');
        if ($ewasteFactor) {
            $this->createEmissionRecord([
                'scope' => 3,
                'ghg_category' => '3.5',
                'scope_3_category' => 5,
                'emission_factor_id' => $ewasteFactor->id,
                'quantity' => 150,
                'unit' => 'kg',
                'factor_value' => $ewasteFactor->factor_kg_co2e,
                'factor_unit' => 'kg CO2e/kg',
                'factor_source' => 'UBA',
                'notes' => 'Elektroschrott (alte IT-Geräte, Kabel)',
                'source_type' => 'manual',
                'data_quality' => 'measured',
            ]);
            $count++;
        }

        // Organic waste (composting) - 800 kg/year (from canteen)
        $bioFactor = $this->findFactor('UBA_WASTE_008');
        if ($bioFactor) {
            $this->createEmissionRecord([
                'scope' => 3,
                'ghg_category' => '3.5',
                'scope_3_category' => 5,
                'emission_factor_id' => $bioFactor->id,
                'quantity' => 800,
                'unit' => 'kg',
                'factor_value' => $bioFactor->factor_kg_co2e,
                'factor_unit' => 'kg CO2e/kg',
                'factor_source' => 'UBA',
                'notes' => 'Bioabfall aus Kantine/Küche',
                'source_type' => 'manual',
                'data_quality' => 'estimated',
            ]);
            $count++;
        }

        return $count;
    }

    /**
     * Seed 3.6 - Business travel
     */
    private function seedBusinessTravel(): int
    {
        $count = 0;

        // Short-haul flights - 15 trips × 1,200 km average (within Europe)
        $shortFlightFactor = $this->findFactor('UBA_FLIGHT_001');
        if ($shortFlightFactor) {
            $this->createEmissionRecord([
                'scope' => 3,
                'ghg_category' => '3.6',
                'scope_3_category' => 6,
                'emission_factor_id' => $shortFlightFactor->id,
                'quantity' => 18000, // 15 trips × 1,200 km
                'unit' => 'passenger-km',
                'factor_value' => $shortFlightFactor->factor_kg_co2e,
                'factor_unit' => 'kg CO2e/pkm',
                'factor_source' => 'UBA',
                'notes' => 'Kurzstreckenflüge Europa (Kundentermine, Konferenzen)',
                'source_type' => 'manual',
                'data_quality' => 'measured',
            ]);
            $count++;
        }

        // Medium-haul flights - 5 trips × 2,500 km average
        $mediumFlightFactor = $this->findFactor('UBA_FLIGHT_002');
        if ($mediumFlightFactor) {
            $this->createEmissionRecord([
                'scope' => 3,
                'ghg_category' => '3.6',
                'scope_3_category' => 6,
                'emission_factor_id' => $mediumFlightFactor->id,
                'quantity' => 12500, // 5 trips × 2,500 km
                'unit' => 'passenger-km',
                'factor_value' => $mediumFlightFactor->factor_kg_co2e,
                'factor_unit' => 'kg CO2e/pkm',
                'factor_source' => 'UBA',
                'notes' => 'Mittelstreckenflüge (Konferenzen, Partner-Meetings)',
                'source_type' => 'manual',
                'data_quality' => 'measured',
            ]);
            $count++;
        }

        // Long-distance train (ICE) - 80 trips × 400 km average
        $iceFactor = $this->findFactor('UBA_TRAIN_001');
        if ($iceFactor) {
            $this->createEmissionRecord([
                'scope' => 3,
                'ghg_category' => '3.6',
                'scope_3_category' => 6,
                'emission_factor_id' => $iceFactor->id,
                'quantity' => 32000, // 80 trips × 400 km
                'unit' => 'passenger-km',
                'factor_value' => $iceFactor->factor_kg_co2e,
                'factor_unit' => 'kg CO2e/pkm',
                'factor_source' => 'UBA',
                'notes' => 'ICE-Fernreisen (Kundenbesuche, interne Meetings)',
                'source_type' => 'manual',
                'data_quality' => 'measured',
            ]);
            $count++;
        }

        // Taxi - 200 trips × 15 km average
        $taxiFactor = $this->findFactor('UBA_TAXI_001');
        if ($taxiFactor) {
            $this->createEmissionRecord([
                'scope' => 3,
                'ghg_category' => '3.6',
                'scope_3_category' => 6,
                'emission_factor_id' => $taxiFactor->id,
                'quantity' => 3000, // 200 trips × 15 km
                'unit' => 'km',
                'factor_value' => $taxiFactor->factor_kg_co2e,
                'factor_unit' => 'kg CO2e/km',
                'factor_source' => 'UBA',
                'notes' => 'Taxi-Fahrten (Flughafen, späte Termine)',
                'source_type' => 'manual',
                'data_quality' => 'financial',
            ]);
            $count++;
        }

        // Hotel nights - 120 nights/year
        $hotelFactor = $this->findFactor('UBA_HOTEL_001');
        if ($hotelFactor) {
            $this->createEmissionRecord([
                'scope' => 3,
                'ghg_category' => '3.6',
                'scope_3_category' => 6,
                'emission_factor_id' => $hotelFactor->id,
                'quantity' => 120,
                'unit' => 'nights',
                'factor_value' => $hotelFactor->factor_kg_co2e,
                'factor_unit' => 'kg CO2e/Nacht',
                'factor_source' => 'UBA',
                'notes' => 'Hotelübernachtungen bei Dienstreisen',
                'source_type' => 'manual',
                'data_quality' => 'financial',
            ]);
            $count++;
        }

        // Long-distance bus - 10 trips × 350 km
        $busFactor = $this->findFactor('UBA_BUS_002');
        if ($busFactor) {
            $this->createEmissionRecord([
                'scope' => 3,
                'ghg_category' => '3.6',
                'scope_3_category' => 6,
                'emission_factor_id' => $busFactor->id,
                'quantity' => 3500,
                'unit' => 'passenger-km',
                'factor_value' => $busFactor->factor_kg_co2e,
                'factor_unit' => 'kg CO2e/pkm',
                'factor_source' => 'UBA',
                'notes' => 'Fernbus-Reisen (Budget-Reisen)',
                'source_type' => 'manual',
                'data_quality' => 'measured',
            ]);
            $count++;
        }

        return $count;
    }

    /**
     * Seed 3.7 - Employee commuting
     * 50 employees with various transport modes
     */
    private function seedEmployeeCommuting(): int
    {
        $count = 0;

        // Distribution for Berlin tech company:
        // - 15 employees by public transport (S-Bahn/U-Bahn)
        // - 12 employees by bicycle (no emissions)
        // - 10 employees by car (petrol)
        // - 8 employees by car (diesel)
        // - 3 employees by electric car
        // - 2 employees remote (no commute)

        // Average commute: 12 km one-way, 220 working days/year

        // S-Bahn commuters - 15 employees × 12 km × 2 × 220 days = 79,200 pkm
        $sbahnFactor = $this->findFactor('UBA_TRAIN_003');
        if ($sbahnFactor) {
            $this->createEmissionRecord([
                'scope' => 3,
                'ghg_category' => '3.7',
                'scope_3_category' => 7,
                'emission_factor_id' => $sbahnFactor->id,
                'quantity' => 79200,
                'unit' => 'passenger-km',
                'factor_value' => $sbahnFactor->factor_kg_co2e,
                'factor_unit' => 'kg CO2e/pkm',
                'factor_source' => 'UBA',
                'notes' => 'Pendeln mit S-Bahn (15 Mitarbeiter)',
                'source_type' => 'manual',
                'data_quality' => 'survey',
            ]);
            $count++;
        }

        // U-Bahn commuters (part of public transport)
        $ubahnFactor = $this->findFactor('UBA_METRO_001');
        if ($ubahnFactor) {
            $this->createEmissionRecord([
                'scope' => 3,
                'ghg_category' => '3.7',
                'scope_3_category' => 7,
                'emission_factor_id' => $ubahnFactor->id,
                'quantity' => 26400, // 5 employees × 12 km × 2 × 220
                'unit' => 'passenger-km',
                'factor_value' => $ubahnFactor->factor_kg_co2e,
                'factor_unit' => 'kg CO2e/pkm',
                'factor_source' => 'UBA',
                'notes' => 'Pendeln mit U-Bahn (5 Mitarbeiter)',
                'source_type' => 'manual',
                'data_quality' => 'survey',
            ]);
            $count++;
        }

        // Petrol car commuters - 10 employees × 15 km × 2 × 220 days = 66,000 km
        $carPetrolFactor = $this->findFactor('UBA_CAR_001');
        if ($carPetrolFactor) {
            $this->createEmissionRecord([
                'scope' => 3,
                'ghg_category' => '3.7',
                'scope_3_category' => 7,
                'emission_factor_id' => $carPetrolFactor->id,
                'quantity' => 66000,
                'unit' => 'km',
                'factor_value' => $carPetrolFactor->factor_kg_co2e,
                'factor_unit' => 'kg CO2e/km',
                'factor_source' => 'UBA',
                'notes' => 'Pendeln mit Benzin-PKW (10 Mitarbeiter)',
                'source_type' => 'manual',
                'data_quality' => 'survey',
            ]);
            $count++;
        }

        // Diesel car commuters - 8 employees × 18 km × 2 × 220 = 63,360 km
        $carDieselFactor = $this->findFactor('UBA_CAR_002');
        if ($carDieselFactor) {
            $this->createEmissionRecord([
                'scope' => 3,
                'ghg_category' => '3.7',
                'scope_3_category' => 7,
                'emission_factor_id' => $carDieselFactor->id,
                'quantity' => 63360,
                'unit' => 'km',
                'factor_value' => $carDieselFactor->factor_kg_co2e,
                'factor_unit' => 'kg CO2e/km',
                'factor_source' => 'UBA',
                'notes' => 'Pendeln mit Diesel-PKW (8 Mitarbeiter)',
                'source_type' => 'manual',
                'data_quality' => 'survey',
            ]);
            $count++;
        }

        // Electric car commuters - 3 employees × 20 km × 2 × 220 = 26,400 km
        $carElectricFactor = $this->findFactor('UBA_CAR_003');
        if ($carElectricFactor) {
            $this->createEmissionRecord([
                'scope' => 3,
                'ghg_category' => '3.7',
                'scope_3_category' => 7,
                'emission_factor_id' => $carElectricFactor->id,
                'quantity' => 26400,
                'unit' => 'km',
                'factor_value' => $carElectricFactor->factor_kg_co2e,
                'factor_unit' => 'kg CO2e/km',
                'factor_source' => 'UBA',
                'notes' => 'Pendeln mit Elektroauto (3 Mitarbeiter)',
                'source_type' => 'manual',
                'data_quality' => 'survey',
            ]);
            $count++;
        }

        // Bus commuters - 5 employees × 10 km × 2 × 220 = 22,000 pkm
        $busFactor = $this->findFactor('UBA_BUS_001');
        if ($busFactor) {
            $this->createEmissionRecord([
                'scope' => 3,
                'ghg_category' => '3.7',
                'scope_3_category' => 7,
                'emission_factor_id' => $busFactor->id,
                'quantity' => 22000,
                'unit' => 'passenger-km',
                'factor_value' => $busFactor->factor_kg_co2e,
                'factor_unit' => 'kg CO2e/pkm',
                'factor_source' => 'UBA',
                'notes' => 'Pendeln mit Linienbus (5 Mitarbeiter)',
                'source_type' => 'manual',
                'data_quality' => 'survey',
            ]);
            $count++;
        }

        return $count;
    }

    /**
     * Find emission factor by source_id
     */
    private function findFactor(string $sourceId): ?EmissionFactor
    {
        return EmissionFactor::where('source_id', $sourceId)->first();
    }

    /**
     * Create an emission record with calculated CO2e
     */
    private function createEmissionRecord(array $data): EmissionRecord
    {
        $quantity = (float) $data['quantity'];
        $factorValue = (float) $data['factor_value'];
        $co2eKg = $quantity * $factorValue;
        $co2eTonnes = $co2eKg / 1000;

        // Get factor for additional GHG breakdown
        $factor = isset($data['emission_factor_id'])
            ? EmissionFactor::find($data['emission_factor_id'])
            : null;

        // Calculate individual GHG emissions
        $co2Kg = $factor ? ($quantity * ($factor->factor_kg_co2 ?? 0)) : $co2eKg * 0.99;
        $ch4Kg = $factor ? ($quantity * ($factor->factor_kg_ch4 ?? 0)) : 0;
        $n2oKg = $factor ? ($quantity * ($factor->factor_kg_n2o ?? 0)) : 0;

        // Determine calculation method for DB enum
        $calcMethod = match ($data['data_quality'] ?? 'estimated') {
            'financial' => 'spend_based',
            'measured', 'calculated' => 'activity_based',
            'survey' => 'average_data',
            default => 'activity_based',
        };

        // Map data_quality to DB enum values
        $dataQuality = match ($data['data_quality'] ?? 'estimated') {
            'measured' => 'measured',
            'calculated', 'financial' => 'calculated',
            default => 'estimated',
        };

        $date = now()->month(6)->day(15); // Mid-year date

        $record = new EmissionRecord([
            'organization_id' => $this->organization->id,
            'assessment_id' => $this->assessment->id,
            'site_id' => $this->site?->id,
            'emission_factor_id' => $data['emission_factor_id'] ?? null,
            'category_id' => $factor?->category_id,
            'year' => $this->year,
            'month' => 6, // June (mid-year for annual data)
            'quarter' => 2,
            'date' => $date,
            'period_start' => now()->startOfYear(),
            'period_end' => now()->endOfYear(),
            'scope' => $data['scope'],
            'ghg_category' => $data['ghg_category'],
            'scope_3_category' => $data['scope_3_category'] ?? null,
            // New columns
            'quantity' => $quantity,
            'unit' => $data['unit'],
            // Original columns
            'activity_quantity' => $quantity,
            'activity_unit' => $data['unit'],
            'factor_value' => $factorValue,
            'factor_unit' => $data['factor_unit'],
            'factor_source' => $data['factor_source'],
            // Emissions in kg
            'co2e_kg' => $co2eKg,
            'co2_kg' => $co2Kg,
            'ch4_kg' => $ch4Kg,
            'n2o_kg' => $n2oKg,
            // Original emission columns
            'emissions_co2' => $co2Kg,
            'emissions_ch4' => $ch4Kg,
            'emissions_n2o' => $n2oKg,
            'emissions_total' => $co2eKg,
            'emissions_tonnes' => $co2eTonnes,
            'uncertainty_percent' => $factor?->uncertainty_percent ?? 20,
            'calculation_method' => $calcMethod,
            'data_quality' => $dataQuality,
            'source_type' => $data['source_type'] ?? 'manual',
            'is_estimated' => $dataQuality === 'estimated',
            'notes' => $data['notes'] ?? null,
            'calculated_at' => now(),
            'is_verified' => false,
            'factor_snapshot' => $factor ? [
                'name' => $factor->name,
                'source' => $factor->source,
                'factor_kg_co2e' => $factor->factor_kg_co2e,
                'unit' => $factor->unit,
                'valid_from' => $factor->valid_from?->format('Y-m-d'),
            ] : null,
        ]);

        // Handle Scope 2 method
        if ($data['scope'] === 2) {
            $record->scope_2_method = ($data['calculation_method'] ?? 'location-based') === 'market-based'
                ? 'market_based'
                : 'location_based';
        }

        $record->id = Str::uuid()->toString();
        $record->save();

        return $record;
    }
}
