<?php
namespace App\Services\Workflows\Backend\Node;

use App\Models\Workflows\UserWorkflow;

class RemoveFromWorkflow implements NodeContract
{
    public function execute(UserWorkflow $userWorkflow, array $node)
    {
        (new UserWorkflow)->where('user_id', $userWorkflow->user_id)
                    ->where('workflow_id', $node[0]['values'])
                    ->delete();
        
        $userWorkflow->next_step = NextNodeCalculator::getFrom($userWorkflow->next_step);
        $userWorkflow->save();
    }
}
