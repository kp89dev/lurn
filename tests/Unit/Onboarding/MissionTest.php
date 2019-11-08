<?php

namespace Tests\Unit\Onboarding;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use App\Models\Onboarding\Mission;
use App\Models\User;


class MissionTest extends \TestCase
{
    public $scenarios;

    protected function setUp()
    {
        parent::setUp();
        //create scenarios 1-7
        $this->scenarios = [
            ['id' => 1, 'points' => 100, 'message' => 'Complete Your Profile'],
            ['id' => 2, 'points' => 200, 'message' => 'Complete Your Evaluation'],
            ['id' => 3, 'points' => 200, 'message' => 'Enroll in a Course'],
            ['id' => 4, 'points' => 300, 'message' => 'Spread the Love'],
            ['id' => 5, 'points' => 300, 'message' => 'Recruit a Friend'],
            ['id' => 6, 'points' => 100, 'message' => 'Finish a Course'],
            ['id' => 7, 'points' => 100, 'message' => 'Buy a Course'],
        ];

        DB::table('scenarios')
            ->insert($this->scenarios);
    }

    /**
     * @test
     */
    public function assert_new_mission_loads_scenarios()
    {
        $user = factory(User::class)->create();

        $this->actingAs($user);

        $mission = new Mission($user);

        foreach($mission->scenarios as $k=>$s) {
            $this->assertEquals($s->id, $this->scenarios[$k-1]['id']);
            $this->assertEquals($s->points, $this->scenarios[$k-1]['points']);
            $this->assertEquals($s->message, $this->scenarios[$k-1]['message']);
        }
    }

    /**
     * @test
     */
    public function assert_scenarios_are_correct_class()
    {
        $user = factory(User::class)->create();

        $this->actingAs($user);

        $mission = new Mission($user);

        $expectedClasses = [
            1 => 'App\Models\Onboarding\ProfileCompleteScenario',
            2 => 'App\Models\Onboarding\EvaluationCompleteScenario',
            3 => 'App\Models\Onboarding\CourseEnrollmentScenario',
            4 => 'App\Models\Onboarding\SocialSharingScenario',
            5 => 'App\Models\Onboarding\ReferralScenario',
        ];

        foreach($mission->scenarios as $k=>$s) {
            $this->assertEquals(get_class($s), $expectedClasses[$k]);
        }
    }
}
