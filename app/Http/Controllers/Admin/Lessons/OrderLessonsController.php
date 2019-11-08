<?php

namespace App\Http\Controllers\Admin\Lessons;

use App\Http\Requests\Admin\Module\StoreModuleRequest;
use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Module;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderLessonsController extends Controller
{
    public function index(Course $course, Module $module)
    {
        $lessons = $module->getOrderedLessons()->get();

        return view('admin.lessons.order', compact('module', 'course', 'lessons'));
    }

    /**
     *
     * @param Course $course
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Course $course, Module $module, Request $request)
    {
        $lessons = explode(" ", trim($request->ordered_lessons));

        foreach ($lessons as $key => $lesson) {
            DB::table('lessons')->where('id','=', $lesson)->update(['order' => $key]);
        }

        return redirect()
            ->route('lessons.index', ['course' => $course->id, 'module' => $module->id])
            ->with('alert-success', 'Lessons order succesfully updated');
    }
}
