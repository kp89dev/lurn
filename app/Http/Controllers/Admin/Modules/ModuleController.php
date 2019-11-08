<?php

namespace App\Http\Controllers\Admin\Modules;

use App\Http\Requests\Admin\Module\StoreModuleRequest;
use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Module;

class ModuleController extends Controller
{

    public function __construct()
    {
        $this->middleware('admin.role.auth:courses,read')->only('index');
        $this->middleware('admin.role.auth:courses,write')->only('create', 'store', 'edit', 'update', 'destroy');
    }

    public function index(Course $course)
    {
        $modules = $course->modules()->ordered()->simplePaginate(20);

        $action = route('lessons.drip');
        $method = 'POST';
        
        return view('admin.modules.index', compact('modules', 'course', 'action', 'method'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param Course $course
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Course $course)
    {
        $module = new Module();
        $action = route('modules.store', ['course' => $course->id]);
        $method = '';
        $lockableTest = $module->availableTest();
        return view('admin.modules.create-edit', compact('course', 'action', 'method', 'module', 'lockableTest'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreModuleRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreModuleRequest $request)
    {
        Module::create(
            $request->only('title', 'description', 'link') +
            [
                'course_id' => $request->course,
                'order'     => Module::getNextOrderValueForModule($request->course),
                'status'    => $request->get('status', 0),
                'hidden'    => $request->get('hidden', 0),
                'type'      => $request->get('type', 'Module'),
                'locked_by_test' => $request->get('locked_by_test', null),
            ]
        );

        return redirect()->route('modules.index', ['course' => $request->course])
                         ->with('alert-success', 'Module succesfully added');
    }

    public function edit(Course $course, Module $module)
    {
        $action = route('modules.update', ['course' => $course->id, 'module' => $module->id]);
        $method = method_field('PUT');

        $lockableTest = $module->availableTest();

        return view('admin.modules.create-edit', compact('course', 'module', 'action', 'method', 'lockableTest'));
    }

    public function update(StoreModuleRequest $request)
    {
        $course = Module::find($request->module);
        $course->fill(
            $request->only('title', 'description', 'link') +
            [
                'course_id' => $request->course,
                'order'     => ($request->get('order') ?: Module::getNextOrderValueForModule($request->course)),
                'status'    => $request->get('status', 0),
                'hidden'    => $request->get('hidden', 0),
                'type'      => $request->get('type', 'Module'),
                'locked_by_test' => $request->get('locked_by_test', null),
            ]);

        $course->save();

        return redirect()->route('modules.index', ['course' => $request->course])
                         ->with('alert-success', 'Module succesfully modified');
    }

    public function destroy(Course $course, Module $module)
    {
        $module->lessons()->delete();
        $module->delete();

        return redirect()->route('modules.index', $course)
            ->with('alert-success', 'Module succesfully deleted');
    }
}
