<?php
namespace App\Listeners\Gamification;

use App\Events\User\UserEnrolled;
use Gamification\Gamification;

class AwardCourseEnrollmentPoints
{
    public function handle(UserEnrolled $event)
    {
        $api = new Gamification;

        if ($event->course->free) {
            $courseType   = 'free';
            $coursePrice  = '0.00';
            $coursePoints = $event->course->buy_with_points ? -$event->course->buy_with_points : 0;
            
            if ($coursePoints) {
                $description = 'Enrolled in a course with points';
            } else {
                $description = 'Enrolled in a free course';
            }
        } else {
            $courseType   = 'paid';
            $coursePrice  = $event->course->infusionsoft->price ?: '0.00';
            $coursePoints = $event->course->infusionsoft->price * 25;
            $description  = 'Enrolled in a premium course';
        }

        $courseMeta = get_class($event->course) . ':' . $event->course->id;

        $api->buyCourse([
            'userId'      => $event->user->id,
            'email'       => $event->user->email,
            'description' => $description,
            'metadata'    => $courseMeta,
            'points'      => $coursePoints,
            'pending'     => false,
            'details'     => [
                'course_type'  => $courseType,
                'course_price' => $coursePrice
            ],
        ]);
    }
}
