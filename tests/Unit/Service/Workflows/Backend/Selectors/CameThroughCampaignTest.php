<?php
namespace Unit\Service\Workflows\Backend\Selectors;

use App\Models\Tracker\Campaign;
use App\Models\Tracker\Identity;
use App\Models\Tracker\Visit;
use App\Models\User;
use App\Services\Workflows\Backend\UserSpecificConditionChecker;
use App\Services\Workflows\View\Conditions\CameThroughCampaign;

class CameThroughCampaignTest extends \TestCase
{
    /**
     * @test
     * @group workflows
     */
    public function user_which_came_through_campaign_passes()
    {
        $user     = factory(User::class)->create();
        $campaign = factory(Campaign::class)->create();
        factory(Identity::class)->create([
            'user_id'    => $user->id,
            'visitor_id' => 'test'
        ]);
        factory(Visit::class)->create([
            'campaign_id' => $campaign->id,
            'visitor_id' => 'test'
        ]);


        $conditions = [
            [
                'type'   => 'and',
                'key'    => CameThroughCampaign::class,
                'values' => [['value' => $campaign->name]]
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
    public function user_which_doesnt_have_campaign_fails()
    {
        $user     = factory(User::class)->create();
        $campaign = factory(Campaign::class)->create();
        factory(Identity::class)->create([
            'user_id'    => $user->id,
            'visitor_id' => 'test'
        ]);
        factory(Visit::class)->create([
            'campaign_id' => $campaign->id,
            'visitor_id' => 'test'
        ]);

        $conditions = [
            [
                'type'   => 'and',
                'key'    => CameThroughCampaign::class,
                'values' => [['value' => 'Other']]
            ]
        ];
        self::assertFalse(
            (new UserSpecificConditionChecker($user->id, ['conditions' => $conditions]))->passes()
        );
    }
}
