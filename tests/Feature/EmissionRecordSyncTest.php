<?php

namespace Tests\Feature;

use App\Models\EmissionRecord;
use App\Models\Organization;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * The saving hook keeps NOT NULL mirror columns in sync:
 * year/month/quarter mirror date; activity_* and emissions_*
 * mirror quantity/unit/co2e_kg — on create AND on update.
 */
class EmissionRecordSyncTest extends TestCase
{
    use RefreshDatabase;

    private Organization $organization;

    protected function setUp(): void
    {
        parent::setUp();
        $this->organization = Organization::factory()->create();
    }

    private function makeRecord(array $overrides = []): EmissionRecord
    {
        return EmissionRecord::create(array_merge([
            'organization_id' => $this->organization->id,
            'scope' => 1,
            'ghg_category' => '1.1',
            'quantity' => 100,
            'unit' => 'kWh',
            'factor_value' => 0.5,
            'factor_unit' => 'kgCO2e/kWh',
            'factor_source' => 'uba',
            'co2e_kg' => 50,
            'calculation_method' => 'activity_based',
            'data_quality' => 'estimated',
            'source_type' => 'manual',
            'is_estimated' => false,
            'date' => '2026-05-15',
        ], $overrides));
    }

    public function test_create_derives_mirror_columns(): void
    {
        $record = $this->makeRecord()->fresh();

        $this->assertEquals(2026, $record->year);
        $this->assertEquals(5, $record->month);
        $this->assertEquals(2, $record->quarter);
        $this->assertEquals(100, (float) $record->activity_quantity);
        $this->assertEquals('kWh', $record->activity_unit);
        $this->assertEquals(50, (float) $record->emissions_total);
        $this->assertEquals(0.05, (float) $record->emissions_tonnes);
        $this->assertNotNull($record->calculated_at);
    }

    public function test_update_resyncs_emission_mirrors(): void
    {
        $record = $this->makeRecord();
        $originalCalculatedAt = $record->fresh()->calculated_at;

        $this->travel(1)->minutes();
        $record->update(['quantity' => 200, 'co2e_kg' => 120]);
        $record->refresh();

        $this->assertEquals(200, (float) $record->activity_quantity);
        $this->assertEquals(120, (float) $record->emissions_total);
        $this->assertEquals(0.12, (float) $record->emissions_tonnes);
        $this->assertTrue($record->calculated_at->gt($originalCalculatedAt));
    }

    public function test_update_date_resyncs_period_columns(): void
    {
        $record = $this->makeRecord();

        $record->update(['date' => '2026-11-02']);
        $record->refresh();

        $this->assertEquals(2026, $record->year);
        $this->assertEquals(11, $record->month);
        $this->assertEquals(4, $record->quarter);
    }

    public function test_explicit_values_win_over_derivation(): void
    {
        $record = $this->makeRecord(['month' => 12, 'quarter' => 4]);

        $this->assertEquals(12, $record->fresh()->month);
        $this->assertEquals(4, $record->fresh()->quarter);
    }

    public function test_missing_date_falls_back_to_period_start(): void
    {
        $record = $this->makeRecord([
            'date' => null,
            'period_start' => '2026-01-01',
            'period_end' => '2026-12-31',
        ])->fresh();

        $this->assertEquals('2026-01-01', $record->date->toDateString());
        $this->assertEquals(1, $record->month);
        $this->assertEquals(1, $record->quarter);
    }
}
