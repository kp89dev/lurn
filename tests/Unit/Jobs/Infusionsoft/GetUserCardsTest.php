<?php
namespace Tests\Unit\Jobs\Infusionsoft;

use App\Jobs\Infusionsoft\GetUserCards;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Infusionsoft\Api\DataService;
use Infusionsoft\Infusionsoft;
use Mockery;

class GetUserCardsTest extends \TestCase
{
    use DatabaseTransactions;

    /**
     * @test
     */
    public function infusionsoft_throws_exception()
    {
        $course = factory(User::class)->make();
        $account = 'ae244';

        $isMock = Mockery::mock(Infusionsoft::class);
        $dataService = Mockery::mock(DataService::class);

        $isMock->shouldReceive('data')->andReturn($dataService);
        $dataService->shouldReceive('query')->andThrow(\Exception::class, 'An error message');

        $this->app->bind(Infusionsoft::class, function($app) use ($isMock){
            return $isMock;
        });

        $job = new GetUserCards($course, $account);
        $result = $job->handle();

        $this->assertInstanceOf(GetUserCards::class, $result);
        $this->assertTrue(is_string($job->getError()));
        $this->assertInstanceOf(\Exception::class,  $job->getException());
        $this->assertEquals('An error message', $job->getException()->getMessage());
    }

    /**
     * @test
     */
    public function infusionsoft_returns_cards()
    {
        $course = factory(User::class)->make();
        $account = 'ae244';
        $cards = [
            [
                'Status'          => 3,
                'Last4'           => '1117',
                'Email'           => 'saklakyo1@gmail.com',
                'NameOnCard'      => 'Ciobanu Marius',
                'Id'              => 15,
                'ContactId'       => 5,
                'ExpirationMonth' => 02,
                'ExpirationYear'  => 22
            ],
            [
                'Status'          => 3,
                'Last4'           => '2234',
                'Email'           => 'saklakyo@gmail.com',
                'NameOnCard'      => ' Marius',
                'Id'              => 13,
                'ContactId'       => 6,
                'ExpirationMonth' => 01,
                'ExpirationYear'  => 19
            ]
        ];
        $isMock = Mockery::mock(Infusionsoft::class);
        $dataService = Mockery::mock(DataService::class);

        $isMock->shouldReceive('data')->andReturn($dataService);
        $dataService->shouldReceive('query')->andReturn($cards);

        $this->app->bind(Infusionsoft::class, function($app) use ($isMock){
            return $isMock;
        });

        $job = new GetUserCards($course, $account);
        $result = $job->handle();

        $this->assertInstanceOf(GetUserCards::class, $result);
        $this->assertEquals($cards, $result->getResponse());
        $this->assertNull($result->getError());
        $this->assertFalse($result->hasError());
    }
}
