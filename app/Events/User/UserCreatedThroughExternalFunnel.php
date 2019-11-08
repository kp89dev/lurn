<?php

namespace App\Events\User;

use App\Models\User;

/**
 * This event should be fired when user accounts are created through external funnels.
 * Use case example: We want to award points to users who signed up through Lurn10x -
 * this event is fired and the AwardLurn10xFunnelPoints listener catches it and awards points.
 */
class UserCreatedThroughExternalFunnel
{
    public $user;

    public $funnel;

    public function __construct(User $user, $funnel = null)
    {
        $this->user = $user;
        $this->funnel = $funnel;
    }
}
