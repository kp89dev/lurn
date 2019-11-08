<?php
namespace Unit\Service\Workflows\Backend\Selectors;

use App\Models\User;
use App\Models\UserLogin;
use App\Services\Workflows\Backend\UserSpecificConditionChecker;
use App\Services\Workflows\View\Conditions\Country;

class CountryTest extends \TestCase
{
    /**
     * @test
     * @group workflows
     */
    public function user_with_country_passes_selection()
    {
        $user = factory(User::class)->create();
        factory(UserLogin::class)->create([
            'user_id' => $user->id,
            'country' => 'Romania'
        ]);
        $conditions = [[
            'type'   => 'and',
            'key'    => Country::class,
            'values' => [['value' => 'Romania']]
        ]];

        self::assertTrue(
            (new UserSpecificConditionChecker($user->id, ['conditions'=>$conditions]))->passes()
        );
    }

    /**
     * @test
     * @group workflows
     */
    public function user_fails_selection()
    {
        $user = factory(User::class)->create();
        factory(UserLogin::class)->create([
            'user_id' => $user->id,
            'country' => 'United States'
        ]);
        $conditions = [
            [
                'type'   => 'and',
                'key'    => Country::class,
                'values' => [['value' => 'Romania']]
            ]
        ];

        self::assertFalse(
            (new UserSpecificConditionChecker($user->id, ['conditions'=>$conditions]))->passes()
        );
    }
}
