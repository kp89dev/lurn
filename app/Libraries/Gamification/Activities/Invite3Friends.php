<?php

namespace Gamification\Activities;

use Gamification\Transformers\Activity;

class Invite3Friends extends Activity
{
    public function __construct()
    {
        $this->description = 'Invited 3 Friends';

        $this->points = 200;
    }

    public function handle($data)
    {
        return $this->transform($data);
    }
}