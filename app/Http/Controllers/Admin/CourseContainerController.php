<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\CourseContainer\StoreCourseContainerRequest;
use App\Models\Course;
use App\Http\Controllers\Controller;
use App\Models\CourseContainer;
use Illuminate\Http\Request;

class CourseContainerController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin.role.auth:course-containers,read')
            ->only('index');
        $this->middleware('admin.role.auth:course-containers,write')
            ->only('create', 'store', 'edit', 'update', 'destroy');
    }

    public function index()
    {
        $courseContainers = CourseContainer::simplePaginate(20);

        return view('admin.course-containers.index', compact('courseContainers'));
}

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
        $courseContainer = new CourseContainer();
        $action = route('course-containers.store');
        $method = '';

        return view('admin.course-containers.create-edit', compact('courseContainer', 'action', 'method'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreCourseContainerRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCourseContainerRequest $request)
    {
        CourseContainer::create($request->only('title'));

        return redirect()
                    ->route('course-containers.index')
                    ->with('alert-success', 'Course Container succesfully added');
    }

    /**
     * @param CourseContainer $courseContainer
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($containerId)
    {
        $courseContainer = CourseContainer::find($containerId);

        $action = route('course-containers.update', ['course_container'=> $courseContainer->id]);
        $method = method_field('PUT');

        return view('admin.course-containers.create-edit', compact('courseContainer', 'action', 'method'));
    }

    /**
     * @param StoreCourseContainerRequest $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(StoreCourseContainerRequest $request)
    {
        $courseContainer = CourseContainer::find($request->course_container);
        $courseContainer->fill($request->all());
        $courseContainer->save();

        return redirect()->route('course-containers.index')->with('alert-success', 'Course succesfully modified');
    }
}
