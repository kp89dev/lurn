<?php
namespace App\Listeners\Tracking;

use App\Services\Contracts\TrackerInterface;
use Illuminate\Auth\Events\Registered;

class TrackRegisteredUser
{
    public function handle($event)
    {
        $tracker = app()->make(TrackerInterface::class);
        $tracker->track('Registered', [
            'email' => $event->user->email
        ], TRUE);
    }
}
