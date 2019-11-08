<?php
namespace Feature\Account;

use App\Jobs\Infusionsoft\CancelSubscription;
use App\Jobs\Infusionsoft\GetContactSubscriptionIdOnProduct;
use App\Models\Course;
use App\Models\CourseInfusionsoft;
use App\Models\CourseSubscriptions;
use App\Models\InfusionsoftContact;
use Tests\UserLoggedInTestCase;
use Illuminate\Contracts\Bus\Dispatcher;

class CancelSubscriptionTest extends UserLoggedInTestCase
{
    /**
     * @test
     */
    public function cancelling_subscription_fails_when_subscription_isnt_found()
    {
        $courses = factory(Course::class, 2)->create();

        //create a random subscription
        $subscription = factory(CourseSubscriptions::class)->create([
            'user_id' => $this->user->id,
            'course_id' => $courses[1]->id
        ]);

        $response = $this->post(route('cancel.subscription'), [
            'subscription' => $subscription->id,
            'course_id'    => $courses[0]->id
        ]);

        $response->assertStatus(302)
                 ->assertSessionHasErrors();
    }

    /**
     * @test
     */
    public function cancelling_subscription_fails_when_the_course_isnt_a_subscription()
    {
        $course = factory(Course::class)->create();

        factory(CourseInfusionsoft::class)->create([
            'course_id'    => $course->id,
            'subscription' => 0
        ]);

        $subscription = factory(CourseSubscriptions::class)->create([
            'user_id' => $this->user->id,
            'course_id' => $course->id,
            'cancelled_at' => null
        ]);

        $response = $this->post(route('cancel.subscription'), [
            'subscription' => $subscription->id,
            'course_id'    => $course->id
        ]);

        $response->assertStatus(302)
                 ->assertSessionHasErrors();
    }

    /**
     * @test
     */
    public function cancelling_getting_active_subscriptions_returns_empty_array()
    {
        $course   = factory(Course::class)->create(['status' => 1]);
        factory(CourseSubscriptions::class)->create([
            'course_id' => $course->id,
            'user_id'   => $this->user->id,
            'cancelled_at' => null
        ]);

        $courseIS = factory(CourseInfusionsoft::class)->create([
            'course_id' => $course->id,
            'subscription' => 1
        ]);
        factory(InfusionsoftContact::class)->create([
            'user_id' => $this->user->id,
            'is_account' => $courseIS->is_account
        ]);

        $commandBus = $this->createMock(Dispatcher::class);
        $getSubscription = $this->createMock(GetContactSubscriptionIdOnProduct::class);
        $getSubscription->expects(static::once())
                    ->method('hasError')
                    ->willReturn(false);
        $getSubscription->expects(static::once())
                    ->method('getResponse')
                    ->willReturn([]);

        $commandBus->expects(static::once())
                    ->method('dispatchNow')
                    ->willReturn($getSubscription);


        $this->app->instance(Dispatcher::class, $commandBus);

        $response = $this->post(route('cancel.subscription'), [
            'subscription'  => $courseIS->is_product_id,
            'course_id'     => $course->id
        ]);

        $response->assertStatus(302);
        self::assertContains("Please try again later", app('session.store')->get('errors')->first());
    }

    /**
     * @test
     */
    public function attempting_to_cancel_returns_error()
    {
        $course   = factory(Course::class)->create(['status' => 1]);
        factory(CourseSubscriptions::class)->create([
            'course_id' => $course->id,
            'user_id'   => $this->user->id,
            'cancelled_at' => null
        ]);

        $courseIS = factory(CourseInfusionsoft::class)->create([
            'course_id' => $course->id,
            'subscription' => 1
        ]);
        factory(InfusionsoftContact::class)->create([
            'user_id' => $this->user->id,
            'is_account' => $courseIS->is_account
        ]);

        $getSubscription = $this->createMock(GetContactSubscriptionIdOnProduct::class);
        $getSubscription->method('hasError')->willReturn(false);
        $getSubscription->method('getResponse')->willReturn([['Id' => 1]]);

        $cancelSubscription = $this->createMock(CancelSubscription::class);
        $cancelSubscription->expects(self::once())->method('hasError')->willReturn(true);
        $cancelSubscription->expects(self::once())->method('getError')->willReturn('An error message');

        $commandBus = $this->createMock(Dispatcher::class);
        $commandBus->expects(static::at(0))->method('dispatchNow')->willReturn($getSubscription);
        $commandBus->expects(static::at(1))->method('dispatchNow')->willReturn($cancelSubscription);

        $this->app->instance(Dispatcher::class, $commandBus);

        $response = $this->post(route('cancel.subscription'), [
            'subscription'  => $courseIS->is_product_id,
            'course_id'     => $course->id
        ]);

        $response->assertStatus(302);
        self::assertContains("An error", app('session.store')->get('errors')->first());
    }

    /**
     * @test
     */
    public function cancelling_subscription_successeds()
    {
        $course   = factory(Course::class)->create(['status' => 1]);
        factory(CourseSubscriptions::class)->create([
            'course_id' => $course->id,
            'user_id'   => $this->user->id,
            'cancelled_at' => null
        ]);

        $courseIS = factory(CourseInfusionsoft::class)->create([
            'course_id' => $course->id,
            'subscription' => 1
        ]);
        factory(InfusionsoftContact::class)->create([
            'user_id' => $this->user->id,
            'is_account' => $courseIS->is_account
        ]);

        $getSubscription = $this->createMock(GetContactSubscriptionIdOnProduct::class);
        $getSubscription->method('hasError')->willReturn(false);
        $getSubscription->method('getResponse')->willReturn([['Id' => 1]]);


        $cancelSubscription = $this->createMock(CancelSubscription::class);
        $cancelSubscription->expects(static::once())->method('hasError')->willReturn(false);

        $commandBus = $this->createMock(Dispatcher::class);
        $commandBus->expects(static::at(0))->method('dispatchNow')->willReturn($getSubscription);
        $commandBus->expects(static::at(1))->method('dispatchNow')->willReturn($cancelSubscription);
        
        $this->app->instance(Dispatcher::class, $commandBus);

        $response = $this->post(route('cancel.subscription'), [
            'subscription'  => $courseIS->is_product_id,
            'course_id'     => $course->id
        ]);

        $response->assertStatus(302)
                 ->assertSessionMissing('errors')
                 ->assertSessionHas('success');

        $this->assertDatabaseMissing('user_courses', [
            'course_id' => $course->id,
            'user_id'   => $this->user->id,
            'cancelled_at' => null
        ]);
    }
}
