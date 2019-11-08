<?php

namespace App\Http\Controllers\Admin;

use App\Commands\Controllers\Admin\Course\Create;
use App\Commands\Controllers\Admin\Course\Edit;
use App\Commands\Controllers\Admin\Course\Store;
use App\Commands\Controllers\Admin\Course\Update;
use App\Http\Requests\Admin\Course\StoreCourseRequest;
use App\Models\Course;
use App\Http\Controllers\Controller;
use App\Models\Labels;
use App\Models\Sendlane;
use App\Models\Category;
use App\Models\CourseBonus;
use App\Services\Sendlane\Sendlane as SendlaneService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;

class CourseController extends Controller
{
    public $indexType = 'full';

    public function __construct()
    {
        $this->middleware('admin.role.auth:courses,read')->only('index');
        $this->middleware('admin.role.auth:courses,write')->only('create', 'store', 'edit', 'update');
    }

    /**
     * Show full course list.
     * Show course list limited by category
     * Show set featured course form
     *
     * @param Request $request
     *
     * @return View
     */
    public function index(Request $request)
    {
        $courses = $this->getIndexCourses($request);
        $action = route('courses.admin.featured');
        $search = route('courses.admin.search');
        $excludeFromBonus = [];

        $indexType = $this->indexType;

        if ($indexType === 'bonus') {
            $excludeFromBonus = array_merge(
                [$request->course],
                CourseBonus::where('course_id', $request->course)->pluck('bonus_course_id')->toArray()
            );
        }

        return view('admin.courses.index', compact('courses', 'action', 'search', 'indexType', 'excludeFromBonus'));
    }

    public function getIndexCourses($request)
    {
        if ($request->bonuses) {
            $course = Course::find($request->course);
            $bonuses = $course->bonuses()->pluck('bonus_course_id');
            $this->indexType = 'bonus';
            $courses = Course::whereIn('id', $bonuses)->simplePaginate(20);

            return $courses;
        }

        if ($request->category) {

            $category = Category::find($request->category);
            $courses = $category->courses()->simplePaginate(20);
            $this->indexType = 'category';

            return $courses;
        }
        
        if ($courses = \Session::get('courses')) {
            return Course::whereIn('id', $courses)->orderBy('id', 'desc')->simplePaginate(20);
        }
        else {
            return Course::simplePaginate(20);
        }

    }

    /**
     * Show the form for creating a new resource.
     *
     * @param Create $courseCreate
     * @return \Illuminate\Http\Response
     */
    public function create(Create $courseCreate)
    {
        /**
         * All functionality in this method was moved to
         * App\Commands\Controllers\Admin\CourseCreate command.
         *
         * The thought is to remove additional functionality from the controller to
         * allow the controller to only be responsible for requests and responses.
         */
        return view('admin.courses.create-edit', $courseCreate->process());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreCourseRequest $request
     * @param Store $courseStore
     *
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function store(StoreCourseRequest $request, Store $courseStore)
    {
        /**
         * All functionality in this method was moved to
         * App\Commands\Controllers\Admin\CourseStore command.
         *
         * The thought is to remove additional functionality from the controller to
         * allow the controller to only be responsible for requests and responses.
         */
        $courseStore->setRequest($request)->process();

        return redirect()->route('courses.index')->with('alert-success', 'Course succesfully added');
    }

    /**
     * @param Course $course
     *
     * @param Edit $courseEdit
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Exception
     */
    public function edit(Course $course, Edit $courseEdit)
    {
        /**
         * All functionality in this method was moved to
         * App\Commands\Controllers\Admin\CourseEdit command.
         *
         * The thought is to remove additional functionality from the controller to
         * allow the controller to only be responsible for requests and responses.
         */
        return view('admin.courses.create-edit', $courseEdit->setCourse($course)->process());
    }

    /**
     * @param StoreCourseRequest $request
     * @param Update $courseUpdate
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function update(StoreCourseRequest $request, Update $courseUpdate)
    {
        /**
         * All functionality in this method was moved to
         * App\Commands\Controllers\Admin\CourseUpdate command.
         *
         * The thought is to remove additional functionality from the controller to
         * allow the controller to only be responsible for requests and responses.
         */
        $courseUpdate->setRequest($request)->process();

        return redirect()->route('courses.index')->with('alert-success', 'Course succesfully modified');
    }
}
