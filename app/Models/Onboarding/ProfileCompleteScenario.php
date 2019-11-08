<?php

namespace App\Models\Onboarding;

use App\Events\Onboarding\ProfileCompleted;
use App\Models\User;

class ProfileCompleteScenario extends BaseScenario
{
    protected $table = 'scenarios';

    public function isCompleted(User $user)
    {
        if ($this->getCompletions($user)) {
            return true;
        }

        if (substr($user->getPrintableImageUrl(), 0, 24) != "https://www.gravatar.com") {
            $this->awardPoints($user);
            event(new ProfileCompleted($user));

            return true;
        } else {
            // Check if gravatar is setup.
            $headers = get_headers($user->getPrintableImageUrl() . '?d=404');

            if (substr($headers[0], 9, 3) != '404') {
                $this->awardPoints($user);
                event(new ProfileCompleted($user));

                return true;
            }
        }

        return false;
    }
}
