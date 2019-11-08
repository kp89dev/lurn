<?php
namespace Tests\Unit\Jobs\Infusionsoft;

use App\Jobs\Infusionsoft\GetContactSubscriptionIdOnProduct;
use App\Models\InfusionsoftContact;
use App\Models\User;
use Infusionsoft\Api\DataService;
use Infusionsoft\Infusionsoft;

class GetContactSubscriptionIdOnProductTest extends \TestCase
{
    /**
     * @test
     */
    public function getting_subscription_id_for_product_throws_exception()
    {
        $contact = factory(InfusionsoftContact::class)->make();
        $contact->user = factory(User::class)->make();

        $isMock = $this->createMock(Infusionsoft::class);
        $dataService = $this->createMock(DataService::class);

        $isMock->expects(self::once())->method('data')->willReturn($dataService);
        $dataService->expects(self::once())
            ->method('query')
            ->will(self::throwException(new \Exception('An error message')));

        $this->app->bind(Infusionsoft::class, function($app) use ($isMock) {
            return $isMock;
        });

        $job = new GetContactSubscriptionIdOnProduct($contact, 111);
        $result = $job->handle();

        self::assertInstanceOf(GetContactSubscriptionIdOnProduct::class, $result);
        self::assertTrue(is_string($job->getError()));
        self::assertInstanceOf(\Exception::class,  $job->getException());
        self::assertEquals('An error message', $job->getException()->getMessage());
    }

    /**
     * @test
     */
    public function getting_subscription_id_for_product_is_successfull()
    {
        $contact = factory(InfusionsoftContact::class)->make();
        $contact->user = factory(User::class)->make();

        $isMock = $this->createMock(Infusionsoft::class);
        $dataService = $this->createMock(DataService::class);

        $isMock->expects(self::once())->method('data')->willReturn($dataService);
        $dataService->expects(self::once())
            ->method('query')
            ->willReturn(['Succesfull']);

        $this->app->bind(Infusionsoft::class, function($app) use ($isMock) {
            return $isMock;
        });

        $job = new GetContactSubscriptionIdOnProduct($contact, 111);
        $result = $job->handle();

        self::assertInstanceOf(GetContactSubscriptionIdOnProduct::class, $result);
        self::assertFalse($result->hasError());
        self::assertEquals(['Succesfull'], $result->getResponse());
    }
}
