<?php
namespace App\Http\ViewComposers;

use Illuminate\View\View;
use App\Models\Course;

class FreeCourseComposer
{

    public $freeCourseList;
    public $freeCourses;

    public function __construct()
    {
        $this->freeCourseList = Course::where('free', 1)->whereIn('slug', ['digital-bootcamp', 'digital-startup', 'fb-bootcamp'])->get();

        if (! $this->freeCourseList->count()) {
            $this->freeCourseList = Course::where('free', 1)->get();
        }
    }

    public function compose(View $view)
    {
        $view->with('freeCourses', $this->freeCourseList);
    }
}
