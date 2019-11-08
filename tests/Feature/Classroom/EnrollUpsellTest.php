<?php
namespace Tests\Feature\Classroom;

use App\Events\User\UserEnrolled;
use App\Jobs\Infusionsoft\CreateInfusionsoftOrder;
use App\Jobs\Infusionsoft\GetUserCards;
use App\Jobs\Infusionsoft\PayInfusionsoftOrder;
use App\Models\Course;
use App\Models\CourseInfusionsoft;
use App\Models\CourseUpsell;
use App\Models\CourseUpsellToken;
use App\Models\InfusionsoftMerchantId;
use App\Models\User;
use Illuminate\Support\Facades\Event;
use Mockery;
use Illuminate\Contracts\Bus\Dispatcher as BusDispatcherContract;

class EnrollUpsellTest extends \LoggedInTestCase
{
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
    public function enroll_page_is_available_for_an_upsell()
    {
        $aCard = $this->cards;
        $courses = factory(Course::class, 2)->create(['status' => 1]);
        $user = factory(User::class)->create();
        factory(CourseInfusionsoft::class)->create([
            'course_id' => $courses[0]->id,
            'price'     => 1497,
            'is_account' => 'uv222'
        ]);
        $aCourseIS = factory(CourseInfusionsoft::class)->create([
            'course_id' => $courses[1]->id,
            'price'     => 222,
            'upsell'    => 1,
            'is_account' => 'uv222'
        ]);
        $upsell = factory(CourseUpsell::class)->create([
            'course_infusionsoft_id' => $aCourseIS->id
        ]);
        $token  = factory(CourseUpsellToken::class)->create([
            'course_upsell_id' => $upsell->id
        ]);
        factory(InfusionsoftMerchantId::class)->create([
            'account' => 'uv222'
        ]);

        $getUserCards = Mockery::mock(GetUserCards::class);
        $getUserCards->shouldReceive('hasError')->andReturn(false);
        $getUserCards->shouldReceive('getResponse')->andReturn($aCard);

        $commandBus = Mockery::mock(BusDispatcherContract::class);
        $commandBus->shouldReceive('dispatchNow')->with(GetUserCards::class)->andReturn($getUserCards);

        $this->app->instance(BusDispatcherContract::class, $commandBus);

        $response = $this->actingAs($user)
            ->get(route('enroll', [
                'course' => $courses[0]->slug,
                'token'  => $token->token
            ]));

        $response->assertStatus(200)
            ->assertSee(htmlentities(json_encode($aCard[0])))
            ->assertSee(number_format('222', 0))
            ->assertSee($token->token);

        $this->assertDatabaseHas('course_upsell_tokens', [
            'course_upsell_id' => $upsell->id,
            'used'             => 0
        ]);
        self::assertEquals(1, CourseUpsellToken::all()->count());
    }

    /**
     * @test
     */
    public function order_success_for_upsells()
    {
        Event::fake();

        $course = factory(Course::class)->create(['status' => 1]);
        $user = factory(User::class)->create();
        factory(CourseInfusionsoft::class)->create([
            'course_id'  => $course->id,
            'is_account' => 'uv222'
        ]);

        $c = factory(Course::class)->create(['status' => 1]);
        $aCourseIS = factory(CourseInfusionsoft::class)->create([
            'course_id' => $c->id,
            'price'     => 222,
            'upsell'    => 1,
            'is_account' => 'uv222'
        ]);
        factory(InfusionsoftMerchantId::class)->create(['account' => 'uv222']);

        $upsell = factory(CourseUpsell::class)->create([
            'course_infusionsoft_id' => $aCourseIS->id,
        ]);
        $token  = factory(CourseUpsellToken::class)->create([
            'course_upsell_id' => $upsell->id
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
                'saved_credit_card' => json_encode($this->cards[0]),
                'token' => $token->token
            ]);

        $response->assertStatus(302)
            ->assertSessionMissing('errors');

        Event::assertDispatched(UserEnrolled::class, function ($e) use ($user, $course) {
            return $e->user->id === $user->id && $course->id = $e->course->id;
        });

        $this->assertDatabaseHas('user_courses', [
            'user_id'   => $user->id,
            'course_id' => $course->id,
            'invoice_id' => 1
        ]);

        $this->assertDatabaseHas('course_upsell_tokens', [
            'course_upsell_id' => $upsell->id,
            'used'             => 1
        ]);
        self::assertEquals(1, CourseUpsellToken::all()->count());
    }
}
