<?php
namespace App\Listeners\Gamification;

use App\Events\Social\FacebookPostShared;
use Gamification\Gamification;

class AwardFacebookPostPoints
{
    public function handle(FacebookPostShared $event)
    {
        $api = new Gamification();

        $api->facebookPost([
            'userId' => $event->user->id,
            'email' => $event->user->email,
            'details' => [
                'postId' => $event->postId
            ]
        ]);
    }
}
