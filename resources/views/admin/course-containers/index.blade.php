@extends('admin.layout')

@section('pagetitle')
    Course Containers
@endsection

@section('breadcrumb')
    <li>
        <i class="fa fa-user"></i>
        <a href="{{ route('course-containers.index') }}">Course Containers</a>
    </li>
@endsection


@section('content')
    <div class="row">
        <div class="col-md-8 col-sm-12"></div>
        <div class="col-md-4 col-sm-12">
            <div class="table-group-actions pull-right">
                <a href='{{ route('course-containers.create') }}' class="btn btn-sm green table-group-action-submit"><i class="fa fa-plus"></i> Add New Course Container</a>
            </div>
        </div>
    </div>
    <table class="table table-striped table-bordered table-hover dataTable no-footer">
        <thead>
            <tr role="row" class="heading">
                <th rowspan="1" colspan="1">Title</th>
                <th rowspan="1" colspan="1">Courses</th>
                <th rowspan="1" colspan="1">Updated At</th>
                <th rowspan="1" colspan="1">Actions</th>
            </tr>
        </thead>
        @foreach($courseContainers as $courseContainer)
            <tr role="row" class="filter">
                <td rowspan="1" colspan="1">{{ $courseContainer->title }}</td>
                <td rowspan="1" colspan="1">
                    {{ $courseContainer->courses()->count() }} course/s
                </td>
                <td rowspan="1" colspan="1">{{ $courseContainer->updated_at }}</td>
                <td>
                    <a href="{{ route('course-containers.edit', ['course-container' => $courseContainer->id]) }}"  class="btn btn-sm green table-group-action-submit"><i class="fa fa-edit"></i></a>
                </td>
            </tr>
        @endforeach
    </table>
    <div class="row">
        {{ $courseContainers->links() }}
    </div>
@endsection
