<?php
namespace App\Events\User;

use App\Models\User;

class UserEmailChanged
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
