<?php
namespace App\Listeners\Tracking;

use App\Events\User\UserCreatedThroughInfusionsoft;
use App\Services\Contracts\TrackerInterface;

class TrackInfusionsoftRegisteredUser
{
    public function handle(UserCreatedThroughInfusionsoft $event)
    {
        $tracker = app()->make(TrackerInterface::class);
        $tracker->track('Infusionsoft user added', [
            'email' => $event->user->email,
            'name'  => $event->user->name
        ], TRUE);
    }
}
