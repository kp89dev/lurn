@extends('admin.layout')

@section('pagetitle')
    Tests <small>manage tests</small>
@endsection

@section('breadcrumb')
    <li>
        <i class="icon-book-open"></i>
        <a href="{{ route('courses.index') }}">Course <b>{{ $course->title }}</b></a>
        <i class="fa fa-angle-right"></i>
    </li>
    <li>
        <i class="fa fa-book"></i>
        <a href="/">Tests</a>
    </li>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-8 col-sm-12"></div>
        <div class="col-md-4 col-sm-12">
            <div class="table-group-actions pull-right">
                <a href='{{ route('tests.create', [$course->id]) }}' class="btn btn-sm green table-group-action-submit"><i class="fa fa-plus"></i> Add New Test</a>
            </div>
        </div>
    </div>
    <br><br>
    <?php $testNo = 1; ?>

    <ul class='lessonList' style="list-style-type: none; margin: 0; padding: 0;">
        @foreach($tests as $test)
            <li id="{{ $test->id }}" style="cursor: crosshair">
                <!-- BEGIN EXTRAS PORTLET-->
                <div class="tools" style="float:right;padding-top:10px;padding-right:10px;">
                    <form id="editTest_{{$test->id}}" method="get" action="{{route('tests.edit', ['course' => $course->id, 'test' => $test->id])}}" style="float:left;">
                        {{ csrf_field() }}
                        <input type="hidden" name="_method" value="delete" />
                        <button type="submit" class="btn btn-sm blue table-group-action-submit">
                            <i class="fa fa-pencil"></i>
                        </button>
                    </form>
                    <form id="removeTest_{{$test->id}}" method="post" action="{{route('tests.destroy', ['test' => $test->id])}}" style="float:right;">
                        {{ csrf_field() }}
                        <input type="hidden" name="_method" value="delete" />
                        <button type="submit" class="btn btn-sm blue table-group-action-submit">
                            <i class="fa fa-trash"></i>
                        </button>
                    </form>
                </div>
                <div class="portlet box blue">
                    <div class="portlet-title">
                        <div class="caption">
                            <b>Test #{{ $testNo++ }} : {{ $test->title }}</b>
                        </div>
                    </div>
                    
                </div>
            </li>
        @endforeach
    </ul>
@endsection

