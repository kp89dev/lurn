<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Course;
use Illuminate\Http\Request;

class ExternalOnboardingController extends Controller
{
    public function index()
    {
        $categories = Category::whereHas('courses', function ($courses) {
        	$courses->free();
        })->get();

        return view('onboarding.index', compact('categories'));
    }

    public function saveInterests(Request $request)
    {
        if (! $request->filled('categories')) {
            return redirect()->back()->withErrors('Please choose a category to continue.');
        }

        $categories = Category::whereIn('id', $request->categories)->pluck('id');

        user()->categories()->sync($categories);

        return redirect(route('onboarding.courses'));
    }

    public function courseChoice()
    {
        $category = user()->categories()->orderBy('category_user.created_at')->first();
        $possibleCourses = $category->courses()->free()->whereDoesntHave('bonusOf')->take(3)->get();

        return view('onboarding.course-choice', compact('possibleCourses', 'category'));
    }

    public function enrollInChoices(Request $request)
    {
        $courses = is_array($courses = request('courses')) ? $courses : [];

        foreach ($courses as &$course) {
            if (($course = Course::find($course)) && $course->free) {
                user()->enroll($course);
            }
        }

        if ($oneCourse = count($courses) === 1) {
            session(['after-onboarding' => $courses[0]->url]);
        }

        return redirect(route('onboarding.demo', $oneCourse ? $courses[0] : null));
    }

    public function demo(Course $course = null)
    {
        return view('onboarding.demo', compact('course'));
    }

    public function remoteRegister()
    {
        session(['redirectTo' => route('onboarding.index')]);

        return view('onboarding.remote-registration');
    }
}
