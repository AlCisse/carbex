<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Seed countries configuration for EU countries.
     */
    public function run(): void
    {
        // This seeder ensures the country configurations are applied.
        // The actual configuration is in config/countries.php

        $this->command->info('Country configurations are defined in config/countries.php');
        $this->command->info('Supported countries: FR, DE, BE, NL, AT, CH, ES, IT');

        // Log the configuration for verification
        $countries = config('countries.countries');
        $supported = ['FR', 'DE', 'BE', 'NL', 'AT', 'CH', 'ES', 'IT'];

        foreach ($supported as $code) {
            if (isset($countries[$code])) {
                $country = $countries[$code];
                $this->command->info("- {$country['name']} ({$code}): VAT {$country['vat']['standard']}%, Currency: {$country['currency']}");
            }
        }

        $this->command->newLine();
        $this->command->info('Banking providers:');
        $this->command->info('- France: Bridge API');
        $this->command->info('- All other countries: FinAPI');
    }
}
