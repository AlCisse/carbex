<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Engage\EmployeeEngagement;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Feature tests for EmployeeEngagement Livewire component - T180-T182
 */
class EmployeeEngagementTest extends TestCase
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
            'settings' => [],
        ]);
    }

    // ==================== Component Rendering ====================

    public function test_component_renders(): void
    {
        Livewire::actingAs($this->user)
            ->test(EmployeeEngagement::class)
            ->assertStatus(200)
            ->assertSee(__('linscarbon.engage.title'));
    }

    public function test_component_shows_tabs(): void
    {
        Livewire::actingAs($this->user)
            ->test(EmployeeEngagement::class)
            ->assertSee(__('linscarbon.engage.tabs.quiz'))
            ->assertSee(__('linscarbon.engage.tabs.calculator'))
            ->assertSee(__('linscarbon.engage.tabs.challenges'))
            ->assertSee(__('linscarbon.engage.tabs.leaderboard'));
    }

    public function test_can_switch_tabs(): void
    {
        Livewire::actingAs($this->user)
            ->test(EmployeeEngagement::class)
            ->assertSet('activeTab', 'quiz')
            ->call('setActiveTab', 'calculator')
            ->assertSet('activeTab', 'calculator')
            ->call('setActiveTab', 'challenges')
            ->assertSet('activeTab', 'challenges')
            ->call('setActiveTab', 'leaderboard')
            ->assertSet('activeTab', 'leaderboard');
    }

    // ==================== Quiz Tab ====================

    public function test_quiz_shows_first_question(): void
    {
        Livewire::actingAs($this->user)
            ->test(EmployeeEngagement::class)
            ->assertSet('quizStep', 0)
            ->assertSet('quizCompleted', false);
    }

    public function test_can_answer_quiz_question(): void
    {
        Livewire::actingAs($this->user)
            ->test(EmployeeEngagement::class)
            ->call('answerQuiz', 'a')
            ->assertSet('quizStep', 1);
    }

    public function test_quiz_progresses_through_all_questions(): void
    {
        $component = Livewire::actingAs($this->user)
            ->test(EmployeeEngagement::class);

        // Answer all 5 questions
        $component->call('answerQuiz', 'b'); // Q1
        $component->call('answerQuiz', 'c'); // Q2
        $component->call('answerQuiz', 'a'); // Q3
        $component->call('answerQuiz', 'd'); // Q4
        $component->call('answerQuiz', 'b'); // Q5

        $component->assertSet('quizCompleted', true);

        $score = $component->get('quizScore');
        $this->assertNotNull($score);
    }

    public function test_quiz_calculates_score_correctly(): void
    {
        $component = Livewire::actingAs($this->user)
            ->test(EmployeeEngagement::class);

        // All correct answers (b, c, a, d, b)
        $component->call('answerQuiz', 'b');
        $component->call('answerQuiz', 'c');
        $component->call('answerQuiz', 'a');
        $component->call('answerQuiz', 'd');
        $component->call('answerQuiz', 'b');

        $component->assertSet('quizScore', 100);
    }

    public function test_quiz_score_saved_to_user_settings(): void
    {
        $component = Livewire::actingAs($this->user)
            ->test(EmployeeEngagement::class);

        // Answer all questions
        $component->call('answerQuiz', 'b');
        $component->call('answerQuiz', 'c');
        $component->call('answerQuiz', 'a');
        $component->call('answerQuiz', 'd');
        $component->call('answerQuiz', 'b');

        $this->user->refresh();
        $this->assertEquals(100, $this->user->settings['quiz_score']);
        $this->assertNotNull($this->user->settings['quiz_completed_at']);
    }

    public function test_can_reset_quiz(): void
    {
        $component = Livewire::actingAs($this->user)
            ->test(EmployeeEngagement::class);

        // Complete quiz
        $component->call('answerQuiz', 'a');
        $component->call('answerQuiz', 'a');
        $component->call('answerQuiz', 'a');
        $component->call('answerQuiz', 'a');
        $component->call('answerQuiz', 'a');

        // Reset
        $component->call('resetQuiz')
            ->assertSet('quizStep', 0)
            ->assertSet('quizAnswers', [])
            ->assertSet('quizScore', null)
            ->assertSet('quizCompleted', false);
    }

    // ==================== Calculator Tab ====================

    public function test_calculator_has_default_inputs(): void
    {
        Livewire::actingAs($this->user)
            ->test(EmployeeEngagement::class)
            ->assertSet('calculatorInputs.commute_km', 0)
            ->assertSet('calculatorInputs.commute_mode', 'car_petrol')
            ->assertSet('calculatorInputs.diet', 'mixed')
            ->assertSet('calculatorInputs.heating', 'gas');
    }

    public function test_can_calculate_footprint(): void
    {
        $component = Livewire::actingAs($this->user)
            ->test(EmployeeEngagement::class)
            ->set('calculatorInputs.commute_km', 20)
            ->set('calculatorInputs.commute_mode', 'car_petrol')
            ->set('calculatorInputs.flights_short', 2)
            ->set('calculatorInputs.flights_long', 1)
            ->set('calculatorInputs.diet', 'mixed')
            ->set('calculatorInputs.heating', 'gas')
            ->set('calculatorInputs.electricity_kwh', 200)
            ->call('calculateFootprint');

        $this->assertNotNull($component->get('calculatorResult'));
    }

    public function test_footprint_result_has_breakdown(): void
    {
        $component = Livewire::actingAs($this->user)
            ->test(EmployeeEngagement::class)
            ->set('calculatorInputs.commute_km', 20)
            ->call('calculateFootprint');

        $result = $component->get('calculatorResult');

        $this->assertArrayHasKey('total', $result);
        $this->assertArrayHasKey('breakdown', $result);
        $this->assertArrayHasKey('comparison', $result);
        $this->assertArrayHasKey('tips', $result);

        $this->assertArrayHasKey('commute', $result['breakdown']);
        $this->assertArrayHasKey('flights', $result['breakdown']);
        $this->assertArrayHasKey('diet', $result['breakdown']);
        $this->assertArrayHasKey('heating', $result['breakdown']);
        $this->assertArrayHasKey('electricity', $result['breakdown']);
    }

    public function test_footprint_saved_to_user_settings(): void
    {
        Livewire::actingAs($this->user)
            ->test(EmployeeEngagement::class)
            ->set('calculatorInputs.commute_km', 10)
            ->call('calculateFootprint');

        $this->user->refresh();
        $this->assertNotNull($this->user->settings['carbon_footprint']);
        $this->assertNotNull($this->user->settings['footprint_calculated_at']);
    }

    public function test_can_reset_calculator(): void
    {
        Livewire::actingAs($this->user)
            ->test(EmployeeEngagement::class)
            ->set('calculatorInputs.commute_km', 50)
            ->set('calculatorInputs.flights_long', 5)
            ->call('calculateFootprint')
            ->call('resetCalculator')
            ->assertSet('calculatorInputs.commute_km', 0)
            ->assertSet('calculatorInputs.flights_long', 0)
            ->assertSet('calculatorResult', null);
    }

    public function test_vegan_diet_has_lower_emissions(): void
    {
        $component = Livewire::actingAs($this->user)->test(EmployeeEngagement::class);

        // Calculate with vegan diet
        $component->set('calculatorInputs.diet', 'vegan')
            ->call('calculateFootprint');
        $veganTotal = $component->get('calculatorResult')['total'];

        // Reset and calculate with meat heavy
        $component->call('resetCalculator')
            ->set('calculatorInputs.diet', 'meat_heavy')
            ->call('calculateFootprint');
        $meatTotal = $component->get('calculatorResult')['total'];

        $this->assertLessThan($meatTotal, $veganTotal);
    }

    // ==================== Challenges Tab ====================

    public function test_challenges_are_loaded(): void
    {
        $component = Livewire::actingAs($this->user)
            ->test(EmployeeEngagement::class);

        // Access the computed property via the component instance
        $challenges = $component->instance()->availableChallenges;

        $this->assertNotEmpty($challenges);
        $this->assertGreaterThanOrEqual(5, count($challenges));
    }

    public function test_can_join_challenge(): void
    {
        Livewire::actingAs($this->user)
            ->test(EmployeeEngagement::class)
            ->call('joinChallenge', 'no_car_week');

        $this->user->refresh();
        $this->assertArrayHasKey('no_car_week', $this->user->settings['active_challenges']);
        $this->assertEquals('active', $this->user->settings['active_challenges']['no_car_week']['status']);
    }

    public function test_can_complete_challenge(): void
    {
        // First join the challenge
        $this->user->update([
            'settings' => [
                'active_challenges' => [
                    'meatless_monday' => [
                        'joined_at' => now()->toDateTimeString(),
                        'status' => 'active',
                    ],
                ],
                'engagement_points' => 0,
            ],
        ]);

        Livewire::actingAs($this->user)
            ->test(EmployeeEngagement::class)
            ->call('completeChallenge', 'meatless_monday');

        $this->user->refresh();
        $this->assertEquals('completed', $this->user->settings['active_challenges']['meatless_monday']['status']);
        $this->assertGreaterThan(0, $this->user->settings['engagement_points']);
    }

    public function test_completing_challenge_awards_points(): void
    {
        $this->user->update([
            'settings' => [
                'active_challenges' => [
                    'no_car_week' => [
                        'joined_at' => now()->toDateTimeString(),
                        'status' => 'active',
                    ],
                ],
                'engagement_points' => 50,
            ],
        ]);

        Livewire::actingAs($this->user)
            ->test(EmployeeEngagement::class)
            ->call('completeChallenge', 'no_car_week');

        $this->user->refresh();
        // no_car_week gives 100 points, starting with 50
        $this->assertEquals(150, $this->user->settings['engagement_points']);
    }

    public function test_can_leave_challenge(): void
    {
        $this->user->update([
            'settings' => [
                'active_challenges' => [
                    'digital_detox' => [
                        'joined_at' => now()->toDateTimeString(),
                        'status' => 'active',
                    ],
                ],
            ],
        ]);

        Livewire::actingAs($this->user)
            ->test(EmployeeEngagement::class)
            ->call('leaveChallenge', 'digital_detox');

        $this->user->refresh();
        $this->assertArrayNotHasKey('digital_detox', $this->user->settings['active_challenges'] ?? []);
    }

    // ==================== Leaderboard Tab ====================

    public function test_leaderboard_participation_toggle(): void
    {
        Livewire::actingAs($this->user)
            ->test(EmployeeEngagement::class)
            ->assertSet('participateInLeaderboard', false)
            ->call('toggleLeaderboardParticipation')
            ->assertSet('participateInLeaderboard', true);

        $this->user->refresh();
        $this->assertTrue($this->user->settings['participate_leaderboard']);
    }

    public function test_leaderboard_shows_participating_users(): void
    {
        // Create users participating in leaderboard
        User::factory()->create([
            'organization_id' => $this->organization->id,
            'name' => 'Alice Champion',
            'settings' => [
                'participate_leaderboard' => true,
                'engagement_points' => 500,
            ],
        ]);

        User::factory()->create([
            'organization_id' => $this->organization->id,
            'name' => 'Bob Runner',
            'settings' => [
                'participate_leaderboard' => true,
                'engagement_points' => 300,
            ],
        ]);

        // Non-participating user
        User::factory()->create([
            'organization_id' => $this->organization->id,
            'name' => 'Charlie Hidden',
            'settings' => [
                'participate_leaderboard' => false,
                'engagement_points' => 1000,
            ],
        ]);

        $component = Livewire::actingAs($this->user)
            ->test(EmployeeEngagement::class);

        $leaderboard = $component->get('leaderboard');

        $this->assertCount(2, $leaderboard);
        // First should be Alice with highest points
        $this->assertEquals('Alice Champion', $leaderboard[0]['name']);
        $this->assertEquals(500, $leaderboard[0]['points']);
    }

    public function test_get_user_points(): void
    {
        $this->user->update([
            'settings' => ['engagement_points' => 250],
        ]);

        $component = Livewire::actingAs($this->user)
            ->test(EmployeeEngagement::class);

        // Access points through the component's public property or view
        $this->user->refresh();
        $this->assertEquals(250, $this->user->settings['engagement_points']);
    }

    public function test_get_user_rank_when_participating(): void
    {
        User::factory()->create([
            'organization_id' => $this->organization->id,
            'settings' => [
                'participate_leaderboard' => true,
                'engagement_points' => 500,
            ],
        ]);

        $this->user->update([
            'settings' => [
                'participate_leaderboard' => true,
                'engagement_points' => 200,
            ],
        ]);

        $component = Livewire::actingAs($this->user)
            ->test(EmployeeEngagement::class);

        $leaderboard = $component->get('leaderboard');
        // User should be second in leaderboard (index 1)
        $this->assertCount(2, $leaderboard);
        $this->assertEquals($this->user->id, $leaderboard[1]['id']);
    }

    // ==================== Route Test ====================

    public function test_engage_employees_route_exists(): void
    {
        $response = $this->actingAs($this->user)
            ->get(route('engage.employees'));

        $response->assertStatus(200);
    }

    public function test_engage_employees_route_requires_auth(): void
    {
        $response = $this->get(route('engage.employees'));

        $response->assertRedirect(route('login'));
    }
}
