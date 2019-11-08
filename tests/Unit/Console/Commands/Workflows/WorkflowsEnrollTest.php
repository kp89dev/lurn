<?php
namespace Tests\Unit\Console\Commands\Workflows;

use App\Jobs\Workflows\HandleWorkflowEnrollemnt;
use App\Models\Workflows\Workflow;
use Illuminate\Support\Facades\Bus;

class WorkflowsEnrollTest extends \TestCase
{
    /**
     * @test
     * @group workflows
     */
    public function dispatches_workflow_enrollment_for_each_active_workflow()
    {
        $workflows = factory(Workflow::class, 2)->create(['status' => 1]);
        $disabledWorkflow = factory(Workflow::class)->create(['status' => 0]);

        Bus::fake();
        $this->artisan('workflows:enroll');

        foreach ($workflows as $workflow) {
            Bus::assertDispatched(HandleWorkflowEnrollemnt::class, function ($job) use ($workflow) {
                return $job->workflow->id === $workflow->id;
            });
        }

        Bus::assertNotDispatched(HandleWorkflowEnrollemnt::class, function ($job) use ($disabledWorkflow) {
            return $job->workflow->id === $disabledWorkflow->id;
        });
    }
}
