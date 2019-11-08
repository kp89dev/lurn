@extends('admin.layout')

@section('pagetitle')
    Lessons <small>lessons for module {{ $module->title }}</small>
@endsection

@section('breadcrumb')
    <li>
        <i class="icon-book-open"></i>
        <a href="{{ route('courses.index') }}">Course <b>{{ $course->title }}</b></a>
        <i class="fa fa-angle-right"></i>
    </li>
    <li>
        <i class="fa fa-book"></i>
        <a href="{{ route('modules.index', ['course' => $course->id ]) }}">Module <b>{{ $module->title }}</b></a>
        <i class="fa fa-angle-right"></i>
    </li>
    <li>
        <i class="fa fa-paper-plane"></i>
        <a href="/">Lessons</a>
    </li>
@endsection


@section('content')
    <div class="row">
        <div class="col-md-8 col-sm-12"></div>
        <div class="col-md-4 col-sm-12">
            <div class="table-group-actions pull-right">
                <a href='{{ route('lessons.create', ['course' => $course->id, 'module' => $module->id]) }}' class="btn btn-sm green table-group-action-submit"><i class="fa fa-plus"></i> Add New Lesson</a>
                <a href='{{ route('lessons.order', ['course' => $course->id, 'module' => $module->id]) }}' class="btn btn-sm green table-group-action-submit"><i class="fa fa-sort-amount-asc"></i> Order Lessons</a>
            </div>
        </div>
    </div>
    <table class="table table-striped table-bordered table-hover dataTable no-footer">
        <thead>
            <tr role="row" class="heading">
                <th rowspan="1" colspan="1">Title</th>
                <th rowspan="1" colspan="1">Link</th>
                <th rowspan="1" colspan="1">Enabled</th>
                <th rowspan="1" colspan="1">Updated At</th>
                <th rowspan="1" colspan="1">Actions</th>
            </tr>
        </thead>
        @foreach($lessons as $lesson)
            <tr role="row" class="filter">
                <td rowspan="1" colspan="1">{{ $lesson->title }}</td>
                <td rowspan="1" colspan="1">{{ $lesson->link }}</td>
                <td rowspan="1" colspan="1">
                    @if ($lesson->status == 1)
                        <span class="label label-success">Yes</span>
                    @else
                        <span class="label label-danger">No</span>
                    @endif
                </td>
                <td rowspan="1" colspan="1">{{ $lesson->updated_at }}</td>
                <td>
                    <a href="{{ route('lessons.edit', ['course' => $course->id, 'module' => $module->id, 'lesson' => $lesson->id]) }}"  class="btn btn-sm green table-group-action-submit"><i class="fa fa-edit"></i></a>
                    <a href="{{ route('lessons.remove', ['course' => $course->id, 'module' => $module->id, 'lesson' => $lesson->id]) }}"  class="btn btn-sm red table-group-action-submit"><i class="fa fa-trash"></i></a>
                </td>
            </tr>
        @endforeach
    </table>
    <div class="row">
        {{ $lessons->links() }}
    </div>
@endsection
