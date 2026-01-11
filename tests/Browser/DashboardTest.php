<?php

namespace Tests\Browser;

use App\Models\Assessment;
use App\Models\EmissionRecord;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

/**
 * Browser tests for Dashboard - T097
 */
class DashboardTest extends DuskTestCase
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
            'employee_count' => 50,
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

        // Create some emissions for the dashboard
        EmissionRecord::factory()
            ->count(5)
            ->forAssessment($this->assessment)
            ->scope1()
            ->create();

        EmissionRecord::factory()
            ->count(3)
            ->forAssessment($this->assessment)
            ->scope2()
            ->create();

        EmissionRecord::factory()
            ->count(7)
            ->forAssessment($this->assessment)
            ->scope3()
            ->create();
    }

    public function test_user_sees_dashboard(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->visit('/dashboard')
                ->assertSee('Tableau de bord')
                ->assertSee('Bilan carbone');
        });
    }

    public function test_dashboard_shows_total_emissions(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->visit('/dashboard')
                ->assertPresent('@total-emissions-card')
                ->assertSee('tCO2e');
        });
    }

    public function test_dashboard_shows_scope_breakdown(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->visit('/dashboard')
                ->assertPresent('@scope-breakdown-chart')
                ->assertSee('Scope 1')
                ->assertSee('Scope 2')
                ->assertSee('Scope 3');
        });
    }

    public function test_dashboard_shows_trend_chart(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->visit('/dashboard')
                ->assertPresent('@trend-chart')
                ->assertSee('Évolution mensuelle');
        });
    }

    public function test_dashboard_shows_equivalents(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->visit('/dashboard')
                ->assertPresent('@carbon-equivalents')
                ->assertSee('équivalent');
        });
    }

    public function test_dashboard_shows_progress_circle(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->visit('/dashboard')
                ->assertPresent('@progress-circle')
                ->assertSee('%');
        });
    }

    public function test_dashboard_shows_recent_transactions(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->visit('/dashboard')
                ->assertPresent('@recent-transactions')
                ->assertSee('Transactions récentes');
        });
    }

    public function test_year_filter_works(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->visit('/dashboard')
                ->assertPresent('@year-selector')
                ->select('@year-selector', (string) now()->year)
                ->waitFor('@dashboard-loading')
                ->waitUntilMissing('@dashboard-loading');
        });
    }

    public function test_site_filter_works(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->visit('/dashboard')
                ->assertPresent('@site-filter')
                ->click('@site-filter')
                ->waitFor('@site-options');
        });
    }

    public function test_dashboard_kpi_cards_clickable(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->visit('/dashboard')
                ->click('@scope-1-card')
                ->waitForLocation('/emissions')
                ->assertQueryStringHas('scope', '1');
        });
    }

    public function test_dashboard_shows_intensity_metrics(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->visit('/dashboard')
                ->assertSee('par employé')
                ->assertSee('par 1000€');
        });
    }

    public function test_dashboard_export_button(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->visit('/dashboard')
                ->assertPresent('@export-dashboard-button')
                ->click('@export-dashboard-button')
                ->waitFor('@export-options')
                ->assertSee('PDF')
                ->assertSee('Excel');
        });
    }

    public function test_empty_dashboard_shows_cta(): void
    {
        // Create a user with no emissions
        $emptyOrg = Organization::factory()->create([
            'onboarding_completed' => true,
        ]);
        $emptyUser = User::factory()->create([
            'organization_id' => $emptyOrg->id,
            'email_verified_at' => now(),
        ]);
        Assessment::factory()
            ->forOrganization($emptyOrg)
            ->active()
            ->create();

        $this->browse(function (Browser $browser) use ($emptyUser) {
            $browser->loginAs($emptyUser)
                ->visit('/dashboard')
                ->assertSee('Commencez votre bilan')
                ->assertSee('Ajouter des émissions');
        });
    }

    public function test_dashboard_responsive_on_mobile(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->resize(375, 812) // iPhone X dimensions
                ->visit('/dashboard')
                ->assertPresent('@mobile-menu-button')
                ->click('@mobile-menu-button')
                ->waitFor('@mobile-menu')
                ->assertSee('Tableau de bord');
        });
    }

    public function test_dashboard_charts_are_interactive(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->visit('/dashboard')
                ->assertPresent('@scope-breakdown-chart')
                // Hover over chart to see tooltip
                ->mouseover('@scope-breakdown-chart')
                ->waitFor('.chart-tooltip')
                ->assertPresent('.chart-tooltip');
        });
    }

    public function test_quick_actions_visible(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->visit('/dashboard')
                ->assertPresent('@quick-actions')
                ->assertSee('Ajouter une émission')
                ->assertSee('Générer un rapport');
        });
    }

    public function test_training_section_visible(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->visit('/dashboard')
                ->assertPresent('@training-section')
                ->assertSee('Formation');
        });
    }

    public function test_evaluation_progress_visible(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->visit('/dashboard')
                ->assertPresent('@evaluation-progress')
                ->assertSee('Progression');
        });
    }
}
