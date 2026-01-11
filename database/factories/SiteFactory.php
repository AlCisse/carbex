<?php

namespace Database\Factories;

use App\Models\Organization;
use App\Models\Site;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Site>
 */
class SiteFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<Site>
     */
    protected $model = Site::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $types = ['headquarters', 'office', 'warehouse', 'factory', 'retail', 'remote', 'other'];

        return [
            'organization_id' => Organization::factory(),
            'name' => $this->faker->company().' '.$this->faker->randomElement(['HQ', 'Office', 'Warehouse', 'Factory']),
            'code' => strtoupper($this->faker->unique()->lexify('SITE-???')),
            'description' => $this->faker->optional()->sentence(),
            'type' => $this->faker->randomElement($types),
            'address_line_1' => $this->faker->streetAddress(),
            'address_line_2' => $this->faker->optional()->secondaryAddress(),
            'city' => $this->faker->city(),
            'postal_code' => $this->faker->postcode(),
            'country' => $this->faker->randomElement(['FR', 'DE', 'GB']),
            'latitude' => $this->faker->optional()->latitude(),
            'longitude' => $this->faker->optional()->longitude(),
            'floor_area_m2' => $this->faker->optional()->randomFloat(2, 100, 10000),
            'employee_count' => $this->faker->optional()->numberBetween(1, 500),
            'electricity_provider' => $this->faker->optional()->company(),
            'renewable_energy' => $this->faker->boolean(30),
            'renewable_percentage' => fn (array $attributes) => $attributes['renewable_energy'] ? $this->faker->randomFloat(2, 10, 100) : 0,
            'is_primary' => false,
            'is_active' => true,
        ];
    }

    /**
     * Indicate that the site is the primary site.
     */
    public function primary(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_primary' => true,
        ]);
    }

    /**
     * Indicate that the site is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Indicate that the site is a headquarters.
     */
    public function headquarters(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'headquarters',
            'is_primary' => true,
        ]);
    }

    /**
     * Indicate that the site is an office.
     */
    public function office(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'office',
        ]);
    }

    /**
     * Indicate that the site is a warehouse.
     */
    public function warehouse(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'warehouse',
        ]);
    }

    /**
     * Indicate that the site is a factory.
     */
    public function factory(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'factory',
        ]);
    }

    /**
     * Indicate that the site uses renewable energy.
     */
    public function renewable(): static
    {
        return $this->state(fn (array $attributes) => [
            'renewable_energy' => true,
            'renewable_percentage' => $this->faker->randomFloat(2, 50, 100),
        ]);
    }

    /**
     * Indicate that the site has complete data.
     */
    public function complete(): static
    {
        return $this->state(fn (array $attributes) => [
            'floor_area_m2' => $this->faker->randomFloat(2, 100, 10000),
            'employee_count' => $this->faker->numberBetween(5, 200),
            'latitude' => $this->faker->latitude(),
            'longitude' => $this->faker->longitude(),
        ]);
    }
}
