<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendFriendRecruitmentEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;

    /**
     * Create a new message instance.
     *
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $code = $this->user->getReferralCode();

        $link = route('referral.index', compact('code'));

        return $this
            ->subject("You're invited to join the Lurn Nation launch!")
            ->markdown('emails.recruit-friend', ['user' => $this->user, 'referral_link' => $link]);
    }
}
