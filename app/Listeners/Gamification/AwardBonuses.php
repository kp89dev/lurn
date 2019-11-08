<?php

namespace App\Listeners\Gamification;

use App\Models\Bonus;
use App\Events\User\UserEnrolled;
use App\Models\UserPointActivity;

class AwardBonuses
{
    /**
     * Handle the event.
     *
     * @param UserPointActivity $activity
     * @return void
     */
    public function handle(UserPointActivity $activity)
    {
        $userPoints = $activity->user->pointsEarned;

        Bonus::where('points_required', '<=', $userPoints)
            ->each(function ($bonus) use ($activity) {
            	if (! $activity->user->enrolled($bonus->course)) {
            	    $activity->user->enroll($bonus->course);

                    event(new UserEnrolled($activity->user, $bonus->course));
                }
            });
    }
}
