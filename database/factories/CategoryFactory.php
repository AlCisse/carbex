<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Category>
 */
class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        $scope = fake()->randomElement([1, 2, 3]);
        // Use unique suffix to avoid code collisions
        $code = $scope . '.' . fake()->unique()->numberBetween(100, 9999);

        return [
            'id' => Str::uuid(),
            'code' => $code,
            'name' => fake()->words(3, true),
            'description' => fake()->sentence(),
            'scope' => $scope,
            'ghg_category' => fake()->randomElement(['stationary_combustion', 'mobile_combustion', 'electricity', 'purchased_goods']),
            'scope_3_category' => $scope === 3 ? fake()->numberBetween(1, 15) : null,
            'parent_id' => null,
            'mcc_codes' => [],
            'keywords' => [fake()->word(), fake()->word()],
            'default_unit' => 'kWh',
            'calculation_method' => fake()->randomElement(['spend_based', 'activity_based', 'hybrid']),
            'icon' => 'lightning-bolt',
            'color' => '#22C55E',
            'sort_order' => fake()->numberBetween(1, 100),
            'is_active' => true,
            'translations' => [
                'en' => ['name' => fake()->words(3, true)],
                'de' => ['name' => fake()->words(3, true)],
            ],
        ];
    }

    public function scope1(): static
    {
        return $this->state(fn (array $attributes) => [
            'scope' => 1,
            'code' => '1.' . fake()->unique()->numberBetween(100, 9999),
        ]);
    }

    public function scope2(): static
    {
        return $this->state(fn (array $attributes) => [
            'scope' => 2,
            'code' => '2.' . fake()->unique()->numberBetween(100, 9999),
        ]);
    }

    public function scope3(): static
    {
        return $this->state(fn (array $attributes) => [
            'scope' => 3,
            'code' => '3.' . fake()->unique()->numberBetween(100, 9999),
            'scope_3_category' => fake()->numberBetween(1, 15),
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
