<?php

namespace Tests\Browser;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTruncation;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

/**
 * Browser tests for Employee Engagement module - T180-T182
 */
class EmployeeEngagementTest extends DuskTestCase
{
    use DatabaseTruncation;

    private User $user;

    private Organization $organization;

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
    }

    public function test_user_can_access_employee_engagement_page(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->visit('/engage/employees')
                ->assertSee(__('linscarbon.engage.title'))
                ->assertSee(__('linscarbon.engage.tabs.quiz'))
                ->assertSee(__('linscarbon.engage.tabs.calculator'))
                ->assertSee(__('linscarbon.engage.tabs.challenges'))
                ->assertSee(__('linscarbon.engage.tabs.leaderboard'));
        });
    }

    public function test_quiz_tab_shows_first_question(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->visit('/engage/employees')
                ->assertSee(__('linscarbon.engage.quiz.question'))
                ->assertSee('1 / 5');
        });
    }

    public function test_user_can_answer_quiz_questions(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->visit('/engage/employees')
                // Click on first answer option
                ->waitFor('button[wire\\:click*="answerQuiz"]')
                ->click('button[wire\\:click*="answerQuiz(\'a\')"]')
                ->waitForText('2 / 5')
                ->assertSee('2 / 5');
        });
    }

    public function test_user_can_complete_quiz(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->visit('/engage/employees');

            // Answer all 5 questions
            for ($i = 0; $i < 5; $i++) {
                $browser->waitFor('button[wire\\:click*="answerQuiz"]')
                    ->click('button[wire\\:click*="answerQuiz(\'b\')"]')
                    ->pause(300);
            }

            // Should see quiz completed
            $browser->waitForText(__('linscarbon.engage.quiz.completed'))
                ->assertSee(__('linscarbon.engage.quiz.completed'))
                ->assertSee('%');
        });
    }

    public function test_user_can_reset_quiz(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->visit('/engage/employees');

            // Complete quiz
            for ($i = 0; $i < 5; $i++) {
                $browser->waitFor('button[wire\\:click*="answerQuiz"]')
                    ->click('button[wire\\:click*="answerQuiz(\'a\')"]')
                    ->pause(300);
            }

            // Reset quiz
            $browser->waitForText(__('linscarbon.engage.quiz.retry'))
                ->click('button[wire\\:click="resetQuiz"]')
                ->waitForText('1 / 5')
                ->assertSee('1 / 5');
        });
    }

    public function test_user_can_switch_to_calculator_tab(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->visit('/engage/employees')
                ->click('button[wire\\:click="setActiveTab(\'calculator\')"]')
                ->waitForText(__('linscarbon.engage.calculator.title'))
                ->assertSee(__('linscarbon.engage.calculator.title'))
                ->assertSee(__('linscarbon.engage.calculator.commute_distance'));
        });
    }

    public function test_user_can_calculate_footprint(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->visit('/engage/employees')
                ->click('button[wire\\:click="setActiveTab(\'calculator\')"]')
                ->waitForText(__('linscarbon.engage.calculator.title'))
                // Fill in some values
                ->type('input[wire\\:model="calculatorInputs.commute_km"]', '25')
                ->select('select[wire\\:model="calculatorInputs.commute_mode"]', 'car_petrol')
                ->select('select[wire\\:model="calculatorInputs.diet"]', 'mixed')
                // Calculate
                ->click('button[wire\\:click="calculateFootprint"]')
                ->waitForText(__('linscarbon.engage.calculator.your_footprint'))
                ->assertSee(__('linscarbon.engage.calculator.tonnes_year'))
                ->assertSee(__('linscarbon.engage.calculator.breakdown'));
        });
    }

    public function test_user_can_switch_to_challenges_tab(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->visit('/engage/employees')
                ->click('button[wire\\:click="setActiveTab(\'challenges\')"]')
                ->waitForText(__('linscarbon.engage.challenges.no_car_week'))
                ->assertSee(__('linscarbon.engage.challenges.no_car_week'))
                ->assertSee(__('linscarbon.engage.challenges.meatless_monday'))
                ->assertSee(__('linscarbon.engage.challenges.join'));
        });
    }

    public function test_user_can_join_challenge(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->visit('/engage/employees')
                ->click('button[wire\\:click="setActiveTab(\'challenges\')"]')
                ->waitForText(__('linscarbon.engage.challenges.no_car_week'))
                // Click join on the first challenge
                ->click('button[wire\\:click="joinChallenge(\'no_car_week\')"]')
                ->waitForText(__('linscarbon.engage.challenges.mark_complete'))
                ->assertSee(__('linscarbon.engage.challenges.mark_complete'))
                ->assertSee(__('linscarbon.engage.challenges.leave'));
        });
    }

    public function test_user_can_switch_to_leaderboard_tab(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->visit('/engage/employees')
                ->click('button[wire\\:click="setActiveTab(\'leaderboard\')"]')
                ->waitForText(__('linscarbon.engage.leaderboard.title'))
                ->assertSee(__('linscarbon.engage.leaderboard.title'))
                ->assertSee(__('linscarbon.engage.leaderboard.participate'));
        });
    }

    public function test_user_can_toggle_leaderboard_participation(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->visit('/engage/employees')
                ->click('button[wire\\:click="setActiveTab(\'leaderboard\')"]')
                ->waitForText(__('linscarbon.engage.leaderboard.participate'))
                // Toggle participation
                ->click('button[wire\\:click="toggleLeaderboardParticipation"]')
                ->pause(500)
                // Should now show rank info
                ->assertSee(__('linscarbon.engage.leaderboard.your_rank'));
        });
    }

    public function test_points_display_visible(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->visit('/engage/employees')
                ->assertSee(__('linscarbon.engage.your_points'));
        });
    }

    public function test_responsive_tabs_on_mobile(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->resize(375, 812) // iPhone X
                ->visit('/engage/employees')
                ->assertSee(__('linscarbon.engage.tabs.quiz'))
                // Tabs should be scrollable on mobile
                ->assertPresent('nav[aria-label="Tabs"]');
        });
    }
}
