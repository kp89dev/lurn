@extends('admin.layout')

@section('pagetitle')
    Workflow "{{ $workflow->name }}" <small>sent emails</small>
@endsection

@section('breadcrumb')
    <li>
        <i class="fa fa-code-fork"></i>
        <a href="{{ route('workflows.index') }}">Workflows</a>
    </li>
    <li>
        <i class="fa fa-angle-right"></i>
        <a href="{{ route('workflows.edit', $workflow) }}">{{ $workflow->name }}</a>
    </li>
    <li>
        <i class="fa fa-angle-right"></i>
        Sent Emails
    </li>
@endsection

@section('content')
    <table class="table table-striped table-bordered table-hover dataTable no-footer">
        <thead>
            <tr role="row" class="heading">
                <th>ID</th>
                <th>Subject</th>
                <th>To</th>
                <th>Status</th>
                <th>Step</th>
                <th>Sent At</th>
                <th>Last Status Update</th>
            </tr>
        </thead>
        @foreach ($stats as $item)
            <tr role="row" class="filter">
                <td>{{ $item->id }}</td>
                <td>{{ $item->subject }}</td>
                <td>
                    @if ($item->user)
                        <a href="{{ route('users.show', $item->user) }}">
                            {{ $item->user->name }}
                            &lt;{{ $item->user->email }}&gt;
                        </a>
                    @else
                        <em>No user.</em>
                    @endif
                </td>
                <td class="{{ $item->statusColor }}">{{ $item->status }}</td>
                <td>{{ $item->step }}</td>
                <td>{{ $item->created_at }}</td>
                <td>{{ $item->last_timestamp }}</td>
            </tr>
        @endforeach
    </table>

    <div class="row">
        <div class="col-md-12 text-center">
            {{ $stats->links() }}
        </div>
    </div>
@endsection
