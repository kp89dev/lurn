<?php

namespace App\Models\Onboarding;

use App\Models\User;

class CourseEnrollmentScenario extends BaseScenario
{
    public function isCompleted(User $user)
    {
        if ($this->getCompletions($user)) {
            return true;
        }

        if ($user->courses()->whereNull('invoice_id')->count()) {
            $this->awardPoints($user);

            return true;
        }
    }
}
