<?php
namespace App\Listeners\Gamification;

use App\Events\Social\SocialMediaShared;
use Gamification\Gamification;

class AwardInstagramPostPoints
{
    public function handle(SocialMediaShared $event)
    {
        $api = new Gamification();

        $api->instagramPost([
            'userId' => $event->user->id,
            'email' => $event->user->email,
            'details' => [
                'postId' => $event->postId
            ]
        ]);
    }
}
