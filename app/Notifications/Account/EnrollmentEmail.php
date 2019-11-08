<?php

namespace App\Notifications\Account;

use App\Models\User;
use App\Models\Course;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class EnrollmentEmail extends Notification
{
    use Queueable;
    
    public function __construct(Course $course)
    {
        $this->course = $course;
    }

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
        //'afterCTA' => 'Thank you for using our application!'
        return (new MailMessage)->markdown(
            'emails.enrollment',[
                'intro' => sprintf('Congrats on your enrollment to %s!',$this->course->title),
                'greeting' => sprintf('Hello, %s!', $notifiable->name),
                'beforeCTA' => 'You have been successfully enrolled in '.$this->course->title,
                'action' => ['text' => 'View Course', 'url' => route('course', $this->course->slug)]
                ]
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
