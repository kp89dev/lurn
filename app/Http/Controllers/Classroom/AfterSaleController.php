<?php
namespace App\Http\Controllers\Classroom;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseUpsell;
use App\Models\CourseUpsellToken;
use Illuminate\Http\Request;
use Closure;

class AfterSaleController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, Closure $next) {
            $this->seoSetup($request);

            return $next($request);
        });
    }
    
    public function index(Request $request, Course $course)
    {
        //$request->session()->put('recent_purchase_id', 6);
        if (! $request->session()->get('recent_purchase_id') ||
            $course->id != $request->session()->get('recent_purchase_id')
        ) {
            return redirect()->route('enroll', compact('course'));
        }

        $request->session()->forget('recent_purchase_id');
        $userCourses = $request->user()->courses->pluck('id');

        $upsells = (new CourseUpsell())
                        ->activeForCourse($course)
                        ->get()
                        ->filter(function ($upsell) use ($userCourses) {
                            return ! in_array($upsell->infusionsoft->course_id, $userCourses->toArray(), false);
                        });

        if (! $upsells->count()) {
            return $this->thankYou($course);
        }

        return $this->upsell($upsells->first(), $course);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function thankYou(Course $course)
    {
        return view('enroll.thank-you', compact('course'));
    }

    /**
     * @param CourseUpsell $upsell
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    private function upsell(CourseUpsell $upsell, Course $course)
    {
        $upsellToken = (new CourseUpsellToken())->generateNew($upsell);

        return view('enroll.upsell', compact('upsell', 'upsellToken', 'course'));
    }
}
