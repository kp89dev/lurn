<?php
namespace App\Events\User;

use App\Models\User;
use Illuminate\Queue\SerializesModels;

class UserMerged
{
    use SerializesModels;

    /**
     * @type User
     */
    public $userMerged;

    /**
     * @type User
     */
    public $user;

    public function __construct(User $user, User $userMerged)
    {
        $this->userMerged = $userMerged;
        $this->user = $user;
    }
}
