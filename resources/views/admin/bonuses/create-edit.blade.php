@extends('admin.layout')

@section('pagetitle')
    Bonuses
    <small>courses assigned automatically to users when they reach the defined required points</small>
@endsection

@section('breadcrumb')
    <li>
        <i class="fa star"></i>
        <a href="{{ route('bonuses.index') }}">Bonuses</a>
    </li>
    <li>
        <i class="fa fa-angle-right"></i>
        {{ $bonus->id ? "Edit Bonus #$bonus->id" : 'Add a New Bonus' }}
    </li>
@endsection

@section('content')
    <form class="form-horizontal form-bordered margin-bottom-40" action="{{ $action }}" method="post">
        @csrf
        @method($bonus->id ? 'put' : 'post')
        <div class="row">
            <div class="col-md-12">
                <div class="portlet box grey">
                    <div class="portlet-title">
                        <div class="caption font-green">
                            <i class="icon-user font-green"></i>
                            <span class="caption-subject bold uppercase"> Bonus Details</span>
                        </div>
                    </div>
                    <div class="portlet-body form">
                        <div class="form-body">
                            <div class="form-group">
                                <label class="control-label col-md-3" for="title">
                                    Course
                                    <span class="required">*</span>
                                </label>
                                <div class='col-md-9'>
                                    <select name="course_id" class="form-control inline" style="width: 100%">
                                        <option value="">Select One</option>
                                        @foreach ($courses as $course)
                                            <option value="{{ $course->id }}" {{ $bonus->course_id == $course->id ? 'selected' : '' }}>(ID: {{ $course->id }}) {{ $course->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-md-3">
                                    Points Required
                                    <span class="required">*</span>
                                </label>

                                <div class="col-md-2">
                                    <input type="number" min="100" class="form-control inline"
                                           value="{{ old('points_required', $bonus->points_required ?: 100) }}"
                                           name="points_required" style="width: auto">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-offset-6 col-md-2 text-center">
                <div class="form-actions noborder">
                    <input type="submit" class="btn blue" value="Save">
                </div>
            </div>
        </div>
    </form>
@endsection
