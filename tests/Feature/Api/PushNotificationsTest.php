<?php

namespace Feature\Classroom;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\PushNotifications;
use Carbon\Carbon;
use App\Models\User;

class PushNotificationsTest extends \LoggedInTestCase
{
    /**
     * @test
     */
    public function test_unread_notifications_listed()
    {
        /** @var \Illuminate\Support\Collection $notifications */
        $notifications = factory(PushNotifications::class, 2)->create(['end_utc' => Carbon::now()->addMinutes(5)]);
        $response = $this->get(route('unread-push-notifications'));
        $response->assertStatus(200)
            ->assertJson($notifications->jsonSerialize());
    }

    /**
     * @test
     */
    public function test_mark_unread_notifications()
    {
        $notification = factory(PushNotifications::class)->create(['end_utc' => Carbon::now()->addMinutes(5)]);
        $this->post(route('mark-push-notification-read'), ['pushNotificationId' => $notification->id]);
        $response = $this->get(route('unread-push-notifications'));
        $response->assertStatus(200)
            ->assertJson([]);
    }
}
