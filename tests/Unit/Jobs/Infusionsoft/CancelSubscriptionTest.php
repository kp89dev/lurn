<?php
namespace Tests\Unit\Jobs\Infusionsoft;

use App\Jobs\Infusionsoft\CancelSubscription;
use App\Models\InfusionsoftContact;
use Infusionsoft\Api\DataService;
use Infusionsoft\Infusionsoft;
use App\Models\User;

class CancelSubscriptionTest extends \TestCase
{
    /**
     * @test
     */
    public function cancel_subscription_throws_exception()
    {
        $contact = factory(InfusionsoftContact::class)->make();
        $contact->user = factory(User::class)->make();

        $isMock = $this->createMock(Infusionsoft::class);
        $dataService = $this->createMock(DataService::class);

        $isMock->expects(self::once())->method('data')->willReturn($dataService);
        $dataService->expects(self::once())
                    ->method('update')
                    ->will(self::throwException(new \Exception('An error message')));

        $this->app->bind(Infusionsoft::class, function($app) use ($isMock) {
            return $isMock;
        });

        $job = new CancelSubscription($contact, 111);
        $result = $job->handle();

        self::assertInstanceOf(CancelSubscription::class, $result);
        self::assertTrue(is_string($job->getError()));
        self::assertInstanceOf(\Exception::class,  $job->getException());
        self::assertEquals('An error message', $job->getException()->getMessage());
    }

    /**
     * @test
     */
    public function cancel_subscription_is_succesfull()
    {
        $contact = factory(InfusionsoftContact::class)->make();
        $contact->user = factory(User::class)->make();

        $isMock = $this->createMock(Infusionsoft::class);
        $dataService = $this->createMock(DataService::class);

        $isMock->expects(self::once())->method('data')->willReturn($dataService);
        $dataService->expects(self::once())
            ->method('update')
            ->willReturn(['Succesfull']);

        $this->app->bind(Infusionsoft::class, function($app) use ($isMock) {
            return $isMock;
        });

        $job = new CancelSubscription($contact, 111);
        $result = $job->handle();

        self::assertInstanceOf(CancelSubscription::class, $result);
        self::assertFalse($result->hasError());
        self::assertEquals(['Succesfull'], $result->getResponse());
    }
}
