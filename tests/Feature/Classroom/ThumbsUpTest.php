<?php
namespace Tests\Feature\Classroom;

use App\Models\Course;
use App\Models\CourseLike;
use App\Models\User;

class ThumbsUpTest extends \TestCase
{
    /**
     * @test
     */
    public function ensureThumbsUpTest()
    {
        $users = factory(User::class, 3)->create(['status' => 'confirmed']);

        $course = factory(Course::class)->create();

        foreach ($users as $user) {
            $this->json(
                'POST', 
                '/api/click-thumbs-up', 
                [
                    'course_id' => $course->id,
                    'user_id' => $user->id,
                    'likes' => 1
                ]
            );
        }

        $this->assertTrue((int) CourseLike::withCourse($course->id)->sum('likes') === 3);
    }
}
