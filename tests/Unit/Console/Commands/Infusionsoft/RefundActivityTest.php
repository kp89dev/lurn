<?php
/**
 * Date: 3/19/18
 * Time: 5:57 PM
 */

namespace Tests\Unit\Console\Commands\Infusionsoft;

use TestCase;
use Mockery as m;

class RefundActivityTest extends TestCase
{
    /**
     * @test
     */
    public function testRefundActivityCommand()
    {
        $refunds = m::mock('App\Commands\Infusionsoft\Refunds');
        $refunds->shouldReceive('setIdentifier')->once()->withAnyArgs()->andReturnSelf();
        $refunds->shouldReceive('setCommand')->once()->withAnyArgs()->andReturnSelf();
        $refunds->shouldReceive('setDate')->once()->withAnyArgs()->andReturnSelf();
        $refunds->shouldReceive('process')->once();
        $this->app->instance('App\Commands\Infusionsoft\Refunds', $refunds);

        $processor = m::mock('App\Models\Queries\ProcessRefunds');
        $processor->shouldReceive('setRefundsHandler')->once()->with($refunds)->andReturnSelf();
        $processor->shouldReceive('process')->once();
        $this->app->instance('App\Models\Queries\ProcessRefunds', $processor);

        $this->artisan('course:refunds');
    }
}