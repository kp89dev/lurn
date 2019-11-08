@extends('admin.layout')

@section('pagetitle')
    Tools
@endsection

@section('breadcrumb')
    <li>
        <i class="icon-book-open"></i>
        <a href="{{ route('tools.index', compact('settings')) }}">Tools</a>
    </li>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-16 text-center">
            <a href="{{ route('tools.launchpad.admin') }}" class="btn btn-large btn-primary">Launchpad Admin</a>
        </div>
    </div>
    <div class="row">
        <div class="col-md-8 col-sm-12"></div>
        <div class="col-md-4 col-sm-12">
            <div class="table-group-actions pull-right">
                <span></span>
                <a href='{{ route('tools.create', compact('settings')) }}' class="btn btn-sm green table-group-action-submit">
                    <i class="fa fa-plus"></i> Add New Rule
                </a>
            </div>
        </div>
    </div>
    <table class="table table-striped table-bordered table-hover dataTable no-footer">
        <thead>
        <tr role="row" class="heading">
            <th>People with access to</th>
            <th>Will have access to</th>

            <th class="text-center">Timestamps</th>
            <th>Actions</th>
        </tr>
        </thead>
        @foreach ($settings as $setting)
            <tr role="row" class="filter">
                <td>{{ $setting->course->title }}</td>
                <td>{{ $setting->tool_name }}</td>

                <td class="text-center">
                    <span class="todo-tasklist-badge badge badge-roundless">Created At</span> <span class="todo-tasklist-date"><i class="fa fa-calendar"></i> {{ $setting->created_at }} </span><br/>
                    <span class="todo-tasklist-badge badge badge-roundless">Updated At</span> <span class="todo-tasklist-date"><i class="fa fa-calendar"></i> {{ $setting->updated_at }} </span>
                </td>
                <td>
                    <a href="{{ route('tools.edit', compact('setting')) }}" class="btn btn-sm green table-group-action-submit">
                        <i class="fa fa-edit"></i>
                    </a>
                    <form action="{{ route('tools.destroy', compact('setting')) }}" method="POST" style="display: inline">
                        {{ method_field('DELETE') }}
                        {{ csrf_field() }}
                        <button class="btn btn-sm red table-group-action-submit"
                                onclick="return confirm('Are you sure you want to delete this Rule ? This action cannot be undone.')">
                            <i class="fa fa-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>
        @endforeach
    </table>
    <div class="row text-center">
        {{ $settings->links() }}
    </div>
@endsection
