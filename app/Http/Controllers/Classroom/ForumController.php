<?php

namespace App\Http\Controllers\Classroom;

use App\Http\Controllers\Controller;
use App\Models\CourseVanillaForum;
use App\Models\Course;

class ForumController extends Controller
{
    public function showRules (Course $course)
    {
        $vanillaForum = CourseVanillaForum::where('course_id', $course->id)->first();
        
        return view('pages.classroom.forum', compact('vanillaForum'));
    }
}
