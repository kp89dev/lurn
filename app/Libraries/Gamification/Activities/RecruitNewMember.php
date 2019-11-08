<?php

namespace Gamification\Activities;

use Gamification\Transformers\Activity;

class RecruitNewMember extends Activity
{
    public function __construct()
    {
        $this->description = 'Recruited a New Member';

        $this->points = 300;
    }

    public function handle($data)
    {
        return $this->transform($data);
    }
}