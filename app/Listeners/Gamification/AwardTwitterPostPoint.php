<?php
namespace App\Listeners\Gamification;

use App\Events\Social\TwitterPostShared;
use Gamification\Gamification;

class AwardTwitterPostPoints
{
    public function handle(TwitterPostShared $event)
    {
        $api = new Gamification();

        $api->twitterPost([
            'userId' => $event->user->id,
            'email' => $event->user->email,
            'details' => [
                'postId' => $event->postId
            ]
        ]);
    }
}
