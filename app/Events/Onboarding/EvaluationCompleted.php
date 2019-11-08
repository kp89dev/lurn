<?php
namespace App\Events\Onboarding;

use App\Models\User;

class EvaluationCompleted
{
    /**
     * @type User
     */
    public $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }
}
