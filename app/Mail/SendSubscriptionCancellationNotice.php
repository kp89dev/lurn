<?php

namespace App\Mail;

use App\Models\CourseSubscriptions;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendSubscriptionCancellationNotice extends Mailable
{
    use Queueable, SerializesModels;

    public $subscription;
    public $data;

    /**
     * Create a new message instance.
     *
     * @param CourseSubscriptions $subscription
     * @param array               $viewData
     */
    public function __construct(CourseSubscriptions $subscription, array $viewData = [])
    {
        $this->subscription = $subscription;
        $this->data = (object) $viewData;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject(sprintf('%s Subscription Cancellation Notice', $this->subscription->course->title))
            ->markdown('emails.subscription-cancellation');
    }
}
