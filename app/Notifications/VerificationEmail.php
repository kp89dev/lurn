<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Crypt;

class VerificationEmail extends Notification
{
    use Queueable;

    /**
     * Get the notification's delivery channels.
     *
     * @return array
     */
    public function via()
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed $notifiable
     * @return MailMessage
     */
    public function toMail(User $notifiable)
    {
        return (new MailMessage)->markdown(
            'emails.verification',['greeting' => sprintf('Hello, %s!', $notifiable->name),
                'beforeCTA' => 'Please confirm that this email address belongs to you.',
                'action' => ['text' => 'Confirm Email Address', 'url' => url('verify', Crypt::encrypt($notifiable->id))],
                'afterCTA' => 'Thank you for using our application!']
            );
    }

    /**
     * Get the array representation of the notification.
     * 
     * @codeCoverageIgnore
     * @return array
     */
    public function toArray()
    {
        return [];
    }
}
