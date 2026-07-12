<?php

namespace Database\Factories;

use App\Models\Assessment;
use App\Models\EmissionRecord;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<EmissionRecord>
 */
class EmissionRecordFactory extends Factory
{
    protected $model = EmissionRecord::class;

    public function definition(): array
    {
        $scope = fake()->randomElement([1, 2, 3]);
        $quantity = fake()->randomFloat(2, 10, 1000);
        $factorValue = fake()->randomFloat(4, 0.01, 10);
        $co2e = $quantity * $factorValue;
        $date = fake()->dateTimeBetween('-1 year', 'now');

        return [
            'id' => Str::uuid(),
            'organization_id' => Organization::factory(),
            'assessment_id' => Assessment::factory(),
            'transaction_id' => null,
            'activity_id' => null,
            'site_id' => null,
            'category_id' => null,
            'emission_factor_id' => null,
            'date' => $date,
            'year' => (int) $date->format('Y'),
            'month' => (int) $date->format('n'),
            'quarter' => (int) ceil((int) $date->format('n') / 3),
            'period_start' => null,
            'period_end' => null,
            'scope' => $scope,
            'ghg_category' => fake()->randomElement(['stationary_combustion', 'mobile_combustion', 'electricity']),
            'scope_3_category' => $scope === 3 ? fake()->numberBetween(1, 15) : null,
            'quantity' => $quantity,
            'unit' => fake()->randomElement(['kWh', 'L', 'kg', 'km']),
            'activity_quantity' => $quantity,
            'activity_unit' => fake()->randomElement(['kWh', 'L', 'kg', 'km']),
            'factor_value' => $factorValue,
            'factor_unit' => 'kgCO2e/unit',
            'factor_source' => 'ADEME',
            'co2e_kg' => $co2e,
            'co2_kg' => $co2e * 0.95,
            'ch4_kg' => $co2e * 0.03,
            'n2o_kg' => $co2e * 0.02,
            'emissions_co2' => $co2e * 0.95,
            'emissions_ch4' => $co2e * 0.03,
            'emissions_n2o' => $co2e * 0.02,
            'emissions_total' => $co2e,
            'emissions_tonnes' => $co2e / 1000,
            'uncertainty_percent' => fake()->randomFloat(2, 5, 30),
            'calculation_method' => 'activity_based',
            'data_quality' => fake()->randomElement(['measured', 'calculated', 'estimated']),
            'source_type' => fake()->randomElement(['manual', 'transaction', 'activity']),
            'is_estimated' => fake()->boolean(30),
            'calculated_at' => now(),
            'notes' => fake()->optional()->sentence(),
            'factor_snapshot' => [],
            'metadata' => [],
        ];
    }

    public function scope1(): static
    {
        return $this->state(fn (array $attributes) => [
            'scope' => 1,
            'ghg_category' => 'stationary_combustion',
            'scope_3_category' => null,
        ]);
    }

    public function scope2(): static
    {
        return $this->state(fn (array $attributes) => [
            'scope' => 2,
            'ghg_category' => 'electricity',
            'scope_3_category' => null,
        ]);
    }

    public function scope3(): static
    {
        return $this->state(fn (array $attributes) => [
            'scope' => 3,
            'ghg_category' => 'purchased_goods',
            'scope_3_category' => fake()->numberBetween(1, 15),
        ]);
    }

    public function withAmount(float $co2eKg): static
    {
        return $this->state(fn (array $attributes) => [
            'co2e_kg' => $co2eKg,
            'co2_kg' => $co2eKg * 0.95,
            'ch4_kg' => $co2eKg * 0.03,
            'n2o_kg' => $co2eKg * 0.02,
        ]);
    }

    public function forOrganization(Organization $organization): static
    {
        return $this->state(fn (array $attributes) => [
            'organization_id' => $organization->id,
        ]);
    }

    public function forAssessment(Assessment $assessment): static
    {
        return $this->state(fn (array $attributes) => [
            'assessment_id' => $assessment->id,
            'organization_id' => $assessment->organization_id,
        ]);
    }
}
