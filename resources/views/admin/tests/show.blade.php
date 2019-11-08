@extends('admin.layout')

@section('pagetitle')
    Tests <small>tests for {{ $course->title }}</small>
@endsection

@section('breadcrumb')
    <li>
        <i class="icon-book-open"></i>
        <a href="{{ route('courses.index') }}">Course <b>{{ $course->title }}</b></a>
        <i class="fa fa-angle-right"></i>
    </li>
    <li>
        <i class="fa fa-book"></i>
        <a href="/">Viewing {{ $test->title }}</a>
    </li>
@endsection

@section('content')
    <table class="table table-striped table-bordered table-hover dataTable no-footer">
        <thead>
        <tr role="row" class="heading">
            <th rowspan="1" colspan="1">
                {{ $test->title }}
                <div class="actions" style="float: right">
                    <a target="_blank" href="{{ route('tests.download-pdf', compact('test', 'course')) }}" class="btn btn-default btn-sm"><i class="fa fa-file-pdf-o"></i>&nbsp; PDF </a>
                </div>
            </th>
        </tr>
        </thead>

        <tr role="row" class="filter">
            <td rowspan="1" colspan="1">
                @include('admin.tests.partials.results', compact('test'))
            </td>
        </tr>
    </table>
@endsection

