<?php

namespace Tests\Admin\Event;


use App\Models\CourseContainer;
use App\Models\Event;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class EventTest extends \SuperAdminLoggedInTestCase
{
    use DatabaseTransactions;

    /**
     * @test
     */
    public function event_list_page_is_available()
    {
        $response = $this->get(route('events.index'));

        $response->assertSee('Events')
            ->assertSee('Add New Event')
            ->assertStatus(200);
    }

    /**
     * @test
     */
    public function add_event_is_available()
    {
        $response = $this->get(route('events.create'));
        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function successfully_add_a_new_event()
    {
        $course_container = factory(CourseContainer::class)->create();

        $response = $this->post(
            route('events.store'), [
                'title'         => 'event title',
                'description'   => 'description of event',
                'course_container_id'  => $course_container->id,
                'start_date'    => '02/01/2017',
                'end_date'      => '02/01/2017',
                'start_time'    => '9:00:00 AM',
                'end_time'      => '10:00:00 AM',
                'location'      => 'Blue Room',
                'address'       => '21 Jump Street',
                'city'          => 'New York',
                'state'         =>'NY',
                'country'       => 'USA'
            ]
        );

        $this->assertDatabaseHas('events', [
                'title'         => 'event title',
                'description'   => 'description of event',
                'course_container_id'  => $course_container->id,
                'start_date'    => '2017-02-01',
                'end_date'      => '2017-02-01',
                'start_time'    => '09:00:00',
                'end_time'      => '10:00:00',
                'location'      => 'Blue Room',
                'address'       => '21 Jump Street',
                'city'          => 'New York',
                'state'         =>'NY',
                'country'       => 'USA'
        ]);

        $response->assertRedirect(route('events.index'))
            ->assertSessionMissing('errors');
    }

    /**
     * @test
     */
    public function edit_page_is_available()
    {
        $course_container = factory(CourseContainer::class)->create();
        $event = factory(Event::class)->create(['course_container_id' => $course_container->id]);

        $response = $this->get(route('events.edit', [
                'event' => $event->id
        ]));

        $response->assertStatus(200);
        $response->assertSee($event->title)
                 ->assertSee($event->description);
    }

    /**
     * @test
     */
    public function successfully_edit_an_event()
    {
        $course_container = factory(CourseContainer::class)->create();
        $event = factory(Event::class)->create(['course_container_id' => $course_container->id]);
        
        $response = $this->put(
            route('events.update', [ 'event' =>$event->id ]), [
            'title'       => 'new title',
            'description'   => 'updated description',
            'course_container_id'  => $course_container->id,
            'start_date'    => '03/01/2017',
            'end_date'      => '03/01/2017',
            'start_time'    => '9:30:00 AM',
            'end_time'      => '10:30:00 PM',
            'location'      => 'Red Room',
            'address'       => '22 Jump Street',
            'city'          => 'Paddington',
            'state'         => 'WY',
            'country'       => 'USA']);

        $this->assertDatabaseHas('events', [
            'title'       => 'new title',
            'description'   => 'updated description',
            'course_container_id'  => $course_container->id,
            'start_date'    => '2017-03-01',
            'end_date'      => '2017-03-01',
            'start_time'    => '09:30:00',
            'end_time'      => '22:30:00',
            'location'      => 'Red Room',
            'address'       => '22 Jump Street',
            'city'          => 'Paddington',
            'state'         => 'WY',
            'country'       => 'USA'
        ]);

        $response->assertRedirect(route('events.index'))
            ->assertSessionMissing('errors');
    }

    /**
     * @test
     */
    public function event_gets_deleted()
    {
        $course_container = factory(CourseContainer::class)->create();
        $event = factory(Event::class)->create(['course_container_id' => $course_container->id]);

        $this->delete(route('events.destroy', $event->id));
        $this->assertDatabaseMissing('events', ['id' => $event->id]);
    }
}
