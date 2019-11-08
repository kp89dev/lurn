<?php
namespace Unit\Service\Workflows\Backend\Node;

use App\Models\Workflows\UserWorkflow;
use App\Services\Workflows\Backend\Node\RemoveFromWorkflow;

class RemoveFromWorkflowTest extends \TestCase
{
    /**
     * @test
     * @group workflows
     */
    public function user_is_successfuly_removed_from_workflow()
    {
        $userWorkflow = factory(UserWorkflow::class)->create([
            'user_id' => 2,
            'next_step' => 3,
            'workflow_id' => 100,
            'next_step_time' => null
        ]);
        $userWorkflowToRemove = factory(UserWorkflow::class)->create([
            'user_id' => 2,
            'deleted_at' => null
        ]);

        $action = new RemoveFromWorkflow();
        $action->execute($userWorkflow, [
            ['values' => $userWorkflowToRemove->workflow_id]
        ]);

        $this->assertDatabaseHas('user_workflows', [
            'user_id'     => $userWorkflow->user_id,
            'workflow_id' => $userWorkflow->workflow_id,
            'next_step'   => 4
        ]);
        $this->assertDatabaseHas('user_workflows', [
            'user_id'     => 2,
            'workflow_id' => $userWorkflowToRemove->workflow_id,
            ['deleted_at', '<>', null]
        ]);
    }
}
