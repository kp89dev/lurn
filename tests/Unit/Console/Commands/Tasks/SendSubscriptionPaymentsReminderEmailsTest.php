<?php

namespace Tests\Unit\Console\Commands\Tasks;

use App\Models\Course;
use App\Models\CourseInfusionsoft;
use App\Models\CourseSubscriptions;
use App\Models\User;

class SendSubscriptionPaymentsReminderEmailsTest extends \TestCase
{
    /**
     * @test
     */
    public function reminds_subscription_payments_about_next_payment()
    {
        $userA = factory(User::class)->create();
        $userB = factory(User::class)->create();
        $course = factory(Course::class)->create(['status' => 1, 'free' => 0]);

        $course->infusionsoft()->create([
            'subscription'               => 1,
            'subscription_price'         => 100,
            'is_subscription_product_id' => 123,
        ]);

        $userA->courseSubscriptions()->create([
            'course_id'              => $course->id,
            'course_infusionsoft_id' => $course->infusionsoft->id,
            'subscription_payment'   => 1,
            'is_product_id'          => $course->infusionsoft->subscription_price,
            'paid_at'                => now()->subDays(15),
            'payments_made'          => 1,
            'payments_required'      => 3,
            'cancelled_at'           => null,
            'status'                 => 0,
        ]);
        
        $userB->courseSubscriptions()->create([
            'course_id'              => $course->id,
            'course_infusionsoft_id' => $course->infusionsoft->id,
            'subscription_payment'   => 1,
            'is_product_id'          => $course->infusionsoft->subscription_price,
            'paid_at'                => now()->subDays(30),
            'payments_made'          => 1,
            'payments_required'      => 3,
            'cancelled_at'           => null,
            'status'                 => 0,
        ]);

        $this->assertDatabaseHas('user_courses', [
            'user_id'            => $userA->id,
            'course_id'          => $course->id,
            'notifications_sent' => 0,
        ]);

        $this->assertDatabaseHas('user_courses', [
            'user_id'            => $userB->id,
            'course_id'          => $course->id,
            'notifications_sent' => 0,
        ]);

        $this->artisan('send:payment-reminders');

        $this->assertDatabaseMissing('user_courses', [
            'user_id'            => $userA->id,
            'course_id'          => $course->id,
            'notifications_sent' => 1,
        ]);

        $this->assertDatabaseHas('user_courses', [
            'user_id'            => $userB->id,
            'course_id'          => $course->id,
            'notifications_sent' => 1,
        ]);
    }
}
