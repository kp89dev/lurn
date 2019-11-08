<?php
namespace App\Events\Social;

use App\Models\User;

class TwitterPostShared
{
    /**
     * @var User
     */
    public $user;

    /**
     * @var int
     */
    public $postId;

    public function __construct(User $user, $postId)
    {
        $this->user = $user;
        $this->postId = $postId;
    }
}
