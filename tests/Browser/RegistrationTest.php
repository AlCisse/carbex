<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class RegistrationTest extends DuskTestCase
{
    /**
     * Test user registration flow.
     */
    public function testUserCanRegister(): void
    {
        $email = 'dusk_test_' . time() . '@linscarbon.io';

        $this->browse(function (Browser $browser) use ($email) {
            $browser->visit('/register')
                    ->assertSee('LinsCarbon')
                    // Step 1: User info
                    ->type('name', 'Dusk Test User')
                    ->type('email', $email)
                    ->type('password', 'DuskTest2026!#')
                    ->type('password_confirmation', 'DuskTest2026!#')
                    ->press('Weiter')
                    ->pause(2000)
                    // Step 2: Organization info + terms
                    ->type('organization_name', 'Dusk Test Company')
                    ->select('#country', 'DE')
                    ->select('#organization_size', '51-250')
                    ->check('#accept_terms')
                    ->check('#accept_privacy')
                    ->press('Konto erstellen')
                    ->pause(3000)
                    // Registration success - redirected to email verification or dashboard
                    ->assertPathIsNot('/register')
                    ->screenshot('registration-success');
        });
    }
}
