@extends('admin.layout')

@section('pagetitle')
Workflows <small>list</small>
@endsection

@section('breadcrumb')
<li>
    <i class="fa fa-code-fork"></i>
    <a href="{{ route('workflows.index', compact('course')) }}">Workflows</a>
</li>
@endsection


@section('content')

<div class="row">
    <div class="col-md-8 col-sm-12">
        <div class="table-group-actions pull-left">
            <a href='{{ route('templates.index') }}' class="btn btn-sm green table-group-action-submit"><i class="fa fa-plus"></i> Email Templates</a>
        </div>
    </div>
    <div class="col-md-4 col-sm-12">
        <div class="table-group-actions pull-right">
            <a href='{{ route('workflows.create') }}' class="btn btn-sm green table-group-action-submit"><i class="fa fa-plus"></i> Add New Workflow</a>
        </div>
    </div>
</div>
<table class="table table-striped table-bordered table-hover dataTable no-footer">
    <thead>
        <tr role="row" class="heading">
            <th rowspan="1" colspan="1">Name</th>
            <th rowspan="1" colspan="1">Stats</th>
            <th rowspan="1" colspan="1">Enabled</th>
            <th rowspan="1" colspan="1">Actions</th>
        </tr>
    </thead>
    @foreach($workflows as $workflow)
    <tr role="row" class="filter">
        <td rowspan="1" colspan="1">{{ $workflow->name }}</td>
        <td>
            @if ($workflow->statSummary())
                @php $sum = $workflow->statSummary()->getPercents() @endphp
                <table class="h-padded-table">
                    <tr>
                        <td>
                            <span class="label label-primary">
                                Open Rate:
                                <strong>{{ number_format($sum['open'] * 100, 1) }}%</strong>
                            </span>
                        </td>
                        <td>
                            <span class="label label-success">
                                CTR:
                                <strong>{{ number_format($sum['click'] * 100, 1) }}%</strong>
                            </span>
                        </td>
                        <td>
                            <span class="label label-warning">
                                Bounce Rate:
                                <strong>{{ number_format($sum['bounce'] * 100, 1) }}%</strong>
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('workflows.email-stats', $workflow) }}">
                                <i class="fa fa-area-chart"></i>
                                Sent Emails
                            </a>
                        </td>
                    </tr>
                </table>
            @else
                Unavailable
            @endif
        </td>
        <td rowspan="1" colspan="1">
            <input type="checkbox" {{ $workflow->status == 1 ? 'CHECKED' : '' }} value="{{ $workflow->status }}" onchange="updateWorkflowStatus({{ $workflow->id }})" class="make-switch" data-size="small">
        </td>
        <td>
            <a href="{{ route('workflows.edit', ['workflow' => $workflow->id]) }}"  class="btn btn-sm green table-group-action-submit"><i class="fa fa-edit"></i></a>
        </td>
    </tr>
    @endforeach
</table>
<div class="row">
    <div class="col-md-12 text-center">
        {{ $workflows->links() }}
    </div>
</div>
@endsection

@section('css')
    <link href="/assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css" rel="stylesheet" type="text/css">
    <style>
        table.h-padded-table {
            margin: 0 -5px;
        }

        table.h-padded-table td {
            padding: 0 5px;
        }

        table.table-striped td {
            vertical-align: middle !important;
        }
    </style>
@endsection

@section('js')
    <script type="text/js" src="/assets/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js"></script>
    <script type='text/javascript'>
        function updateWorkflowStatus(workflowId)
        {
            $.ajax({
                type: "POST",
                url: '{{ route("workflows.update-status") }}',
                data: { workflow: workflowId }
            }).done(function(){
                alert('Status Updated Successfully');
            }).error(function() {
                alert('Error: There was an error updating the workflow. Please try again later.')
            });
        }
    </script>
@endsection
