<?php
namespace App\Services\Workflows\Backend\Node;

use App\Models\Workflows\UserWorkflow;
use App\Models\Template;
use App\Mail\WorkflowTriggeredActionSendEmail;
use Illuminate\Support\Facades\Mail;
use App\Models\EmailStatus;
use Carbon\Carbon;

class SendEmail implements NodeContract
{

    public static $emailStatusId = '';

    /**
     * @TODO filter opt out users
     */
    public function execute(UserWorkflow $userWorkflow, array $node)
    {
        $step = $userWorkflow->next_step;
        $template = Template::find($node['value']['value']);

        $variables = [
            '$$USERNAME$$' => $userWorkflow->user->name,
            '$$FIRSTNAME$$' => $userWorkflow->user->firstName
        ];

        $content = str_replace(array_keys($variables), array_values($variables), $template->content);

        $userWorkflow->next_step = NextNodeCalculator::getFrom($userWorkflow->next_step);
        $userWorkflow->save();

        $emailStatus = EmailStatus::create([
                'user_id' => $userWorkflow->user_id,
                'workflow_id' => $userWorkflow->workflow_id,
                'step' => $step,
                'status' => 0,
                'last_timestamp' => Carbon::now(),
                'subject' => $template->subject,
        ]);

        self::$emailStatusId = $emailStatus->id;

        $response = Mail::to($userWorkflow->user->email)
            ->send(new WorkflowTriggeredActionSendEmail($template->subject, $content));
    }

    public function getEmailStatusId()
    {
        return self::$emailStatusId;
    }
}
