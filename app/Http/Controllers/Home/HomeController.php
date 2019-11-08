<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use App\Models\CourseFeature;
use App\Models\UserActivities;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $featured = CourseFeature::with(['course', 'course.container'])->get();

        $featuredCourses = $featured->filter(function ($course) {
            return $course->free_bootcamp === 0;
        })->all();

        $featuredBootcamp = $featured->filter(function ($course) {
            return $course->free_bootcamp === 1;
        })->all();

        $offset = 0;
        $activities = collect();
        $activitiesCount = UserActivities::count();

        while ($activities->count() < 3 && $offset < $activitiesCount) {
            $activitiesSet = UserActivities::with(['activeUser'])
                ->orderBy('id', 'DESC')
                ->skip($offset)
                ->limit(50)
                ->get();

            foreach ($activitiesSet as $activitySet) {
                if (isset($activitySet->user->setting->image)) {
                    $activities->push($activitySet);
                }

                $offset++;
            }
        }
        $cdn_url = str_finish(config('app.cdn_assets', '/'), '/');
        return view('home.index', compact('featuredCourses', 'featuredBootcamp', 'activities', 'cdn_url'));
    }
}
