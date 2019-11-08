<?php
namespace App\Listeners\Tracking;

use App\Services\Contracts\TrackerInterface;
use Illuminate\Auth\Events\Login;

class TrackSuccessfullLogins
{
    public function handle(Login $event)
    {
        $tracker = app()->make(TrackerInterface::class);
        $tracker->track('Login', [
            'email' => $event->user->email
        ], TRUE);
    }
}
