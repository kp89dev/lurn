<?php
namespace Tests\Unit\Console\Commands\Workflows;

use App\Console\Commands\Workflows\WorkflowsNodeHandler;
use App\Jobs\Workflows\HandleWorkflowUser;
use App\Models\Workflows\UserWorkflow;
use Illuminate\Support\Facades\Bus;

class WorkflowsNodeHandlerTest extends \TestCase
{
    /**
     * @test
     * @group workflows
     */
    public function no_action_is_dispatched_when_user_hit_goal()
    {
        factory(UserWorkflow::class)->create([
            'hit_goal'           => 1,
            'deleted_at'     => null,
            'next_step_time' => null
        ]);

        Bus::fake();

        (new WorkflowsNodeHandler())->handle();

        Bus::assertNotDispatched(HandleWorkflowUser::class);
    }

    /**
     * @test
     * @group workflows
     */
    public function no_action_is_dispatched_when_user_is_deleted()
    {
        factory(UserWorkflow::class)->create([
            'hit_goal'           => 0,
            'deleted_at'     => date('Y-m-d H:i:s', strtotime('-2 minute')),
            'next_step_time' => null
        ]);

        Bus::fake();

        (new WorkflowsNodeHandler())->handle();

        Bus::assertNotDispatched(HandleWorkflowUser::class);
    }

    /**
     * @test
     * @group workflows
     */
    public function no_action_is_dispatched_when_user_doesnt_met_delay_time()
    {
        factory(UserWorkflow::class)->create([
            'hit_goal'       => 0,
            'deleted_at'     => date('Y-m-d H:i:s', strtotime('+2 minute')),
            'next_step_time' => null
        ]);

        Bus::fake();

        (new WorkflowsNodeHandler())->handle();

        Bus::assertNotDispatched(HandleWorkflowUser::class);
    }

    /**
     * @test
     * @group workflows
     */
    public function no_action_is_dispatched_when_theres_no_user_enrolled()
    {
        Bus::fake();

        (new WorkflowsNodeHandler())->handle();

        Bus::assertNotDispatched(HandleWorkflowUser::class);
    }

    /**
     * @test
     * @group workflows
     */
    public function action_dispatched()
    {
        factory(UserWorkflow::class)->create([
            'hit_goal'       => 0,
            'deleted_at'     => null,
            'next_step_time' => null
        ]);

        Bus::fake();

        (new WorkflowsNodeHandler())->handle();

        Bus::assertDispatched(HandleWorkflowUser::class);
    }

    /**
     * @test
     * @group workflows
     */
    public function action_dispatched_when_time_is_met()
    {
        factory(UserWorkflow::class)->create([
            'hit_goal'       => 0,
            'deleted_at'     => null,
            'next_step_time' => date('Y-m-d H:i:s', strtotime('+30 seconds'))
        ]);

        Bus::fake();

        (new WorkflowsNodeHandler())->handle();

        Bus::assertDispatched(HandleWorkflowUser::class);
    }
}
