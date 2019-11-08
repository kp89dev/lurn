@extends('admin.layout')

@section('pagetitle')
    Badges
@endsection

@section('breadcrumb')
    <li>
        <i class="icon-book-open"></i>
        <a href="{{ route('badges.index', compact('course')) }}">Badges</a>
    </li>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-8 col-sm-12"></div>
        <div class="col-md-4 col-sm-12">
            <div class="table-group-actions pull-right">
                <span></span>
                <a href='{{ route('badges.create', compact('course')) }}' class="btn btn-sm green table-group-action-submit">
                    <i class="fa fa-plus"></i> Add Badge
                </a>
            </div>
        </div>
    </div>
    <table class="table table-striped table-bordered table-hover dataTable no-footer">
        <thead>
        <tr role="row" class="heading">
            <th>Image</th>
            <th>Title</th>
            <th>Enable</th>
            <th class="text-center">Timestamps</th>
            <th>Actions</th>
        </tr>
        </thead>
        @foreach ($badges as $badge)
            <tr role="row" class="filter">
                <td><img src="{{ $badge->src }}" width="100"></td>
                <td>{{ $badge->title }}</td>
                <td rowspan="1" colspan="1">
                    @if ($badge->status == 1)
                        <span class="label label-success">Yes</span>
                    @else
                        <span class="label label-danger">No</span>
                    @endif
                </td>
                <td class="text-center">
                    <span class="todo-tasklist-badge badge badge-roundless">Created At</span> <span class="todo-tasklist-date"><i class="fa fa-calendar"></i> {{ $badge->created_at }} </span><br/>
                    <span class="todo-tasklist-badge badge badge-roundless">Updated At</span> <span class="todo-tasklist-date"><i class="fa fa-calendar"></i> {{ $badge->updated_at }} </span>
                </td>
                <td>
                    <a href="{{ route('badges.edit', compact('badge', 'course')) }}" class="btn btn-sm green table-group-action-submit">
                        <i class="fa fa-edit"></i>
                    </a>
                    <form action="{{ route('badges.destroy', compact('badge', 'course')) }}" method="POST" style="display: inline">
                        {{ method_field('DELETE') }}
                        {{ csrf_field() }}
                        <button class="btn btn-sm red table-group-action-submit"
                                onclick="return confirm('Are you sure you want to delete this Badge ? This action cannot be undone.')">
                            <i class="fa fa-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>
        @endforeach
    </table>
    <div class="row text-center">
        {{ $badges->links() }}
    </div>
@endsection
