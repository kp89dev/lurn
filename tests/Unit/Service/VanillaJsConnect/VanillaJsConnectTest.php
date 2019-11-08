<?php

namespace Unit\Service\VanillaJsConnect;

use \App\Models\Course;
use App\Models\CourseVanillaForum;

class VanillaJsConnectTest extends \TestCase
{
    
    public function testVanillaForumPrepRequestNoUser()
    {
        $course = factory(Course::class)->create();
        factory(CourseVanillaForum::class)->create(['course_id' => $course->id, 'forum_rules' => null]);

        $response = $this->post(route('webhook.vanilla.request',$course->id));
        $response->assertStatus(200)
            ->assertSee('{"showRules":false}');
    }
    
    public function testVanillaNoUserRedirect()
    {
        $response = $this->get(route('webhook.vanilla'));
        $response->assertStatus(302)
            ->assertSee('login');
    }
}
