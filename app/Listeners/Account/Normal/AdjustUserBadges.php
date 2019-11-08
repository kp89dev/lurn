<?php
namespace App\Listeners\Account\Normal;

use App\Events\User\UserMerged;

class AdjustUserBadges
{
    /**
     * @param UserMerged $event
     */
    public function handle(UserMerged $event)
    {
        $badges = $event->userMerged->badges;

        foreach ($badges as $badge) {
            tap($badge, function($obj) use ($event) {
                $obj->user_id = $event->user->id;
            })->save();
        }
    }
}
