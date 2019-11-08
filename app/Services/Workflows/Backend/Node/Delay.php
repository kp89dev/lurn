<?php
namespace App\Services\Workflows\Backend\Node;

use App\Models\Workflows\UserWorkflow;

class Delay implements NodeContract
{
    public function execute(UserWorkflow $userWorkflow, array $node)
    {
        //calculate next_step_action
        $nextStepTime = date('Y-m-d H:i:s', strtotime("+ {$node['value']['delay']} {$node['value']['delayUnit']}"));

        $userWorkflow->next_step_time = $nextStepTime;
        $userWorkflow->next_step = NextNodeCalculator::getFrom($userWorkflow->next_step);
        $userWorkflow->save();
    }
}
