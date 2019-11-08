<?php

namespace App\Mail;

use App\Models\CartReminder;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendAbandonedCartReminder extends Mailable
{
    use Queueable, SerializesModels;

    public $reminder;

    /**
     * Create a new message instance.
     *
     * @param CartReminder $reminder
     */
    public function __construct(CartReminder $reminder)
    {
        $this->reminder = $reminder;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject(sprintf('Interested in %s?', $this->reminder->course->title))
            ->markdown('emails.cart-reminder', ['reminder' => $this->reminder]);
    }
}
