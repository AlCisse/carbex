<?php

namespace Database\Factories;

use App\Models\Action;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Action>
 */
class ActionFactory extends Factory
{
    protected $model = Action::class;

    public function definition(): array
    {
        return [
            'id' => Str::uuid(),
            'organization_id' => Organization::factory(),
            'category_id' => null,
            'title' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'status' => Action::STATUS_TODO,
            'due_date' => fake()->dateTimeBetween('now', '+6 months'),
            'co2_reduction_percent' => fake()->randomFloat(2, 1, 30),
            'estimated_cost' => fake()->numberBetween(1000, 100000),
            'difficulty' => fake()->randomElement([
                Action::DIFFICULTY_EASY,
                Action::DIFFICULTY_MEDIUM,
                Action::DIFFICULTY_HARD,
            ]),
            'priority' => fake()->numberBetween(1, 5),
            'assigned_to' => null,
            'metadata' => [],
        ];
    }

    public function todo(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Action::STATUS_TODO,
        ]);
    }

    public function inProgress(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Action::STATUS_IN_PROGRESS,
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Action::STATUS_COMPLETED,
        ]);
    }

    public function overdue(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Action::STATUS_TODO,
            'due_date' => fake()->dateTimeBetween('-3 months', '-1 day'),
        ]);
    }

    public function easy(): static
    {
        return $this->state(fn (array $attributes) => [
            'difficulty' => Action::DIFFICULTY_EASY,
            'estimated_cost' => fake()->numberBetween(100, 1000),
        ]);
    }

    public function hard(): static
    {
        return $this->state(fn (array $attributes) => [
            'difficulty' => Action::DIFFICULTY_HARD,
            'estimated_cost' => fake()->numberBetween(50000, 200000),
        ]);
    }

    public function forOrganization(Organization $organization): static
    {
        return $this->state(fn (array $attributes) => [
            'organization_id' => $organization->id,
        ]);
    }
}
