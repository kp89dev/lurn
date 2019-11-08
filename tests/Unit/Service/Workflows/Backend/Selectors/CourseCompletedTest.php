<?php
namespace Unit\Service\Workflows\Backend\Selectors;

use App\Models\User;
use App\Models\UserCourse;
use App\Models\UserLogin;
use App\Services\Workflows\Backend\UserSpecificConditionChecker;
use App\Services\Workflows\View\Conditions\CourseCompleted;

class CourseCompletedTest extends \TestCase
{
    /**
     * @test
     * @group workflows
     */
    public function user_with_course_completed_passes()
    {
        $user = factory(User::class)->create();
        $userCourse = factory(UserCourse::class)->create([
            'user_id' => $user->id,
            'completed_at' => date('Y-m-d H:i:s', strtotime('-2 minute'))
        ]);
        $conditions = [
            [
                'type'   => 'and',
                'key'    => CourseCompleted::class,
                'values' => ['value' => $userCourse->course_id]
            ]
        ];
        self::assertTrue(
            (new UserSpecificConditionChecker($user->id, ['conditions' => $conditions]))->passes()
        );
    }

    /**
     * @test
     * @group workflows
     */
    public function user_without_course_completed_fails()
    {
        $user = factory(User::class)->create();
        $userCourse = factory(UserCourse::class)->create([
            'user_id' => $user->id,
            'completed_at' => date('Y-m-d H:i:s', strtotime('-7 minute'))
        ]);
        $conditions = [
            [
                'type'   => 'and',
                'key'    => CourseCompleted::class,
                'values' => ['value' => $userCourse->course_id]
            ]
        ];
        self::assertFalse(
            (new UserSpecificConditionChecker($user->id, ['conditions' => $conditions]))->passes()
        );
    }
}
