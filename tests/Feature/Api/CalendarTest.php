<?php

namespace Feature\Classroom;

use App\Models\Course;
use App\Models\Event;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CalendarTestTest extends \LoggedInTestCase
{
    use DatabaseTransactions;

    /**
     * @test
     */
    public function events_get_listed()
    {
        $course = factory(Course::class)->create();
        $this->user->enroll($course);
        factory(Event::class, 10)->create(['course_container_id' => $course->course_container_id]);

        $response = $this->get(url('api/events?' . http_build_query(['start' => date('Y-m-01'), 'end' => date('Y-m-28')])));
        $json = json_decode($response->getContent());

        self::assertTrue(isset($json->courses, $json->events));
    }
}