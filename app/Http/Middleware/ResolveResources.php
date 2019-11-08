<?php

namespace App\Http\Middleware;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\Module;
use App\Models\Test;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class ResolveResources
{
    private $request, $course, $module, $lesson, $test;

    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $this->request = $request;

        $this->resolveCourse();
        $this->resolveModule();
        $this->resolveLesson();
        $this->resolveTest();

        return $next($request);
    }

    private function resolveCourse()
    {
        if ($slug = $this->request->course) {
            $this->course = Course::findBySlug($slug, ! user_is_admin()) or abort(404);
            $this->request->merge(['course' => $this->course]);
            View::share('course', $this->course);
        }
    }

    private function resolveModule()
    {
        if ($slug = $this->request->module) {
            $this->module = $this->course->modules()->findBySlug($slug, ! user_is_admin());
            $this->module instanceof Module or abort(404);

            $this->request->merge(['module' => $this->module]);
            View::share('currentModule', $this->module);
        }
    }

    private function resolveLesson()
    {
        if ($slug = $this->request->lesson) {
            $this->lesson = $this->module->lessons()->findBySlug($slug, ! user_is_admin());
            $this->lesson instanceof Lesson or abort(404);

            $this->request->merge(['lesson' => $this->lesson]);
            View::share('currentLesson', $this->lesson);
        }
    }

    private function resolveTest()
    {
        if ($id = $this->request->test) {
            $this->test = $this->course->tests()->find($id);
            $this->test instanceof Test or abort(404);

            $this->request->merge(['test' => $this->test]);
            View::share('currentTest', $this->test);
        }
    }
}
