@extends('admin.layout')

@section('pagetitle')
    Events <small>events list</small>
@endsection

@section('breadcrumb')
    <li>
        <i class="fa fa-calendar"></i>
        <a href="{{ route('events.index') }}">Events</a>
    </li>
@endsection


@section('content')
    <div class="row">
        <div class="col-md-8 col-sm-12"></div>
        <div class="col-md-4 col-sm-12">
            <div class="table-group-actions pull-right">
               <span>
               </span>
                <a href="{{ route('events.create') }}" class="btn btn-sm green table-group-action-submit"><i class="fa fa-plus"></i> Add New Event</a>
            </div>
        </div>
    </div>
    <table class="table table-striped table-bordered table-hover dataTable no-footer">
        <thead>
            <tr role="row" class="heading">
                <th rowspan="1" colspan="1">Event ID</th>
                <th rowspan="1" colspan="1">Container</th>
                <th rowspan="1" colspan="1">Name</th>
                <th rowspan="1" colspan="1">Dates</th>
                <th rowspan="1" colspan="1">Actions</th>
            </tr>
        </thead>
        @foreach($events as $event)
            <tr role="row" class="filter">
                <td rowspan="1" colspan="1">{{ $event->id }}</td>
                <td rowspan="1" colspan="1">{{ $event->container->title }}</td>
                <td rowspan="1" colspan="1">{{ $event->title }}</td>
                <td rowspan="1" colspan="1">{{ $event->start_date->format('m/d/Y') }} - {{ $event->end_date->format('m/d/Y') }}</td>
                <td>
                    <a href="{{route('events.edit', ['event_id' => $event->id])}}"  class="btn btn-sm green table-group-action-submit"><i class="fa fa-edit"></i></a>
                    <form action="{{ route('events.destroy', ['event' => $event->id]) }}" style="display: inline" method="POST">
                       {{ method_field('DELETE') }}
                        {{ csrf_field() }}
                        <a onclick="$(this).closest('form').submit()" class="btn btn-sm red table-group-action-submit"><i class="fa fa-trash"></i></a>
                    </form>
                </td>
            </tr>
        @endforeach
    </table>
    <div class="row">
        {{ $events->links() }}
    </div>
@endsection
