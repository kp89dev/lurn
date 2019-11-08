<?php
namespace Unit\Listeners\Tracking;

use App\Listeners\Tracking\TrackInfusionsoftRegisteredUser;
use App\Listeners\Tracking\TrackRegisteredUser;
use App\Models\User;
use App\Services\Contracts\TrackerInterface;
use Illuminate\Auth\Events\Registered;

class TrackRegisteredUserTest extends \TestCase
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
                ['Registered']
            );

        $this->app->bind(TrackerInterface::class, function ($app) use($trackerMock){
            return $trackerMock;
        });

        $user  = factory(User::class)->make();
        $event = new Registered($user);

        $handler = new TrackRegisteredUser();
        $handler->handle($event);
    }
}
