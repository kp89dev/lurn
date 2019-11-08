<?php
namespace App\Jobs\Workflows;

use App\Models\Workflows\Workflow;
use App\Models\Workflows\WorkflowLog;
use App\Services\Workflows\Backend\ConditionParser;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;

class HandleWorkflowEnrollemnt implements ShouldQueue
{
    use Queueable;
    /**
     * @type Workflow
     */
    public $workflow;

    public function __construct($workflow)
    {
        if (is_int($workflow)) {
            $workflow = Workflow::find($workflow);
        }

        $this->workflow = $workflow;
    }

    public function handle()
    {
        if ($this->workflow->status == 0) {
            return; //was disabled meanwhile
        }

        $query = $this->conditionsParser()->run();
        $rawQuery = 'INSERT IGNORE INTO `user_workflows` ' .
            "SELECT null, user_result.id, {$this->workflow->id}, 0, 1, null, null, now(), now() FROM (" .
            $query->toSql() .
            ') as user_result '.
            'JOIN user_settings ON user_result.id = user_settings.user_id ' .
            'WHERE user_settings.receive_updates = 1';
        $bindings = $query->getBindings();

        WorkflowLog::store($this->workflow->id, $rawQuery, 'enrollment');
        DB::insert($rawQuery, $bindings);
    }

    private function conditionsParser()
    {
        $query =  DB::table('users')->select("users.*");

        return app()->makeWith(ConditionParser::class, [
            'query' => $query,
            'conditions' => $this->workflow->enroll
        ]);
    }
}
