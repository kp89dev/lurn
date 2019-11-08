<?php

namespace Tests\Unit\Console\Commands\Tasks;

use App\Mail\SendSubscriptionCancellationNotice;
use App\Models\Course;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class SendSubscriptionCancellationNotificationsTest extends \TestCase
{
    /**
     * @test
     */
    public function sends_subscription_cancellation_notices()
    {
        Mail::fake();

        $course = factory(Course::class)->create();
        $user = factory(User::class)->create();

        $user->enroll($course);
        $user->courseSubscriptions()->update([
            'paid_at'              => now()->subDays(38)->subHour(),
            'subscription_payment' => 1,
            'payments_made'        => 1,
            'payments_required'    => 3,
        ]);

        $this->artisan('notify:subscription-cancellations');

        Mail::assertSent(SendSubscriptionCancellationNotice::class, function ($mail) use ($user, $course) {
            return $mail->hasTo($user->email);
        });
    }
}
