@extends('admin.layout')

@section('pagetitle')
    Course Containers
@endsection

@section('breadcrumb')
    <li>
        <i class="fa fa-bookmark-o"></i>
        <a href="{{ route('course-containers.index') }}">Course Containers</a>
    </li>
    <li>
        <i class="fa fa-angle-right"></i>
        @if ($courseContainer->title)
            <a href="{{ route('course-containers.edit', ['course_container'=> $courseContainer->id]) }}">Edit</a>
        @else
            <a href="{{ route('course-containers.create') }}">Add New</a>
        @endif
    </li>
@endsection

@section('content')
    <form action="{{ $action }}" method="post" class="form-horizontal form-bordered">
        {{csrf_field() }}
        {{ $method }}
        <div class="row">
            <div class="col-md-12">
                <div class="portlet box grey">
                    <div class="portlet-title">
                        <div class="caption font-green">
                            <i class="icon-user font-green"></i>
                            <span class="caption-subject bold uppercase"> Course Container Details</span>
                        </div>
                    </div>
                    <div class="portlet-body form">
                        <div class="form-body">
                            <div class="form-group">
                                <label class="control-label col-md-3" for="title">Title <span class="required">*</span></label>
                                <div class='col-md-9'>
                                    <input type="text" class="form-control" id="form_control_1" value="{{ old('title', $courseContainer->title) }}" name="title">
                                </div>
                                <br><br>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-offset-4 col-md-3 text-center">
                <div class="form-actions noborder">
                    <input type="submit" class="btn blue" value="Save">
                </div>
            </div>
        </div>
    </form>
@endsection
