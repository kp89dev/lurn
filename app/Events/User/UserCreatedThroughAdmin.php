<?php
namespace App\Events\User;

use App\Models\User;

class UserCreatedThroughAdmin
{
    public $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }
}
