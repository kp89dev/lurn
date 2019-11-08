<?php

namespace App\Http\Controllers\Api;

use App\Models\Event;
use App\Models\CourseBonus;

class CalendarController
{
    public function events()
    {
        $courses = user()->courses()
            ->whereNotIn ('courses.id', CourseBonus::all()->pluck('bonus_course_id'))
            ->where('user_courses.status','!==', 1)
            ->get();

        $events = Event::whereIn('course_container_id', $courses->pluck('course_container_id'))
            ->whereBetween('start_date', [request('start'), request('end')])
            ->get()
            ->each(function ($event) {
            	$event->start_ts = $event->start->getTimestamp();
            	$event->end_ts = $event->end->getTimestamp();
            });

        $courses->each(function ($course) {
        	$course->render = true;
        });

        return response()->json(compact('courses', 'events'));
    }
}