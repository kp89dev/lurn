@extends('admin.layout')

@section('pagetitle')
    User Roles
@endsection

@section('breadcrumb')
    <li>
        <i class="icon-book-open"></i>
        <a href="{{ route('roles.index') }}">User Roles</a>
    </li>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-8 col-sm-12"></div>
        <div class="col-md-4 col-sm-12">
            <div class="table-group-actions pull-right">
                <span></span>
                <a href='{{ route('roles.create') }}' class="btn btn-sm green table-group-action-submit">
                    <i class="fa fa-plus"></i> Add Role
                </a>
            </div>
        </div>
    </div>
    <table class="table table-striped table-bordered table-hover dataTable no-footer">
        <thead>
            <tr role="row" class="heading">
                <th>ID</th>
                <th>Title</th>
                <th>Created At</th>
                <th>Updated At</th>
                <th>Actions</th>
            </tr>
        </thead>
        @foreach ($roles as $role)
            <tr role="row" class="filter">
                <td>{{ $role->id }}</td>
                <td>{{ $role->title }}</td>
                <td>{{ $role->created_at }}</td>
                <td>{{ $role->updated_at }}</td>
                <td>
                    <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-sm green table-group-action-submit">
                        <i class="fa fa-edit"></i>
                    </a>
                </td>
            </tr>
        @endforeach
    </table>
@endsection
