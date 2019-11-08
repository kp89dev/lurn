@extends('admin.layout')

@section('pagetitle')
Users <small>users list</small>
@endsection

@section('breadcrumb')
<li>
    <i class="fa fa-user"></i>
    <a href="{{ route('users.index') }}">Users</a>
</li>
@endsection


@section('content')

<form action="{{ $search }}" method="post" class="form-horizontal form-bordered">
    {{csrf_field() }}
    <div class="row">
        <div class="col-md-12">
            <div class="portlet box grey">
                <div class="portlet-body form">
                    <div class="form-body">
                        <div class="form-group">
                            <label class="control-label col-md-2" for="term">Search </label>
                            <div class='col-md-5'>
                                <input type="text" class="form-control" id="term" value="" name="term">
                            </div>
                            <div class='col-md-5'>
                                <input type="submit" class="btn blue" value="Search">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
<div class="row">
    <div class="col-md-8 col-sm-12"></div>
    <div class="col-md-4 col-sm-12">
        <div class="table-group-actions pull-right">
            <a href='{{ route('users.create') }}' class="btn btn-sm green table-group-action-submit"><i class="fa fa-plus"></i> Add New User</a>
        </div>
    </div>
</div>
<table class="table table-striped table-bordered table-hover dataTable no-footer">
    <thead>
        <tr role="row" class="heading">
            <th rowspan="1" colspan="1">User ID</th>
            <th rowspan="1" colspan="1">Name</th>
            <th rowspan="1" colspan="1">Email</th>
            <th rowspan="1" colspan="1">Updated At</th>
            <th rowspan="1" colspan="1">Actions</th>
        </tr>
    </thead>
    @foreach($users as $user)
    <tr role="row" class="filter">
        <td rowspan="1" colspan="1">{{ $user->id }}</td>
        <td rowspan="1" colspan="1">{{ $user->name }}</td>
        <td rowspan="1" colspan="1">{{ $user->email }}</td>
        <td rowspan="1" colspan="1">{{ $user->updated_at }}</td>
        <td>
            <a href="{{ route('users.edit', ['user' => $user->id]) }}"  class="btn btn-sm green table-group-action-submit"><i class="fa fa-edit"></i></a>
            <form action="{{ route('users.destroy', ['user' => $user->id]) }}" style="display: inline" method="POST">
                {{ method_field('DELETE') }}
                {{ csrf_field() }}
                <a onclick="$(this).closest('form').submit()" class="btn btn-sm red table-group-action-submit"><i class="fa fa-trash"></i></a>
            </form>
            <a href="{{ route('users.show', ['user' => $user->id]) }}" class="btn btn-sm green table-group-action-submit"><i class="fa fa-eye"></i>
        </td>
    </tr>
    @endforeach
</table>
<div class="row">
    <div class="col-md-12 text-center">
        {{ $users->links() }}
    </div>
</div>
@endsection
