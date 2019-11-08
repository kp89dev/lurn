<?php

namespace App\Listeners\Auth;

use App\Models\User;
use App\Notifications\VerificationEmail;

class SendVerificationEmail
{
    public function handle($event)
    {
        $user = User::find($event->user->id);
        if ($user->statusName == 'unconfirmed') {
            $user->notify(new VerificationEmail);
        }
    }
}