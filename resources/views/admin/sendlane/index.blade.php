@extends('admin.layout')

@section('pagetitle')
    Sendlane <small>available sendlane accounts</small>
@endsection

@section('breadcrumb')
    <li>
        <i class="fa fa-user"></i>
        <a href="{{ route('sendlane.index') }}">Sendlane</a>
    </li>
@endsection


@section('content')
    <div class="row">
        <div class="col-md-8 col-sm-12"></div>
        <div class="col-md-4 col-sm-12">
            <div class="table-group-actions pull-right">
                <span></span>
                <a href='{{ route('sendlane.create') }}' class="btn btn-sm green table-group-action-submit"><i class="fa fa-plus"></i> Add New Account</a>
            </div>
        </div>
    </div>
    <table class="table table-striped table-bordered table-hover dataTable no-footer">
        <thead>
        <tr role="row" class="heading">
            <th rowspan="1" colspan="1">Email</th>
            <th rowspan="1" colspan="1">Api</th>
            <th rowspan="1" colspan="1">Created At</th>
            <th rowspan="1" colspan="1">Updated At</th>
            <th rowspan="1" colspan="1">Actions</th>
        </tr>
        </thead>
        @foreach($accounts as $account)
            <tr role="row" class="filter">
                <td rowspan="1" colspan="1">{{ $account->email }}</td>
                <td rowspan="1" colspan="1">{{ $account->api }}</td>
                <td rowspan="1" colspan="1">{{ $account->created_at }}</td>
                <td rowspan="1" colspan="1">{{ $account->updated_at }}</td>
                <td>
                    <a href="{{ route('sendlane.edit', ['sendlane' => $account->id]) }}"  class="btn btn-sm green table-group-action-submit"><i class="fa fa-edit"></i></a>
                </td>
            </tr>
        @endforeach
    </table>
@endsection

