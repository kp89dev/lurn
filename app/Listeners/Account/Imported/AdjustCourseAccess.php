<?php
namespace App\Listeners\Account\Imported;

use App\Events\User\ImportedUserMerged;
use App\Models\Course;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdjustCourseAccess
{
    /**
     * @param ImportedUserMerged $event
     *
     * @return bool
     */
    public function handle(ImportedUserMerged $event)
    {
        $courses = $event->importedUser->getImportedUserCoursesIds();

        if (! $courses->count()) {
            return true; //no course access
        }

        foreach ($courses as $course) {
            $event->user->courses()->attach($course->course_id);
        }
    }
}
