<?php
namespace App\Events\User;

use App\Models\User;
use Illuminate\Queue\SerializesModels;

class UserRecruited
{
    use SerializesModels;

    /**
     * @type User
     */
    public $recruiter;

    /**
     * @type User
     */
    public $user;

    public function __construct(User $user, User $recruiter)
    {
        $this->recruiter = $recruiter;
        $this->user = $user;
    }
}
