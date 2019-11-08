<?php
namespace Unit\Listeners\Tracking;

use App\Events\User\UserEnrolled;
use App\Listeners\Tracking\TrackEnrollments;
use App\Models\Course;
use App\Models\CourseInfusionsoft;
use App\Models\User;
use App\Services\Contracts\TrackerInterface;

class TrackEnrollmentsTest extends \TestCase
{
    /**
     * @test
     */
    public function track_gets_correctly_called()
    {
        $trackerMock = $this->createMock(TrackerInterface::class);
        $trackerMock->expects(self::exactly(2))
                    ->method('track')
                    ->withConsecutive(
                        ['Enrolled'],
                        ['payment']
                    );

        $this->app->bind(TrackerInterface::class, function ($app) use($trackerMock){
            return $trackerMock;
        });

        $user = factory(User::class)->make();
        $course = factory(Course::class)->make();
        $course->infusionsoft = factory(CourseInfusionsoft::class)->make();

        $event = new UserEnrolled($user, $course);

        $handler = new TrackEnrollments();
        $handler->handle($event);
    }
}
