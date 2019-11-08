<?php
namespace App\Http\Controllers\Admin\Lessons;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Lesson\StoreLessonRequest;
use App\Http\Requests\Admin\Lesson\StoreDripRequest;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Module;
use Illuminate\Http\Request;

class LessonController extends Controller
{

    public function __construct()
    {
        $this->middleware('admin.role.auth:courses,read')->only('index');
        $this->middleware('admin.role.auth:courses,write')->only('create', 'store', 'edit', 'update', 'destroy');
    }

    public function index(Course $course, Module $module)
    {
        $lessons = $module->getOrderedLessons()->simplePaginate(20);

        return view('admin.lessons.index', compact('course', 'module', 'lessons'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param Course $course
     * @param Module $module
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Course $course, Module $module)
    {
        $lesson = new Lesson();
        $action = route('lessons.store', ['course' => $course, 'module' => $module]);
        $method = '';

        return view('admin.lessons.create-edit', compact('course', 'action', 'method', 'module', 'lesson'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Course              $course
     * @param Module              $module
     * @param StoreLessonRequest  $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Course $course, Module $module, StoreLessonRequest $request)
    {
        $module->lessons()->create(
            $request->only('title', 'description', 'type', 'link') +
            [
                'order' => Lesson::getNextOrderValueForModule($module->id),
                'status' => $request->get('status', 0)
            ]
        );

        return redirect()
                ->route('lessons.index', ['course' => $course->id, 'module' => $module->id])
                ->with('alert-success', 'Lesson succesfully added');
    }

    /**
     * @param Course $course
     * @param Module $module
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Course $course, Module $module, Lesson $lesson)
    {
        $action = route('lessons.update', ['course' => $course, 'module' => $module, 'lesson' => $lesson]);
        $method = method_field('PUT');

        return view('admin.lessons.create-edit', compact('module', 'course', 'lesson', 'action', 'method'));
    }

    /**
     * @param StoreLessonRequest $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(StoreLessonRequest $request)
    {
        $lesson = Lesson::find($request->lesson);
        $lesson->fill($request->only('title', 'description', 'type', 'link') + ['status' => $request->get('status', 0)]);
        $lesson->save();

        return redirect()->route(
                'lessons.index', ['course' => $lesson->module->course->id, 'module' => $lesson->module->id]
            )->with('alert-success', 'Lessons succesfully modified');
    }

    /**
     * @param Course $course
     * @param Module $module
     * @param Lesson $lesson
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function show(Course $course, Module $module, Lesson $lesson)
    {
        //admins are accesing show page for some reason. fixing it by redirecting to edit
        return redirect()->route('lessons.edit', ['course' => $course, 'module' => $module, 'lesson' => $lesson]);
    }


    public function updateDrip(StoreDripRequest $request)
    {
        $lesson = new Lesson();
        $drips = $request->input('drip_delay');
        foreach ($drips as $id => $value) {
            $lesson = Lesson::find($id);
            $lesson->drip_delay = $value;
            $lesson->save();
        }

        return redirect()->route(
                'modules.index', ['course' => $lesson->module->course->id]
            )->with('alert-success', 'Drip delays succesfully modified');
    }
    
    /**
     * Update DB to soft delete lesson
     * @return \Illuminate\Http\RedirectResponse
     */
    public function remove(Course $course, Module $module, Lesson $lesson)
    {
        $lesson->status = 0;
        $lesson->removed_at = date('Y-m-d G:i:s');
        $lesson->removed_by = user()->id;
        $lesson->save();
        
        return redirect()->route(
                'lessons.index', ['course' => $lesson->module->course->id, 'module' => $lesson->module->id]
            )->with('alert-success', 'Lessons succesfully removed');
    }

    /**
     * Preview a lesson.
     * 
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function preview(Request $request)
    {
        $course = Course::find($request->course_id);
        $courseProgress = $course->getProgress();
        $currentModule = Module::find($request->module_id);
        $relatedLessons = (object) ['previous' => 0, 'next' => 0];

        $currentLesson = new Lesson();
        $currentLesson->module_id = $currentModule->id;
        $currentLesson->type = $request->type;
        $currentLesson->title = $request->title;
        $currentLesson->description = $request->description;
        $currentLesson->link = $request->link;
        $currentLesson->slug = str_slug($request->title, '-');

        $preview = true;

        return response()
            ->view(
                'pages.classroom.lesson-preview', 
                compact('course', 'courseProgress', 'currentModule', 'currentLesson', 'relatedLessons', 'preview')
            )->header('X-XSS-Protection', 0);
    }
}
