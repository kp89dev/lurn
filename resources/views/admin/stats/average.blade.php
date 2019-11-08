@extends('admin.layout')

@section('pagetitle')
    Stats
    <small>Average revenue per user</small>
@endsection

@section('breadcrumb')
    <li>
        <i class="fa fa-user"></i>
        <a href="{{ route('stats.index') }}">Stats</a>
    </li>
@endsection

@section('content')
    <table class="table table-striped table-bordered table-hover dataTable no-footer">
        <thead>
        <tr role="row" class="heading">
            <th>Days from registration</th>
            <th>Total</th>
            <th># buyers</th>
            <th>Average amount</th>
        </tr>
        </thead>
        @foreach ($stats['data'] as $stat)
            <tr role="row" class="filter">
                <td>{{ $stat->days }}</td>
                <td>{{ $stat->total }}</td>
                <td>{{ $stats['count'] }}</td>
                <td style="width:20%;" class="text-center">${{ number_format($stat->total / $stats['count'], 2) }}</td>
            </tr>
        @endforeach
    </table>
@endsection

@section('css')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/2.8.0/css/flag-icon.min.css" rel="stylesheet" type="text/css">
@endsection
