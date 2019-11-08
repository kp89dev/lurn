<?php
namespace App\Http\ViewComposers;

use Illuminate\View\View;
use App\Models\Course;

class FeaturedCourseComposer
{

    public $featuredCourseList;
    public $featuredCourses;

    public function __construct()
    {
        $featuredCourses = collect();
        $course = new Course();
        $this->featuredCourseList = $course->getFeatured();
        $featuredCourseList = $course->getFeatured();
        foreach ($featuredCourseList as $featured) {
            $featuredCourses->push(Course::find($featured['courseID']));
        }
        $this->featuredCourses = $featuredCourses;
    }

    public function compose(View $view)
    {
        $view->with('featuredCourses', $this->featuredCourses->flatten());
    }
}
