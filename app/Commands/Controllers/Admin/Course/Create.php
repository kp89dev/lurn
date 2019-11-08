<?php

namespace App\Commands\Controllers\Admin\Course;

use App\Commands\Controllers\Admin\Course\CourseBase;
use App\Models\Course;
use App\Models\CourseBonus;
use App\Models\Labels;
use App\Models\Sendlane;

class Create extends CourseBase
{
    /**
     * @return array
     */
    public function process()
    {
        $this->course = new Course();
        $lists = [];

        $hasBonus = false;
        $excludeFromBonus = collect($this->course->id);
        $bonusCourses = CourseBonus::all();
        $bonusCourses->each(function ($item) use ($excludeFromBonus) {
            $excludeFromBonus->push($item->bonus_course_id);
        });

        $labels = Labels::all();
        if ($this->request->sendlane) {
            $lists = json_decode($this->getSendlaneLists($this->request->sendlane), true);
        }

        return [
            'course' => $this->course,
            'action' => route('courses.store'),
            'method' => '',
            'sendlaneAccounts' => Sendlane::all(),
            'lists' => $lists,
            'recommendations' => $this->course->getRecommedations(),
            'courseSEO' => $this->course->getCourseSEO(),
            'seoaction' => 'create',
            'hasBonus' => $hasBonus,
            'excludeFromBonus' => $excludeFromBonus,
            'postRegistrationDescription' => $this->getPostRegistrationDescription(),
            'labels' => $labels,
        ];
    }
}