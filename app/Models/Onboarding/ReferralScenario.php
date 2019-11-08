<?php

namespace App\Models\Onboarding;

use App\Models\User;

class ReferralScenario extends BaseScenario
{
    public function isCompleted(User $user)
    {
        if ($this->getCompletions($user) >= 3) {
            return true;
        }

        if ($this->getCompletions() < 3 && $this->getCompletions() > 0) {
            /**
             * TODO: referral links
             */
            // $this->complete($user);

            return true;
        }

        return false;
    }
}
