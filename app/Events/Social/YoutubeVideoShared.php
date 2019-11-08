<?php
namespace App\Events\Social;

use App\Models\User;

class YoutubeVideoShared
{
    /**
     * @var User
     */
    public $user;

    /**
     * @var string
     */
    public $youtubeLink;

    public function __construct(User $user, $youtubeLink)
    {
        $this->user = $user;
        $this->youtubeLink = $youtubeLink;
    }
}
