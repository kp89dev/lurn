@extends('admin.layout')

@section('pagetitle')
    Users
    <small>users list</small>
@endsection

@section('breadcrumb')
    <li>
        <i class="fa fa-user"></i>
        <a href="{{ route('users.index') }}">Users</a>
    </li>
    <li>
        <i class="fa fa-angle-right"></i>
        @if ($user->id)
            <a href="{{ route('users.edit', ['id' => $user->id]) }}">Edit</a>
        @else
            <a href="{{ route('users.create') }}">Add</a>
        @endif
    </li>
@endsection

@section('content')
    <form action="{{ $action }}" method="post" id="main-form">
        {{csrf_field() }}
        {{ $method }}
        <div class="row">
            <div class="col-md-6">
                <div class="portlet light bordered">
                    <div class="portlet-title">
                        <div class="caption font-green">
                            <i class="icon-user font-green"></i>
                            <span class="caption-subject bold uppercase"> User Details</span>
                        </div>
                    </div>
                    <div class="portlet-body form">

                        <div class="form-body">
                            <div class="form-group form-md-line-input form-md-floating-label">
                                <input type="text" class="form-control {{ old('name', $user->name) ? 'edited' : '' }}" id="form_control_1" value="{{ old('name', $user->name) }}" name="name" data-lpignore="true">
                                <label for="form_control_1">Name</label>
                                <span class="help-block">Full user name</span>
                            </div>
                            <div class="form-group form-md-line-input form-md-floating-label">
                                <input type="email" class="form-control {{ old('email', $user->email) ? 'edited' : '' }}" id="form_control_1" value="{{ old('email', $user->email) }}" name="email" data-lpignore="true">
                                <label for="form_control_1">Email</label>
                                <span class="help-block">User email</span>
                            </div>
                            <div class="form-group form-md-line-input form-md-floating-label">
                                <input type="password" class="form-control" id="form_control_1" name="password" data-lpignore="true">
                                <label for="form_control_1">Password</label>
                                <span class="help-block">User Password</span>
                            </div>
                            <div class="form-group form-md-line-input form-md-floating-label" >
                                <input type="password" class="form-control" id="form_control_1" name="repeat_password" data-lpignore="true">
                                <label for="form_control_1">Repeat Password</label>
                                <span class="help-block">User Password</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="portlet light bordered">
                    <div class="portlet-title">
                        <div class="caption font-green">
                            <i class="icon-settings font-green"></i>
                            <span class="caption-subject bold uppercase"> User Settings</span>
                        </div>
                    </div>
                    <div class="portlet-body form">
                        <div class="form-body">
                            <div class="form-group form-md-radios">
                                <label>User Role</label>
                                <div class="md-radio-inline">
                                    <div class="md-radio">
                                        <input type="radio" id="radio14" name="status" value='99' {{ $user->status == 99 ? "CHECKED" : "" }} class="md-radiobtn">
                                        <label for="radio14">
                                            <span class="inc"></span>
                                            <span class="check"></span>
                                            <span class="box"></span>
                                            Admin </label>
                                    </div><br/>
                                    <div class="md-radio">
                                        <input type="radio" id="radio16" name="status" value='80' {{ $user->status == 80 ? "CHECKED" : "" }} class="md-radiobtn">
                                        <label for="radio16">
                                            <span class="inc"></span>
                                            <span class="check"></span>
                                            <span class="box"></span>
                                            Course Editor </label>
                                    </div><br/>
                                    <div class="md-radio">
                                        <input type="radio" id="radio15" name="status" value='1' {{ $user->status == 1 ? "CHECKED" : "" }} class="md-radiobtn">
                                        <label for="radio15">
                                            <span class="inc"></span>
                                            <span class="check"></span>
                                            <span class="box"></span>Active User </label>
                                    </div><br/>
                                    <div class="md-radio has-error">
                                        <input type="radio" id="radio15" name="status" value='0' {{ $user->status == 0 ? "CHECKED" : "" }} class="md-radiobtn">
                                        <label for="radio15">
                                            <span class="inc"></span>
                                            <span class="check"></span>
                                            <span class="box"></span>Unconfirmed </label>
                                    </div><br/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="portlet-body form">
                        <div class="form-body">
                            <div class="form-group form-md-radios">
                                <label>Admin Role(applicable for admin users only)
                                    <select name="adminRole" class="input-large">
                                        <option value="">None</option>
                                        @foreach(\App\Models\UserRole::all() as $role)
                                            <option value="{{ $role->id }}"
                                            @if($user->adminRole()->first() && $user->adminRole()->first()->id == $role->id)
                                                selected
                                            @endif>{{ $role->title }}</option>
                                        @endforeach
                                    </select>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="portlet light bordered">
                    <div class="portlet-title">
                        <div class="caption font-green">
                            <i class="icon-book-open font-green"></i>
                            <span class="caption-subject bold uppercase">Course Access</span>
                        </div>
                    </div>
                    <div class="portlet-body form row" style="margin: 0;">
                        <div class="form-body">
                            <div class="form-group form-md-checkboxes">
                                @foreach(App\Models\Course::all() as $course)
                                    <div class="md-checkbox md-checkbox-inline col-md-3">
                                        <input type="checkbox" class="md-check" id="{{'course_'.$course->id}}" name="courses[]" value="{{ $course->id }}" @if(isset($userCourses) and $userCourses->where('id', $course->id)->first()) checked=checked @endif >
                                        <label for="{{'course_'. $course->id }}">
                                            <span class="inc"></span>
                                            <span class="check"></span>
                                            <span class="box"></span>
                                            {{ $course->title }}
                                        </label>
                                        @if(isset($userCourses) && $userCourses->where('id', $course->id)->first() && $course->getOnboardingModule())
                                            <form action="{{ route('users.toggle-onboarding', ['user' => $user->id ]) }}" method="post" style="display: inline">
                                                @csrf
                                                <input type="hidden" name="course" value="{{ $course->id }}">
                                                    @if ($course->userIsBoarded($user->id))
                                                        <a onclick='$(this).closest("form").submit()' class="label label-sm label-success">
                                                            <input type="hidden" name="action" value="0">
                                                            Enable Onboarding
                                                        </a>
                                                    @else
                                                        <a onclick='$(this).closest("form").submit()' class="label label-sm label-danger">
                                                            <input type="hidden" name="action" value="1">
                                                            Disable Onboarding
                                                        </a>
                                                    @endif

                                            </form>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

        <div class="row">
            <div class="col-md-offset-4 col-md-3 text-center">
                <div class="form-actions noborder">
                    <input onclick='$("#main-form").submit()'  type="submit" class="btn blue" value="Save">
                </div>
            </div>
        </div>

@endsection
