<?php

namespace App\Models\Resources;

use App\Models\Course;
use App\Models\CourseBonus;
use App\Models\CourseFeature;
use App\Models\CourseRecommendations;

class CourseRelatives
{
    protected $id;
    protected $course;

    /**
     * CourseRecommendations constructor.
     *
     * @param Course $course
     */
    public function __construct(Course $course)
    {
        $this->id = $course->id;
        $this->course = $course;
    }

    /**
     * Compute recommendations from the current course.
     *
     * @param int $howMany
     * @return mixed
     */
    public function getRecommended($howMany = 4)
    {
        $excludedCourses = $this->getExcludedCoursesList();
        $recommendations = $this->course->recommendations()
            ->whereNotIn('recommended_course_id', $excludedCourses)
            ->orderBy('order')
            ->pluck('recommended_course_id');

        $this->scanCategoriesForMoreRelatedCourses($howMany, $recommendations, $excludedCourses);

        $excludedCourses = $excludedCourses->merge($recommendations)->unique();

        if ($needed = $howMany - count($recommendations)) {
            $availableCourses = Course::whereNotIn('id', $excludedCourses)->take($needed);

            if ($availableCourses->count()) {
                $recommendations = $recommendations->merge($availableCourses->pluck('id'));
            }
        }

        $availableBonusIds = CourseBonus::whereNotIn('bonus_course_id', $excludedCourses)->pluck('bonus_course_id');
        $recommendations = $recommendations->merge($availableBonusIds)->unique();

        $courseIds = $recommendations->implode(',') ?: 0;
        $recommendation = Course::whereIn('id', $recommendations)->orderByRaw("field (id, $courseIds)")->get();

        return $recommendation;
    }

    /**
     * Excludes some courses from the active subscriptions,
     * those which are bonuses of the current course.
     *
     * @return mixed
     */
    private function getExcludedCoursesList()
    {
        $exclusions = user() ? user()->courseSubscriptions()->active()->pluck('course_id') : collect();

        if ($exclusions->contains($this->id)) {
            $excludedBonusList = CourseBonus::where('course_id', '!=', $this->id)->pluck('bonus_course_id');
        }
        else {
            $exclusions->push($this->id);
            $excludedBonusList = CourseBonus::all()->pluck('bonus_course_id');
        }
        
        $excludedDisabled = Course::where('status',0)->orWhere('purchasable',0)->pluck('id');
        $exclusions = $exclusions->merge($excludedDisabled)->unique();
        
        return $exclusions->merge($excludedBonusList)->unique();
    }

    /**
     * Goes through each course's categories to get more related courses.
     *
     * @param $howMany
     * @param $recommendations
     * @param $excludedCourses
     */
    private function scanCategoriesForMoreRelatedCourses($howMany, $recommendations, $excludedCourses)
    {
        $categories = $this->course->categories;
        $categoriesRemaining = $categories->count();
        $excludedCategory = [];

        while ($howMany - count($recommendations) && $categoriesRemaining) {
            foreach ($categories as $category) {
                if (! in_array($category->id, $excludedCategory)) {
                    $categoryCourse = $category->courses->whereNotIn('id', $excludedCourses)->first();

                    if ($categoryCourse) {
                        $recommendations->push($categoryCourse->id);
                        $excludedCourses->push($categoryCourse->id);
                    }
                    else {
                        $categoriesRemaining -= 1;
                        $excludedCategory[] = $category->id;
                    }
                }
            }
        }
    }

    public function getRecommendations()
    {
        $recommendations = collect();
        $excludedIds = Course::where('status',0)->orWhere('purchasable',0)->pluck('id');
        $excludedIds->push($this->id);

        $this->course->recommendations()
            ->with('course')
            ->each(function ($recom) use (&$recommendations, &$excludedIds) {
                $excludedIds->push($recom->recommended_course_id);
                $recommendations->push([
                    'courseID'    => $recom->course->id,
                    'courseTitle' => $recom->course->title,
                    'order'       => $recom->order,
                ]);
            });

        $excludedBonusIds = CourseBonus::whereNotIn('course_id', $excludedIds)->pluck('bonus_course_id');
        $excludedIds = $excludedIds->merge($excludedBonusIds)->unique();

        Course::whereNotIn('id', $excludedIds)
            ->each(function ($course) use (&$recommendations) {
                $recommendations->push([
                    'courseID'    => $course->id,
                    'courseTitle' => $course->title,
                    'order'       => null,
                ]);
            });

        return $recommendations;
    }

    public function setRecommendations($recommendations)
    {
        $this->course->recommendations()->delete();
        $order = 1;

        foreach ($recommendations as $recommendedCourse) {
            if ($recommendedCourse != 'none') {
                CourseRecommendations::create([
                    'course_id'             => $this->id,
                    'recommended_course_id' => $recommendedCourse,
                    'order'                 => $order++,
                ]);
            }
        }

        return $order;
    }

    public function getFeatured($isAdmin = false)
    {
        $featured = collect();
        $excludedIds = $isAdmin ? collect() : user()->courseSubscriptions()->active()->pluck('course_id');

        CourseFeature::whereNotIn('course_id', $excludedIds)
            ->withBootcamps()
            ->each(function ($item) use (&$featured, &$excludedIds) {
                $excludedIds->push($item->course->id);
                $featured->push([
                    'courseID'    => $item->course->id,
                    'courseTitle' => $item->course->title,
                    'order'       => $item->order,
                ]);
            });

        $excludedIds = $excludedIds->merge(CourseBonus::pluck('bonus_course_id'))->unique();

        Course::whereNotIn('id', $excludedIds)
            ->each(function ($course) use (&$featured) {
                $featured->push([
                    'courseID'    => $course->id,
                    'courseTitle' => $course->title,
                    'order'       => null,
                ]);
            });

        return $featured;
    }
}
