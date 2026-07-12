<?php

namespace Database\Seeders;

use App\Models\Organization;
use App\Models\ReductionTarget;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Seeder for ReductionTarget (Trajectoire SBTi) model
 *
 * Creates realistic reduction targets aligned with SBTi recommendations
 */
class ReductionTargetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $organizations = Organization::all();

        if ($organizations->isEmpty()) {
            $this->command->warn('No organizations found. Run AssessmentSeeder first.');

            return;
        }

        foreach ($organizations as $organization) {
            $this->createTargetsForOrganization($organization);
        }

        $this->command->info('Reduction targets seeded successfully!');
    }

    /**
     * Create reduction targets for a specific organization.
     */
    private function createTargetsForOrganization(Organization $organization): void
    {
        $currentYear = (int) date('Y');
        $baselineYear = $currentYear - 1; // 2024

        // Target 1: Short-term 2030 target (SBTi aligned)
        $this->createOrUpdateTarget($organization->id, $baselineYear, 2030, [
            'scope_1_reduction' => 25.20,
            'scope_2_reduction' => 25.20,
            'scope_3_reduction' => 15.00,
            'is_sbti_aligned' => true,
            'notes' => 'Objectif court terme aligné SBTi 1.5°C. Réduction annuelle de 4.2% pour Scopes 1&2 et 2.5% pour Scope 3.',
        ]);

        // Target 2: Mid-term 2035 target
        $this->createOrUpdateTarget($organization->id, $baselineYear, 2035, [
            'scope_1_reduction' => 46.20,
            'scope_2_reduction' => 46.20,
            'scope_3_reduction' => 27.50,
            'is_sbti_aligned' => true,
            'notes' => 'Objectif moyen terme. Trajectoire linéaire vers la neutralité carbone.',
        ]);

        // Target 3: Long-term 2050 Net Zero target
        $this->createOrUpdateTarget($organization->id, $baselineYear, 2050, [
            'scope_1_reduction' => 90.00,
            'scope_2_reduction' => 90.00,
            'scope_3_reduction' => 90.00,
            'is_sbti_aligned' => true,
            'notes' => 'Objectif Net Zero 2050. Réduction de 90% des émissions avec compensation pour les émissions résiduelles.',
        ]);

        // Target 4: Custom ambitious target (not SBTi aligned - faster pace)
        $this->createOrUpdateTarget($organization->id, $baselineYear, 2028, [
            'scope_1_reduction' => 30.00,
            'scope_2_reduction' => 50.00,
            'scope_3_reduction' => 10.00,
            'is_sbti_aligned' => false,
            'notes' => 'Objectif accéléré pour Scope 2 grâce au passage à l\'électricité verte. Focus prioritaire sur l\'énergie.',
        ]);

        $this->command->info("  Created 4 reduction targets for: {$organization->name}");
    }

    /**
     * Create or update a reduction target with proper UUID handling.
     */
    private function createOrUpdateTarget(string $organizationId, int $baselineYear, int $targetYear, array $data): ReductionTarget
    {
        $target = ReductionTarget::where('organization_id', $organizationId)
            ->where('baseline_year', $baselineYear)
            ->where('target_year', $targetYear)
            ->first();

        if ($target) {
            $target->update($data);

            return $target;
        }

        $target = new ReductionTarget(array_merge($data, [
            'organization_id' => $organizationId,
            'baseline_year' => $baselineYear,
            'target_year' => $targetYear,
        ]));
        $target->id = Str::uuid()->toString();
        $target->save();

        return $target;
    }
}
