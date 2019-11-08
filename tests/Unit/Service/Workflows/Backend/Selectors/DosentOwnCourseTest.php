<?php
namespace Unit\Service\Workflows\Backend\Selectors;

use App\Models\User;
use App\Models\UserCourse;
use App\Services\Workflows\Backend\UserSpecificConditionChecker;
use App\Services\Workflows\View\Conditions\DosentOwnCourse;

class DosentOwnCourseTest extends \TestCase
{
    /**
     * @test
     * @group workflows
     */
    public function user_selected_when_deosnt_own_course()
    {
        $user = factory(User::class)->create();
        factory(UserCourse::class)->create([
            'user_id'   => $user->id,
            'course_id' => 8
        ]);

        $conditions = [
            [
                'type'   => 'and',
                'key'    => DosentOwnCourse::class,
                'values' => [['value' => 7]]
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
    public function user_not_selected_when_owns_course()
    {
        $user = factory(User::class)->create();
        factory(UserCourse::class)->create(['user_id' => $user->id, 'course_id' => 7]);

        $conditions = [
            [
                'type'   => 'and',
                'key'    => DosentOwnCourse::class,
                'values' => [['value' => 7]]
            ]
        ];
        self::assertFalse(
            (new UserSpecificConditionChecker($user->id, ['conditions' => $conditions]))->passes()
        );
    }
}
