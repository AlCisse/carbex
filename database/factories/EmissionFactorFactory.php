<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\EmissionFactor;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<EmissionFactor>
 */
class EmissionFactorFactory extends Factory
{
    protected $model = EmissionFactor::class;

    public function definition(): array
    {
        return [
            'id' => Str::uuid(),
            'category_id' => null,
            'name' => fake()->words(3, true),
            'description' => fake()->sentence(),
            'scope' => fake()->randomElement([1, 2, 3]),
            'unit' => fake()->randomElement(['kWh', 'L', 'kg', 'km', 'EUR']),
            'factor_kg_co2e' => fake()->randomFloat(6, 0.01, 10),
            'factor_kg_co2' => fake()->randomFloat(6, 0.01, 8),
            'factor_kg_ch4' => fake()->optional()->randomFloat(8, 0.0001, 0.01),
            'factor_kg_n2o' => fake()->optional()->randomFloat(8, 0.00001, 0.001),
            'country' => fake()->randomElement(['FR', 'DE', 'GB', null]),
            'source' => fake()->randomElement(['ademe', 'defra', 'uba', 'epa']),
            'source_id' => fake()->uuid(),
            'methodology' => fake()->randomElement(['location-based', 'market-based', 'spend-based']),
            'uncertainty_percent' => fake()->randomFloat(2, 5, 30),
            'valid_from' => fake()->dateTimeBetween('-2 years', 'now'),
            'valid_until' => fake()->optional()->dateTimeBetween('now', '+2 years'),
            'is_active' => true,
            'metadata' => [],
        ];
    }

    public function scope1(): static
    {
        return $this->state(fn (array $attributes) => [
            'scope' => 1,
            'unit' => fake()->randomElement(['L', 'kWh', 'm3', 'kg']),
        ]);
    }

    public function scope2(): static
    {
        return $this->state(fn (array $attributes) => [
            'scope' => 2,
            'unit' => 'kWh',
            'methodology' => fake()->randomElement(['location-based', 'market-based']),
        ]);
    }

    public function scope3(): static
    {
        return $this->state(fn (array $attributes) => [
            'scope' => 3,
            'unit' => fake()->randomElement(['EUR', 'kg', 'passenger-km', 'tonne-km']),
        ]);
    }

    public function forFuel(string $fuelType = 'diesel'): static
    {
        $factors = [
            'diesel' => 2.68,
            'petrol' => 2.31,
            'lpg' => 1.61,
        ];

        return $this->state(fn (array $attributes) => [
            'scope' => 1,
            'name' => ucfirst($fuelType) . ' - ' . fake()->word(),
            'unit' => 'L',
            'factor_kg_co2e' => $factors[$fuelType] ?? 2.5,
        ]);
    }

    public function forElectricity(string $country = 'FR'): static
    {
        $factors = [
            'FR' => 0.052,  // France (nuclear-heavy)
            'DE' => 0.366,  // Germany
            'GB' => 0.193,  // UK
            'PL' => 0.765,  // Poland (coal-heavy)
        ];

        return $this->state(fn (array $attributes) => [
            'scope' => 2,
            'name' => "Electricity - {$country}",
            'unit' => 'kWh',
            'country' => $country,
            'factor_kg_co2e' => $factors[$country] ?? 0.4,
            'methodology' => 'location-based',
        ]);
    }

    public function spendBased(): static
    {
        return $this->state(fn (array $attributes) => [
            'scope' => 3,
            'unit' => 'EUR',
            'factor_kg_co2e' => fake()->randomFloat(4, 0.1, 2),
            'methodology' => 'spend-based',
        ]);
    }

    public function forCategory(Category $category): static
    {
        return $this->state(fn (array $attributes) => [
            'category_id' => $category->id,
            'scope' => $category->scope,
        ]);
    }

    public function forCountry(string $country): static
    {
        return $this->state(fn (array $attributes) => [
            'country' => $country,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'valid_from' => fake()->dateTimeBetween('-3 years', '-2 years'),
            'valid_until' => fake()->dateTimeBetween('-1 year', '-1 month'),
        ]);
    }
}
