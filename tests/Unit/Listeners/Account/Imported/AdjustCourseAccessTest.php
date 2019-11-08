<?php
namespace Tests\Unit\Listerners\Account\Imported;

use App\Events\User\ImportedUserMerged;
use App\Listeners\Account\Imported\AdjustCourseAccess;
use App\Models\Course;
use App\Models\ImportedUser;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;

class AdjustCourseAccessTest extends \TestCase
{
    use DatabaseTransactions;

    /**
     * @test
     */
    public function handle_imported_user_successfully_moves_the_course_access()
    {
        $user = factory(User::class)->create(['email' => 'user@lurn.com']);
        $userToMerge = factory(ImportedUser::class)->create([
            'email'      => 'imported@import.com',
            'connection' => 'inbox'
        ]);

        //create a course relation
        DB::table('course_subscriptions')->insert([
            [
                'user_id'    => $userToMerge->user_id,
                'course_id'  => 117,
                'from_table' => 'inbox'
            ], [
                'user_id'    => $userToMerge->user_id,
                'course_id'  => 118,
                'from_table' => 'inbox'
            ]
        ]);

        $event    = new ImportedUserMerged($user, $userToMerge);
        $listener = new AdjustCourseAccess();
        $result = $listener->handle($event);

        static::assertNull($result);
        $this->assertDatabaseHas('user_courses', [
            'user_id'   => $user->id,
            'course_id' => 117
        ]);
        $this->assertDatabaseHas('user_courses', [
            'user_id'   => $user->id,
            'course_id' => 118
        ]);
    }
}
