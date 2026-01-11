<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

/**
 * Browser tests for Onboarding flow - T095
 */
class OnboardingTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_guest_sees_welcome_page(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->assertSee('Carbex')
                ->assertSee('Essai gratuit');
        });
    }

    public function test_guest_can_navigate_to_login(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->clickLink('Se connecter')
                ->assertPathIs('/login');
        });
    }

    public function test_guest_can_navigate_to_register(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->clickLink('Essai gratuit')
                ->assertPathIs('/register');
        });
    }

    public function test_user_can_register(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/register')
                ->type('name', 'Test User')
                ->type('email', 'testuser@example.com')
                ->type('password', 'password123')
                ->type('password_confirmation', 'password123')
                ->press('S\'inscrire')
                ->waitForLocation('/onboarding')
                ->assertPathIs('/onboarding');
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
                ->assertSee('Informations entreprise')
                ->type('company_name', 'Ma Société Test')
                ->type('siret', '12345678901234')
                ->select('sector', 'technology')
                ->select('size', 'small')
                ->press('Suivant')
                ->waitFor('@onboarding-step-2');
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
                ->assertSee('Configuration des sites')
                ->type('site_name', 'Siège social')
                ->type('site_address', '1 rue de Paris')
                ->type('site_city', 'Paris')
                ->type('site_postal_code', '75001')
                ->press('Suivant')
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
                ->assertSee('Connexion bancaire')
                // Can skip bank connection
                ->press('Passer cette étape')
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
                ->assertSee('Félicitations')
                ->press('Accéder au tableau de bord')
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
                ->assertSee('Configuration des sites')
                ->press('Précédent')
                ->waitFor('@onboarding-step-1')
                ->assertSee('Informations entreprise');
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
                ->press('Suivant')
                ->waitFor('.validation-error')
                ->assertSee('Le nom de l\'entreprise est requis');
        });
    }
}
