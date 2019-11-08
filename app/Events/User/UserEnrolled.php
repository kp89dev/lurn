<?php
namespace App\Events\User;

use App\Models\Course;
use App\Models\User;

class UserEnrolled
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
