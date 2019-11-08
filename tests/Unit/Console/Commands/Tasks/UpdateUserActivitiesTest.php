<?php
namespace Tests\Unit\Console\Commands\Tasks;

use App\Models\Course;
use App\Models\User;
use App\Models\UserActivities;
use App\Models\UserCertificate;
use App\Models\UserCourse;

class UpdateUserActivitiesTest extends \TestCase
{
    /**
     * @test
     */
    public function bought_activity_is_updated_successfully()
    {
        $courses = factory(Course::class, 5)->create();
        $user    = factory(User::class)->create();

        $courses->each(function ($course) use ($user) {
            $user->courses()->attach($course);
        });

        $this->artisan('task:update_activities');

        $this->assertDatabaseHas('user_activities', [
            'user_id'       => $user->id,
            'activity_type' => UserActivities::COURSE_BOUGHT
        ]);
    }

    /**
     * @test
     */
    public function finished_course_activity_is_updated_successfully()
    {
        $courses = factory(Course::class, 5)->create();
        $user    = factory(User::class)->create();

        $courses->each(function ($course) use ($user) {
            $user->courses()->attach($course);
        });

        (new UserCourse)->where('user_id', $user->id)
            ->where('course_id', $courses->first()->id)
            ->first()
            ->markCourseAsCompleted();

        $this->artisan('task:update_activities');

        $this->assertDatabaseHas('user_activities', [
            'user_id'       => $user->id,
            'activity_type' => UserActivities::COURSE_FINISHED
        ]);
    }

    /**
     * @test
     */
    public function certified_activity_is_updated_successfully()
    {
        $userCertificate = factory(UserCertificate::class, 5)->create();

        $this->artisan('task:update_activities');

        $lastUserCertificate = $userCertificate->last();

        $this->assertDatabaseHas('user_activities', [
            'user_id'       => $lastUserCertificate->user_id,
            'activity_type' => UserActivities::GOT_CERTIFIED
        ]);

        self::assertCount(1, UserActivities::all(), 'Only one certified activity expected');
    }

    /**
     * @test
     */
    public function clearing_old_acivity_successfully()
    {
        factory(UserActivities::class,  10)->create();

        $this->artisan('task:update_activities');
        self::assertCount(3, UserActivities::all(), 'Only 3 activities expected to be found');
    }
}
