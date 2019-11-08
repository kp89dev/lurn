<?php

namespace App\Models\Onboarding;

use App\Models\User;

class EvaluationCompleteScenario extends BaseScenario
{
    public function isCompleted(User $user)
    {
        return $this->getCompletions($user);
    }
}
