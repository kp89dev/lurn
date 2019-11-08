<?php

namespace Tests\Feature\Webhook;

use App\Models\CourseVanillaForum;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\UserLoggedInTestCase;
use App\Models\Course;
use App\Models\UserCourse;


class VanillaForumTest extends UserLoggedInTestCase
{
    /**
     * @test
     */
    public function prep_available()
    {
        $course = factory(Course::class)->create();
        $courseVF = factory(CourseVanillaForum::class)->create(['course_id' => $course->id]);


        $response = $this->post(route('webhook.vanilla.request', ['course' => $course]));

        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function prep_skips_rules_when_appropriate()
    {
        $course = factory(Course::class)->create();
        $courseVF = factory(CourseVanillaForum::class)->create(['course_id' => $course->id]);
        $userCourse = factory(UserCourse::class)->create([
            'user_id'       => $this->user->id,
            'course_id'     => $course->id,
            'status'        => 3,
            'forum_rules'   => 'testrules'
        ]);

        $response = $this->post(route('webhook.vanilla.request', ['course' => $course]));

        $obj = json_decode($response->getContent());

        $this->assertObjectHasAttribute('showRules', $obj);
        $this->assertFalse($obj->showRules);
    }

    /**
     * @test
     */
    public function prep_shows_rules_when_appropriate()
    {
        $course = factory(Course::class)->create();
        $courseVF = factory(CourseVanillaForum::class)->create(['course_id' => $course->id]);
        $userCourse = factory(UserCourse::class)->create([
            'user_id'       => $this->user->id,
            'course_id'     => $course->id,
            'status'        => 1,
            'forum_rules'   => 'testrules'
        ]);

        $response = $this->post(route('webhook.vanilla.request', ['course' => $course]));

        $response->assertJson(['showRules' => false]);
    }
}
