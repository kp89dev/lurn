<?php
namespace App\Http\Controllers\Admin\CourseBonuses;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Course;
use App\Models\CourseBonus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Log;

class ResourceController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin.role.auth:courses,read')->only('index');
        $this->middleware('admin.role.auth:courses,write')->only('create', 'store', 'edit', 'update');
    }

    /**
     * @param Course $course
     * @param CourseBonus $courseBonus
     * @return RedirectResponse
     */
    public function index(Course $course, CourseBonus $courseBonus)
    {
        $bonuses = true;

        $excludeFromBonus = array_merge(
            [$course->id],
            $courseBonus->where('course_id', $course->id)->pluck('bonus_course_id')->toArray()
        );

        return redirect()->route('courses.index', compact('bonuses', 'course', 'excludeFromBonus'));
    }

    /**
     * @param Request $request
     * @param Course $course
     * @param CourseBonus $courseBonus
     * @return RedirectResponse
     */
    public function store(Request $request, Course $course, CourseBonus $courseBonus)
    {
        $courseBonus->create([
            'bonus_course_id' => $request->get('bonus_course_id'),
            'course_id' => $course->id,
        ]);

        return redirect()->back()->with('alert-success', 'Course Bonus succesfully added');
    }

    /**
     * @param Course $course
     * @param CourseBonus $courseBonus
     * @return RedirectResponse
     */
    public function destroy(Course $course, CourseBonus $courseBonus)
    {
        try {
            $courseBonus->delete();
        } catch (\Exception $e) {
            $message = catch_and_return('There was a problem deleting the Course Bonus.', $e);
            return redirect()->back()->with('alert-error', $message);
        }

        return redirect()->back()->with('alert-success', 'Course Bonus succesfully removed');
    }
}
