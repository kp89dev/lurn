<?php
namespace App\Events\User;

use App\Models\Course;
use App\Models\User;

class UserCreatedThroughInfusionsoft
{
    public $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }
}
