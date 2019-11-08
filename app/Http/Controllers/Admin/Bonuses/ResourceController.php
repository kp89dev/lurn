<?php

namespace App\Http\Controllers\Admin\Bonuses;

use App\Models\Bonus;
use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;

class ResourceController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin.role.auth:courses,read')->only('index');
        $this->middleware('admin.role.auth:courses,write')->only('create', 'store', 'edit', 'update');
    }

    public function index()
    {
        return view('admin.bonuses.index')->withBonuses(Bonus::paginate());
    }

    public function create()
    {
        $bonus = new Bonus;
        $action = route('bonuses.store');
        $courses = Course::all();

        return view('admin.bonuses.create-edit', compact('action', 'bonus', 'courses'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'course_id'       => 'required|exists:courses,id',
            'points_required' => 'required|integer|min:1',
        ], ['course_id.*' => 'Please select a course.']);

        Bonus::create($request->only('course_id', 'points_required'));

        return redirect()
            ->route('bonuses.index')
            ->withSuccess(['alert-success' => 'Bonus created successfully.']);
    }

    public function edit(Bonus $bonus)
    {
        $action = route('bonuses.update', $bonus);
        $courses = Course::all();

        return view('admin.bonuses.create-edit', compact('action', 'bonus', 'courses'));
    }

    public function update(Bonus $bonus, Request $request)
    {
        $this->validate($request, [
            'course_id'       => 'required|exists:courses,id',
            'points_required' => 'required|integer|min:1',
        ], ['course_id.*' => 'Please select a course.']);

        $bonus->update($request->only('course_id', 'points_required'));

        return redirect()
            ->route('bonuses.index')
            ->withSuccess(['alert-success' => 'Bonus updated successfully.']);
    }

    public function destroy(Bonus $bonus)
    {
        $bonus->delete();

        return redirect()
            ->route('bonuses.index')
            ->withSuccess(['alert-success' => 'Bonus deleted successfully.']);
    }
}
