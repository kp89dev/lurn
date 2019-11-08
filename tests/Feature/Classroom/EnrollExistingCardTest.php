<?php
namespace Tests\Feature\Classroom;

use App\Events\User\UserEnrolled;
use App\Jobs\Infusionsoft\CreateInfusionsoftOrder;
use App\Jobs\Infusionsoft\GetUserCards;
use App\Jobs\Infusionsoft\PayInfusionsoftOrder;
use App\Models\Course;
use App\Models\CourseInfusionsoft;
use App\Models\InfusionsoftMerchantId;
use App\Models\User;
use Illuminate\Contracts\Bus\Dispatcher as BusDispatcherContract;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Event;
use Mockery;

class EnrollExistingCardTest extends \TestCase
{
    use DatabaseTransactions;

    protected $cards = [
        [
            'Status'          => 3,
            'Last4'           => '1117',
            'Email'           => 'saklakyo1@gmail.com',
            'NameOnCard'      => 'Ciobanu Marius',
            'Id'              => 15,
            'ContactId'       => 5,
            'ExpirationMonth' => 02,
            'ExpirationYear'  => 22,
            'CardType'        => 'Visa'
        ]
    ];
    
    /**
     * @test
     */
    public function enroll_page_is_available_with_credit_cards()
    {
        $aCard = $this->cards;
        $course = factory(Course::class)->create(['status' => 1]);
        $user = factory(User::class)->create();
        factory(CourseInfusionsoft::class)->create([
            'course_id' => $course->id,
            'price'     => 1497
        ]);


        $getUserCards = Mockery::mock(GetUserCards::class);
        $getUserCards->shouldReceive('hasError')->andReturn(false);
        $getUserCards->shouldReceive('getResponse')->andReturn($aCard);

        $commandBus = Mockery::mock(BusDispatcherContract::class);
        $commandBus->shouldReceive('dispatchNow')->with(GetUserCards::class)->andReturn($getUserCards);

        $this->app->instance(BusDispatcherContract::class, $commandBus);

        $response = $this->actingAs($user)
                         ->get(route('enroll', ['course' => $course->slug]));

        $response->assertStatus(200)
                 ->assertSee(htmlentities(json_encode($aCard[0])))
                 ->assertSee(number_format('1497', 0));
    }

    /**
     * @test
     */
    public function enroll_page_is_available_without_credit_cards()
    {
        $aCard = [];
        $course = factory(Course::class)->create(['status' => 1]);
        $user = factory(User::class)->create();
        factory(CourseInfusionsoft::class)->create([
            'course_id' => $course->id,
            'price'     => 1497,
            'subscription' => 1
        ]);


        $getUserCards = Mockery::mock(GetUserCards::class);
        $getUserCards->shouldReceive('hasError')->andReturn(false);
        $getUserCards->shouldReceive('getResponse')->andReturn($aCard);

        $commandBus = Mockery::mock(BusDispatcherContract::class);
        $commandBus->shouldReceive('dispatchNow')->with(GetUserCards::class)->andReturn($getUserCards);

        $this->app->instance(BusDispatcherContract::class, $commandBus);

        $response = $this->actingAs($user)
            ->get(route('enroll', ['course' => $course->slug]));

        $response->assertStatus(200)
                ->assertSee(number_format('1497', 0))
                ->assertSee('/ month');
    }

    /**
     * @test
     */
    public function process_errors_on_create_order_when_using_saved_cards()
    {
        $course = factory(Course::class)->create(['status' => 1]);
        $user = factory(User::class)->create();
        factory(CourseInfusionsoft::class)->create([
            'course_id' => $course->id
        ]);

        $commandBus = Mockery::mock(BusDispatcherContract::class);
        $createInfusionsoftOrderMock = Mockery::mock(CreateInfusionsoftOrder::class);
        $createInfusionsoftOrderMock->shouldReceive('hasError')->andReturn(true);
        $createInfusionsoftOrderMock->shouldReceive('getError')->andReturn("An error message");

        $commandBus->shouldReceive('dispatchNow')
                    ->with(CreateInfusionsoftOrder::class)
                    ->andReturn($createInfusionsoftOrderMock);

        $this->app->instance(BusDispatcherContract::class, $commandBus);

        $response = $this->actingAs($user)
            ->post(route('do.enrollment', ['course' => $course->slug]), [
                'saved_credit_card' => json_encode($this->cards[0])
            ]);

        $response->assertStatus(302);
        self::assertEquals("An error message", app('session.store')->get('errors')->first());
    }

    /**
     * @test
     */
    public function order_fails_when_payment_is_refused()
    {
        $course = factory(Course::class)->create(['status' => 1]);
        $user = factory(User::class)->create();
        factory(CourseInfusionsoft::class)->create([
            'course_id' => $course->id
        ]);

        $commandBus = Mockery::mock(BusDispatcherContract::class);
        $createInfusionsoftOrderMock = Mockery::mock(CreateInfusionsoftOrder::class);
        $createInfusionsoftOrderMock->shouldReceive('hasError')->andReturn(false);
        $createInfusionsoftOrderMock->shouldReceive('getResponse')->andReturn(['InvoiceId' => 1]);

        $commandBus->shouldReceive('dispatchNow')
            ->with(CreateInfusionsoftOrder::class)
            ->andReturn($createInfusionsoftOrderMock);

        $payInvoiceMock = Mockery::mock(PayInfusionsoftOrder::class);
        $payInvoiceMock->shouldReceive('hasError')->andReturn(true);
        $payInvoiceMock->shouldReceive('getError')->andReturn("An error message");

        $commandBus->shouldReceive('dispatchNow')
            ->with(PayInfusionsoftOrder::class)
            ->andReturn($payInvoiceMock);

        $this->app->instance(BusDispatcherContract::class, $commandBus);

        $response = $this->actingAs($user)
            ->post(route('do.enrollment', ['course' => $course->slug]), [
                'saved_credit_card' => json_encode($this->cards[0])
            ]);

        $response->assertStatus(302);
        self::assertEquals("An error message", app('session.store')->get('errors')->first());
    }

    /**
     * @test
     */
    public function order_success()
    {
        Event::fake();

        $course = factory(Course::class)->create(['status' => 1]);
        $user = factory(User::class)->create();
        factory(CourseInfusionsoft::class)->create([
            'course_id' => $course->id,
            'is_account' => 'uv222'
        ]);
        factory(InfusionsoftMerchantId::class)->create([
            'account' => 'uv222'
        ]);
        $commandBus = Mockery::mock(BusDispatcherContract::class);
        $createInfusionsoftOrderMock = Mockery::mock(CreateInfusionsoftOrder::class);
        $createInfusionsoftOrderMock->shouldReceive('hasError')->andReturn(false);
        $createInfusionsoftOrderMock->shouldReceive('getResponse')->andReturn(['InvoiceId' => 1]);

        $commandBus->shouldReceive('dispatchNow')
            ->with(CreateInfusionsoftOrder::class)
            ->andReturn($createInfusionsoftOrderMock);

        $payInvoiceMock = Mockery::mock(PayInfusionsoftOrder::class);
        $payInvoiceMock->shouldReceive('hasError')->andReturn(false);

        $commandBus->shouldReceive('dispatchNow')
            ->with(PayInfusionsoftOrder::class)
            ->andReturn($payInvoiceMock);

        $this->app->instance(BusDispatcherContract::class, $commandBus);

        $response = $this->actingAs($user)
            ->post(route('do.enrollment', ['course' => $course->slug]), [
                'saved_credit_card' => json_encode($this->cards[0])
            ]);

        $response->assertStatus(302)
                 ->assertSessionMissing('errors');

        Event::assertDispatched(UserEnrolled::class, function ($e) use ($user, $course) {
            return $e->user->id === $user->id && $course->id = $e->course->id;
        });

        $this->assertDatabaseHas('user_courses',[
            'user_id'   => $user->id,
            'course_id' => $course->id,
            'invoice_id' => 1
        ]);
    }
}
