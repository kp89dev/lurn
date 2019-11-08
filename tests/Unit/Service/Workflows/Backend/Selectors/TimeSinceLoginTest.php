<?php
namespace Unit\Service\Workflows\Backend\Selectors;

use App\Models\User;
use App\Models\UserLogin;
use App\Services\Workflows\Backend\UserSpecificConditionChecker;
use App\Services\Workflows\View\Conditions\TimeSinceLogin;

class TimeSinceLoginTest extends \TestCase
{
    /**
     * @test
     * @group workflows
     */
    public function user_last_login_select_passes()
    {
        $user = factory(User::class)->create();
        factory(UserLogin::class)->create([
            'user_id'   => $user->id,
            'created_at' => date('Y-m-d H:i:s', strtotime('-8 days'))
        ]);


        $conditions = [
            [
                'type'   => 'and',
                'key'    => TimeSinceLogin::class,
                'values' => [['value' => '7'], ['value' => 'days']]
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
    public function user_last_login_select_fails()
    {
        $user = factory(User::class)->create();
        factory(UserLogin::class)->create([
            'user_id'   => $user->id,
            'created_at' => date('Y-m-d H:i:s', strtotime('-5 days'))
        ]);

        $conditions = [
            [
                'type'   => 'and',
                'key'    => TimeSinceLogin::class,
                'values' => [['value' => '7'], ['value' => 'days']]
            ]
        ];
        self::assertFalse(
            (new UserSpecificConditionChecker($user->id, ['conditions' => $conditions]))->passes()
        );
    }
}
