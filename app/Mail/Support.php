<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Support extends Mailable
{
    use Queueable, SerializesModels;

    public $sender;
    public $msg;

    /**
     * Create a new message instance.
     *
     * @param array  $sender
     * @param string $message
     */
    public function __construct(array $sender, string $message)
    {
        $this->sender = (object) $sender;
        $this->msg = $message;
    }

    /**
     * Build the message.
     * 
     * @codeCoverageIgnore
     * @return $this
     */
    public function build()
    {
        return $this
            ->from($this->sender->email, $this->sender->name ?? null)
            ->markdown('emails.support');
    }
}
