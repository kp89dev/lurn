<?php
namespace App\Listeners\Account\Normal;

use App\Events\User\UserMerged;
use App\Listeners\Account\Helpers\EmailUpdater;
use App\Models\CourseTool;

class AdjustToolsEmail
{
    use EmailUpdater;

    /**
     * @param UserMerged $event
     *
     * @return bool
     */
    public function handle(UserMerged $event)
    {
        $courses = $event->userMerged->courses()->get();

        foreach ($courses as $course) {
            $courseTools = CourseTool::where('course_id', '=', $course->id)->get();

            if (is_null($courseTools)) {
                continue;
            }
            
            foreach ($courseTools as $courseTool) {
                foreach (config('tools') as $tool) {
                    if ($tool['name'] != $courseTool->tool_name) {
                        continue;
                    }

                    $this->sendEmailUpdateRequest(
                        $event->userMerged->email,
                        $event->user->email,
                        $tool['updateUrl']
                    );
                }
            }
        }
    }
}
