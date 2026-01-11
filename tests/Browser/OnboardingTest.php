<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

/**
 * Browser tests for Onboarding flow - T095
 * Tests use German (de) locale
 */
class OnboardingTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_guest_sees_welcome_page(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->assertSee('Carbex')
                ->assertSee('Kostenlos starten');
        });
    }

    public function test_guest_can_navigate_to_login(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->clickLink('Anmelden')
                ->assertPathIs('/login');
        });
    }

    public function test_guest_can_navigate_to_register(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->clickLink('Kostenlos starten')
                ->assertPathIs('/register');
        });
    }

    public function test_user_can_register(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/register')
                // Step 1: Account info
                ->waitFor('#name')
                ->type('name', 'Test User')
                ->type('email', 'testuser@example.com')
                // Password must have: 8+ chars, mixed case, numbers, symbols
                ->type('password', 'Password123!')
                ->type('password_confirmation', 'Password123!')
                ->press('Weiter')
                ->pause(1500)
                // Step 2: Organization info should appear
                ->waitFor('#organization_name', 10)
                ->assertSee('Organisation');
        });
    }

    public function test_onboarding_step1_company_info(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/onboarding')
                ->waitFor('@onboarding-step-1')
                ->assertSee('Unternehmensinformationen')
                ->type('company_name', 'Mein Testunternehmen')
                ->select('sector', 'technology')
                ->select('size', 'small');
        });
    }

    public function test_onboarding_step2_site_configuration(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/onboarding?step=2')
                ->assertSee('Standortkonfiguration')
                ->type('site_name', 'Hauptsitz')
                ->type('site_address', 'Hauptstraße 1')
                ->type('site_city', 'Berlin')
                ->type('site_postal_code', '10115')
                ->press('Weiter')
                ->waitFor('@onboarding-step-3');
        });
    }

    public function test_onboarding_step3_bank_connection(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/onboarding?step=3')
                ->assertSee('Bankverbindung')
                // Can skip bank connection
                ->press('Diesen Schritt überspringen')
                ->waitFor('@onboarding-step-4');
        });
    }

    public function test_onboarding_completion_redirects_to_dashboard(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/onboarding?step=4')
                ->assertSee('Herzlichen Glückwunsch')
                ->press('Zum Dashboard')
                ->waitForLocation('/dashboard')
                ->assertPathIs('/dashboard');
        });
    }

    public function test_onboarding_progress_indicator(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/onboarding')
                ->assertPresent('.progress-indicator')
                ->assertSeeIn('.progress-indicator', '1')
                ->assertSeeIn('.progress-indicator', '4');
        });
    }

    public function test_user_can_go_back_in_onboarding(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/onboarding?step=2')
                ->assertSee('Standortkonfiguration')
                ->press('Zurück')
                ->waitFor('@onboarding-step-1')
                ->assertSee('Unternehmensinformationen');
        });
    }

    public function test_validation_errors_displayed(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/onboarding')
                ->press('Weiter')
                ->waitFor('.validation-error')
                ->assertSee('Der Unternehmensname ist erforderlich');
        });
    }
}
