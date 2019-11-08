<?php
namespace App\Jobs\Workflows;

use App\Models\Workflows\UserWorkflow;
use App\Models\Workflows\WorkflowLog;
use App\Services\Workflows\Backend\UserSpecificConditionChecker;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use MongoDB\Driver\Exception\LogicException;

class HandleWorkflowUser implements ShouldQueue
{
    use Queueable;
    /**
     * @type int
     */
    private $workflowUser;

    public function __construct(int $userWorkflowId)
    {
        $this->workflowUser = UserWorkflow::findOrFail($userWorkflowId);
    }

    public function handle()
    {
        $goal = $this->goal();
        WorkflowLog::store($this->workflowUser->workflow_id, $goal->sqlQuery, 'goal');

        while(true) {
            //check if goal was met before every action
            if ($goal->passes()) {
                $this->markUserWithGoalHit();

                return;
            }

            if (! $this->canContinueWithNextAction()) {
                return;
            }

            $this->executeNextAction();
        }
    }

    private function markUserWithGoalHit()
    {
        $this->workflowUser->hit_goal = 1;
        $this->workflowUser->save();
    }

    private function executeNextAction()
    {
        $nextAction = $this->getActionDetails();

        app('App\Services\Workflows\Backend\Node\\' . $this->getActionNameFromNode($nextAction))
            ->execute($this->workflowUser, $nextAction);
    }

    /**
     * @return bool
     */
    private function canContinueWithNextAction()
    {
        //does the next step exist?
        if (empty($this->getActionDetails())) {
            $this->workflowUser->delete();

            return false;
        }

        if (is_null($this->workflowUser->next_step_time)) {
            return true;
        }

        $firstDate  = Carbon::now()->subMinute(1);
        $secondDate = Carbon::now()->addMinute(1);

        if ($this->workflowUser->next_step_time->between($firstDate, $secondDate)) {
            $this->workflowUser->fill(['next_step_time' => null])->save();
            return true;
        }



        return false;
    }

    /**
     * @return mixed
     */
    private function goal()
    {
        return app()->makeWith(UserSpecificConditionChecker::class, [
            'userId' => $this->workflowUser->id,
            'nodeConditions' => $this->workflowUser->workflow->goal
        ]);
    }

    /**
     * @param $nextAction
     *
     * @return string
     */
    private function getActionNameFromNode($nextAction)
    {
        if (isset($nextAction['key'])) {
           return ucfirst(class_basename($nextAction['key']));
        }

        throw new LogicException('Unable to detect next action handler');
    }

    /**
     * @return mixed
     */
    private function getActionDetails()
    {
        return array_get($this->workflowUser->workflow->workflow, $this->workflowUser->next_step);
    }
}
