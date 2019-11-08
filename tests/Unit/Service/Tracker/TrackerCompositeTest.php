<?php
namespace Unit\Service\Tracker;

use App\Services\Contracts\TrackerInterface;
use App\Services\Tracker\TrackerComposite;

class TrackerCompositeTest extends \TestCase
{
    /**
     * @test
     */
    public function track_method_delagated_correctly()
    {
        $tracker1 = $this->createMock(TrackerInterface::class);
        $tracker2 = $this->createMock(TrackerInterface::class);
        $param1 = 'param1';
        $param2 = 'param2';
        $param3 = 'param3';

        $tracker1->expects(self::once())
                 ->method('track')
                 ->with($param1, $param2, $param3);
        $tracker2->expects(self::once())
            ->method('track')
            ->with($param1, $param2, $param3);

        $composite = new TrackerComposite([$tracker1, $tracker2]);
        $composite->track($param1, $param2, $param3);
    }

    /**
     * @test
     */
    public function identity_method_delagated_correctly()
    {
        $tracker1 = $this->createMock(TrackerInterface::class);
        $tracker2 = $this->createMock(TrackerInterface::class);
        $param1 = 'param1';

        $tracker1->expects(self::once())
            ->method('identity')
            ->with($param1);
        $tracker2->expects(self::once())
            ->method('identity')
            ->with($param1);

        $composite = new TrackerComposite([$tracker1, $tracker2]);
        $composite->identity($param1);
    }
}
