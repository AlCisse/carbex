<?php

namespace Tests\Browser;

use App\Models\Assessment;
use App\Models\Category;
use App\Models\EmissionFactor;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

/**
 * Browser tests for Emission Entry flow - T096
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

        // Create some categories and factors for testing
        Category::factory()->scope1()->create([
            'name' => 'Combustion fixe',
            'code' => '1.1',
        ]);

        Category::factory()->scope2()->create([
            'name' => 'Électricité',
            'code' => '2.1',
        ]);

        Category::factory()->scope3()->create([
            'name' => 'Achats de biens',
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

    public function test_user_can_select_scope(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->visit('/emissions')
                ->click('@scope-1-tab')
                ->assertSee('Combustion fixe');
        });
    }

    public function test_user_can_open_emission_entry_form(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->visit('/emissions')
                ->click('@add-emission-button')
                ->waitFor('@emission-form-modal')
                ->assertSee('Nouvelle émission');
        });
    }

    public function test_user_can_select_category(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->visit('/emissions')
                ->click('@add-emission-button')
                ->waitFor('@emission-form-modal')
                ->click('@category-selector')
                ->waitFor('@category-options')
                ->click('@category-option-1-1');
        });
    }

    public function test_user_can_enter_quantity(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->visit('/emissions')
                ->click('@add-emission-button')
                ->waitFor('@emission-form-modal')
                ->type('quantity', '1000')
                ->assertInputValue('quantity', '1000');
        });
    }

    public function test_user_can_select_unit(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->visit('/emissions')
                ->click('@add-emission-button')
                ->waitFor('@emission-form-modal')
                ->select('unit', 'kWh')
                ->assertSelected('unit', 'kWh');
        });
    }

    public function test_emission_calculation_preview(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->visit('/emissions')
                ->click('@add-emission-button')
                ->waitFor('@emission-form-modal')
                ->select('category', '2.1')
                ->type('quantity', '10000')
                ->select('unit', 'kWh')
                ->waitFor('@emission-preview')
                ->assertPresent('@emission-preview')
                ->assertSee('kg CO2e');
        });
    }

    public function test_user_can_save_emission(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->visit('/emissions')
                ->click('@add-emission-button')
                ->waitFor('@emission-form-modal')
                ->select('category', '1.1')
                ->type('quantity', '500')
                ->select('unit', 'L')
                ->type('date', now()->format('Y-m-d'))
                ->press('Enregistrer')
                ->waitForText('Émission enregistrée')
                ->assertSee('Émission enregistrée');
        });
    }

    public function test_emission_appears_in_list_after_save(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->visit('/emissions')
                ->click('@add-emission-button')
                ->waitFor('@emission-form-modal')
                ->select('category', '1.1')
                ->type('quantity', '750')
                ->select('unit', 'L')
                ->press('Enregistrer')
                ->waitForText('Émission enregistrée')
                ->waitForReload()
                ->assertSee('750');
        });
    }

    public function test_user_can_edit_emission(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->visit('/emissions')
                // Assuming there's an existing emission in the list
                ->click('@emission-row-1')
                ->waitFor('@emission-edit-modal')
                ->clear('quantity')
                ->type('quantity', '999')
                ->press('Mettre à jour')
                ->waitForText('Émission mise à jour')
                ->assertSee('999');
        });
    }

    public function test_user_can_delete_emission(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->visit('/emissions')
                ->click('@emission-row-1')
                ->waitFor('@emission-edit-modal')
                ->click('@delete-emission-button')
                ->waitFor('@confirm-delete-modal')
                ->press('Confirmer la suppression')
                ->waitForText('Émission supprimée')
                ->assertSee('Émission supprimée');
        });
    }

    public function test_validation_required_fields(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->visit('/emissions')
                ->click('@add-emission-button')
                ->waitFor('@emission-form-modal')
                ->press('Enregistrer')
                ->waitFor('.validation-error')
                ->assertSee('La catégorie est requise')
                ->assertSee('La quantité est requise');
        });
    }

    public function test_scope_filter_works(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->visit('/emissions')
                ->click('@scope-2-tab')
                ->waitFor('@emissions-list')
                ->assertSee('Scope 2')
                ->assertDontSee('Combustion fixe');
        });
    }

    public function test_date_filter_works(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->visit('/emissions')
                ->type('date_from', '2024-01-01')
                ->type('date_to', '2024-06-30')
                ->press('Filtrer')
                ->waitFor('@emissions-list');
        });
    }

    public function test_bulk_import_modal(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->visit('/emissions')
                ->click('@import-button')
                ->waitFor('@import-modal')
                ->assertSee('Importer des émissions')
                ->assertSee('Télécharger le modèle');
        });
    }
}
