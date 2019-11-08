<?php
namespace App\Listeners\Account;

use App\Events\User\UserEmailChanged;
use App\Listeners\Account\Helpers\EmailUpdater;
use App\Models\CourseTool;

class ToolEmailUpdater
{
    use EmailUpdater;

    /**
     * @param UserEmailChanged $event
     */
    public function handle(UserEmailChanged $event)
    {
        $courses = $event->user->courses()->get();

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
                        $event->user->getOriginal()['email'],
                        $event->user->email,
                        $tool['updateUrl']
                    );
                }
            }
        }
    }
}
