<?php
namespace Tests\Unit\Jobs\Infusionsoft;

use App\Jobs\Infusionsoft\GetUserContactId;
use App\Models\InfusionsoftContact;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Infusionsoft\Api\DataService;
use Infusionsoft\Infusionsoft;
use Mockery;

class GetUserContactIdTest extends \TestCase
{
    use DatabaseTransactions;

    /**
     * @test
     */
    public function infusionsoft_throws_exception()
    {
        $user = factory(User::class)->make();
        $account = 'ae244';

        $isMock = Mockery::mock(Infusionsoft::class);
        $dataService = Mockery::mock(DataService::class);

        $isMock->shouldReceive('contacts')->andReturn($dataService);
        $dataService->shouldReceive('findByEmail')->andThrow(\Exception::class, 'An error message');

        $this->app->bind(Infusionsoft::class, function($app) use ($isMock){
            return $isMock;
        });

        $job = new GetUserContactId($user, $account);
        $result = $job->handle();

        $this->assertInstanceOf(GetUserContactId::class, $result);
        $this->assertTrue(is_string($job->getError()));
        $this->assertInstanceOf(\Exception::class,  $job->getException());
        $this->assertEquals('An error message', $job->getException()->getMessage());
    }

    /**
     * @test
     */
    public function infusionsoft_returns_contact_id()
    {
        $user = factory(User::class)->create();
        $account = 'ae244';

        $isMock = Mockery::mock(Infusionsoft::class);
        $dataService = Mockery::mock(DataService::class);

        $isMock->shouldReceive('contacts')->andReturn($dataService);
        $dataService->shouldReceive('findByEmail')->andReturn([['Id' => 5]]);

        $this->app->bind(Infusionsoft::class, function($app) use ($isMock){
            return $isMock;
        });

        $job = new GetUserContactId($user, $account);
        $result = $job->handle();

        $this->assertDatabaseHas('user_infusionsoft', [
            'user_id' => $user->id,
            'is_contact_id' => 5,
            'is_account' => $account
        ]);

        $this->assertInstanceOf(GetUserContactId::class, $result);
        $this->assertInstanceOf(InfusionsoftContact::class, $result->getResponse());
    }
}
