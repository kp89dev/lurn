<?php
namespace Unit\Listeners\Tracking;

use App\Events\User\UserCreatedThroughInfusionsoft;
use App\Listeners\Tracking\TrackInfusionsoftRegisteredUser;
use App\Models\Course;
use App\Models\User;
use App\Services\Contracts\TrackerInterface;

class TrackInfusionsoftRegisteredUserTest extends \TestCase
{
    /**
     * @test
     */
    public function handle_calls_track()
    {
        $trackerMock = $this->createMock(TrackerInterface::class);
        $trackerMock->expects(self::exactly(1))
            ->method('track')
            ->withConsecutive(
                ['Infusionsoft user added']
            );

        $this->app->bind(TrackerInterface::class, function ($app) use($trackerMock){
            return $trackerMock;
        });

        $user  = factory(User::class)->make();
        $event = new UserCreatedThroughInfusionsoft($user);

        $handler = new TrackInfusionsoftRegisteredUser();
        $handler->handle($event);
    }
}
