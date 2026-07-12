<?php

namespace Database\Factories;

use App\Models\Organization;
use App\Models\ReductionTarget;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<ReductionTarget>
 */
class ReductionTargetFactory extends Factory
{
    protected $model = ReductionTarget::class;

    public function definition(): array
    {
        $baselineYear = fake()->numberBetween(2020, 2023);
        $targetYear = $baselineYear + fake()->numberBetween(5, 10);

        return [
            'id' => Str::uuid(),
            'organization_id' => Organization::factory(),
            'baseline_year' => $baselineYear,
            'target_year' => $targetYear,
            'scope_1_reduction' => fake()->randomFloat(2, 20, 50),
            'scope_2_reduction' => fake()->randomFloat(2, 20, 50),
            'scope_3_reduction' => fake()->randomFloat(2, 15, 40),
            'is_sbti_aligned' => fake()->boolean(70),
            'notes' => fake()->optional()->sentence(),
        ];
    }

    public function sbtiAligned(): static
    {
        return $this->state(function (array $attributes) {
            $years = $attributes['target_year'] - $attributes['baseline_year'];

            return [
                'scope_1_reduction' => ReductionTarget::SBTI_SCOPE_1_2_MIN_RATE * $years,
                'scope_2_reduction' => ReductionTarget::SBTI_SCOPE_1_2_MIN_RATE * $years,
                'scope_3_reduction' => ReductionTarget::SBTI_SCOPE_3_MIN_RATE * $years,
                'is_sbti_aligned' => true,
            ];
        });
    }

    public function notSbtiAligned(): static
    {
        return $this->state(fn (array $attributes) => [
            'scope_1_reduction' => 10,
            'scope_2_reduction' => 10,
            'scope_3_reduction' => 5,
            'is_sbti_aligned' => false,
        ]);
    }

    public function forOrganization(Organization $organization): static
    {
        return $this->state(fn (array $attributes) => [
            'organization_id' => $organization->id,
        ]);
    }

    public function forPeriod(int $baselineYear, int $targetYear): static
    {
        return $this->state(fn (array $attributes) => [
            'baseline_year' => $baselineYear,
            'target_year' => $targetYear,
        ]);
    }
}
