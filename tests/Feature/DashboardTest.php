<?php

namespace Feature;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\Module;
use App\Models\User;

class DashboardTest extends \LoggedInTestCase
{
    /**
     * @test
     */
    public function dashboard_is_accessible_to_loggedin_user()
    {
        $this->get(route('dashboard'))
            ->assertStatus(200)
            ->assertSeeText('Newsfeed');
    }

    /**
     * @test
     */
    public function users_courses_get_listed()
    {
        $courses = factory(Course::class, 5)->create();
        $this->user->courses()->sync($courses);

        $response = $this->get(route('dashboard'));

        foreach ($courses as $course) {
            $response->assertSeeText($course->title);
        }
    }

    /**
     * @test
     */
    public function displays_the_current_course()
    {
        $course = factory(Course::class)->create(['status' => 1]);
        $module = factory(Module::class)->create(['course_id' => $course, 'type' => 'Module', 'status' => 1]);
        factory(Lesson::class)->create(['module_id' => $module->id, 'type' => 'Lesson', 'status' => 1]);

        $this->user->courses()->attach($course);

        $this->get(route('dashboard'))
             ->assertSeeText('Continue Course');
    }

    /**
     * @test
     */
    public function feedback_gets_saved()
    {
        $user = factory(User::class)->create();
        $this->post(url('api/feedback'), [
            'user_id'  => $user->id,
            'grade'    => 10,
            'feedback' => 'lorem ipsum dolor sit amet',
        ]);

        $this->assertDatabaseHas('feedback', ['feedback' => 'lorem ipsum dolor sit amet']);
    }
}
