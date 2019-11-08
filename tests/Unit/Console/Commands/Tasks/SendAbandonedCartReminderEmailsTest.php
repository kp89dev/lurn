<?php

namespace Tests\Unit\Console\Commands\Tasks;

use App\Models\CartReminder;
use App\Models\Course;
use App\Models\User;
use Carbon\Carbon;

class SendAbandonedCartReminderEmailsTest extends \TestCase
{
    /**
     * @test
     */
    public function reminds_user_about_abandoned_cart()
    {
        $now    = Carbon::now();
        $user   = factory(User::class)->create();
        $course = factory(Course::class)->create(['status' => 1, 'free' => 0]);

        Carbon::setTestNow($now->copy()->subHours(30));

        factory(CartReminder::class)->create([
            'course_id' => $course->id,
            'user_id'   => $user->id
        ]);

        $this->assertDatabaseHas('cart_reminders', [
            'course_id' => $course->id,
            'user_id'   => $user->id
        ]);

        Carbon::setTestNow($now);

        $this->artisan('send:cart-reminders');

        $this->assertDatabaseMissing('cart_reminders', [
            'course_id' => $course->id,
            'user_id'   => $user->id
        ]);
    }

    /**
     * @test
     */
    public function api_saves_and_removes_cart_reminders()
    {
        $user = factory(User::class)->create();
        $course = factory(Course::class)->create(['status' => 1, 'free' => 0]);

        $this->post('/api/save-cart-reminder', [
            'course_id' => $course->id, 
            'user_id' => $user->id
        ]);

        $this->assertDatabaseHas('cart_reminders', [
            'course_id' => $course->id,
            'user_id' => $user->id
        ]);

        $this->post('/api/remove-cart-reminder', [
            'course_id' => $course->id, 
            'user_id' => $user->id
        ]);

        $this->assertDatabaseMissing('cart_reminders', [
            'course_id' => $course->id,
            'user_id' => $user->id
        ]);
    }
}
