<?php

namespace Tests\Unit;

use App\Models\Assessment;
use App\Models\Category;
use App\Models\EmissionRecord;
use App\Models\Organization;
use App\Models\Transaction;
use App\Services\Dashboard\DashboardCacheService;
use App\Services\Dashboard\DashboardService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

/**
 * Unit tests for DashboardService - T094
 */
class DashboardServiceTest extends TestCase
{
    use RefreshDatabase;

    private DashboardService $service;

    private Organization $organization;

    protected function setUp(): void
    {
        parent::setUp();

        // Mock the cache service to bypass caching in tests
        $cacheMock = Mockery::mock(DashboardCacheService::class);
        $cacheMock->shouldReceive('remember')
            ->andReturnUsing(fn ($key, $ttl, $callback) => $callback());

        $this->service = new DashboardService($cacheMock);
        $this->organization = Organization::factory()->create();
    }

    public function test_get_kpis_returns_correct_structure(): void
    {
        $assessment = Assessment::factory()
            ->forOrganization($this->organization)
            ->active()
            ->create();

        EmissionRecord::factory()
            ->count(3)
            ->forAssessment($assessment)
            ->create();

        $result = $this->service->getKpis($this->organization->id);

        $this->assertArrayHasKey('total_emissions', $result);
        $this->assertArrayHasKey('scope_1', $result);
        $this->assertArrayHasKey('scope_2', $result);
        $this->assertArrayHasKey('scope_3', $result);
        $this->assertArrayHasKey('transactions', $result);

        $this->assertArrayHasKey('kg', $result['total_emissions']);
        $this->assertArrayHasKey('tonnes', $result['total_emissions']);
        $this->assertArrayHasKey('trend_percent', $result['total_emissions']);
        $this->assertArrayHasKey('trend_direction', $result['total_emissions']);
    }

    public function test_get_kpis_calculates_totals_correctly(): void
    {
        $assessment = Assessment::factory()
            ->forOrganization($this->organization)
            ->active()
            ->create();

        // Create specific emission amounts
        EmissionRecord::factory()->forAssessment($assessment)->scope1()->withAmount(1000)->create();
        EmissionRecord::factory()->forAssessment($assessment)->scope2()->withAmount(2000)->create();
        EmissionRecord::factory()->forAssessment($assessment)->scope3()->withAmount(3000)->create();

        $result = $this->service->getKpis($this->organization->id);

        $this->assertEquals(6000, $result['total_emissions']['kg']);
        $this->assertEquals(6, $result['total_emissions']['tonnes']);
        $this->assertEquals(1000, $result['scope_1']['kg']);
        $this->assertEquals(2000, $result['scope_2']['kg']);
        $this->assertEquals(3000, $result['scope_3']['kg']);
    }

    public function test_get_kpis_calculates_percentages_correctly(): void
    {
        $assessment = Assessment::factory()
            ->forOrganization($this->organization)
            ->active()
            ->create();

        EmissionRecord::factory()->forAssessment($assessment)->scope1()->withAmount(2500)->create();
        EmissionRecord::factory()->forAssessment($assessment)->scope2()->withAmount(2500)->create();
        EmissionRecord::factory()->forAssessment($assessment)->scope3()->withAmount(5000)->create();

        $result = $this->service->getKpis($this->organization->id);

        $this->assertEquals(25, $result['scope_1']['percent']);
        $this->assertEquals(25, $result['scope_2']['percent']);
        $this->assertEquals(50, $result['scope_3']['percent']);
    }

    public function test_get_scope_breakdown_returns_correct_structure(): void
    {
        $assessment = Assessment::factory()
            ->forOrganization($this->organization)
            ->active()
            ->create();

        EmissionRecord::factory()->forAssessment($assessment)->scope1()->create();
        EmissionRecord::factory()->forAssessment($assessment)->scope2()->create();
        EmissionRecord::factory()->forAssessment($assessment)->scope3()->create();

        $result = $this->service->getScopeBreakdown($this->organization->id);

        $this->assertIsArray($result);
        $this->assertCount(3, $result);

        foreach ($result as $scope) {
            $this->assertArrayHasKey('scope', $scope);
            $this->assertArrayHasKey('label', $scope);
            $this->assertArrayHasKey('value', $scope);
            $this->assertArrayHasKey('percent', $scope);
            $this->assertArrayHasKey('count', $scope);
            $this->assertArrayHasKey('color', $scope);
        }
    }

    public function test_get_monthly_trend_returns_correct_structure(): void
    {
        $assessment = Assessment::factory()
            ->forOrganization($this->organization)
            ->active()
            ->create();

        EmissionRecord::factory()->forAssessment($assessment)->create([
            'date' => '2024-01-15',
        ]);
        EmissionRecord::factory()->forAssessment($assessment)->create([
            'date' => '2024-02-15',
        ]);

        $result = $this->service->getMonthlyTrend(
            $this->organization->id,
            null,
            Carbon::parse('2024-01-01'),
            Carbon::parse('2024-03-31')
        );

        $this->assertArrayHasKey('categories', $result);
        $this->assertArrayHasKey('series', $result);
        $this->assertIsArray($result['categories']);
        $this->assertCount(3, $result['series']); // Scope 1, 2, 3
    }

    public function test_get_top_categories_returns_correct_structure(): void
    {
        $category = Category::factory()->scope1()->create();
        $assessment = Assessment::factory()
            ->forOrganization($this->organization)
            ->active()
            ->create();

        EmissionRecord::factory()
            ->forAssessment($assessment)
            ->create(['category_id' => $category->id]);

        $result = $this->service->getTopCategories($this->organization->id);

        $this->assertIsArray($result);
        if (count($result) > 0) {
            $first = $result[0];
            $this->assertArrayHasKey('id', $first);
            $this->assertArrayHasKey('name', $first);
            $this->assertArrayHasKey('code', $first);
            $this->assertArrayHasKey('scope', $first);
            $this->assertArrayHasKey('value', $first);
            $this->assertArrayHasKey('count', $first);
        }
    }

    public function test_get_top_categories_limits_results(): void
    {
        $assessment = Assessment::factory()
            ->forOrganization($this->organization)
            ->active()
            ->create();

        // Create 15 different categories with emissions
        for ($i = 0; $i < 15; $i++) {
            $category = Category::factory()->create();
            EmissionRecord::factory()
                ->forAssessment($assessment)
                ->withAmount(1000 * ($i + 1))
                ->create(['category_id' => $category->id]);
        }

        $result = $this->service->getTopCategories($this->organization->id, null, null, null, 5);

        $this->assertCount(5, $result);
    }

    public function test_get_intensity_metrics_returns_correct_structure(): void
    {
        $this->organization->update(['employee_count' => 50]);

        $result = $this->service->getIntensityMetrics($this->organization->id);

        $this->assertArrayHasKey('per_employee', $result);
        $this->assertArrayHasKey('per_1000_eur', $result);
        $this->assertArrayHasKey('total_spend', $result);
        $this->assertArrayHasKey('employee_count', $result);
    }

    public function test_filters_by_date_range(): void
    {
        $assessment = Assessment::factory()
            ->forOrganization($this->organization)
            ->active()
            ->create();

        EmissionRecord::factory()->forAssessment($assessment)->withAmount(1000)->create([
            'date' => '2024-01-15',
        ]);
        EmissionRecord::factory()->forAssessment($assessment)->withAmount(2000)->create([
            'date' => '2024-06-15',
        ]);
        EmissionRecord::factory()->forAssessment($assessment)->withAmount(3000)->create([
            'date' => '2024-12-15',
        ]);

        $result = $this->service->getKpis(
            $this->organization->id,
            null,
            Carbon::parse('2024-01-01'),
            Carbon::parse('2024-06-30')
        );

        // Should include Jan and June emissions, not December
        $this->assertEquals(3000, $result['total_emissions']['kg']);
    }

    public function test_handles_empty_data(): void
    {
        $result = $this->service->getKpis($this->organization->id);

        $this->assertEquals(0, $result['total_emissions']['kg']);
        $this->assertEquals(0, $result['total_emissions']['tonnes']);
        $this->assertEquals(0, $result['scope_1']['kg']);
        $this->assertEquals(0, $result['scope_2']['kg']);
        $this->assertEquals(0, $result['scope_3']['kg']);
    }

    public function test_get_dashboard_data_returns_all_sections(): void
    {
        $result = $this->service->getDashboardData($this->organization->id);

        $this->assertArrayHasKey('kpis', $result);
        $this->assertArrayHasKey('scope_breakdown', $result);
        $this->assertArrayHasKey('monthly_trend', $result);
        $this->assertArrayHasKey('top_categories', $result);
        $this->assertArrayHasKey('recent_transactions', $result);
        $this->assertArrayHasKey('period', $result);
    }

    public function test_trend_calculation(): void
    {
        $assessment = Assessment::factory()
            ->forOrganization($this->organization)
            ->active()
            ->create();

        // Current period emissions
        EmissionRecord::factory()->forAssessment($assessment)->withAmount(2000)->create([
            'date' => now()->subDays(30),
        ]);

        // Previous period emissions (higher, so trend should be negative)
        EmissionRecord::factory()->forAssessment($assessment)->withAmount(4000)->create([
            'date' => now()->subDays(60),
        ]);

        $result = $this->service->getKpis(
            $this->organization->id,
            null,
            now()->subDays(31),
            now()
        );

        // Trend should show decrease (negative)
        $this->assertEquals('down', $result['total_emissions']['trend_direction']);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
