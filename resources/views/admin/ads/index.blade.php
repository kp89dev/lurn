@extends('admin.layout')

@section('pagetitle')
Ads
@endsection

@section('breadcrumb')
<li>
    <i class="fa fa-book"></i>
    <a href="{{ route('ads.index') }}">Ads</a>
</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8 col-sm-12"></div>
    <div class="col-md-4 col-sm-12">
        <div class="table-group-actions pull-right">
            <span>
            </span>
            <a href='{{ route('ads.create') }}' class="btn btn-sm green table-group-action-submit"><i class="fa fa-plus"></i> Create New Ad</a>
        </div>
    </div>
</div>
<table class="table table-striped table-bordered table-hover dataTable no-footer">
    <thead>
        <tr role="row" class="heading">
            <th rowspan="1" colspan="1">Ad ID</th>
            <th rowspan="1" colspan="1">Admin Title</th>
            <th rowspan="1" colspan="1">Image</th>
            <th rowspan="1" colspan="1">Location</th>
            <th rowspan="1" colspan="1">Position</th>
            <th rowspan="1" colspan="1">Enabled</th>
            <th rowspan="1" colspan="1">Updated At</th>
            <th rowspan="1" colspan="1">Actions</th>
        </tr>
    </thead>
    @foreach($ads as $ad)
    <tr role="row" class="filter">
        <td rowspan="1" colspan="1">{{ $ad->id }}</td>
        <td rowspan="1" colspan="1">{{ $ad->admin_title }}</td>
        <td rowspan="1" colspan="1"><img src="{{ $ad->getPrintableImageUrl() }}" width="100"/></td>
        <td rowspan="1" colspan="1">{{ $ad->location }}</td>
        <td rowspan="1" colspan="1">{{ $ad->position }}</td>
        <td rowspan="1" colspan="1">
            @if ($ad->status == 1)
            <span class="label label-success">Yes</span>
            @else
            <span class="label label-danger">No</span>
            @endif
        </td>
        <td rowspan="1" colspan="1">{{ $ad->updated_at }}</td>
        <td>
            <a href="{{ route('ads.edit', ['user' => $ad->id]) }}"  class="btn btn-sm green table-group-action-submit"><i class="fa fa-edit"></i></a>
        </td>
    </tr>
    @endforeach
</table>
<div class="row">
    {{ $ads->links() }}
</div>
@endsection
