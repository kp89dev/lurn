<?php

namespace Tests\Unit\Jobs\Infusionsoft;

use App\Jobs\Infusionsoft\CreateInfusionsoftOrder;
use App\Models\Course;
use App\Models\CourseInfusionsoft;
use Infusionsoft\Api\OrderService;
use Infusionsoft\Infusionsoft;
use Mockery;

class CreateInfusionsoftOrderTest extends \TestCase
{
    /**
     * @test
     */
    public function infusionsoft_throws_exception()
    {
        $course = factory(Course::class)->make();
        $card = new \stdClass();
        $card->ContactId = 1;
        $card->Id = 1;

        $isMock = Mockery::mock(Infusionsoft::class);
        $orderService = Mockery::mock(OrderService::class);

        $isMock->shouldReceive('orders')->with('xml')->andReturn($orderService);
        $orderService->shouldReceive('placeOrder')->andThrow(\Exception::class, 'An error message');

        $this->app->bind(Infusionsoft::class, function ($app) use ($isMock) {
            return $isMock;
        });

        $job = new CreateInfusionsoftOrder($course, $card);
        $result = $job->handle();

        $this->assertInstanceOf(CreateInfusionsoftOrder::class, $result);
        $this->assertTrue(is_string($job->getError()));
        $this->assertInstanceOf(\Exception::class, $job->getException());
        $this->assertEquals('An error message', $job->getException()->getMessage());
    }

    /**
     * @test
     */
    public function create_order_is_returns_call_result()
    {
        $course = factory(Course::class)->make();
        $card = new \stdClass();
        $card->ContactId = 1;
        $card->Id = 1;

        $isMock = Mockery::mock(Infusionsoft::class);
        $orderService = Mockery::mock(OrderService::class);

        $isMock->shouldReceive('orders')->with('xml')->andReturn($orderService);
        $orderService->shouldReceive('placeOrder')
            ->andReturnNull();

        $this->app->bind(Infusionsoft::class, function ($app) use ($isMock) {
            return $isMock;
        });

        $job = new CreateInfusionsoftOrder($course, $card);
        $result = $job->handle();

        self::assertEquals($job, $result);
    }
}
