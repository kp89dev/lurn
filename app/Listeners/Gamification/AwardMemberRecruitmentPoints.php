<?php
namespace App\Listeners\Gamification;

use App\Events\User\UserRecruited;
use Gamification\Gamification;

class AwardMemberRecruitmentPoints
{
    public function handle(UserRecruited $event)
    {
        $api = new Gamification;

        $api->recruitNewMember([
            'user'        => $event->recruiter,
            'userId'      => $event->recruiter->id,
            'email'       => $event->recruiter->email,
            'memberEmail' => $event->user->email,
        ]);
    }
}
