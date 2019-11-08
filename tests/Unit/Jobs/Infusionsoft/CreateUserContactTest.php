<?php
namespace Test\Unit\Jobs\Infusionsoft;

use App\Jobs\Infusionsoft\CreateUserContact;
use App\Models\InfusionsoftContact;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Infusionsoft\Api\ContactService;
use Infusionsoft\Infusionsoft;
use Mockery;

class CreateUserContactTest extends \TestCase
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
        $contactsService = Mockery::mock(ContactService::class);

        $isMock->shouldReceive('contacts')->andReturn($contactsService);
        $contactsService->shouldReceive('add')->andThrow(\Exception::class, 'An error message');

        $this->app->bind(Infusionsoft::class, function($app) use ($isMock){
            return $isMock;
        });

        $job = new CreateUserContact($course, $account);
        $result = $job->handle();

        $this->assertInstanceOf(CreateUserContact::class, $result);
        $this->assertTrue(is_string($job->getError()));
        $this->assertInstanceOf(\Exception::class,  $job->getException());
        $this->assertEquals('An error message', $job->getException()->getMessage());
    }

    /**
     * @test
     */
    public function create_user_returns_call_result()
    {
        $user = factory(User::class)->create();
        $account = 'ae244';

        $isMock = Mockery::mock(Infusionsoft::class);
        $contactsService = Mockery::mock(ContactService::class);

        $isMock->shouldReceive('contacts')->andReturn($contactsService);
        $contactsService->shouldReceive('add')->andReturn($contact = mt_rand(1, 10));

        $this->app->bind(Infusionsoft::class, function($app) use ($isMock){
            return $isMock;
        });

        $job = new CreateUserContact($user, $account);
        $result = $job->handle();

        $this->assertDatabaseHas('user_infusionsoft', [
            'user_id' => $user->id,
            'is_contact_id' => $contact,
            'is_account' => $account
        ]);

        $this->assertEquals($contact, $result->getResponse()->is_contact_id);
    }
}
