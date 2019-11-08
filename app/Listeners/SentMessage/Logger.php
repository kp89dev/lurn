<?php

namespace App\Listeners\SentMessage;

use Illuminate\Mail\Events\MessageSent;
use App\Services\Workflows\Backend\Node\SendEmail;
use App\Models\EmailStatus;

class Logger
{
    public function handle(MessageSent $event)
    {
        if (isset($event->data['triggered']) && $event->data['triggered'] == 'workflow') {
            $emailStatusId = (new SendEmail)->getEmailStatusId();
            $sesMessageId = $event->message->getHeaders()->get('X-SES-Message-ID');
            $aws_id = $sesMessageId ? $sesMessageId : $event->message->getId();

            EmailStatus::find($emailStatusId)->update(compact('aws_id'));
        }
    }
}
