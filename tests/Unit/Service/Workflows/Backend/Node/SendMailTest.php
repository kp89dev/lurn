<?php
namespace Unit\Service\Workflows\Backend\Node;

use App\Models\Template;
use App\Models\User;
use App\Models\Workflows\UserWorkflow;
use App\Models\Workflows\Workflow;
use App\Services\Workflows\Backend\Node\SendEmail;
use Illuminate\Support\Facades\Mail;

class SendMailTest extends \TestCase
{
    /**
     * @test
     * @group workflows
     */
    public function send_mail_action()
    {
        $user = factory(User::class)->create();
        $workflow = factory(Workflow::class)->create();
        $userWorkflow = factory(UserWorkflow::class)->create([
            'user_id'        => $user->id,
            'workflow_id'    => $workflow->id,
            'next_step'      => 3,
            'next_step_time' => null
        ]);

        $template = factory(Template::class)->create([
            'content' => 'some content $$USERNAME$$ and $$FIRSTNAME$$'
        ]);
        
        $action = new SendEmail();
        $action->execute($userWorkflow, ['value' => ['value' => $template->id]]);

        $this->assertDatabaseHas('user_workflows', [
            'user_id'     => $userWorkflow->user_id,
            'workflow_id' => $userWorkflow->workflow_id,
            'next_step'   => 4
        ]);

        $this->assertDatabaseHas('email_statuses', [
            'user_id'     => $userWorkflow->user_id,
            'workflow_id' => $userWorkflow->workflow_id,
            'step'        => 3
        ]);
    }
}
