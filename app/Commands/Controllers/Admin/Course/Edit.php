<?php

namespace App\Commands\Controllers\Admin\Course;

use App\Commands\Controllers\Admin\Course\CourseBase;
use App\Models\Course;
use App\Models\CourseBonus;
use App\Models\Sendlane;
use Exception;

/**
 * This class was derived from the logic that was inside the
 * App\Http\Controllers\Admin\CourseController.php > edit method.
 *
 * The concept behind a command is to move functionality from
 * a controller or class that it does not belong in. By creating
 * a command, it follows the Single Responsibility rule in
 * PHP SOLID design principles.
 *
 * In plain terms, it cleans up the class using this class to make
 * it more readable and easier to interpret. It also eliminates
 * bugs from not being able to see something for many lines of code.
 *
 * Everything in this class follows the SOLID design principles.
 *
 * To use for other controllers or classes, simply create a
 * command class in the App\Commands namespace, and extend the base
 * App\Commands\Base.php Class either through another Base class like
 * this one, or directly.
 *
 * The only public methods on Command classes should be setter
 * methods and a process method that takes no parameters.
 *
 * Class CourseEdit
 * @package App\Commands\Controllers\Admin
 */
class Edit extends CourseBase
{
    /**
     * @param Course $course
     * @return $this
     */
    public function setCourse(Course $course)
    {
        $this->course = $course;
        return $this;
    }

    /**
     * @return array
     */
    public function process()
    {
        $lists = [];
        $hasBonus = false;
        $excludeFromBonus = collect($this->course->id);
        $bonusCourses = CourseBonus::all();

        $bonusCourses->each(function ($item) use ($excludeFromBonus) {
            $excludeFromBonus->push($item->bonus_course_id);
        });

        if ($bonusCourses->contains('course_id', $this->course->id)) {
            $hasBonus = true;
        }

        if ($this->request->sendlane) {
            $lists = json_decode($this->getSendlaneLists($this->request->sendlane), true);
        }

        return [
            'course' => $this->course,
            'action' => route('courses.update', ['course' => $this->course->id]),
            'method' => method_field('PUT'),
            'sendlaneAccounts' => Sendlane::all(),
            'lists' => $lists,
            'recommendations' => $this->course->getRecommedations(),
            'courseSEO' => $this->course->getCourseSEO(),
            'seoaction' => route('seo.update.course', ['course' => $this->course->id]),
            'hasBonus' => $hasBonus,
            'excludeFromBonus' => $excludeFromBonus,
            'postRegistrationDescription' => $this->getPostRegistrationDescription(),
        ];
    }
}
