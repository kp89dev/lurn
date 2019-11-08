<?php
namespace Tests\Feature\Remote;

use App\Events\User\UserCreatedThroughInfusionsoft;
use App\Events\User\UserEnrolled;
use App\Models\Course;
use App\Models\CourseInfusionsoft;
use App\Models\ImportedUser;
use App\Models\User;
use App\Services\Logger\Cloudwatch as CloudwatchLogger;
use Illuminate\Support\Facades\Event;

class RegisterTest extends \TestCase
{
    /**
     * @test
     */
    public function registration_rejected_when_token_is_wrong()
    {
        $this->mockLogger('Failed Auth');

        $response = $this->post(route('remote.register'), []);
        $response->assertStatus(204);

        $response = $this->post(route('remote.register'), ['token' => 'wrong_string']);
        $response->assertStatus(204);
    }

    /**
     * @test
     */
    public function registration_fails_when_field_is_missing()
    {
        $this->mockLogger('Missing field', 1);

        $response = $this->post(route('remote.register'), [
            'token' => 'a_random_string',
            'email' => ''
        ]);
        $response->assertStatus(204);
    }

    /**
     * @test
     */
    public function registration_fails_when_course_cant_be_found_by_product_id()
    {
        $user = factory(User::class)->create();
        $userToMerge = factory(User::class)->create([
            'email' => 'email@tomerge.com'
        ]);
        $user->mergedAccounts()->attach($userToMerge, ['from_table' => 'users']);
        $this->mockLogger('Course not found', 1);

        $response = $this->post(route('remote.register'), [
            'token'      => 'a_random_string',
            'email'      => 'email@tomerge.com',
            'contact_id' => 1,
            'product_id' => 1,
            'invoice_id' => 1,
            'name'       => 'Test',
        ]);
        $response->assertStatus(204);
    }

    /**
     * @test
     */
    public function user_already_has_course_access()
    {
        $user = factory(User::class)->create();
        $course = factory(Course::class)->create();
        $courseIS = factory(CourseInfusionsoft::class)->create([
            'course_id' => $course->id
        ]);
        $user->courses()->attach($course);
        $this->mockLogger('User has access', 1);

        $response = $this->post(route('remote.register'), [
            'token'      => 'a_random_string',
            'email'      => $user->email,
            'contact_id' => 1,
            'product_id' => $courseIS->is_product_id,
            'invoice_id' => 1,
            'name'       => 'Test',
        ]);

        $response->assertStatus(204);
    }

    /**
     * @test
     */
    public function user_gets_course_access()
    {
        Event::fake();
        $user = factory(User::class)->create();
        $course = factory(Course::class)->create();
        $courseIS = factory(CourseInfusionsoft::class)->create([
            'course_id' => $course->id
        ]);

        $response = $this->post(route('remote.register'), [
            'token'      => 'a_random_string',
            'email'      => $user->email,
            'contact_id' => 1,
            'product_id' => $courseIS->is_product_id,
            'invoice_id' => 1,
            'name'       => 'Test',
        ]);

        $response->assertStatus(204);

        $this->assertDatabaseHas('user_courses', [
            'user_id' => $user->id,
            'course_id' => $course->id
        ]);

        Event::assertDispatched(UserEnrolled::class, function ($e) use($user, $course) {
            return $e->user->email === $user->email && $e->course->id === $course->id;
        });
    }

    /**
     * @test
     */
    public function user_gets_course_access_from_merged_user()
    {
        Event::fake();
        $user = factory(User::class)->create();
        $userToMerge = factory(User::class)->create([
            'email' => 'email@tomerge.com'
        ]);
        $user->mergedAccounts()->attach($userToMerge, ['from_table' => 'users']);
        $course = factory(Course::class)->create();
        $courseIS = factory(CourseInfusionsoft::class)->create([
            'course_id' => $course->id
        ]);

        $response = $this->post(route('remote.register'), [
            'token'      => 'a_random_string',
            'email'      => 'email@tomerge.com',
            'contact_id' => 1,
            'product_id' => $courseIS->is_product_id,
            'invoice_id' => 1,
            'name'       => 'Test',
        ]);

        $response->assertStatus(204);

        $this->assertDatabaseHas('user_courses', [
            'user_id' => $user->id,
            'course_id' => $course->id
        ]);

        Event::assertDispatched(UserEnrolled::class, function ($e) use($user, $course) {
            return $e->user->email === $user->email && $e->course->id === $course->id;
        });
    }

    /**
     * @test
     */
    public function user_gets_course_access_from_imported_user()
    {
        Event::fake();
        $user = factory(User::class)->create();
        $userToMerge = factory(ImportedUser::class)->create([
            'email' => 'email@tomerge.com'
        ]);
        $user->mergedImportedAccounts()->attach($userToMerge, ['from_table' => 'users_import_all']);
        $course = factory(Course::class)->create();
        $courseIS = factory(CourseInfusionsoft::class)->create([
            'course_id' => $course->id
        ]);

        $response = $this->post(route('remote.register'), [
            'token'      => 'a_random_string',
            'email'      => 'email@tomerge.com',
            'contact_id' => 1,
            'product_id' => $courseIS->is_product_id,
            'invoice_id' => 1,
            'name'       => 'Test',
        ]);

        $response->assertStatus(204);

        $this->assertDatabaseHas('user_courses', [
            'user_id' => $user->id,
            'course_id' => $course->id
        ]);

        Event::assertDispatched(UserEnrolled::class, function ($e) use($user, $course) {
            return $e->user->email === $user->email && $e->course->id === $course->id;
        });
    }

    /**
     * @test
     */
    public function unimported_user_get_course_access()
    {
        Event::fake();
        $email = 'imported@user.com';
        factory(ImportedUser::class)->create([
            'email' => $email
        ]);


        $course = factory(Course::class)->create();
        $courseIS = factory(CourseInfusionsoft::class)->create([
            'course_id' => $course->id
        ]);

        $response = $this->post(route('remote.register'), [
            'token'      => 'a_random_string',
            'email'      => $email,
            'contact_id' => 1,
            'product_id' => $courseIS->is_product_id,
            'invoice_id' => 1,
            'name'       => 'Test',
        ]);

        $response->assertStatus(204);
        $createdUser = User::where('email', $email)->first();
        $this->assertDatabaseHas('user_courses', [
            'user_id'   => $createdUser->id,
            'course_id' => $course->id
        ]);

        Event::assertDispatched(UserCreatedThroughInfusionsoft::class, function ($e) use($email) {
            return $e->user->email === $email;
        });

        Event::assertDispatched(UserEnrolled::class, function ($e) use($email, $course) {
            return $e->user->email === $email && $e->course->id === $course->id;
        });
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function mockLogger($expectedMessage, $called = 2)
    {
        $loggerMock = $this->getMockBuilder(CloudwatchLogger::class)
            ->disableOriginalConstructor()
            ->setMethods(['info'])
            ->getMock();

        $loggerMock->expects(static::exactly($called))
            ->method('info')
            ->with(static::stringContains($expectedMessage));

        $this->app->bind(CloudwatchLogger::class, function ($app) use ($loggerMock) {
            return $loggerMock;
        });
    }
}
