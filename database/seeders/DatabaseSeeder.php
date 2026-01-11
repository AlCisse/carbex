<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // Reference data
            CountrySeeder::class,
            MccCategorySeeder::class,

            // Emission factors
            AdemeFactorSeeder::class,
            UbaFactorSeeder::class,
            EuCountryFactorSeeder::class,
            Scope3FactorSeeder::class,
        ]);

        // Seed test data in local/development environment
        if (app()->environment('local', 'development', 'testing')) {
            $this->call([
                TestDataSeeder::class,
            ]);
        }
    }
}
