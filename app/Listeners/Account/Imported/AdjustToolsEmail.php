<?php
namespace App\Listeners\Account\Imported;

use App\Events\User\ImportedUserMerged;
use App\Listeners\Account\Helpers\EmailUpdater;
use App\Models\CourseTool;

class AdjustToolsEmail
{
    use EmailUpdater;

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
            $courseTools = CourseTool::where('course_id', '=', $course->course_id)->get();

            if (is_null($courseTools)) {
                continue;
            }

            foreach ($courseTools as $courseTool) {
                foreach (config('tools') as $tool) {
                    if ($tool['name'] != $courseTool->tool_name) {
                        continue;
                    }

                    $this->sendEmailUpdateRequest(
                        $event->importedUser->email,
                        $event->user->email,
                        $tool['updateUrl']
                    );
                }
            }
        }
    }
}
