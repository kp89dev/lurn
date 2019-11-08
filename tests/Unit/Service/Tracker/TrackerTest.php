<?php
namespace Unit\Service\Tracker;

use App\Http\Controllers\Tracker\IndexController;
use App\Services\Tracker\Tracker;

class TrackerTest extends \TestCase
{
    /**
     * @test
     */
    public function identity_returns_true()
    {
        $tracker = new Tracker();
        self::assertTrue($tracker->identity('marius'));
    }

    /**
     * @test
     */
    public function track_calls_the_tracker_successfully()
    {
        $tracker = new Tracker();

        $controllerMock = $this->createMock(IndexController::class);
        $controllerMock->expects($this->once())
                        ->method('index')
                        ->withAnyParameters();

        $this->app->bind(IndexController::class, function($app) use ($controllerMock) {
            return $controllerMock;
        });

        $tracker->track('some_event', ['param' => 'value'], true);
    }
}
