<?php

namespace Database\Factories;

use App\Models\Assessment;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Assessment>
 */
class AssessmentFactory extends Factory
{
    protected $model = Assessment::class;

    public function definition(): array
    {
        return [
            'id' => Str::uuid(),
            'organization_id' => Organization::factory(),
            'year' => fake()->unique()->numberBetween(2000, 2099),
            'revenue' => fake()->numberBetween(100000, 50000000),
            'employee_count' => fake()->numberBetween(10, 500),
            'status' => Assessment::STATUS_DRAFT,
            'progress' => [],
        ];
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Assessment::STATUS_DRAFT,
        ]);
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Assessment::STATUS_ACTIVE,
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Assessment::STATUS_COMPLETED,
            'progress' => [
                '1.1' => 'completed',
                '1.2' => 'completed',
                '2.1' => 'completed',
                '3.1' => 'completed',
            ],
        ]);
    }

    public function forYear(int $year): static
    {
        return $this->state(fn (array $attributes) => [
            'year' => $year,
        ]);
    }

    public function forOrganization(Organization $organization): static
    {
        return $this->state(fn (array $attributes) => [
            'organization_id' => $organization->id,
        ]);
    }
}
