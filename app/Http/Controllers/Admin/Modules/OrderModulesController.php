<?php

namespace App\Http\Controllers\Admin\Modules;

use App\Http\Requests\Admin\Module\StoreModuleRequest;
use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Module;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderModulesController extends Controller
{
    public function index(Course $course)
    {
        $modules = $course->modules()->ordered()->get();

        return view('admin.modules.order', compact('modules', 'course'));
    }

    /**
     *
     * @param Course $course
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Course $course, Request $request)
    {
        $modules = explode(" ", trim($request->ordered_modules));

        foreach ($modules as $key => $module) {
            DB::table('modules')->where('id','=', $module)->update(['order' => $key]);
        }

        return redirect()
            ->route('modules.index', ['course' => $course->id])
            ->with('alert-success', 'Modules order succesfully updated');
    }
}
