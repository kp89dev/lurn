<?php
namespace Unit\Service\Workflows\Backend\Selectors;

use App\Models\CourseInfusionsoft;
use App\Models\User;
use App\Models\UserCourse;
use App\Services\Workflows\Backend\UserSpecificConditionChecker;
use App\Services\Workflows\View\Conditions\TotalPurchasedAmount;

class TotalPurchasedAmountTest extends \TestCase
{
    /**
     * @test
     * @group workflows
     */
    public function user_total_amount_select_passes()
    {
        $user = $this->prepareRequiredData();
        $conditions = [
            [
                'type'   => 'and',
                'key'    => TotalPurchasedAmount::class,
                'values' => [['value' => '>'], ['value' => '1600']]
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
    public function user_total_amount_select_fails()
    {
        $user = $this->prepareRequiredData();


        $conditions = [
            [
                'type'   => 'and',
                'key'    => TotalPurchasedAmount::class,
                'values' => [['value' => '>'], ['value' => '1900']]
            ]
        ];
        self::assertFalse(
            (new UserSpecificConditionChecker($user->id, ['conditions' => $conditions]))->passes()
        );
    }

    /**
     * @return mixed
     */
    private function prepareRequiredData()
    {
        $user = factory(User::class)->create();
        $course1 = factory(CourseInfusionsoft::class)->create([
            'upsell' => 0,
            'price'  => 1200
        ]);
        $course2 = factory(CourseInfusionsoft::class)->create([
            'upsell' => 0,
            'price'  => 200
        ]);
        $course3 = factory(CourseInfusionsoft::class)->create([
            'upsell' => 0,
            'price'  => 300
        ]);

        factory(UserCourse::class)->create([
            'user_id'                => $user->id,
            'course_infusionsoft_id' => $course1->id
        ]);

        factory(UserCourse::class)->create([
            'user_id'                => $user->id,
            'course_infusionsoft_id' => $course2->id
        ]);
        factory(UserCourse::class)->create([
            'user_id'                => $user->id,
            'course_infusionsoft_id' => $course3->id
        ]);

        return $user;
    }
}
