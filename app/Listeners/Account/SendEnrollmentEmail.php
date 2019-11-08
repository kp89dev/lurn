<?php

namespace App\Listeners\Account;

use App\Models\User;
use App\Models\Course;
use App\Models\UserCourse;
use App\Notifications\Account\EnrollmentEmail;
use Illuminate\Support\Facades\Log;

class SendEnrollmentEmail
{
    public function handle($event)
    {
        $user = User::find($event->user->id);
        $course = Course::find($event->course->id);
        $userCourse = UserCourse::where('course_id',$course->id)
            ->where('user_id', $user->id)
            ->get()
            ->last();
        if (is_null($userCourse->cancelled_at) && $userCourse->status == 0) {
            $user->notify(new EnrollmentEmail($course));
        }
    }
}
