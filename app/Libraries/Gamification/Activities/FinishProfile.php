<?php

namespace Gamification\Activities;

use Gamification\Transformers\Activity;

class FinishProfile extends Activity
{
    public function __construct()
    {
        $this->description = 'Completed Your Profile';

        $this->points = 100;
    }

    public function handle($data)
    {
        return $this->transform($data);
    }
}