<?php
namespace Unit\Listeners\Sendlane;

use App\Events\User\UserEnrolled;
use App\Listeners\Sendlane\AddToSendlane;
use App\Models\Course;
use App\Models\CourseSendlane;
use App\Models\Sendlane;
use App\Models\User;
use App\Services\Sendlane\Sendlane as SendlaneService;
use Illuminate\Support\Facades\Log;

class AddToSendlaneTest extends \TestCase
{
    /**
     * @test
     */
    public function subscriber_successfully_added_to_sendlane()
    {
        $course = factory(Course::class)->create();
        $sendlane = factory(Sendlane::class)->create();
        $user = factory(User::class)->create();

        $courseSendlane = factory(CourseSendlane::class)->create([
            'course_id'   => $course->id,
            'sendlane_id' => $sendlane->id
        ]);


        $sendlaneMock = \Mockery::mock(SendlaneService::class);

        $sendlaneMock->shouldReceive('request')->with(
            \Mockery::on(function($argument) {
                return strpos($argument, 'list-subscriber-add') !== false;
            }),
            [
                'email' => $user->name . '<'.$user->email.'>',
                'list_id' => $courseSendlane->list_id]

        );

        $this->app->bind(SendlaneService::class, function ($app) use ($sendlaneMock) {
            return $sendlaneMock;
        });

        $event   = new UserEnrolled($user, $course);
        $handler = new AddToSendlane();
        $handler->handle($event);
    }
}
