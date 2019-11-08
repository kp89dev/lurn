<?php

namespace App\Listeners\Gamification;

use App\Events\Course\CourseCompleted;
use Gamification\Gamification;

class AwardCourseCompletionPoints
{
    public function handle(CourseCompleted $event)
    {
        $api = new Gamification;

        if ($event->course->free) {
            $courseType = 'free';
            $coursePrice = '0.00';
            $coursePoints = 300;
            $description = 'Completed a free course';
        }
        else {
            $courseType = 'paid';
            $coursePrice = $event->course->infusionsoft->price ?: '0.00';
            $coursePoints = $event->course->infusionsoft->price * 10;
            $description = 'Completed a premium course';
        }

        $courseMeta = get_class($event->course) . ':' . $event->course->id;

        $api->finishCourse([
            'user'        => $event->user,
            'userId'      => $event->user->id,
            'email'       => $event->user->email,
            'description' => $description,
            'metadata'    => $courseMeta,
            'points'      => $coursePoints,
            'details'     => [
                'course_type'  => $courseType,
                'course_price' => $coursePrice,
            ],
        ]);
    }
}
