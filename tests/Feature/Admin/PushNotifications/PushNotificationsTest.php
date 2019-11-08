<?php

namespace Tests\Feature\Admin\PushNotifications;

use App\Models\PushNotifications;


class PushNotificationsTest extends \SuperAdminLoggedInTestCase
{
    /**
     * @test
     */
    public function push_notifications_page_available()
    {
        $response = $this->get(route('push-notifications.index'));
        
        $response->assertStatus(200)
            ->assertSee('Push Notifications')
            ->assertSee("Add New Push Notification");
    }
    
    /**
     * @test
     */
    public function push_notifications_get_listed()
    {
        $pn = factory(PushNotifications::class)->create(['admin_title' => 'Test Push']);
        
        $response = $this->get(route('push-notifications.index'))
            ->assertSee('Test Push');
    }
    
    /**
     * @test
     */
    public function add_push_notification_page_is_available()
    {
        $response = $this->get(route('push-notifications.create'));
        
        $response->assertStatus(200)
            ->assertSee('Push Notification Details');
    }
    
    /**
     * @test
     */
    public function successfully_add_a_new_push_notification()
    {
        $response = $this->post(route('push-notifications.store'), [
                'admin_title'   => 'Fake Title Admin',
                'start_date'    => '1/1/2017',
                'end_date'      => '1/1/2017',
                'start_time'    => '5:00:00 AM',
                'end_time'      => '6:00:00 AM',
                'timezone'      => '-6',
                'content'       => 'This is a test push notification. This is only a test.',
                'cta_type'      => 'Internal',
                'internal_cta_type' => 'Link',
                'internal_link' => 'http://www.google.com'
        ]);
        
        $this->assertDatabaseHas('push_notifications', [
            'admin_title'   => 'Fake Title Admin',
            'end_utc'       => '2017-01-01 12:00:00'
        ]);
        
        $response->assertRedirect(route('push-notifications.index'))
            ->assertSessionMissing('errors');
    }
    
    /**
     * @test
     */
    public function edit_page_is_accessible()
    {
        $pn = factory(PushNotifications::class)->create();
        
        $response = $this->get(route('push-notifications.edit', [
            'pushNotificationId' => $pn->id
        ]));
        
        $response->assertStatus(200)
            ->assertSee($pn->admin_title);
    }
    
    /**
     * @test
     */
    public function successfully_edit_a_push_notification()
    {
        $pn = factory(PushNotifications::class)->create();
        
        $response = $this->put(route('push-notifications.update', ['pushNotification' => $pn->id]), [
            'admin_title'   => 'Fake Title Admin',
            'start_date'    => '1/1/2017',
            'end_date'      => '1/1/2017',
            'start_time'    => '5:00:00 AM',
            'end_time'      => '6:00:00 AM',
            'timezone'      => '-6',
            'content'       => 'This is a test push notification. This is only a test.',
            'cta_type'      => 'Internal',
            'internal_cta_type' => 'Link',
            'internal_link' => 'http://www.google.com'
        ]);
        
        $this->assertDatabaseHas('push_notifications', [
            'id'            => $pn->id,
            'admin_title'   => 'Fake Title Admin',
            'end_utc'       => '2017-01-01 12:00:00'
        ]);
        
        $response->assertRedirect(route('push-notifications.index'))
            ->assertSessionMissing('errors');
    }
}
