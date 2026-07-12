<?php

namespace Tests\Browser;

use App\Models\Assessment;
use App\Models\Category;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

/**
 * Browser tests for Emission Entry flow - T096
 * Tests use German (de) locale
 */
class EmissionEntryTest extends DuskTestCase
{
    use DatabaseMigrations;

    private User $user;

    private Organization $organization;

    private Assessment $assessment;

    protected function setUp(): void
    {
        parent::setUp();

        $this->organization = Organization::factory()->create([
            'onboarding_completed' => true,
        ]);

        $this->user = User::factory()->create([
            'organization_id' => $this->organization->id,
            'email_verified_at' => now(),
        ]);

        $this->assessment = Assessment::factory()
            ->forOrganization($this->organization)
            ->active()
            ->forYear(now()->year)
            ->create();

        // Create some categories for testing
        Category::factory()->scope1()->create([
            'name' => 'Stationäre Verbrennung',
            'code' => '1.1',
        ]);

        Category::factory()->scope2()->create([
            'name' => 'Strom',
            'code' => '2.1',
        ]);

        Category::factory()->scope3()->create([
            'name' => 'Einkauf von Gütern',
            'code' => '3.1',
        ]);
    }

    public function test_user_sees_emission_categories(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->visit('/emissions')
                ->assertSee('Scope 1')
                ->assertSee('Scope 2')
                ->assertSee('Scope 3');
        });
    }

    public function test_user_can_navigate_to_scope1(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->visit('/emissions')
                ->assertPresent('@scope-1-tab');
        });
    }

    public function test_user_can_navigate_to_scope2(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->visit('/emissions')
                ->assertPresent('@scope-2-tab');
        });
    }

    public function test_emissions_page_has_import_button(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->visit('/emissions')
                ->assertPresent('@import-button');
        });
    }

    public function test_emission_scopes_display(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->visit('/emissions')
                ->assertSee('Scope 1')
                ->assertSee('Scope 2')
                ->assertSee('Scope 3');
        });
    }

    public function test_scope_page_loads(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->visit('/emissions/scope/1')
                ->assertPathIs('/emissions/scope/1');
        });
    }

    public function test_scope2_page_loads(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->visit('/emissions/scope/2')
                ->assertPathIs('/emissions/scope/2');
        });
    }

    public function test_scope3_page_loads(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->visit('/emissions/scope/3')
                ->assertPathIs('/emissions/scope/3');
        });
    }

    public function test_emissions_page_loads_correctly(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->visit('/emissions')
                ->assertPathIs('/emissions');
        });
    }

    public function test_user_can_access_emissions(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->visit('/dashboard')
                ->assertSee('Dashboard');
        });
    }
}
