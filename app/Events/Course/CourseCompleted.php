<?php
namespace App\Events\Course;

use App\Models\Course;
use App\Models\User;

class CourseCompleted
{
    /**
     * @type User
     */
    public $user;

    /**
     * @type Course
     */
    public $course;

    public function __construct(User $user, Course $course)
    {
        $this->user = $user;
        $this->course = $course;
    }
}
