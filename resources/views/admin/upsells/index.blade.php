@extends('admin.layout')

@section('pagetitle')
    Upsell <small>upsells list</small>
@endsection

@section('breadcrumb')
    <li>
        <i class="fa fa-user"></i>
        <a href="{{ route('upsells.index') }}">Upsells</a>
    </li>
@endsection


@section('content')
    <div class="row">
        <div class="col-md-8 col-sm-12"></div>
        <div class="col-md-4 col-sm-12">
            <div class="table-group-actions pull-right">
				<span></span>
                <a href='{{ route('upsells.create') }}' class="btn btn-sm green table-group-action-submit"><i class="fa fa-plus"></i> Add New Upsell</a>
            </div>
        </div>
    </div>
    <table class="table table-striped table-bordered table-hover dataTable no-footer">
        <thead>
            <tr role="row" class="heading">
                <th rowspan="1" colspan="1">Upsell ID</th>
                <th rowspan="1" colspan="1">For Course</th>
                <th rowspan="1" colspan="1">Appears After</th>
                <th rowspan="1" colspan="1">Enabled</th>
                <th rowspan="1" colspan="1">Updated At</th>
                <th rowspan="1" colspan="1">Actions</th>
            </tr>
        </thead>
        @foreach($upsells as $upsell)
            <tr role="row" class="filter">
                <td rowspan="1" colspan="1">{{ $upsell->id }}</td>
                <td rowspan="1" colspan="1">{{ $upsell->infusionsoft->course->title }}</td>
                <td rowspan="1" colspan="1">{{ $upsell->succeedingCourse->title }}</td>
                <td rowspan="1" colspan="1">
                    @if ($upsell->status == 1)
                        <span class="label label-success">Yes</span>
                    @else
                        <span class="label label-danger">No</span>
                    @endif
                </td>
                <td rowspan="1" colspan="1">{{ $upsell->updated_at }}</td>
                <td>
                    <a href="{{ route('upsells.edit', ['upsells' => $upsell]) }}"  class="btn btn-sm green table-group-action-submit"><i class="fa fa-edit"></i></a>
                </td>
            </tr>
        @endforeach
    </table>
    <div class="row">
        {{ $upsells->links() }}
    </div>
@endsection
