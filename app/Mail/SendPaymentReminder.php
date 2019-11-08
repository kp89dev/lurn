<?php

namespace App\Mail;

use App\Models\CourseSubscriptions;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendPaymentReminder extends Mailable
{
    use Queueable, SerializesModels;

    public $subscription;

    /**
     * Create a new message instance.
     *
     * @param CourseSubscriptions $subscription
     */
    public function __construct(CourseSubscriptions $subscription)
    {
        $this->subscription = $subscription;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $numberToWord = [2 => 'second', 3 => 'third', 4 => 'forth'];
        $numberWord = $numberToWord[$this->subscription->payments_made + 1];

        if ($this->subscription->payments_made + 1 == $this->subscription->payments_required) {
            $numberWord .= ' and final';
        }

        $this->subscription->increment('notifications_sent');

        return $this
            ->subject(sprintf('%s Payment Reminder', $this->subscription->course->title))
            ->markdown('emails.payment-reminder', compact('numberWord'));
    }
}
