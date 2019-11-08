<?php
namespace App\Console\Commands\Workflows;

use App\Jobs\Workflows\HandleWorkflowEnrollemnt;
use App\Models\Workflows\Workflow;
use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;

class WorkflowsEnroll extends Command
{
    use DispatchesJobs;
    
    protected $signature   = 'workflows:enroll';
    protected $description = 'Enrolls users meeting enrollment critiria into workflows';

    public function handle()
    {
        Workflow::where('status', 1)->each(function($item, $key) {
           $this->dispatch(
               (new HandleWorkflowEnrollemnt($item->id))
           );
        });
    }
}
