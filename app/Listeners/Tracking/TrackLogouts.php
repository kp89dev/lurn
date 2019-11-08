<?php
namespace App\Listeners\Tracking;

use App\Models\User;
use App\Services\Contracts\TrackerInterface;
use Illuminate\Auth\Events\Logout;

class TrackLogouts
{
    public function handle(Logout $event)
    {
        if (! $event->user instanceof User) {
            return true;
        }
        
        $tracker = app()->make(TrackerInterface::class);
        $tracker->track('Logout', [
            'email' => $event->user->email
        ], TRUE);
    }
}
