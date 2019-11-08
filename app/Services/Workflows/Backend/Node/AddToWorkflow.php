<?php
namespace App\Services\Workflows\Backend\Node;

use App\Models\Workflows\UserWorkflow;

class AddToWorkflow implements NodeContract
{
    public function execute(UserWorkflow $userWorkflow, array $node)
    {
        UserWorkflow::create([
            'user_id'     => $userWorkflow->user_id,
            'workflow_id' => $node['value']['value'],
            'hit_goal'    => 0,
            'next_step'   => 1
        ]);
        
        $userWorkflow->next_step = NextNodeCalculator::getFrom($userWorkflow->next_step);
        $userWorkflow->save();
    }
}
