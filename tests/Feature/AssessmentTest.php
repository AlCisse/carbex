<?php

namespace Tests\Feature;

use App\Models\Assessment;
use App\Models\EmissionRecord;
use App\Models\Organization;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Feature tests for Assessment (Bilan) - T089
 */
class AssessmentTest extends TestCase
{
    use RefreshDatabase;

    private Organization $organization;

    protected function setUp(): void
    {
        parent::setUp();

        $this->organization = Organization::factory()->create();
    }

    public function test_can_create_assessment(): void
    {
        $assessment = Assessment::factory()
            ->forOrganization($this->organization)
            ->create([
                'year' => 2024,
                'revenue' => 1000000,
                'employee_count' => 50,
            ]);

        $this->assertDatabaseHas('assessments', [
            'id' => $assessment->id,
            'organization_id' => $this->organization->id,
            'year' => 2024,
        ]);
    }

    public function test_assessment_starts_as_draft(): void
    {
        $assessment = Assessment::factory()
            ->forOrganization($this->organization)
            ->create();

        $this->assertEquals(Assessment::STATUS_DRAFT, $assessment->status);
        $this->assertTrue($assessment->isDraft());
    }

    public function test_can_activate_draft_assessment(): void
    {
        $assessment = Assessment::factory()
            ->forOrganization($this->organization)
            ->draft()
            ->create();

        $result = $assessment->activate();

        $this->assertTrue($result);
        $this->assertEquals(Assessment::STATUS_ACTIVE, $assessment->status);
        $this->assertTrue($assessment->isActive());
    }

    public function test_cannot_activate_non_draft_assessment(): void
    {
        $assessment = Assessment::factory()
            ->forOrganization($this->organization)
            ->active()
            ->create();

        $result = $assessment->activate();

        $this->assertFalse($result);
    }

    public function test_can_complete_active_assessment(): void
    {
        $assessment = Assessment::factory()
            ->forOrganization($this->organization)
            ->active()
            ->create();

        $result = $assessment->complete();

        $this->assertTrue($result);
        $this->assertEquals(Assessment::STATUS_COMPLETED, $assessment->status);
        $this->assertTrue($assessment->isCompleted());
    }

    public function test_cannot_complete_draft_assessment(): void
    {
        $assessment = Assessment::factory()
            ->forOrganization($this->organization)
            ->draft()
            ->create();

        $result = $assessment->complete();

        $this->assertFalse($result);
    }

    public function test_can_reopen_completed_assessment(): void
    {
        $assessment = Assessment::factory()
            ->forOrganization($this->organization)
            ->completed()
            ->create();

        $result = $assessment->reopen();

        $this->assertTrue($result);
        $this->assertEquals(Assessment::STATUS_ACTIVE, $assessment->status);
    }

    public function test_total_emissions_calculation(): void
    {
        $assessment = Assessment::factory()
            ->forOrganization($this->organization)
            ->active()
            ->create();

        EmissionRecord::factory()
            ->forAssessment($assessment)
            ->withAmount(1000)
            ->create();
        EmissionRecord::factory()
            ->forAssessment($assessment)
            ->withAmount(2000)
            ->create();
        EmissionRecord::factory()
            ->forAssessment($assessment)
            ->withAmount(1500)
            ->create();

        $this->assertEquals(4500, $assessment->total_emissions_kg);
        $this->assertEquals(4.5, $assessment->total_emissions_tonnes);
    }

    public function test_emissions_by_scope(): void
    {
        $assessment = Assessment::factory()
            ->forOrganization($this->organization)
            ->active()
            ->create();

        EmissionRecord::factory()
            ->forAssessment($assessment)
            ->scope1()
            ->withAmount(1000)
            ->create();
        EmissionRecord::factory()
            ->forAssessment($assessment)
            ->scope2()
            ->withAmount(2000)
            ->create();
        EmissionRecord::factory()
            ->forAssessment($assessment)
            ->scope3()
            ->withAmount(3000)
            ->create();

        $byScope = $assessment->emissions_by_scope;

        $this->assertEquals(1000, $byScope[1]);
        $this->assertEquals(2000, $byScope[2]);
        $this->assertEquals(3000, $byScope[3]);
    }

    public function test_completion_percent_calculation(): void
    {
        $assessment = Assessment::factory()
            ->forOrganization($this->organization)
            ->create([
                'progress' => [
                    '1.1' => 'completed',
                    '1.2' => 'completed',
                    '2.1' => 'pending',
                    '3.1' => 'not_applicable',
                ],
            ]);

        $this->assertEquals(50, $assessment->completion_percent);
    }

    public function test_update_category_progress(): void
    {
        $assessment = Assessment::factory()
            ->forOrganization($this->organization)
            ->create(['progress' => []]);

        $assessment->updateCategoryProgress('1.1', 'completed');

        $this->assertEquals('completed', $assessment->fresh()->progress['1.1']);
    }

    public function test_for_year_scope(): void
    {
        // Use different organizations to avoid unique constraint on (org, year)
        $org2 = Organization::factory()->create();

        Assessment::factory()->forOrganization($this->organization)->forYear(2023)->create();
        Assessment::factory()->forOrganization($this->organization)->forYear(2024)->create();
        Assessment::factory()->forOrganization($org2)->forYear(2024)->create();

        $this->assertCount(1, Assessment::forYear(2023)->get());
        $this->assertCount(2, Assessment::forYear(2024)->get());
    }

    public function test_active_scope_filter(): void
    {
        Assessment::factory()->forOrganization($this->organization)->draft()->create();
        Assessment::factory()->forOrganization($this->organization)->active()->create();
        Assessment::factory()->forOrganization($this->organization)->completed()->create();

        $this->assertCount(1, Assessment::active()->get());
    }

    public function test_belongs_to_organization(): void
    {
        $assessment = Assessment::factory()
            ->forOrganization($this->organization)
            ->create();

        $this->assertNotNull($assessment->organization);
        $this->assertEquals($this->organization->id, $assessment->organization->id);
    }

    public function test_has_many_emission_records(): void
    {
        $assessment = Assessment::factory()
            ->forOrganization($this->organization)
            ->create();

        EmissionRecord::factory()
            ->count(5)
            ->forAssessment($assessment)
            ->create();

        $this->assertCount(5, $assessment->emissionRecords);
    }
}
