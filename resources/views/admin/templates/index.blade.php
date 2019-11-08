@extends('admin.layout')

@section('pagetitle')
Templates <small>list</small>
@endsection

@section('breadcrumb')
<li>
    <i class="fa fa-code-fork"></i>
    <a href="/admin/templates">Templates</a>
</li>
@endsection


@section('content')

<div class="row">
    <div class="col-md-8 col-sm-12"></div>
    <div class="col-md-4 col-sm-12">
        <div class="table-group-actions pull-right">
            <a href='{{ route('templates.create') }}' class="btn btn-sm green table-group-action-submit"><i class="fa fa-plus"></i> Add New Template</a>
        </div>
    </div>
</div>
<table class="table table-striped table-bordered table-hover dataTable no-footer">
    <thead>
        <tr role="row" class="heading">
            <th rowspan="1" colspan="1">Title</th>
            <th rowspan="1" colspan="1">Subject Line</th>
            <th rowspan="1" colspan="1">Actions</th>
        </tr>
    </thead>
    @foreach ($templates as $template)
    <tr role="row" class="filter">
        <td rowspan="1" colspan="1">{{ $template->title }}</td>
        <td rowspan="1" colspan="1">{{ $template->subject }}</td>
        <td>
            <a href="{{ route('templates.edit', ['template' => $template->id]) }}"  class="btn btn-sm green table-group-action-submit"><i class="fa fa-edit"></i></a>
        </td>
    </tr>
    @endforeach
</table>
<div class="row">
    <div class="col-md-12 text-center">
        
    </div>
</div>
@endsection

@section('css')
    <link href="/assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css" rel="stylesheet" type="text/css">
@endsection

@section('js')
    <script type="text/js" src="/assets/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js"></script>
@endsection
