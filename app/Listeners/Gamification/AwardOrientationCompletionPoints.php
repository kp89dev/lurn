<?php
namespace App\Listeners\Gamification;

use App\Events\Onboarding\OrientationCompleted;
use Gamification\Gamification;

class AwardOrientationCompletionPoints
{
    public function handle(OrientationCompleted $event)
    {
        $api = new Gamification();

        $api->finishLurnOrientation([
            'userId' => $event->user->id,
            'email' => $event->user->email,
            'details' => [
                'dateCompletion' => \date('Y-m-d'),
            ]
        ]);
    }
}
