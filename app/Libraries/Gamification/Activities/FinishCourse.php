<?php

namespace Gamification\Activities;

use Gamification\Transformers\Activity;

class FinishCourse extends Activity
{
    public function __construct()
    {
        $this->schema = [
            'details.course_price' => 'required_if:details.course_type,paid',
            'details.course_type' => 'required|in:free,paid'
        ];

        $this->description = 'Completed a course';
    }

    public function handle($data)
    {
        return $this->transform($data);
    }
}