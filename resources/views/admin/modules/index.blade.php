@extends('admin.layout')

@section('pagetitle')
    Modules <small>modules list</small>
@endsection

@section('breadcrumb')
    <li>
        <i class="icon-book-open"></i>
        <a href="{{ route('courses.index') }}">Course <b>{{ $course->title }}</b></a>
        <i class="fa fa-angle-right"></i>
    </li>
    <li>
        <i class="fa fa-book"></i>
        <a href="">Modules</a>
    </li>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-8 col-sm-12"></div>
        <div class="col-md-4 col-sm-12">
            <div class="table-group-actions pull-right">
                <a href='{{ route('modules.create', [$course->id]) }}' class="btn btn-sm green table-group-action-submit"><i class="fa fa-plus"></i> Add New Module</a>
                <a href='{{ route('modules.order', [$course->id]) }}' class="btn btn-sm green table-group-action-submit"><i class="fa fa-sort-amount-asc"></i> Order Modules</a>
            </div>
        </div>
    </div>
    <br><br>
    <?php $moduleNumber = 1 + (($modules->currentPage() - 1) * $modules->perPage()); ?>

    <div class="row">
        <div class="col-md-12">
        @if(count($modules)>0)
             @if($course->drip)
            <form method="{{$method}}" action="{{$action}}" class="form-inline">
            {{csrf_field()}}
            <div class="text-center">
                <input type="submit" class="btn btn-success btn-large" value="Save Drip Delays">
            </div>
            @endif
            @foreach($modules as $module)
                <div class="portlet">
                    <div class="portlet-title">
                        <div class="caption">
                            @if ($module->status)
                                <b>Order #{{ $moduleNumber++ }} : {{ $module->title }}</b>
                            @else
                                <em>Order #{{ $moduleNumber++ }} : {{ $module->title }}</em>
                            @endif

                            @if ($module->type === "Link")
                                &nbsp; - &nbsp; <a href="{{ $module->link }}" target="_blank"><i class="fa fa-external-link" aria-hidden="true"></i> External Link</a>
                            @endif

                            @if ($module->status === 0)
                                &mdash; <em class="text-danger"> Disabled</em>
                            @endif
                        </div>

                        <div class="tools">
                            <a href="{{ route('modules.edit', ['course' => $course->id, 'module' => $module->id]) }}" class="fa fa-pencil"> Edit Module</a>
                            <a href="{{ route('lessons.index', ['course' => $course->id, 'module' => $module->id])}}" class="fa fa-film"> Manage Lessons</a>
                            <a href="javascript:" onclick="deleteModule(this)" class="fa fa-pencil"> Delete Module</a>
                            <form action="{{ route('modules.destroy', [$course->id, $module->id]) }}" method="POST">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <input type="hidden" name="_method" value="DELETE">
                            </form>
                        </div>
                    </div>

                    <div class="portlet-body">
                        <div class="note">
                        
                            @foreach($module->getOrderedLessons()->get() as $moduleLesson)
                                @if($course->drip)
                                        <div class="input-group col-sm-1 my-3">
                                            <div class="input-group-addon">Day #</div>
                                            <input type="number" class="form-control" placeholder="Day" name="drip_delay[{{$moduleLesson->id}}]" value="{{$moduleLesson->drip_delay}}">
                                        </div>
                                @endif
                                <a class="jstree-anchor" href="{{ route('lessons.edit', ['course' => $course->id, 'module' => $module->id, 'lessons' => $moduleLesson->id]) }}">
                                    <i class="jstree-icon jstree-themeicon fa fa-film icon-state-warning jstree-themeicon-custom"></i>
                                    <b>Lesson #{{ $moduleLesson->order }} - {{$moduleLesson->title}} (Id : {{$moduleLesson->id}})</b>
                                </a>
                                    @if ($moduleLesson->type === 'Link')
                                        &mdash; <a href="{{ $moduleLesson->link }}"><i class="fa fa-external-link"></i> External Link</a>
                                    @endif

                                    @if ($moduleLesson->status === 0)
                                        &mdash; <em class="text-danger">Disabled</em>
                                    @endif

                                <br/>
                            @endforeach
                        </div>
                    </div>
                </div>

            @endforeach
            <div class="row">
                {{ $modules->links() }}
            </div>
        </div>
        @if($course->drip)
        <div class="text-center">
            <input type="submit" class="btn btn-success btn-large" value="Save Drip Delays">
        </div>
        </form>
        @endif
        @else
            No modules found.
        @endif
    </div>

    <script>
        window.onload = function () {
            Layout.setSidebarMenuActiveLink('set','#sidebarOtherCourse');
        };

        function deleteModule (el) {
            if (confirm("Are you sure you want to delete this module? This will also remove all of the lessons associated with this module. \n\nThis action can't be undone!")) {
                $(el).next('form').submit();
            }
        }
    </script>
@endsection

@section('script')
@endsection
