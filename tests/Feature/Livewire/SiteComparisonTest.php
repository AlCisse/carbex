<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Sites\SiteComparison;
use App\Models\EmissionRecord;
use App\Models\Organization;
use App\Models\Site;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Feature tests for SiteComparison Livewire component - T174-T175
 */
class SiteComparisonTest extends TestCase
{
    use RefreshDatabase;

    private Organization $organization;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->organization = Organization::factory()->create();
        $this->user = User::factory()->create([
            'organization_id' => $this->organization->id,
        ]);
    }

    // ==================== Component Rendering ====================

    public function test_component_renders(): void
    {
        Livewire::actingAs($this->user)
            ->test(SiteComparison::class)
            ->assertStatus(200)
            ->assertSee(__('linscarbon.sites.comparison.title'));
    }

    public function test_component_shows_filters(): void
    {
        Livewire::actingAs($this->user)
            ->test(SiteComparison::class)
            ->assertSee(__('linscarbon.sites.comparison.year'))
            ->assertSee(__('linscarbon.sites.comparison.scope'))
            ->assertSee(__('linscarbon.sites.comparison.metric'));
    }

    public function test_component_shows_empty_state_without_sites(): void
    {
        Livewire::actingAs($this->user)
            ->test(SiteComparison::class)
            ->assertSee(__('linscarbon.sites.comparison.no_sites'));
    }

    // ==================== Sites Data ====================

    public function test_shows_sites_from_organization(): void
    {
        $site1 = Site::factory()->create([
            'organization_id' => $this->organization->id,
            'name' => 'Paris Office',
            'is_active' => true,
        ]);

        $site2 = Site::factory()->create([
            'organization_id' => $this->organization->id,
            'name' => 'Lyon Warehouse',
            'is_active' => true,
        ]);

        Livewire::actingAs($this->user)
            ->test(SiteComparison::class)
            ->assertSee('Paris Office')
            ->assertSee('Lyon Warehouse');
    }

    public function test_does_not_show_inactive_sites(): void
    {
        Site::factory()->create([
            'organization_id' => $this->organization->id,
            'name' => 'Active Site',
            'is_active' => true,
        ]);

        Site::factory()->create([
            'organization_id' => $this->organization->id,
            'name' => 'Inactive Site',
            'is_active' => false,
        ]);

        Livewire::actingAs($this->user)
            ->test(SiteComparison::class)
            ->assertSee('Active Site')
            ->assertDontSee('Inactive Site');
    }

    public function test_does_not_show_sites_from_other_organizations(): void
    {
        $otherOrg = Organization::factory()->create();

        Site::factory()->create([
            'organization_id' => $this->organization->id,
            'name' => 'My Site',
            'is_active' => true,
        ]);

        Site::factory()->create([
            'organization_id' => $otherOrg->id,
            'name' => 'Other Org Site',
            'is_active' => true,
        ]);

        Livewire::actingAs($this->user)
            ->test(SiteComparison::class)
            ->assertSee('My Site')
            ->assertDontSee('Other Org Site');
    }

    // ==================== Emissions Calculation ====================

    public function test_calculates_emissions_per_site(): void
    {
        $site = Site::factory()->create([
            'organization_id' => $this->organization->id,
            'name' => 'Test Site',
            'is_active' => true,
        ]);

        EmissionRecord::factory()->create([
            'organization_id' => $this->organization->id,
            'site_id' => $site->id,
            'scope' => 1,
            'co2e_kg' => 1000,
            'year' => now()->year,
        ]);

        EmissionRecord::factory()->create([
            'organization_id' => $this->organization->id,
            'site_id' => $site->id,
            'scope' => 2,
            'co2e_kg' => 500,
            'year' => now()->year,
        ]);

        $component = Livewire::actingAs($this->user)
            ->test(SiteComparison::class);

        $siteEmissions = $component->get('siteEmissions');
        $testSite = $siteEmissions->firstWhere('name', 'Test Site');

        $this->assertEquals(1000, $testSite['scope_1']);
        $this->assertEquals(500, $testSite['scope_2']);
        $this->assertEquals(1500, $testSite['total_co2e_kg']);
    }

    public function test_calculates_intensity_per_m2(): void
    {
        $site = Site::factory()->create([
            'organization_id' => $this->organization->id,
            'name' => 'Office',
            'floor_area_m2' => 100,
            'is_active' => true,
        ]);

        EmissionRecord::factory()->create([
            'organization_id' => $this->organization->id,
            'site_id' => $site->id,
            'scope' => 1,
            'co2e_kg' => 500,
            'year' => now()->year,
        ]);

        $component = Livewire::actingAs($this->user)
            ->test(SiteComparison::class);

        $siteEmissions = $component->get('siteEmissions');
        $office = $siteEmissions->firstWhere('name', 'Office');

        $this->assertEquals(5, $office['per_m2']); // 500kg / 100m² = 5 kg/m²
    }

    public function test_calculates_intensity_per_employee(): void
    {
        $site = Site::factory()->create([
            'organization_id' => $this->organization->id,
            'name' => 'Factory',
            'employee_count' => 10,
            'is_active' => true,
        ]);

        EmissionRecord::factory()->create([
            'organization_id' => $this->organization->id,
            'site_id' => $site->id,
            'scope' => 1,
            'co2e_kg' => 2000,
            'year' => now()->year,
        ]);

        $component = Livewire::actingAs($this->user)
            ->test(SiteComparison::class);

        $siteEmissions = $component->get('siteEmissions');
        $factory = $siteEmissions->firstWhere('name', 'Factory');

        $this->assertEquals(200, $factory['per_employee']); // 2000kg / 10 = 200 kg/employee
    }

    // ==================== Filters ====================

    public function test_can_filter_by_year(): void
    {
        $site = Site::factory()->create([
            'organization_id' => $this->organization->id,
            'name' => 'Site A',
            'is_active' => true,
        ]);

        EmissionRecord::factory()->create([
            'organization_id' => $this->organization->id,
            'site_id' => $site->id,
            'scope' => 1,
            'co2e_kg' => 1000,
            'year' => 2024,
        ]);

        EmissionRecord::factory()->create([
            'organization_id' => $this->organization->id,
            'site_id' => $site->id,
            'scope' => 1,
            'co2e_kg' => 2000,
            'year' => 2025,
        ]);

        $component = Livewire::actingAs($this->user)
            ->test(SiteComparison::class)
            ->set('selectedYear', 2024);

        $siteEmissions = $component->get('siteEmissions');
        $siteA = $siteEmissions->firstWhere('name', 'Site A');

        $this->assertEquals(1000, $siteA['total_co2e_kg']);
    }

    public function test_can_filter_by_scope(): void
    {
        $site = Site::factory()->create([
            'organization_id' => $this->organization->id,
            'name' => 'Site B',
            'is_active' => true,
        ]);

        EmissionRecord::factory()->create([
            'organization_id' => $this->organization->id,
            'site_id' => $site->id,
            'scope' => 1,
            'co2e_kg' => 1000,
            'year' => now()->year,
        ]);

        EmissionRecord::factory()->create([
            'organization_id' => $this->organization->id,
            'site_id' => $site->id,
            'scope' => 2,
            'co2e_kg' => 500,
            'year' => now()->year,
        ]);

        $component = Livewire::actingAs($this->user)
            ->test(SiteComparison::class)
            ->set('selectedScope', 1);

        $siteEmissions = $component->get('siteEmissions');
        $siteB = $siteEmissions->firstWhere('name', 'Site B');

        // When filtering by scope, total should only include that scope
        $this->assertEquals(1000, $siteB['scope_1']);
    }

    public function test_can_change_sort_order(): void
    {
        Site::factory()->create([
            'organization_id' => $this->organization->id,
            'name' => 'Alpha',
            'is_active' => true,
        ]);

        Site::factory()->create([
            'organization_id' => $this->organization->id,
            'name' => 'Zulu',
            'is_active' => true,
        ]);

        $component = Livewire::actingAs($this->user)
            ->test(SiteComparison::class)
            ->set('sortBy', 'name_asc');

        $siteEmissions = $component->get('siteEmissions');
        $this->assertEquals('Alpha', $siteEmissions->first()['name']);

        $component->set('sortBy', 'name_desc');
        $siteEmissions = $component->get('siteEmissions');
        $this->assertEquals('Zulu', $siteEmissions->first()['name']);
    }

    // ==================== Summary Stats ====================

    public function test_calculates_total_emissions(): void
    {
        $site1 = Site::factory()->create([
            'organization_id' => $this->organization->id,
            'is_active' => true,
        ]);

        $site2 = Site::factory()->create([
            'organization_id' => $this->organization->id,
            'is_active' => true,
        ]);

        EmissionRecord::factory()->create([
            'organization_id' => $this->organization->id,
            'site_id' => $site1->id,
            'scope' => 1,
            'co2e_kg' => 1000,
            'year' => now()->year,
        ]);

        EmissionRecord::factory()->create([
            'organization_id' => $this->organization->id,
            'site_id' => $site2->id,
            'scope' => 1,
            'co2e_kg' => 2000,
            'year' => now()->year,
        ]);

        $component = Livewire::actingAs($this->user)
            ->test(SiteComparison::class);

        $this->assertEquals(3, $component->get('totalEmissions')); // 3000 kg = 3 tonnes
    }

    public function test_identifies_top_emitter(): void
    {
        $site1 = Site::factory()->create([
            'organization_id' => $this->organization->id,
            'name' => 'Small Site',
            'is_active' => true,
        ]);

        $site2 = Site::factory()->create([
            'organization_id' => $this->organization->id,
            'name' => 'Big Site',
            'is_active' => true,
        ]);

        EmissionRecord::factory()->create([
            'organization_id' => $this->organization->id,
            'site_id' => $site1->id,
            'scope' => 1,
            'co2e_kg' => 100,
            'year' => now()->year,
        ]);

        EmissionRecord::factory()->create([
            'organization_id' => $this->organization->id,
            'site_id' => $site2->id,
            'scope' => 1,
            'co2e_kg' => 5000,
            'year' => now()->year,
        ]);

        $component = Livewire::actingAs($this->user)
            ->test(SiteComparison::class);

        $topEmitter = $component->get('topEmitter');
        $this->assertEquals('Big Site', $topEmitter['name']);
    }

    // ==================== Recommendations ====================

    public function test_generates_high_emitter_recommendation(): void
    {
        $site1 = Site::factory()->create([
            'organization_id' => $this->organization->id,
            'name' => 'Normal Site',
            'is_active' => true,
        ]);

        $site2 = Site::factory()->create([
            'organization_id' => $this->organization->id,
            'name' => 'High Emitter',
            'is_active' => true,
        ]);

        EmissionRecord::factory()->create([
            'organization_id' => $this->organization->id,
            'site_id' => $site1->id,
            'scope' => 1,
            'co2e_kg' => 1000,
            'year' => now()->year,
        ]);

        EmissionRecord::factory()->create([
            'organization_id' => $this->organization->id,
            'site_id' => $site2->id,
            'scope' => 1,
            'co2e_kg' => 10000,
            'year' => now()->year,
        ]);

        $component = Livewire::actingAs($this->user)
            ->test(SiteComparison::class);

        $recommendations = $component->get('recommendations');

        // High Emitter should have a recommendation
        $this->assertTrue($recommendations->has($site2->id));
    }

    public function test_generates_missing_data_recommendations(): void
    {
        $site = Site::factory()->create([
            'organization_id' => $this->organization->id,
            'name' => 'Incomplete Site',
            'floor_area_m2' => null,
            'employee_count' => null,
            'is_active' => true,
        ]);

        EmissionRecord::factory()->create([
            'organization_id' => $this->organization->id,
            'site_id' => $site->id,
            'scope' => 1,
            'co2e_kg' => 100,
            'year' => now()->year,
        ]);

        $component = Livewire::actingAs($this->user)
            ->test(SiteComparison::class);

        $recommendations = $component->get('recommendations');

        $this->assertTrue($recommendations->has($site->id));
        $siteRecs = $recommendations->get($site->id)['items'];

        // Should have recommendations for missing area and employees
        $messages = $siteRecs->pluck('message')->toArray();
        $this->assertContains(__('linscarbon.sites.recommendations.missing_area'), $messages);
        $this->assertContains(__('linscarbon.sites.recommendations.missing_employees'), $messages);
    }

    // ==================== Route Test ====================

    public function test_sites_comparison_route_exists(): void
    {
        $response = $this->actingAs($this->user)
            ->get(route('sites.comparison'));

        $response->assertStatus(200);
    }

    public function test_sites_comparison_route_requires_auth(): void
    {
        $response = $this->get(route('sites.comparison'));

        $response->assertRedirect(route('login'));
    }
}
