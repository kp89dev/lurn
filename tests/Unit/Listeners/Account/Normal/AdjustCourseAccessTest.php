<?php
namespace Tests\Unit\Listerners\Account\Normal;

use App\Events\User\UserMerged;
use App\Listeners\Account\Normal\AdjustCourseAccess;
use App\Models\Course;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AdjustCourseAccessTest extends \TestCase
{
    use DatabaseTransactions;

    /**
     * @test
     */
    public function handle_successfully_moves_the_course_access()
    {
        $user = factory(User::class)->create(['email' => 'user@lurn.com']);
        $userToMerge = factory(User::class)->create(['email' => 'user@user.com']);

        $course = factory(Course::class)->create();
        //create a course relation
        $userToMerge->courses()->attach($course);

        $event    = new UserMerged($user, $userToMerge);
        $listener = new AdjustCourseAccess();
        $result = $listener->handle($event);

        static::assertNull($result);
        $this->assertDatabaseHas('user_courses', [
            'user_id'   => $user->id,
            'course_id' => $course->id
        ]);

        static::assertEquals(1, $user->courses->count());
    }
}
