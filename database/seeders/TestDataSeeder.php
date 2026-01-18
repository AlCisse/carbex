<?php

namespace Database\Seeders;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * TestDataSeeder - Creates test data for development
 *
 * Run with: php artisan db:seed --class=TestDataSeeder
 *
 * Creates:
 * - Test organization
 * - Test user (test@linscarbon.fr / password)
 * - Assessments (2023, 2024, 2025)
 * - Actions (10 reduction actions)
 * - Reduction targets (4 SBTi trajectories)
 */
class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding test data...');

        // Create test organization if not exists
        $organization = Organization::where('slug', 'test-company')->first();

        if (! $organization) {
            $organization = new Organization([
                'name' => 'Test Company',
                'legal_name' => 'Test Company SAS',
                'slug' => 'test-company',
                'country' => 'FR',
                'sector' => 'technology',
                'employee_count' => 50,
                'annual_turnover' => 2800000.00,
                'address_line_1' => '123 Rue de la République',
                'city' => 'Paris',
                'postal_code' => '75001',
                'status' => 'active',
            ]);
            $organization->id = Str::uuid()->toString();
            $organization->save();
            $this->command->info('Created test organization: Test Company');
        }

        // Create test user if not exists
        $user = User::where('email', 'test@linscarbon.fr')->first();

        if (! $user) {
            $user = User::create([
                'organization_id' => $organization->id,
                'email' => 'test@linscarbon.fr',
                'password' => Hash::make('password'),
                'first_name' => 'Test',
                'last_name' => 'User',
                'name' => 'Test User',
                'role' => 'owner',
                'email_verified_at' => now(),
            ]);
            $this->command->info('Created test user: test@linscarbon.fr / password');
        }

        // Run dependent seeders
        $this->call([
            AssessmentSeeder::class,
            EmissionRecordSeeder::class,
            ActionSeeder::class,
            ReductionTargetSeeder::class,
        ]);

        $this->command->newLine();
        $this->command->info('=================================');
        $this->command->info('Test data seeding complete!');
        $this->command->info('=================================');
        $this->command->newLine();
        $this->command->info('Login credentials:');
        $this->command->info('  Email: test@linscarbon.fr');
        $this->command->info('  Password: password');
        $this->command->newLine();
    }
}
