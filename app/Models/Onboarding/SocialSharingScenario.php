<?php

namespace App\Models\Onboarding;

use App\Models\User;

class SocialSharingScenario extends BaseScenario
{
    public function isCompleted(User $user)
    {
        /**
         * TODO: social sharing
         */
        if ($this->getCompletions($user)) {
            return true;
        }

        return false;
    }
}
