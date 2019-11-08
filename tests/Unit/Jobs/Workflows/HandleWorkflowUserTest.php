<?php
namespace Unit\Jobs\Workflows;

use App\Jobs\Workflows\HandleWorkflowUser;
use App\Models\Workflows\UserWorkflow;
use App\Models\Workflows\Workflow;
use App\Services\Workflows\Backend\Node\AddToWorkflow;
use App\Services\Workflows\Backend\UserSpecificConditionChecker;

class HandleWorkflowUserTest extends \TestCase
{
    /**
     * @test
     * @group workflows
     */
    public function user_is_marked_with_goal_hit_when_goal_passes()
    {
        $workflow  = factory(Workflow::class)->create(['status' => 1]);
        $uWorkflow = factory(UserWorkflow::class)->create(['hit_goal' => 0, 'workflow_id' => $workflow->id]);

        $userConditionChecker = $this->createMock(UserSpecificConditionChecker::class);
        $userConditionChecker->expects(self::once())->method('passes')->willReturn(true);

        $this->app->bind(UserSpecificConditionChecker::class, function($app) use ($userConditionChecker) {
            return $userConditionChecker;
        });

        (new HandleWorkflowUser($uWorkflow->id))->handle();

        $this->assertDatabaseHas('user_workflows', [
            'user_id' => $uWorkflow->user_id,
            'workflow_id' => $uWorkflow->workflow_id,
            'hit_goal' => 1
        ]);
    }

    /**
     * @test
     * @group workflows
     */
    public function canContinueWithNextAction_stops_when_there_is_no_next_action()
    {
        $workflow  = factory(Workflow::class)->create(['status' => 1, 'workflow' => []]);
        $uWorkflow = factory(UserWorkflow::class)->create([
            'hit_goal'    => 0,
            'workflow_id' => $workflow->id,
            'deleted_at'  => null
        ]);

        $userConditionChecker = $this->createMock(UserSpecificConditionChecker::class);
        $userConditionChecker->expects(self::once())->method('passes')->willReturn(false);

        $this->app->bind(UserSpecificConditionChecker::class, function($app) use ($userConditionChecker) {
            return $userConditionChecker;
        });

        (new HandleWorkflowUser($uWorkflow->id))->handle();

        $this->assertDatabaseHas('user_workflows', [
            'user_id'     => $uWorkflow->user_id,
            'workflow_id' => $uWorkflow->workflow_id,
            'hit_goal'    => 0,
            ['deleted_at', '<>', null]
        ]);
    }

    /**
     * @test
     * @group workflows
     */
    public function canContinueWithNextAction_stops_when_waiting_time_isnt_met()
    {
        $workflow = factory(Workflow::class)->create([
            'status'   => 1,
            'workflow' => [0 => '', 1 => 'next_step :)']
        ]);
        $uWorkflow = factory(UserWorkflow::class)->create([
            'hit_goal'         => 0,
            'workflow_id'      => $workflow->id,
            'deleted_at'       => null,
            'next_step_time'   => date('Y-m-d H:i:s', strtotime("+10 minute")),
            'next_step'        => 1
        ]);

        $userConditionChecker = $this->createMock(UserSpecificConditionChecker::class);
        $userConditionChecker->expects(self::once())->method('passes')->willReturn(false);

        $this->app->bind(UserSpecificConditionChecker::class, function($app) use ($userConditionChecker) {
            return $userConditionChecker;
        });

        (new HandleWorkflowUser($uWorkflow->id))->handle();

        $this->assertDatabaseHas('user_workflows', [
            'user_id'     => $uWorkflow->user_id,
            'workflow_id' => $uWorkflow->workflow_id,
            'hit_goal'    => 0,
            'deleted_at'  => null,
            'next_step'   => 1,
            ['next_step_time', '<>', null]
        ]);
    }

    /**
     * @test
     * @group workflows
     * @expectedException \Exception
     */
    public function executes_next_action_when_there_is_no_waiting_time()
    {
        $workflow = factory(Workflow::class)->create([
            'status'   => 1,
            'workflow' => [0 => '', 1 => ['key' => 'App\AddToWorkflow']]
        ]);
        $uWorkflow = factory(UserWorkflow::class)->create([
            'hit_goal'         => 0,
            'workflow_id'      => $workflow->id,
            'deleted_at'       => null,
            'next_step_time'   => null,
            'next_step'        => 1
        ]);

        $userConditionChecker = $this->createMock(UserSpecificConditionChecker::class);
        $userConditionChecker->expects(self::exactly(1))->method('passes')->willReturn(false);

        $this->app->bind(UserSpecificConditionChecker::class, function($app) use ($userConditionChecker) {
            return $userConditionChecker;
        });
        $this->app->bind(AddToWorkflow::class, function($app) {
            $mock = $this->createMock(AddToWorkflow::class);
            $mock->expects(self::once())->method('execute')->willThrowException(new \Exception('Forced breaking Loop'));

            return $mock;
        });

        (new HandleWorkflowUser($uWorkflow->id))->handle();

        $this->assertDatabaseHas('user_workflows', [
            'user_id'     => $uWorkflow->user_id,
            'workflow_id' => $uWorkflow->workflow_id,
            'hit_goal'    => 0,
            'next_step_time'  => null,
            'next_step'   => 1,
            ['deleted_at', '<>', null]
        ]);
    }

    /**
     * @test
     * @group workflows
     * @expectedException \Exception
     */
    public function executes_next_action_when_waiting_time_is_met()
    {
        $workflow = factory(Workflow::class)->create([
            'status'   => 1,
            'workflow' => [0 => '', 1 => ['key' => 'App\AddToWorkflow']]
        ]);
        $uWorkflow = factory(UserWorkflow::class)->create([
            'hit_goal'         => 0,
            'workflow_id'      => $workflow->id,
            'deleted_at'       => null,
            'next_step_time'   => date('Y-m-d H:i:s', strtotime("+30 seconds")),
            'next_step'        => 1
        ]);

        $userConditionChecker = $this->createMock(UserSpecificConditionChecker::class);
        $userConditionChecker->expects(self::exactly(1))->method('passes')->willReturn(false);

        $this->app->bind(UserSpecificConditionChecker::class, function($app) use ($userConditionChecker) {
            return $userConditionChecker;
        });
        $this->app->bind(AddToWorkflow::class, function($app) {
            $mock = $this->createMock(AddToWorkflow::class);
            $mock->expects(self::once())->method('execute')->willThrowException(new \Exception('Forced breaking Loop'));

            return $mock;
        });

        (new HandleWorkflowUser($uWorkflow->id))->handle();

        $this->assertDatabaseHas('user_workflows', [
            'user_id'     => $uWorkflow->user_id,
            'workflow_id' => $uWorkflow->workflow_id,
            'hit_goal'    => 0,
            'next_step_time'  => null,
            'next_step'   => 1,
            ['deleted_at', '<>', null]
        ]);
    }
}
