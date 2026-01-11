<?php

namespace Database\Seeders;

use App\Models\Assessment;
use App\Models\Organization;
use Illuminate\Database\Seeder;

/**
 * Seeder for Assessment (Bilan) model
 *
 * Creates test assessments for existing organizations
 */
class AssessmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $organizations = Organization::all();

        if ($organizations->isEmpty()) {
            $this->command->warn('No organizations found. Creating a test organization...');
            $organizations = collect([
                Organization::create([
                    'name' => 'Test Company',
                    'legal_name' => 'Test Company SAS',
                    'slug' => 'test-company',
                    'country' => 'FR',
                    'sector' => 'technology',
                    'employee_count' => 50,
                ]),
            ]);
        }

        foreach ($organizations as $organization) {
            $this->createAssessmentsForOrganization($organization);
        }

        $this->command->info('Assessments seeded successfully!');
    }

    /**
     * Create assessments for a specific organization.
     */
    private function createAssessmentsForOrganization(Organization $organization): void
    {
        $currentYear = (int) date('Y');

        // Assessment 2024 - Completed
        Assessment::updateOrCreate(
            [
                'organization_id' => $organization->id,
                'year' => $currentYear - 1,
            ],
            [
                'revenue' => 2500000.00,
                'employee_count' => 45,
                'status' => Assessment::STATUS_COMPLETED,
                'progress' => [
                    '1.1' => 'completed',
                    '1.2' => 'completed',
                    '1.4' => 'not_applicable',
                    '1.5' => 'not_applicable',
                    '2.1' => 'completed',
                    '3.1' => 'completed',
                    '3.2' => 'completed',
                    '3.3' => 'completed',
                    '3.5' => 'completed',
                    '4.1' => 'completed',
                    '4.2' => 'completed',
                    '4.3' => 'completed',
                    '4.4' => 'not_applicable',
                    '4.5' => 'completed',
                ],
            ]
        );

        // Assessment 2025 - Active (current year)
        Assessment::updateOrCreate(
            [
                'organization_id' => $organization->id,
                'year' => $currentYear,
            ],
            [
                'revenue' => 2800000.00,
                'employee_count' => 50,
                'status' => Assessment::STATUS_ACTIVE,
                'progress' => [
                    '1.1' => 'completed',
                    '1.2' => 'pending',
                    '1.4' => 'not_applicable',
                    '1.5' => 'not_applicable',
                    '2.1' => 'completed',
                    '3.1' => 'pending',
                    '3.2' => 'pending',
                    '3.3' => 'pending',
                    '3.5' => 'pending',
                    '4.1' => 'pending',
                    '4.2' => 'pending',
                    '4.3' => 'pending',
                    '4.4' => 'not_applicable',
                    '4.5' => 'pending',
                ],
            ]
        );

        // Assessment 2023 - Completed (historical)
        Assessment::updateOrCreate(
            [
                'organization_id' => $organization->id,
                'year' => $currentYear - 2,
            ],
            [
                'revenue' => 2200000.00,
                'employee_count' => 40,
                'status' => Assessment::STATUS_COMPLETED,
                'progress' => [
                    '1.1' => 'completed',
                    '1.2' => 'completed',
                    '1.4' => 'not_applicable',
                    '1.5' => 'not_applicable',
                    '2.1' => 'completed',
                    '3.1' => 'completed',
                    '3.2' => 'completed',
                    '3.3' => 'completed',
                    '3.5' => 'completed',
                    '4.1' => 'completed',
                    '4.2' => 'completed',
                    '4.3' => 'completed',
                    '4.4' => 'not_applicable',
                    '4.5' => 'completed',
                ],
            ]
        );

        $this->command->info("  Created 3 assessments for: {$organization->name}");
    }
}
