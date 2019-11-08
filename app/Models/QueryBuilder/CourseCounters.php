<?php

namespace App\Models\QueryBuilder;

use App\Models\Course;
use Illuminate\Support\Facades\DB;

class CourseCounters
{
    protected $course;

    public function __construct(Course $course)
    {
        $this->course = $course;
    }

    public function get()
    {
        $modulesCount = "select count(*) from modules where course_id = :course and status = 1 and hidden = 0";
        $lessonsCount = "select count(*)
            from lessons l
            join modules m
              on m.id = l.module_id
            where course_id = :course
              and m.status = 1
              and m.hidden = 0
              and l.status = 1";
        $studentsCount = "select count(distinct user_id) from user_courses where course_id = :course";
        $likesCount = "select count(distinct user_id) from course_likes where course_id = :course";

        return DB::select("
            select
              ($modulesCount) as modules,
              ($lessonsCount) as lessons,
              ($studentsCount) as students,
              ($likesCount) as likes
        ", ['course' => $this->course->id])[0];
    }
}
