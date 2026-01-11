<?php

namespace Database\Factories;

use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Organization>
 */
class OrganizationFactory extends Factory
{
    protected $model = Organization::class;

    public function definition(): array
    {
        $name = fake()->company();

        return [
            'id' => Str::uuid(),
            'name' => $name,
            'legal_name' => $name . ' SAS',
            'slug' => Str::slug($name),
            'country' => 'FR',
            'locale' => 'fr',
            'timezone' => 'Europe/Paris',
            'currency' => 'EUR',
            'default_currency' => 'EUR',
            'business_id' => fake()->numerify('###########'),
            'vat_number' => 'FR' . fake()->numerify('###########'),
            'sector' => fake()->randomElement(['technology', 'manufacturing', 'services', 'retail']),
            'size' => fake()->randomElement(['small', 'medium', 'large']),
            'employee_count' => fake()->numberBetween(10, 500),
            'annual_turnover' => fake()->numberBetween(100000, 50000000),
            'address_line_1' => fake()->streetAddress(),
            'city' => fake()->city(),
            'postal_code' => fake()->postcode(),
            'phone' => fake()->phoneNumber(),
            'email' => fake()->companyEmail(),
            'fiscal_year_start_month' => 1,
            'settings' => [],
            'onboarding_completed' => true,
            'status' => 'active',
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'onboarding_completed' => false,
        ]);
    }
}
