<?php

namespace Tests\Feature\Admin;

use App\Models\CourseBonus;
use Mockery;
use Tests\TestCase;
use App\Models\Course;

class ResourceControllerTest extends \SuperAdminLoggedInTestCase
{
    /**
     * @test
     */
    public function index_redirects()
    {
        $course = factory(Course::class)->create();

        $response = $this->get(route('course-bonuses.index', ['course' => $course]));

        $response->assertStatus(302)
            ->assertRedirect(route(
                'courses.index',
                [
                    'bonuses' => true,
                    'course' => $course,
                    'excludeFromBonus[0]' => $course->id
                ]
            ));
    }

    /**
     * @test
     */
    public function store()
    {
        $course = factory(Course::class)->create();

        $bonus = factory(Course::class)->create();

        $this->call('POST', route('course-bonuses.store', [$course->id]), [
            'bonus_course_id' => $bonus->id
        ]);

        $this->assertDatabaseHas('course_bonuses', [
            'course_id' => $course->id,
            'bonus_course_id' => $bonus->id,
        ]);
    }

    /**
     * @test
     */
    public function destroy_success()
    {
        $course = factory(Course::class)->create();

        $bonus = factory(Course::class)->create();

        $courseBonus = factory(CourseBonus::class)->create([
            'course_id' => $course->id,
            'bonus_course_id' => $bonus->id,
        ]);

        $this->call('DELETE', route('course-bonuses.destroy', [$course->id, $courseBonus->id]));
        $this->assertDatabaseMissing('course_bonuses', [
            'id' => $courseBonus->id
        ]);
    }
}
