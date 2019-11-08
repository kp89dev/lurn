@extends('admin.layout')

@section('pagetitle')
    Users
    <small>users list</small>
@endsection

@section('breadcrumb')
    <li>
        <i class="fa fa-user"></i>
        <a href="{{ route('user-logins.index')}}">User Logins</a>
    </li>
@endsection

@section('content')
    <table class="table table-striped table-bordered table-hover dataTable no-footer">
        <thead>
            <tr role="row" class="heading">
                <th>User</th>
                <th>Country</th>
                <th>City</th>
                <th>Region</th>
                <th>Timezone</th>
                <th>Status</th>
                <th>Attempt On</th>
            </tr>
        </thead>
        @foreach ($logs as $log)
            <tr role="row" class="filter">
                <td>
                    @if ($log->user)
                        <a href="{{ route('users.show', $log->user_id) }}">
                            {{ $log->user->name }}
                        </a>
                    @else
                        None
                    @endif
                </td>
                <td><span class="flag-icon flag-icon-{{ strtolower($log->countryCode) }}"></span></td>
                <td>{{ $log->city}}</td>
                <td>{{ $log->regionName }}</td>
                <td>{{ $log->timezone }}</td>
                <td class="text-center" style={{ $log->successful ?: "background-color:#ffc4ba" }}>
                    @if ($log->successful)
                        <i class="fa fa-check font-green"></i>
                    @else
                        <i class="fa fa-times-circle-o" title="Failed Login"></i>
                    @endif
                </td>
                <td style="width:20%;" class="text-center">{{ $log->created_at->format(DATE_RFC2822) }}</td>
            </tr>
        @endforeach
    </table>
    <div class="row">
        <div class="col-md-12 text-center">
            {{ $logs->links() }}
        </div>
    </div>
@endsection

@section('css')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/2.8.0/css/flag-icon.min.css" rel="stylesheet" type="text/css">
@endsection
