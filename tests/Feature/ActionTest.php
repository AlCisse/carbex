<?php

namespace Tests\Feature;

use App\Models\Action;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Feature tests for Action (Plan de Transition) - T090
 */
class ActionTest extends TestCase
{
    use RefreshDatabase;

    private Organization $organization;

    protected function setUp(): void
    {
        parent::setUp();

        $this->organization = Organization::factory()->create();
    }

    public function test_can_create_action(): void
    {
        $action = Action::factory()
            ->forOrganization($this->organization)
            ->create([
                'title' => 'Remplacer les véhicules diesel',
                'co2_reduction_percent' => 15,
                'estimated_cost' => 50000,
            ]);

        $this->assertDatabaseHas('actions', [
            'id' => $action->id,
            'title' => 'Remplacer les véhicules diesel',
            'co2_reduction_percent' => 15,
        ]);
    }

    public function test_action_starts_as_todo(): void
    {
        $action = Action::factory()
            ->forOrganization($this->organization)
            ->create();

        $this->assertEquals(Action::STATUS_TODO, $action->status);
        $this->assertTrue($action->isTodo());
    }

    public function test_can_start_todo_action(): void
    {
        $action = Action::factory()
            ->forOrganization($this->organization)
            ->todo()
            ->create();

        $result = $action->start();

        $this->assertTrue($result);
        $this->assertEquals(Action::STATUS_IN_PROGRESS, $action->status);
        $this->assertTrue($action->isInProgress());
    }

    public function test_can_complete_action(): void
    {
        $action = Action::factory()
            ->forOrganization($this->organization)
            ->inProgress()
            ->create();

        $result = $action->complete();

        $this->assertTrue($result);
        $this->assertEquals(Action::STATUS_COMPLETED, $action->status);
        $this->assertTrue($action->isCompleted());
    }

    public function test_can_reopen_completed_action(): void
    {
        $action = Action::factory()
            ->forOrganization($this->organization)
            ->completed()
            ->create();

        $result = $action->reopen();

        $this->assertTrue($result);
        $this->assertEquals(Action::STATUS_IN_PROGRESS, $action->status);
    }

    public function test_action_is_overdue(): void
    {
        $overdueAction = Action::factory()
            ->forOrganization($this->organization)
            ->overdue()
            ->create();

        $futureAction = Action::factory()
            ->forOrganization($this->organization)
            ->todo()
            ->create(['due_date' => now()->addMonth()]);

        $this->assertTrue($overdueAction->isOverdue());
        $this->assertFalse($futureAction->isOverdue());
    }

    public function test_completed_action_is_not_overdue(): void
    {
        $action = Action::factory()
            ->forOrganization($this->organization)
            ->completed()
            ->create(['due_date' => now()->subMonth()]);

        $this->assertFalse($action->isOverdue());
    }

    public function test_status_label_accessor(): void
    {
        $todo = Action::factory()->forOrganization($this->organization)->todo()->create();
        $inProgress = Action::factory()->forOrganization($this->organization)->inProgress()->create();
        $completed = Action::factory()->forOrganization($this->organization)->completed()->create();

        $this->assertNotEmpty($todo->status_label);
        $this->assertNotEmpty($inProgress->status_label);
        $this->assertNotEmpty($completed->status_label);
    }

    public function test_difficulty_label_accessor(): void
    {
        $easy = Action::factory()->forOrganization($this->organization)->easy()->create();
        $hard = Action::factory()->forOrganization($this->organization)->hard()->create();

        $this->assertNotEmpty($easy->difficulty_label);
        $this->assertNotEmpty($hard->difficulty_label);
    }

    public function test_cost_indicator_accessor(): void
    {
        $cheap = Action::factory()->forOrganization($this->organization)->create(['estimated_cost' => 500]);
        $medium = Action::factory()->forOrganization($this->organization)->create(['estimated_cost' => 5000]);
        $expensive = Action::factory()->forOrganization($this->organization)->create(['estimated_cost' => 25000]);
        $veryExpensive = Action::factory()->forOrganization($this->organization)->create(['estimated_cost' => 100000]);

        $this->assertEquals('€', $cheap->cost_indicator);
        $this->assertEquals('€€', $medium->cost_indicator);
        $this->assertEquals('€€€', $expensive->cost_indicator);
        $this->assertEquals('€€€€', $veryExpensive->cost_indicator);
    }

    public function test_status_scope_filters(): void
    {
        Action::factory()->forOrganization($this->organization)->todo()->count(2)->create();
        Action::factory()->forOrganization($this->organization)->inProgress()->count(3)->create();
        Action::factory()->forOrganization($this->organization)->completed()->count(1)->create();

        $this->assertCount(2, Action::todo()->get());
        $this->assertCount(3, Action::inProgress()->get());
        $this->assertCount(1, Action::completed()->get());
        $this->assertCount(5, Action::pending()->get());
    }

    public function test_overdue_scope(): void
    {
        Action::factory()->forOrganization($this->organization)->overdue()->count(2)->create();
        Action::factory()->forOrganization($this->organization)->todo()->create(['due_date' => now()->addMonth()]);

        $this->assertCount(2, Action::overdue()->get());
    }

    public function test_difficulty_scope(): void
    {
        Action::factory()->forOrganization($this->organization)->easy()->count(3)->create();
        Action::factory()->forOrganization($this->organization)->hard()->count(1)->create();

        $this->assertCount(3, Action::difficulty(Action::DIFFICULTY_EASY)->get());
        $this->assertCount(1, Action::difficulty(Action::DIFFICULTY_HARD)->get());
    }

    public function test_order_by_priority(): void
    {
        Action::factory()->forOrganization($this->organization)->create(['priority' => 1]);
        Action::factory()->forOrganization($this->organization)->create(['priority' => 5]);
        Action::factory()->forOrganization($this->organization)->create(['priority' => 3]);

        $ordered = Action::orderByPriority('desc')->pluck('priority')->toArray();
        $this->assertEquals([5, 3, 1], $ordered);
    }

    public function test_order_by_due_date(): void
    {
        Action::factory()->forOrganization($this->organization)->create(['due_date' => '2024-12-01']);
        Action::factory()->forOrganization($this->organization)->create(['due_date' => '2024-06-01']);
        Action::factory()->forOrganization($this->organization)->create(['due_date' => '2024-09-01']);

        $ordered = Action::orderByDueDate('asc')->pluck('due_date')->map(fn ($d) => $d->format('Y-m-d'))->toArray();
        $this->assertEquals(['2024-06-01', '2024-09-01', '2024-12-01'], $ordered);
    }

    public function test_belongs_to_organization(): void
    {
        $action = Action::factory()
            ->forOrganization($this->organization)
            ->create();

        $this->assertNotNull($action->organization);
        $this->assertEquals($this->organization->id, $action->organization->id);
    }
}
