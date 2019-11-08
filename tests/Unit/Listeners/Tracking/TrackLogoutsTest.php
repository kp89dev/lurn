<?php
namespace Unit\Listeners\Tracking;

use App\Listeners\Tracking\TrackLogouts;
use App\Models\User;
use App\Services\Contracts\TrackerInterface;
use Illuminate\Auth\Events\Logout;

class TrackLogoutsTest extends \TestCase
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
                ['Logout']
            );

        $this->app->bind(TrackerInterface::class, function ($app) use($trackerMock){
            return $trackerMock;
        });

        $user  = factory(User::class)->make();
        $event = new Logout($user);

        $handler = new TrackLogouts();
        $handler->handle($event);
    }
}
