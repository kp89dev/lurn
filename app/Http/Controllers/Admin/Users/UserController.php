<?php
namespace App\Http\Controllers\Admin\Users;

use App\Http\Requests\Admin\User\StoreUserRequest;
use App\Http\Requests\Admin\User\UpdateUserRequest;
use App\Models\Course;
use App\Models\LessonSubscriptions;
use App\Models\User;
use App\Models\UserCourse;
use App\Models\UserRole;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use App\Events\User\UserCreatedThroughAdmin;
use App\Events\User\UserEnrolled;
use Illuminate\Support\Facades\Event;

class UserController extends Controller
{

    public function __construct()
    {
        $this->middleware('admin.role.auth:users,read')
            ->only('index', 'impersonate');
        $this->middleware('admin.role.auth:users,write')
            ->only('create', 'store', 'edit', 'update', 'destroy');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $search = route('users.admin.search');
        $users = (\Session::get('users') ? User::whereIn('id', \Session::get('users'))->orderBy('id', 'DESC')->simplePaginate(20) : User::orderBy('id', 'DESC')->simplePaginate(20));

        return view('admin.users.index', compact('users', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user = new User();
        $action = route('users.store');
        $method = '';
        $userCourses = new Collection();

        return view('admin.users.create-edit', compact('user', 'action', 'method', 'userCourses'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreUserRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUserRequest $request)
    {
        $user = User::create(
                $request->only('email', 'name', 'status') + [
                'password' => bcrypt($request->password)
        ]);

        if ($request->adminRole) {
            $role = UserRole::find($request->adminRole);
            $user->adminRole()->save($role);
        } else {
            $user->adminRole()->sync([]);
        }

        if (is_array($request->courses)){
            $user->courses()->sync(array_fill_keys($request->courses, ['status' => 0, 'added_by' => user()->id]));
            
            foreach ($request->courses as $givenCourse) {
                $course = Course::find($givenCourse);
                event(new UserEnrolled($user, $course));
            }
        }
        
        event(new UserCreatedThroughAdmin($user));
        
        return redirect()->route('users.index')->with('alert-success', 'User succesfully added');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        $action = route('users.update', ['id' => $user->id]);
        $method = method_field('PUT');
        $userCourses = $user->enrolledCourses();

        return view('admin.users.create-edit', compact('user', 'action', 'method', 'userCourses'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $user->fill($request->only('name', 'email', 'status'));

        if ($request->getPassword()) {
            $user->password = bcrypt($request->getPassword());
        }
        $user->save();

        if ($request->adminRole) {
            $role = UserRole::find($request->adminRole);
            $user->adminRole()->sync([$role->id]);
        } else {
            $user->adminRole()->sync([]);
        }

        $removeCourses = ($request->courses ? $user->courses->pluck('id')->diff($request->courses) : $user->courses->pluck('id'));
        foreach ($removeCourses as $removedCourse) {
            UserCourse::where('course_id', $removedCourse)
                ->where('user_id', $user->id)
                ->update([
                    'status' => 1,
                    'cancelled_at' => now(),
                    'cancelled_reason' => 'Removed through admin panel',
                    'cancelled_by' => user()->id
                ]);
        }

        if ($request->courses) {
            foreach ($request->courses as $givenCourse) {
                $existingCourse = UserCourse::where('user_id', $user->id)
                    ->where('course_id', $givenCourse)
                    ->where('status', '!=', 1)
                    ->orderBy('updated_at', 'asc')
                    ->first();

                if ($existingCourse instanceof UserCourse) {
                    continue;
                }

                $course = Course::find($givenCourse);
                $user->courses()->attach($course, [
                    'added_by' => user()->id,
                    'status' => 0,
                    'course_infusionsoft_id' => $course->infusionsoft->id
                ]);
            }
        }

        return redirect()->route('users.index')->with('alert-success', 'User succesfully updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int    $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (Auth::user()->id == $id) {
            return redirect()->back()->with(['alert-danger' => 'You cannot delete you own account']);
        }

        User::find($id)->delete();

        request()->session()->flash('alert-success', 'User was successfully deleted!');
        return redirect()->back();
    }

    public function impersonate(Request $request)
    {
        $request->session()->put('admin_impersonator', encrypt(Auth::user()->id));

        $impersonated = User::findOrFail($request->user);
        Auth::login($impersonated);

        return redirect()->route('dashboard');
    }

    public function toggleOnboarding(Request $request, User $user)
    {
        $course = Course::findOrFail($request->get('course'));
        $course->setUserOnboardingStatus($user->id, $request->input('action'));

        request()->session()->flash('alert-success', 'User was successfully updated!');

        return redirect()->back();
    }
}
