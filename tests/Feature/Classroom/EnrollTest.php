<?php
namespace Tests\Feature\Classroom;

use App\Models\Course;
use App\Models\User;

class EnrollTest extends \TestCase
{
    /**
     * @test
     */
    public function enrolls_on_free_courses()
    {
        $user = factory(User::class)->create(['status' => 'confirmed']);
        $this->actingAs($user);

        $course = factory(Course::class)->create(['free' => true]);

        $this->get(route('enroll', $course->slug))
            ->assertRedirect(route('course', $course->slug));

        $this->assertTrue((bool) $user->enrolled($course));
    }
}
