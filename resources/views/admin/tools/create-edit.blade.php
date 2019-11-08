@extends('admin.layout')

@section('pagetitle')
    Tools
@endsection

@section('breadcrumb')
    <li>
        <i class="book-open"></i>
        <a href="{{ route('tools.index', compact('course')) }}">Tools Rules</a>
    </li>
    <li>
        <i class="icon-edit"></i>
        @if ($courseTool->title)
            <a href="{{ route('tools.edit', compact('courseTool')) }}">Edit</a>
        @else
            <a href="{{ route('tools.create', compact('courseTool')) }}">Add New</a>
        @endif
    </li>
@endsection

@section('content')
    <form action="{{ $action }}" method="post" class="form-horizontal form-bordered" enctype="multipart/form-data">
        {{csrf_field() }}
        {{ $method }}
        <div class="row">
            <div class="col-md-12">
                <div class="portlet box grey">
                    <div class="portlet-title">
                        <div class="caption font-green">
                            <i class="icon-user font-green"></i>
                            <span class="caption-subject bold uppercase"> Rule Details</span>
                        </div>
                    </div>
                    <div class="portlet-body form">
                        <div class="form-body">
                            <div class="form-group">
                                <label class="control-label col-md-3" for="title">Select Tool <span class="required">*</span></label>
                                <div class='col-md-9'>
                                    <select class="form-control" name="tool_name">
                                        @foreach (config('tools') as $tool)
                                            <option value="{{ $tool['name'] }}" {{ $courseTool->tool_name == $tool['name'] ? "SELECTED" : "" }}>{{ $tool['name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <br><br>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3" for="title">Select Course <span class="required">*</span></label>
                                <div class='col-md-9'>
                                    <select class="form-control" name="course_id">
                                        @foreach (App\Models\Course::whereNotIn('id', App\Models\CourseBonus::all()->pluck('bonus_course_id'))->get() as $course)
                                            <option value="{{ $course->id }}" {{ $course->id == $courseTool->course_id ? "SELECTED" : "" }}>{{ $course->title }}</option>
                                        @endforeach
                                    </select>
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
