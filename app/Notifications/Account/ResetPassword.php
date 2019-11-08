<?php

namespace App\Notifications\Account;

use App\Models\User;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPassword extends Notification
{
    /**
     * The password reset token.
     *
     * @var string
     */
    public $token;

    /**
     * Create a notification instance.
     *
     * @param  string  $token
     * @return void
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Get the notification's channels.
     *
     * @param  mixed  $notifiable
     * @return array|string
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Build the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail(User $notifiable)
    {
        return (new MailMessage)->markdown(
            'emails.reset_password',['greeting' => sprintf('Hello, %s!', $notifiable->name),
                'beforeCTA' => 'You are receiving this email because we received a password reset request for your account.',
                'action' => ['text' => 'Reset Password', 'url' => url(config('app.url').route('password.reset', $this->token, false))],
                'afterCTA' => 'If you did not request a password reset, no further action is required.']
            );
    }
}
