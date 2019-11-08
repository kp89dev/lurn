<?php

namespace App\Notifications\Account;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class AccountMergeConfirmation extends Notification
{
    use Queueable;
    /**
     * @type
     */
    private $token;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     * 
     * @codeCoverageIgnore
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)->markdown(
            'emails.merge',['greeting' => sprintf('Hello, %s!', $notifiable->name),
                'beforeCTA' => 'You are receiving this email because we received an account merge request',
                'action' => ['text' => 'Merge Account', 'url' => route('account-merge.confirm', ['token' => $this->token ])],
                'afterCTA' => 'If you did not request an account merge, no further action is required.']
            );
    }

    /**
     * Get the array representation of the notification.
     *
     * @codeCoverageIgnore
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
