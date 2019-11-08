<?php
namespace App\Listeners\Gamification;

use App\Events\Onboarding\ProfileCompleted;
use Gamification\Gamification;

class AwardProfileCompletionPoints
{
    public function handle(ProfileCompleted $event)
    {
        $api = new Gamification();

        $api->finishProfile([
            'userId' => $event->user->id,
            'email' => $event->user->email,
        ]);
    }
}
