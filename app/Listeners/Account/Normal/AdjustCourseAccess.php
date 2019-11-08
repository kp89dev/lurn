<?php
namespace App\Listeners\Account\Normal;

use App\Events\User\ImportedUserMerged;
use App\Events\User\UserMerged;
use App\Models\Course;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdjustCourseAccess
{
    /**
     * @param UserMerged $event
     *
     * @return bool
     */
    public function handle(UserMerged $event)
    {
        $courses = $event->userMerged->courses()->get();

        foreach ($courses as $course) {
            $event->user->courses()->attach($course);
            $event->userMerged->courses()->detach($course);
        }
    }
}
