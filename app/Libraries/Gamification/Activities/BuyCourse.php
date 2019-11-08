<?php

namespace Gamification\Activities;

use Gamification\Transformers\Activity;

class BuyCourse extends Activity
{
    public function __construct()
    {
        $this->schema = [
            'details.course_price' => 'required_if:details.course_type,paid',
            'details.course_type' => 'required|in:free,paid'
        ];
    }

    public function handle($data)
    {
        return $this->transform($data);
    }
}