@extends('admin.layout')

@section('pagetitle')
    Test Results
@endsection

@section('breadcrumb')
    <li>
        <i class="fa fa-check-square"></i>
        <a href="{{ route('test-results.index') }}">Results</a>
    </li>
    <li><i class="fa fa-angle-right"></i></li>
    <li>
        &nbsp
        <i class="fa fa-legal"></i>
        <span> {{ $testResult->test->title }}</span>
    </li>
@endsection

@section('content')
    <table class="table table-striped table-bordered table-hover dataTable no-footer">
        <thead>
            <tr role="row" class="heading">
                <th rowspan="1" colspan="1">
                    {{ $testResult->test->title }}
                    <div class="actions" style="float: right">
                        <a href="{{ route('test-results.download-pdf', ['testResult' => $testResult->id]) }}" class="btn btn-default btn-sm"><i class="fa fa-file-pdf-o"></i>&nbsp; PDF </a>
                    </div>
                </th>
            </tr>
        </thead>

        <tr role="row" class="filter">
            <td rowspan="1" colspan="1">
                @include('admin.test-results.partials.results', compact('testResult'))
            </td>
        </tr>
    </table>
@endsection

