<?php
namespace App\Http\Controllers\Admin\Badges;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Badge\StoreBadgeRequest;
use App\Models\Badge;
use App\Models\Course;
use Illuminate\Support\Facades\Storage;
use App\Models\Credly;

class BadgesController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin.role.auth:badge-requests,read')->only('index');
        $this->middleware('admin.role.auth:badge-requests,write')->only('create', 'store', 'edit', 'update', 'destroy');
    }

    public function index(Course $course)
    {
        $badges = $course->badges()->simplePaginate(20);

        return view('admin.badges.index', compact('badges','course'));
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
        $badge  = new Badge();
        $action = route('badges.store', ['course' => $course]);
        $method = '';

        return view('admin.badges.create-edit', compact('course', 'action', 'method', 'badge'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Course            $course
     * @param StoreBadgeRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Course $course, StoreBadgeRequest $request)
    {
        $model = $course->badges()->create($request->only('title', 'content', 'status'));

        if ($request->file('image')) {
            $path = $request->file('image')->store('badges/' . $course->id, 'static');
            $model->image = $path;
            
            $credly = new Credly();
            $credly->saveBadge($model);
            
            $model->save();
        }

        return redirect()
            ->route('badges.index', ['course' => $course->id])
            ->with('alert-success', 'Badge succesfully added');
    }

    /**
     * @param Course $course
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Course $course, Badge $badge)
    {
        $action = route('badges.update', ['course' => $course, 'badge' => $badge]);
        $method = method_field('PUT');

        return view('admin.badges.create-edit', compact('course', 'badge', 'action', 'method'));
    }

    /**
     * @param StoreBadgeRequest $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(StoreBadgeRequest $request, Course $course, Badge $badge)
    {
        $badge->fill($request->only('title', 'content', 'status'));

        if ($request->file('image')) {
            $path = $request->file('image')->store('badges/' . $course->id, 'static');

            if ($path) {
                Storage::disk('static')->delete($badge->image);
                $badge->image = $path;
            }
        }

        $badge->save();

        return redirect()->route( 'badges.index',  [ 'course' => $course ])
                         ->with('alert-success', 'Badge succesfully modified');
    }

    /**
     * @param Course $course
     * @param Badge  $badge
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy(Course $course, Badge $badge)
    {
        Storage::disk('static')->delete($badge->image);
        $badge->delete();

        return redirect()->route( 'badges.index',  [ 'course' => $course ])
                         ->with('alert-success', 'Badge succesfully deleted');
    }
}
