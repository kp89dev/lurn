@extends('admin.layout')

@section('pagetitle')
    Test Results
@endsection

@section('breadcrumb')
    <li>
        <i class="icon-book-open"></i>
        <span>Results</span>
    </li>
@endsection

@section('content')
    <table class="table table-striped table-bordered table-hover dataTable no-footer">
        <thead>
        <tr role="row" class="heading">
            <th rowspan="1" colspan="1">User</th>
            <th rowspan="1" colspan="1">Course</th>
            <th rowspan="1" colspan="1">Test Title</th>
            <th rowspan="1" colspan="1">Result</th>
            <th rowspan="1" colspan="1">&nbsp;</th>
        </tr>
        </thead>
        @foreach ($results as $result)
            <tr role="row" class="filter">
                <td rowspan="1" colspan="1">
                    @if ($result->user)
                        {{ $result->user->name }}
                    @else
                        <i>User not imported</i>
                    @endif
                </td>
                <td rowspan="1" colspan="1">{{ $result->test->course->title }}</td>
                <td rowspan="1" colspan="1">{{ $result->test->title }}</td>
                <td rowspan="1" colspan="1">
                    @if ($result->result > 0)
                        <span class="label label-sm label-success">Passed <b>{{ $result->mark }}</b></span>
                    @else
                        <span class="label label-sm label-danger">Failed <b>{{ $result->mark }}</b></span>
                    @endif
                </td>
                <td rowspan="1" colspan="1" class="text-center">
                    <a href="{{ route('test-results.show', ['result' => $result]) }}" class="active"><i class="fa fa-eye"></i></a>
                </td>
            </tr>
        @endforeach
    </table>
    <div class="row">
        <div class="col-md-12 text-center">
            {{ $results->links() }}
        </div>
    </div>
@endsection

