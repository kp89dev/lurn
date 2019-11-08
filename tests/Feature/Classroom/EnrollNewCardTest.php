<?php
namespace Tests\Feature\Classroom;

use App\Jobs\Infusionsoft\AddContactCard;
use App\Jobs\Infusionsoft\CreateUserContact;
use App\Jobs\Infusionsoft\GetUserContactId;
use App\Jobs\Infusionsoft\ValidateCard;
use App\Models\Course;
use App\Models\CourseInfusionsoft;
use App\Models\InfusionsoftContact;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Mockery;
use Illuminate\Contracts\Bus\Dispatcher as BusDispatcherContract;

class EnrollNewCardTest extends \TestCase
{
    use DatabaseTransactions;

    /**
     * @test
     */
    public function prepareOrder_fails_when_failing_to_get_Infusionsoft_contact()
    {
        $course = factory(Course::class)->create(['status' => 1]);
        $user = factory(User::class)->create();
        factory(CourseInfusionsoft::class)->create([
            'course_id' => $course->id
        ]);

        $commandBus = Mockery::mock(BusDispatcherContract::class);
        $getISContactService = Mockery::mock(GetUserContactId::class);
        $getISContactService->shouldReceive('hasError')->andReturn(true);
        $getISContactService->shouldReceive('getError')->andReturn("An error message");

        $commandBus->shouldReceive('dispatchNow')
            ->with(GetUserContactId::class)
            ->andReturn($getISContactService);

        $this->app->instance(BusDispatcherContract::class, $commandBus);

        $response = $this->actingAs($user)
            ->post(route('do.enrollment', ['course' => $course->slug]), $this->getCardArray());

        $response->assertStatus(302);
        self::assertEquals("An error message", app('session.store')->get('errors')->first());
    }

    /**
     * @test
     */
    public function prepareOrder_fails_when_failing_to_add_Infusionsoft_contact()
    {
        $course = factory(Course::class)->create(['status' => 1]);
        $user = factory(User::class)->create();
        factory(CourseInfusionsoft::class)->create([
            'course_id' => $course->id
        ]);

        $commandBus = Mockery::mock(BusDispatcherContract::class);
        $getISContactService = Mockery::mock(GetUserContactId::class);
        $getISContactService->shouldReceive('hasError')->andReturn(false);
        $getISContactService->shouldReceive('getResponse')->andReturnNull();

        $commandBus->shouldReceive('dispatchNow')
            ->with(GetUserContactId::class)
            ->andReturn($getISContactService);

        $createISContactService = Mockery::mock(CreateUserContact::class);
        $createISContactService->shouldReceive('hasError')->andReturn(true);
        $createISContactService->shouldReceive('getError')->andReturn('An error message');

        $commandBus->shouldReceive('dispatchNow')
            ->with(CreateUserContact::class)
            ->andReturn($createISContactService);

        $this->app->instance(BusDispatcherContract::class, $commandBus);

        $response = $this->actingAs($user)
            ->post(route('do.enrollment', ['course' => $course->slug]), $this->getCardArray());

        $response->assertStatus(302);
        self::assertEquals("An error message", app('session.store')->get('errors')->first());
    }

    /**
     * @test
     */
    public function prepareOrder_fails_when_failing_to_validate_Infusionsoft_card()
    {
        $course = factory(Course::class)->create(['status' => 1]);
        $user = factory(User::class)->create();
        $courseIS = factory(CourseInfusionsoft::class)->create([
            'course_id' => $course->id
        ]);
        factory(InfusionsoftContact::class)->create([
            'user_id' => $user->id,
            'is_account' => $courseIS->is_account
        ]);

        $commandBus = Mockery::mock(BusDispatcherContract::class);
        $validISCard = Mockery::mock(ValidateCard::class);
        $validISCard->shouldReceive('hasError')->andReturn(true);
        $validISCard->shouldReceive('getError')->andReturn('An error message');

        $commandBus->shouldReceive('dispatchNow')
            ->with(ValidateCard::class)
            ->andReturn($validISCard);

        $this->app->instance(BusDispatcherContract::class, $commandBus);

        $response = $this->actingAs($user)
            ->post(route('do.enrollment', ['course' => $course->slug]), $this->getCardArray());

        $response->assertStatus(302);
        self::assertEquals("An error message", app('session.store')->get('errors')->first());
    }

    /**
     * @test
     */
    public function prepareOrder_fails_when_failing_to_add_Infusionsoft_card()
    {
        $course = factory(Course::class)->create(['status' => 1]);
        $user = factory(User::class)->create();
        $courseIS = factory(CourseInfusionsoft::class)->create([
            'course_id' => $course->id
        ]);
        factory(InfusionsoftContact::class)->create([
            'user_id' => $user->id,
            'is_account' => $courseIS->is_account
        ]);

        $commandBus = Mockery::mock(BusDispatcherContract::class);
        $validISCard = Mockery::mock(ValidateCard::class);
        $validISCard->shouldReceive('hasError')->andReturn(false);

        $commandBus->shouldReceive('dispatchNow')
            ->with(ValidateCard::class)
            ->andReturn($validISCard);

        $addISCard = Mockery::mock(AddContactCard::class);
        $addISCard->shouldReceive('hasError')->andReturn(true);
        $addISCard->shouldReceive('getError')->andReturn('An error message');

        $commandBus->shouldReceive('dispatchNow')
            ->with(AddContactCard::class)
            ->andReturn($addISCard);

        $this->app->instance(BusDispatcherContract::class, $commandBus);

        $response = $this->actingAs($user)
            ->post(route('do.enrollment', ['course' => $course->slug]), $this->getCardArray());

        $response->assertStatus(302);
        self::assertEquals("An error message", app('session.store')->get('errors')->first());
    }

    /**
     * @return array
     */
    private function getCardArray()
    {
        return [
            'card' => [
                'expDate'    => '10/22',
                'cvv'        => '222',
                'nameOnCard' => 'Marius Ciobanu',
                'number'     => '4111 1111 1111 1111'
            ],
            'payment_type' => 'full'
        ];
    }
}
