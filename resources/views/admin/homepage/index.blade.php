@extends('admin.layout')

@section('pagetitle')
    Homepage Options
@endsection

@section('breadcrumb')
    <li>
        <i class="icon-book-open"></i>
        <a href="{{ route('courses.index') }}">Homepage Featered Courses</a>
        <i class="fa fa-angle-right"></i>
    </li>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-8 col-sm-12"></div>
        <div class="col-md-4 col-sm-12">
            <div class="table-group-actions pull-right">

            </div>
        </div>
    </div>
    <br><br>

    <div class="row">
        <div class="col-md-12">
            <div class="portlet">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-star"></i>
                        Featured Courses <small>(ordered)</small>
                    </div>
                </div>
                <div class="portlet-body">
                    <form action="{{ route('homepage.store-featured')  }}" method="post" class="form-horizontal form-bordered">
                        {{csrf_field() }}
                        <input type="hidden" value="0" name="freeBootcamp"/>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="portlet-body form">
                                    <div class="form-body">
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-md-2">
                                                    <select name="featured1" class="form-control inline" style="width: auto">
                                                        <option value="none">Select Course</option>
                                                        @foreach($courses as $course)
                                                            <option value="{{ $course->id }}" {{ isset($featured[0]) && $featured[0]->course_id == $course->id ? "SELECTED" : "" }}>{{ $course->title }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-2">
                                                    <select name="featured2" class="form-control inline" style="width: auto">
                                                        <option value="none">Select Course</option>
                                                        @foreach($courses as $course)
                                                            <option value="{{ $course->id }}" {{ isset($featured[1]) && $featured[1]->course_id == $course->id ? "SELECTED" : "" }}>{{ $course->title }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-2 text-center">
                                                    <select name="featured3" class="form-control inline" style="width: auto">
                                                        <option value="none">Select Course</option>
                                                        @foreach($courses as $course)
                                                            <option value="{{ $course->id }}" {{ isset($featured[2]) && $featured[2]->course_id == $course->id ? "SELECTED" : "" }}>{{ $course->title }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-2">
                                                    <select name="featured4" class="form-control inline" style="width: auto">
                                                        <option value="none">Select Course</option>
                                                        @foreach($courses as $course)
                                                            <option value="{{ $course->id }}" {{ isset($featured[3]) && $featured[3]->course_id == $course->id ? "SELECTED" : "" }}>{{ $course->title }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-4">
                                                     <input type="submit" class="btn blue" value="Save">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="portlet">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-star"></i>
                        Free Bootcamps
                    </div>
                </div>
                <div class="portlet-body">
                    <form action="{{ route('homepage.store-featured') }}" method="post" class="form-horizontal form-bordered">
                        {{csrf_field() }}
                        <input type="hidden" value="1" name="freeBootcamp"/>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="portlet-body form">
                                    <div class="form-body">
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <select name="featured1" class="form-control inline" style="width: auto">
                                                        <option value="none">Select Course</option>
                                                        @foreach($courses as $course)
                                                            <option value="{{ $course->id }}" {{ isset($freeBootcamp[0]) && $freeBootcamp[0]->course_id == $course->id ? "SELECTED" : "" }}>{{ $course->title }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-3">
                                                    <select name="featured2" class="form-control inline" style="width: auto">
                                                        <option value="none">Select Course</option>
                                                        @foreach($courses as $course)
                                                            <option value="{{ $course->id }}" {{ isset($freeBootcamp[1]) && $freeBootcamp[1]->course_id == $course->id ? "SELECTED" : "" }}>{{ $course->title }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-3">
                                                    <select name="featured3" class="form-control inline" style="width: auto">
                                                        <option value="none">Select Course</option>
                                                        @foreach($courses as $course)
                                                            <option value="{{ $course->id }}" {{ isset($freeBootcamp[2]) && $freeBootcamp[2]->course_id == $course->id ? "SELECTED" : "" }}>{{ $course->title }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-2">
                                                    <input type="submit" class="btn blue" value="Save">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('pagescript')
    <script>
        $(function() {
            Layout.setSidebarMenuActiveLink('set','#sidebarOtherCourse');
        });
    </script>
@endsection
