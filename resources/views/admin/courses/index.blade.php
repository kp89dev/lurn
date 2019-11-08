@extends('admin.layout')

@section('pagetitle')
Courses <small>courses list</small>
@endsection

@section('breadcrumb')
<li>
    <i class="fa icon-book-open"></i>
    <a href="{{ route('courses.index') }}">Courses</a>
</li>
@endsection


@section('content')
@if ($indexType == 'full')
    <form action="{{ $search }}" method="post" class="form-horizontal form-bordered">
        {{csrf_field() }}
        <div class="row">
            <div class="col-md-12">
                <div class="portlet box grey">
                    <div class="portlet-body form">
                        <div class="form-body">
                            <div class="form-group">
                                <label class="control-label col-md-2" for="term">Search </label>
                                <div class='col-md-5'>
                                    <input type="text" class="form-control" id="term" value="" name="term">
                                </div>
                                <div class='col-md-5'>
                                    <input type="submit" class="btn blue" value="Search">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <div class="row">
        <div class="col-md-8 col-sm-12"></div>
        <div class="col-md-4 col-sm-12">
            <div class="table-group-actions pull-right">
                <span>
                </span>
                <a href='{{ route('courses.create') }}' class="btn btn-sm green table-group-action-submit"><i class="fa fa-plus"></i> Add New Course</a>
            </div>
        </div>
    </div>
@elseif ($indexType == 'bonus')
    <div class="row">
        <div class="col-md-8 col-sm-12"></div>
        <div class="col-md-4 col-sm-12">
            <div class="table-group-actions pull-right">
                <form class="form-inline" action="{{ route('course-bonuses.store', ['course' => $excludeFromBonus[0]]) }}" method="post">
                    {{ csrf_field() }}
                    <input type="hidden" name="course_id" value="{{ $excludeFromBonus[0] }}">
                    <select class="form-control form-control-sm" name="bonus_course_id" required>
                        <option value="">Select a Bonus Course</option>
                        @foreach (App\Models\Course::whereNotIn('id', $excludeFromBonus)->get() as $bonus)
                        <option value="{{ $bonus->id }}">{{ $bonus->title }}</option>
                        @endforeach
                    </select>
                    <button class="btn green table-group-action-submit" type="submit"><i class="fa fa-plus"></i> Add New Bonus</button>
                </form>
            </div>
        </div>
    </div>
@endif
<table class="table table-striped table-bordered table-hover dataTable no-footer">
    <thead>
        <tr role="row" class="heading">
            <th rowspan="1" colspan="1">Course ID</th>
            <th rowspan="1" colspan="1">Title</th>
            <th rowspan="1" colspan="1">Thumbnail</th>
            <th rowspan="1" colspan="1">Modules</th>
            <th rowspan="1" colspan="1">Tests</th>
            <th rowspan="1" colspan="1">Badges</th>
            <th rowspan="1" colspan="1">Certificates</th>
            @if ($indexType !== 'bonus')           
            <th rowspan="1" colspan="1">Bonuses</th>
            @else
            <th rowspan="1" colspan="1">Bonus Of</th>
            @endif
            <th rowspan="1" colspan="1">Container</th>
            <th rowspan="1" colspan="1">Enabled</th>
            <th rowspan="1" colspan="1">Purchasable</th>
            <th rowspan="1" colspan="1">Free</th>
            <th rowspan="1" colspan="1">Updated At</th>
            <th rowspan="1" colspan="1">Actions</th>
        </tr>
    </thead>
    @foreach($courses as $course)
    <tr role="row" class="filter">
        <td rowspan="1" colspan="1">{{ $course->id }}</td>
        <td rowspan="1" colspan="1">{{ $course->title }}</td>
        <td rowspan="1" colspan="1"><img src="{{ $course->getPrintableImageUrl() }}" width="100"/></td>        
        <td rowspan="1" colspan="1">
            <a href="{{ route('modules.index',  ['course' => $course->id]) }}">
                {{ $course->modules()->count() }} {{ str_plural('module', $course->modules()->count()) }}
            </a>
        </td>
        <td rowspan="1" colspan="1">
            <a href="{{ route('tests.index',  ['course' => $course->id]) }}">
                {{ $course->tests()->count() }} {{ str_plural('test', $course->tests()->count()) }}
            </a>
        </td>
        <td rowspan="1" colspan="1">
            <a href="{{ route('badges.index',  ['course' => $course->id]) }}">
                {{ $course->badges()->count() }} {{ str_plural('badge', $course->badges()->count()) }}
            </a>
        </td>
        <td rowspan="1" colspan="1">
            <a href="{{ route('certs.index', ['course' => $course->id]) }}">
                {{ $course->certificates()->count()}} {{ str_plural('cert', $course->certificates()->count()) }}
            </a>
        </td>
        @if ($indexType !== 'bonus')
        <td rowspan="1" colspan="1">
            <a href="{{ route('course-bonuses.index', ['course' => $course->id]) }}">
                {{ $course->bonuses()->count()}} {{ str_plural('bonus', $course->bonuses()->count()) }}
            </a>
        </td>
        @else
        <td rowspan="1" colspan="1">
            {{App\Models\Course::where('id', request()->course)->pluck('title')->first()}}
        </td>
        @endif
        <td rowspan="1" colspan="1">{{ $course->container->title }}</td>
        <td rowspan="1" colspan="1">
            @if ($course->status == 1)
            <span class="label label-success">Yes</span>
            @else
            <span class="label label-danger">No</span>
            @endif
        </td>
        <td rowspan="1" colspan="1">
            @if ($course->purchasable == 1)
            <span class="label label-success">Yes</span>
            @else
            <span class="label label-danger">No</span>
            @endif
        </td>
        <td rowspan="1" colspan="1">
            @if ($course->free)
                <span class="label label-success">Yes</span>
            @else
                <span class="label label-danger">No</span>
            @endif
        </td>
        <td rowspan="1" colspan="1">{{ $course->updated_at }}</td>
        <td>
            <a href="{{ route('courses.edit', ['user' => $course->id]) }}"  class="btn btn-sm green table-group-action-submit"><i class="fa fa-edit"></i></a>
            @if ($indexType == 'bonus')
            <form class="form-inline" style="display: inline" action="{{ route('course-bonuses.destroy', ['course' => $excludeFromBonus[0], 'id' => $course->id]) }}" method="post">
                {{ csrf_field() }}
                {{ method_field('DELETE') }}
                <input type="hidden" name="course_id" value="{{ $excludeFromBonus[0] }}">
                <input type="hidden" name="bonus_course_id" value="{{ $course->id }}">
                <button class="btn btn-sm red table-group-action-submit"><i class="fa fa-trash"></i></button>
            </form>
            @endif
        </td>
    </tr>
    @endforeach
</table>
<div class="row">
    {{ $courses->links() }}
</div>
@endsection
