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
 * SECURITY: This seeder should ONLY be run in local/development environments.
 * Never run in production.
 *
 * Run with: php artisan db:seed --class=TestDataSeeder
 *
 * Environment variables:
 * - TEST_USER_EMAIL: Test user email (default: test@linscarbon.local)
 * - TEST_USER_PASSWORD: Test user password (randomly generated if not set)
 *
 * Creates:
 * - Test organization
 * - Test user
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
        // SECURITY: Prevent running in production
        if (app()->environment('production')) {
            $this->command->error('TestDataSeeder cannot be run in production!');

            return;
        }

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

        // SECURITY: Use environment variables or generate secure random password
        $testEmail = env('TEST_USER_EMAIL', 'test@linscarbon.local');
        $testPassword = env('TEST_USER_PASSWORD', Str::random(16));

        // Create test user if not exists
        $user = User::where('email', $testEmail)->first();
        $passwordGenerated = false;

        if (! $user) {
            // If password is not in env, we generated it, so flag it to display
            $passwordGenerated = ! env('TEST_USER_PASSWORD');

            $user = User::create([
                'organization_id' => $organization->id,
                'email' => $testEmail,
                'password' => Hash::make($testPassword),
                'first_name' => 'Test',
                'last_name' => 'User',
                'name' => 'Test User',
                'role' => 'owner',
                'email_verified_at' => now(),
            ]);
            $this->command->info("Created test user: {$testEmail}");
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
        $this->command->info("  Email: {$testEmail}");

        if ($passwordGenerated) {
            $this->command->warn("  Password: {$testPassword}");
            $this->command->warn('  (Save this password! It was randomly generated.)');
        } else {
            $this->command->info('  Password: <from TEST_USER_PASSWORD env var>');
        }

        $this->command->newLine();
    }
}
