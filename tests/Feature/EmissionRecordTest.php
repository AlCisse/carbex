<?php

namespace Tests\Feature;

use App\Models\Assessment;
use App\Models\EmissionRecord;
use App\Models\Organization;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Feature tests for EmissionRecord (EmissionSource) - T088
 */
class EmissionRecordTest extends TestCase
{
    use RefreshDatabase;

    private Organization $organization;

    private Assessment $assessment;

    protected function setUp(): void
    {
        parent::setUp();

        $this->organization = Organization::factory()->create();
        $this->assessment = Assessment::factory()
            ->forOrganization($this->organization)
            ->active()
            ->create();
    }

    public function test_can_create_emission_record(): void
    {
        $record = EmissionRecord::factory()
            ->forAssessment($this->assessment)
            ->create([
                'quantity' => 1000,
                'factor_value' => 0.5,
                'co2e_kg' => 500,
            ]);

        $this->assertDatabaseHas('emission_records', [
            'id' => $record->id,
            'quantity' => 1000,
            'co2e_kg' => 500,
        ]);
    }

    public function test_can_create_scope1_record(): void
    {
        $record = EmissionRecord::factory()
            ->forAssessment($this->assessment)
            ->scope1()
            ->create();

        $this->assertEquals(1, $record->scope);
        $this->assertNull($record->scope_3_category);
    }

    public function test_can_create_scope2_record(): void
    {
        $record = EmissionRecord::factory()
            ->forAssessment($this->assessment)
            ->scope2()
            ->create();

        $this->assertEquals(2, $record->scope);
        $this->assertEquals('electricity', $record->ghg_category);
    }

    public function test_can_create_scope3_record(): void
    {
        $record = EmissionRecord::factory()
            ->forAssessment($this->assessment)
            ->scope3()
            ->create();

        $this->assertEquals(3, $record->scope);
        $this->assertNotNull($record->scope_3_category);
    }

    public function test_co2e_tonnes_accessor(): void
    {
        $record = EmissionRecord::factory()
            ->forAssessment($this->assessment)
            ->withAmount(5000) // 5000 kg
            ->create();

        $this->assertEquals(5, $record->co2e_tonnes);
    }

    public function test_scope_label_accessor(): void
    {
        $record1 = EmissionRecord::factory()->forAssessment($this->assessment)->scope1()->create();
        $record2 = EmissionRecord::factory()->forAssessment($this->assessment)->scope2()->create();
        $record3 = EmissionRecord::factory()->forAssessment($this->assessment)->scope3()->create();

        $this->assertNotEmpty($record1->scope_label);
        $this->assertNotEmpty($record2->scope_label);
        $this->assertNotEmpty($record3->scope_label);
    }

    public function test_for_assessment_scope(): void
    {
        $otherAssessment = Assessment::factory()
            ->forOrganization($this->organization)
            ->create();

        EmissionRecord::factory()->count(3)->forAssessment($this->assessment)->create();
        EmissionRecord::factory()->count(2)->forAssessment($otherAssessment)->create();

        $this->assertCount(3, EmissionRecord::forAssessment($this->assessment->id)->get());
        $this->assertCount(2, EmissionRecord::forAssessment($otherAssessment->id)->get());
    }

    public function test_for_scope_filter(): void
    {
        EmissionRecord::factory()->count(2)->forAssessment($this->assessment)->scope1()->create();
        EmissionRecord::factory()->count(1)->forAssessment($this->assessment)->scope2()->create();
        EmissionRecord::factory()->count(3)->forAssessment($this->assessment)->scope3()->create();

        $this->assertCount(2, EmissionRecord::forScope(1)->get());
        $this->assertCount(1, EmissionRecord::forScope(2)->get());
        $this->assertCount(3, EmissionRecord::forScope(3)->get());
    }

    public function test_in_period_scope(): void
    {
        EmissionRecord::factory()->forAssessment($this->assessment)->create([
            'date' => '2024-06-15',
        ]);
        EmissionRecord::factory()->forAssessment($this->assessment)->create([
            'date' => '2024-01-15',
        ]);
        EmissionRecord::factory()->forAssessment($this->assessment)->create([
            'date' => '2023-06-15',
        ]);

        $records = EmissionRecord::inPeriod('2024-01-01', '2024-12-31')->get();
        $this->assertCount(2, $records);
    }

    public function test_belongs_to_assessment(): void
    {
        $record = EmissionRecord::factory()
            ->forAssessment($this->assessment)
            ->create();

        $this->assertNotNull($record->assessment);
        $this->assertEquals($this->assessment->id, $record->assessment->id);
    }

    public function test_from_transactions_scope(): void
    {
        EmissionRecord::factory()->forAssessment($this->assessment)->create([
            'source_type' => 'transaction',
        ]);
        EmissionRecord::factory()->forAssessment($this->assessment)->create([
            'source_type' => 'manual',
        ]);

        $this->assertCount(1, EmissionRecord::fromTransactions()->get());
    }

    public function test_soft_delete(): void
    {
        $record = EmissionRecord::factory()
            ->forAssessment($this->assessment)
            ->create();

        $record->delete();

        $this->assertSoftDeleted($record);
        $this->assertCount(0, EmissionRecord::all());
        $this->assertCount(1, EmissionRecord::withTrashed()->get());
    }
}
