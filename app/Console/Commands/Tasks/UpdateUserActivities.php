<?php
namespace App\Console\Commands\Tasks;

use App\Models\UserActivities;
use App\Models\UserCertificate;
use App\Models\UserCourse;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateUserActivities extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'task:update_activities';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates user activities for homepage';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->updateBoughtCourse();
        $this->updateFinishedCourseActivity();
        $this->updateCertifiedActivity();

        $this->clearOldActivity();
    }

    private function updateBoughtCourse()
    {
        $userBoughtCourse = UserCourse::with(['course', 'user'])
            ->orderBy('id', 'DESC')
            ->first();

        if (! $userBoughtCourse) {
             return;
        }

        (new UserActivities)->create([
            'user_id'       => $userBoughtCourse->user_id,
            'activity_text' => 'Bought ' . $userBoughtCourse->course->title,
            'activity_type' => UserActivities::COURSE_BOUGHT,
            'activity_time' => $userBoughtCourse->created_at
        ]);
    }

    private function updateFinishedCourseActivity()
    {
        $userFinishedCourse = UserCourse::with(['course', 'user'])
            ->orderBy('completed_at', 'DESC')
            ->first();

        if (!$userFinishedCourse) {
            return;
        }

        (new UserActivities)->create([
            'user_id'       => $userFinishedCourse->user_id,
            'activity_text' => 'Completed ' . $userFinishedCourse->course->title,
            'activity_type' => UserActivities::COURSE_FINISHED,
            'activity_time' => $userFinishedCourse->completed_at
        ]);
    }

    private function updateCertifiedActivity()
    {
        $userCertified = UserCertificate::orderBy('id', 'DESC')->first();

        if (!$userCertified) {
            return;
        }

        (new UserActivities)->create([
            'user_id'       => $userCertified->user_id,
            'activity_text' => 'Got certified on ' . $userCertified->test->course->title,
            'activity_type' => UserActivities::GOT_CERTIFIED,
            'activity_time' => $userCertified->created_at
        ]);
    }

    private function clearOldActivity()
    {
        DB::delete("
          DELETE `user_activities` FROM `user_activities` 
          LEFT JOIN (SELECT `id` FROM `user_activities` ORDER BY `id` DESC LIMIT 3) as ua2 
          ON user_activities.id = ua2.id
          WHERE ua2.id IS NULL
        ");
    }
}
