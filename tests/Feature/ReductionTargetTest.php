<?php

namespace Tests\Feature;

use App\Models\Organization;
use App\Models\ReductionTarget;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Feature tests for ReductionTarget (Trajectoire SBTi) - T091
 */
class ReductionTargetTest extends TestCase
{
    use RefreshDatabase;

    private Organization $organization;

    protected function setUp(): void
    {
        parent::setUp();

        $this->organization = Organization::factory()->create();
    }

    public function test_can_create_reduction_target(): void
    {
        $target = ReductionTarget::factory()
            ->forOrganization($this->organization)
            ->create([
                'baseline_year' => 2020,
                'target_year' => 2030,
                'scope_1_reduction' => 42,
                'scope_2_reduction' => 42,
                'scope_3_reduction' => 25,
            ]);

        $this->assertDatabaseHas('reduction_targets', [
            'id' => $target->id,
            'baseline_year' => 2020,
            'target_year' => 2030,
        ]);
    }

    public function test_years_to_target_calculation(): void
    {
        $target = ReductionTarget::factory()
            ->forOrganization($this->organization)
            ->forPeriod(2020, 2030)
            ->create();

        $this->assertEquals(10, $target->years_to_target);
    }

    public function test_annual_rate_calculation(): void
    {
        $target = ReductionTarget::factory()
            ->forOrganization($this->organization)
            ->forPeriod(2020, 2030)
            ->create([
                'scope_1_reduction' => 50, // 50% over 10 years = 5%/year
                'scope_2_reduction' => 40, // 40% over 10 years = 4%/year
                'scope_3_reduction' => 30, // 30% over 10 years = 3%/year
            ]);

        $this->assertEquals(5, $target->scope_1_annual_rate);
        $this->assertEquals(4, $target->scope_2_annual_rate);
        $this->assertEquals(3, $target->scope_3_annual_rate);
    }

    public function test_sbti_compliance_check(): void
    {
        // SBTi minimum: 4.2%/year for S1/S2, 2.5%/year for S3
        $compliantTarget = ReductionTarget::factory()
            ->forOrganization($this->organization)
            ->forPeriod(2020, 2030) // 10 years
            ->create([
                'scope_1_reduction' => 50, // 5%/year - compliant
                'scope_2_reduction' => 45, // 4.5%/year - compliant
                'scope_3_reduction' => 30, // 3%/year - compliant
            ]);

        // Use different years to avoid unique constraint violation
        $nonCompliantTarget = ReductionTarget::factory()
            ->forOrganization($this->organization)
            ->forPeriod(2021, 2031) // Different period
            ->create([
                'scope_1_reduction' => 30, // 3%/year - NOT compliant
                'scope_2_reduction' => 30, // 3%/year - NOT compliant
                'scope_3_reduction' => 15, // 1.5%/year - NOT compliant
            ]);

        $this->assertTrue($compliantTarget->isScope1SbtiCompliant());
        $this->assertTrue($compliantTarget->isScope2SbtiCompliant());
        $this->assertTrue($compliantTarget->isScope3SbtiCompliant());
        $this->assertTrue($compliantTarget->isFullySbtiCompliant());

        $this->assertFalse($nonCompliantTarget->isScope1SbtiCompliant());
        $this->assertFalse($nonCompliantTarget->isScope2SbtiCompliant());
        $this->assertFalse($nonCompliantTarget->isScope3SbtiCompliant());
        $this->assertFalse($nonCompliantTarget->isFullySbtiCompliant());
    }

    public function test_sbti_compliance_attribute(): void
    {
        $target = ReductionTarget::factory()
            ->forOrganization($this->organization)
            ->sbtiAligned()
            ->create();

        $compliance = $target->sbti_compliance;

        $this->assertArrayHasKey('scope_1', $compliance);
        $this->assertArrayHasKey('scope_2', $compliance);
        $this->assertArrayHasKey('scope_3', $compliance);
        $this->assertArrayHasKey('overall', $compliance);
    }

    public function test_get_sbti_recommended_targets(): void
    {
        $recommended = ReductionTarget::getSbtiRecommendedTargets(2020, 2030);

        // 10 years * 4.2% = 42% for S1/S2
        // 10 years * 2.5% = 25% for S3
        $this->assertEquals(42, $recommended['scope_1_reduction']);
        $this->assertEquals(42, $recommended['scope_2_reduction']);
        $this->assertEquals(25, $recommended['scope_3_reduction']);
    }

    public function test_expected_emissions_for_year(): void
    {
        $target = ReductionTarget::factory()
            ->forOrganization($this->organization)
            ->forPeriod(2020, 2030)
            ->create([
                'scope_1_reduction' => 50, // 50% total reduction
            ]);

        $baselineEmissions = 1000;

        // At baseline year, emissions = baseline
        $this->assertEquals(1000, $target->getExpectedEmissionsForYear(2020, $baselineEmissions, 1));

        // At target year, emissions = baseline * (1 - reduction)
        $this->assertEquals(500, $target->getExpectedEmissionsForYear(2030, $baselineEmissions, 1));

        // At midpoint (2025), emissions should be interpolated
        $midpoint = $target->getExpectedEmissionsForYear(2025, $baselineEmissions, 1);
        $this->assertEqualsWithDelta(750, $midpoint, 1); // ~25% reduction at midpoint
    }

    public function test_for_baseline_year_scope(): void
    {
        ReductionTarget::factory()->forOrganization($this->organization)->forPeriod(2020, 2030)->create();
        ReductionTarget::factory()->forOrganization($this->organization)->forPeriod(2021, 2031)->create();
        ReductionTarget::factory()->forOrganization($this->organization)->forPeriod(2020, 2025)->create();

        $this->assertCount(2, ReductionTarget::forBaselineYear(2020)->get());
        $this->assertCount(1, ReductionTarget::forBaselineYear(2021)->get());
    }

    public function test_for_target_year_scope(): void
    {
        // Use different organizations to avoid unique constraint on (org, baseline, target)
        $org2 = Organization::factory()->create();

        ReductionTarget::factory()->forOrganization($this->organization)->forPeriod(2020, 2030)->create();
        ReductionTarget::factory()->forOrganization($org2)->forPeriod(2020, 2030)->create();
        ReductionTarget::factory()->forOrganization($this->organization)->forPeriod(2020, 2025)->create();

        $this->assertCount(2, ReductionTarget::forTargetYear(2030)->get());
        $this->assertCount(1, ReductionTarget::forTargetYear(2025)->get());
    }

    public function test_sbti_aligned_scope(): void
    {
        // Use explicit different periods to avoid unique constraint collisions
        ReductionTarget::factory()->forOrganization($this->organization)->forPeriod(2020, 2030)->create(['is_sbti_aligned' => true]);
        ReductionTarget::factory()->forOrganization($this->organization)->forPeriod(2021, 2031)->create(['is_sbti_aligned' => true]);
        ReductionTarget::factory()->forOrganization($this->organization)->forPeriod(2022, 2032)->create(['is_sbti_aligned' => false]);

        $this->assertCount(2, ReductionTarget::sbtiAligned()->get());
    }

    public function test_active_scope(): void
    {
        $currentYear = now()->year;

        ReductionTarget::factory()->forOrganization($this->organization)->forPeriod($currentYear - 5, $currentYear + 5)->create();
        ReductionTarget::factory()->forOrganization($this->organization)->forPeriod($currentYear - 10, $currentYear - 1)->create();

        $this->assertCount(1, ReductionTarget::active()->get());
    }

    public function test_belongs_to_organization(): void
    {
        $target = ReductionTarget::factory()
            ->forOrganization($this->organization)
            ->create();

        $this->assertNotNull($target->organization);
        $this->assertEquals($this->organization->id, $target->organization->id);
    }
}
