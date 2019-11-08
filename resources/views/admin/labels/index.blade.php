@extends('admin.layout')

@section('pagetitle')
    Labels <small>labels list</small>
@endsection

@section('breadcrumb')
    <li>
        <i class="fa fa-calendar"></i>
        <a href="{{ route('labels.index') }}">labels</a>
    </li>
@endsection


@section('content')
    <div class="row">
        <div class="col-md-8 col-sm-12"></div>
        <div class="col-md-4 col-sm-12">
            <div class="table-group-actions pull-right">
               <span>
               </span>
                <a href="{{ route('labels.create') }}" class="btn btn-sm green table-group-action-submit"><i class="fa fa-plus"></i> Add New Label</a>
            </div>
        </div>
    </div>
    <table class="table table-striped table-bordered table-hover dataTable no-footer">
        <thead>
            <tr role="row" class="heading">
                <th rowspan="1" colspan="1">Name</th>
                <th rowspan="1" colspan="1">Updated At</th>
                <th rowspan="1" colspan="1">Created At</th>
                <th rowspan="1" colspan="1">Actions</th>
            </tr>
        </thead>
        @foreach($labels as $label)
            <tr role="row" class="filter">
                <td rowspan="1" colspan="1">{{ $label->title }}</td>
                <td rowspan="1" colspan="1">{{ $label->updated_at->format('m/d/Y H:i:s') }}</td>
                <td rowspan="1" colspan="1">{{ $label->created_at->format('m/d/Y H:i:s') }}</td>
                <td>
                    <a href="{{ route('labels.edit', ['label' => $label->id])}}"  class="btn btn-sm green table-group-action-submit"><i class="fa fa-edit"></i></a>
                    <form action="{{ route('labels.destroy', ['event' => $label->id]) }}" style="display: inline" method="POST">
                       {{ method_field('DELETE') }}
                        {{ csrf_field() }}
                        <a onclick="$(this).closest('form').submit()" class="btn btn-sm red table-group-action-submit"><i class="fa fa-trash"></i></a>
                    </form>
                </td>
            </tr>
        @endforeach
    </table>
    <div class="row">
        {{ $labels->links() }}
    </div>
@endsection
