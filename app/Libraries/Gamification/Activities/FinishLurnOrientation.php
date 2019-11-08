<?php

namespace Gamification\Activities;

use Gamification\Transformers\Activity;

class FinishLurnOrientation extends Activity
{
    public function __construct()
    {
        $this->description = 'Completed Orientation';

        $this->points = 200;
    }

    public function handle($data)
    {
        return $this->transform($data);
    }
}