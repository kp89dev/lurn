<?php
namespace App\Console\Commands\Workflows;

use App\Jobs\Workflows\HandleWorkflowUser;
use App\Models\Workflows\UserWorkflow;
use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;

class WorkflowsNodeHandler extends Command
{
    use DispatchesJobs;

    protected $signature   = 'workflows:node_handler';
    protected $description = 'Handle workflow nodes';

    public function handle()
    {
        $offset = 0;
        $limit  = 1000;
        $query = UserWorkflow::where('hit_goal', 0)
            ->whereNull('deleted_at')
            ->where(function ($query) {
                $query->whereNull('next_step_time')
                    ->orWhereBetween(
                        'next_step_time',
                        [
                            date('Y-m-d H:i:s', strtotime('-1 minute')),
                            date('Y-m-d H:i:s', strtotime('1 minute'))
                        ]
                    );
            });


        while (true) {
            $users = $query->offset($offset)->limit($limit)->get();

            if (! count($users)) {
                return;
            }

            foreach ($users as $workflowUser) {
                $this->dispatch(
                    (new HandleWorkflowUser($workflowUser->id))->onQueue('user_workflows')
                );
            }

            $offset += 1000;
        }
    }
}
