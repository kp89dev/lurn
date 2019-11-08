@extends('admin.layout')

@section('pagetitle')
    User Roles
@endsection

@section('breadcrumb')
    <li>
        <i class="book-open"></i>
        <a href="{{ route('roles.index') }}">User Roles</a>
    </li>
    <li>
        <i class="icon-edit"></i>
        @if ($role->title)
            <a href="{{ route('roles.edit', $role->id) }}">Edit</a>
        @else
            <a href="{{ route('roles.create') }}">Add Role</a>
        @endif
    </li>
@endsection

@section('content')
    <form action="{{ $action }}" method="post" class="form-horizontal form-bordered">
        {{csrf_field() }}
        {{ $method }}
        <div class="row">
            <div class="col-md-12">
                <div class="portlet box grey">
                    <div class="portlet-title">
                        <div class="caption font-green">
                            <i class="icon-user font-green"></i>
                            <span class="caption-subject bold uppercase"> User Role Details</span>
                        </div>
                    </div>
                    <div class="portlet-body form">
                        <div class="form-body">
                            <div class="form-group">
                                <label class="control-label col-md-3" for="title">Title <span class="required">*</span></label>
                                <div class='col-md-9'>
                                    <input type="text" name="title" value="{{ old('title', $role->title) }}"
                                           class="form-control {{ old('title', $role->title) ? 'edited' : '' }}">
                                </div>
                                <br><br>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-md-3" for="answer">Permissions <span class="required">*</span></label>
                                <div class="col-md-9">
                                    @foreach ($availablePermissions as $availablePermission)
                                        <div class="row">
                                            <div class="col-md-3 col-sm-2">
                                                {{ $availablePermission['title'] }}:
                                            </div>
                                            @foreach($availablePermission['permissions'] as $rolePermission)
                                                <div class="col-md-2 col-sm-2">
                                                    <label> {{ ucfirst($rolePermission) }}
                                                        <input type="checkbox" name="permissions[{{ $availablePermission['name'] }}][]" value="{{ $rolePermission }}"
                                                            @if (!empty($role->permissions[$availablePermission['name']]) && in_array($rolePermission, $role->permissions[$availablePermission['name']]))
                                                                checked
                                                            @endif
                                                        >
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-offset-4 col-md-3 text-center">
                <div class="form-actions noborder">
                    <input type="submit" class="btn blue" value="Save">
                </div>
            </div>
        </div>
    </form>
@endsection