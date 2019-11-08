<?php
namespace Tests\Unit\Jobs\Infusionsoft;

use App\Jobs\Infusionsoft\AddContactCard;
use App\Models\Course;
use App\Models\InfusionsoftContact;
use App\Models\User;
use Infusionsoft\Api\DataService;
use Infusionsoft\Infusionsoft;
use Mockery;

class AddContactCardTest extends \TestCase
{
    /**
     * @test
     */
    public function card_infusionsoft_throws_exception()
    {
        $contact = factory(InfusionsoftContact::class)->make();
        $contact->user = factory(User::class)->make();

        $card   = new \stdClass();
        $card->ContactId = 1;
        $card->Id = 1;
        $card->number = 12341234;
        $card->cvv = 101;
        $card->expMonth = 10;
        $card->expYear = 22;
        $card->nameOnCard = 'Marius';
        $card->type = 'Visa';

        $isMock = Mockery::mock(Infusionsoft::class);
        $dataService = Mockery::mock(DataService::class);

        $isMock->shouldReceive('data')->andReturn($dataService);
        $dataService->shouldReceive('add')->andThrow(\Exception::class, 'An error message');

        $this->app->bind(Infusionsoft::class, function($app) use ($isMock){
            return $isMock;
        });

        $job = new AddContactCard($contact, $card);
        $result = $job->handle();

        $this->assertInstanceOf(AddContactCard::class, $result);
        $this->assertTrue(is_string($job->getError()));
        $this->assertInstanceOf(\Exception::class,  $job->getException());
        $this->assertEquals('An error message', $job->getException()->getMessage());
    }

    /**
     * @test
     */
    public function create_order_is_returns_call_result()
    {
        $contact = factory(InfusionsoftContact::class)->make();
        $contact->user = factory(User::class)->make();

        $card   = new \stdClass();
        $card->ContactId = 1;
        $card->Id = 1;
        $card->number = 12341234;
        $card->cvv = 101;
        $card->expMonth = 10;
        $card->expYear = 22;
        $card->nameOnCard = 'Marius';
        $card->type = 'Visa';

        $isMock = Mockery::mock(Infusionsoft::class);
        $dataService = Mockery::mock(DataService::class);

        $isMock->shouldReceive('data')->andReturn($dataService);
        $dataService->shouldReceive('add')->andReturn(['Call Result']);

        $this->app->bind(Infusionsoft::class, function($app) use ($isMock){
            return $isMock;
        });

        $job = new AddContactCard($contact, $card);
        $result = $job->handle();

        $this->assertEquals($job, $result);
        $this->assertEquals(['Call Result'], $result->getResponse());
    }
}
