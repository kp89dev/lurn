<?php

namespace Gamification\Activities;

use Gamification\Transformers\Activity;

class FinishEvaluation extends Activity
{
    public function __construct()
    {
        $this->description = 'Completed Your First Evaluation';

        $this->points = 500;
    }

    public function handle($data)
    {
        return $this->transform($data);
    }
}