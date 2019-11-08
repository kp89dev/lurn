<?php
namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Event;
use App\Models\Onboarding\Mission;
use App\Services\Contracts\TrackerInterface;
use Carbon\Carbon;
use Illuminate\View\View;
use Closure;
use App\Models\CourseBonus;
use App\Models\Ad;

class DashboardController extends Controller
{

    public function __construct()
    {
        $this->middleware(function ($request, Closure $next) {
            $this->seoSetup($request);

            return $next($request);
        });
    }

    /**
     * @return View
     */
    public function index()
    {
        $userCourses = user()->activeCourses();
        
        
        
        $bounusCourses = CourseBonus::all()->pluck('bonus_course_id');
        $excludedCourses = $bounusCourses->merge($userCourses->pluck('id'))->unique();
        
        $recommendedCourses = Course::whereNotIn('id', $excludedCourses)->take(3)->get();
        
        
        
        $courseInProgress = user()->getInProgressCourse();
        $upcomingEvents = Event::whereIn('course_container_id', $userCourses->pluck('course_container_id'))
            ->whereBetween('start_date', [Carbon::now()->format('Y-m-d'), Carbon::now()->addDays(15)->format('Y-m-d')])
            ->orderBy('start_date')
            ->take(10)
            ->get();
        $ads = new Ad;
        $adFirst = $ads->getByLocationAndPosition('dashboard', 'first');
        $adSecond = $ads->getByLocationAndPosition('dashboard', 'second');
        $onboarding = new Mission(user());

        return view('pages.dashboard', compact('userCourses', 'courseInProgress', 'recommendedCourses', 'upcomingEvents', 'adFirst', 'adSecond', 'onboarding'));
    }
}
