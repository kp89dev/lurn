<?php
namespace Unit\Service\Workflows\Backend\Node;

use App\Models\Workflows\UserWorkflow;
use App\Services\Workflows\Backend\Node\Delay;

class DelayTest extends \TestCase
{
    /**
     * @test
     * @group workflows
     */
    public function user_delay_is_applied_successfully()
    {
        $userWorkflow = factory(UserWorkflow::class)->create([
            'next_step' => 3,
            'next_step_time' => null
        ]);

        $action = new Delay();
        $action->execute($userWorkflow, [
            'value' => ['delay' => 5, 'delayUnit' => 'days']
        ]);

        $this->assertDatabaseHas('user_workflows', [
            'user_id'     => $userWorkflow->user_id,
            'workflow_id' => $userWorkflow->workflow_id,
            ['next_step_time', '<>', null]
        ]);
    }
}
