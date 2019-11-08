@extends('admin.layout')

@section('pagetitle')
    Bonuses
    <small>courses assigned automatically to users when they reach the defined required points</small>
@endsection

@section('breadcrumb')
    <li>
        <i class="fa star"></i>
        <a href="{{ route('bonuses.index') }}">Bonuses</a>
    </li>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-8 col-sm-12"></div>
        <div class="col-md-4 col-sm-12">
            <div class="table-group-actions pull-right">
            <span>
            </span>
                <a href='{{ route('bonuses.create') }}' class="btn btn-sm green table-group-action-submit">
                    <i class="fa fa-plus"></i> Add New Bonus
                </a>
            </div>
        </div>
    </div>

    <table class="table table-striped table-bordered table-hover dataTable no-footer">
        <thead>
            <tr role="row" class="heading">
                <th>Course</th>
                <th>Points Required</th>
                <th>Created</th>
                <th>Updated</th>
                <th>Actions</th>
            </tr>
        </thead>
        @foreach ($bonuses as $bonus)
            <tr role="row" class="filter">
                <td>
                    <a href="{{ route('courses.edit', $bonus->course) }}">
                        {{ $bonus->course->title }}
                    </a>
                </td>
                <td>{{ number_format($bonus->points_required, 0) }}</td>
                <td>{{ $bonus->created_at }}</td>
                <td>{{ $bonus->updated_at }}</td>
                <td>
                    <a href="{{ route('bonuses.edit', $bonus) }}" class="btn btn-sm green table-group-action-submit">
                        <i class="fa fa-edit"></i>
                    </a>

                    <form class="form-inline" style="display: inline" action="{{ route('bonuses.destroy', $bonus) }}" method="post"
                          onsubmit="return confirm('Are you sure that you want to delete this bonus?')">
                        @csrf
                        @method('delete')
                        <button class="btn btn-sm red table-group-action-submit">
                            <i class="fa fa-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>
        @endforeach
    </table>
    <div class="row">
        {{ $bonuses->links() }}
    </div>
@endsection
