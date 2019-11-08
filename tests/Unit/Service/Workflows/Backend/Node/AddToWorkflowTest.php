<?php
namespace Unit\Service\Workflows\Backend\Node;

use App\Models\Workflows\UserWorkflow;
use App\Services\Workflows\Backend\Node\AddToWorkflow;

class AddToWorkflowTest extends \TestCase
{
    /**
     * @test
     * @group workflows
     */
    public function user_is_added_to_the_new_workflows()
    {
        $userWorkflow = factory(UserWorkflow::class)->create([
            'user_id' => 300,
            'next_step' => 3
        ]);

        $action = new AddToWorkflow();
        $action->execute($userWorkflow, [
            'value' => ['value' => 5]
        ]);

        $this->assertDatabaseHas('user_workflows', [
            'user_id'     => $userWorkflow->user_id,
            'workflow_id' => $userWorkflow->workflow_id,
            'next_step'   => 4
        ]);

        $this->assertDatabaseHas('user_workflows', [
            'user_id'     => $userWorkflow->user_id,
            'workflow_id' => 5,
            'next_step'   => 1
        ]);
    }
}
