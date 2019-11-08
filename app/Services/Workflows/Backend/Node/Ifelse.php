<?php
namespace App\Services\Workflows\Backend\Node;

use App\Models\Workflows\UserWorkflow;
use App\Models\Workflows\WorkflowLog;
use App\Services\Workflows\Backend\UserSpecificConditionChecker;

class Ifelse implements NodeContract
{
    /**
     * @param UserWorkflow $userWorkflow
     * @param array        $node
     */
    public function execute(UserWorkflow $userWorkflow, array $node)
    {
        $conditionParser = $this->conditionParser($userWorkflow->user_id, $node);
        $branch = $conditionParser->passes();
        WorkflowLog::store(
            $userWorkflow->workflow_id,
            $conditionParser->sqlQuery,
            'ifelse'
        );
        $userWorkflow->next_step = NextNodeCalculator::getFrom($userWorkflow->next_step, $branch);
        $userWorkflow->save();
    }

    /**
     * @param $userId
     * @param $node
     *
     * @return mixed
     */
    private function conditionParser($userId, $node)
    {
        return app()->makeWith(UserSpecificConditionChecker::class, [
            'userId'         => $userId,
            'nodeConditions' => $node
        ]);
    }
}
