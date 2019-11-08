@extends('admin.layout')

@section('pagetitle')
    Stats
    <small>Per-country stats</small>
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
            <th>Country</th>
            <th>Revenue</th>
        </tr>
        </thead>
        @foreach ($countries as $country=>$total)
            <tr role="row" class="filter">
                <td>
                    {{ $country }}
                </td>
                <td style="width:20%;" class="text-center">${{ number_format($total, 2) }}</td>
            </tr>
        @endforeach
    </table>
@endsection

@section('css')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/2.8.0/css/flag-icon.min.css" rel="stylesheet" type="text/css">
@endsection
