@extends('admin.layout')

@section('pagetitle')
    Push Notifications <small>notification list</small>
@endsection

@section('breadcrumb')
<li>
    <i class="fa fa-microphone"></i>
    <a href="{{ route('push-notifications.index') }}">Push Notifications</a>
</li>
@endsection


@section('content')
    <div class="row">
        <div class="col-md-8 col-sm-12"></div>
        <div class="col-md-4 col-sm-12">
            <div class="table-group-actions pull-right">
               <span>
               </span>
                <a href="{{ route('push-notifications.create') }}" class="btn btn-sm green table-group-action-submit"><i class="fa fa-plus"></i> Add New Push Notification</a>
            </div>
        </div>
    </div>
    <table class="table table-striped table-bordered table-hover dataTable no-footer">
        <thead>
            <tr role="row" class="heading">
                <th rowspan="1" colspan="1">Notification ID</th>
                <th rowspan="1" colspan="1">Admin Title</th>
                <th rowspan="1" colspan="1">Dates</th>
                <th rowspan="1" colspan="1">Actions</th>
            </tr>
        </thead>
        @foreach($pushNotifications as $pushNotification)
            <tr role="row" class="filter">
                <td rowspan="1" colspan="1">{{ $pushNotification->id }}</td>
                <td rowspan="1" colspan="1">{{ $pushNotification->admin_title }}</td>
                <td rowspan="1" colspan="1">{{ $pushNotification->start_date }} {{ $pushNotification->start_time }} - {{ $pushNotification->end_date }} {{ $pushNotification->end_time }} </td>
                <td>
                    <a href="{{route('push-notifications.edit', ['pushNotification' => $pushNotification->id])}}"  class="btn btn-sm green table-group-action-submit"><i class="fa fa-edit"></i></a>
                    <form action="{{ route('push-notifications.destroy', ['pushNotification' => $pushNotification->id]) }}" style="display: inline" method="POST">
                       {{ method_field('DELETE') }}
                        {{ csrf_field() }}
                        <a onclick="$(this).closest('form').submit()" class="btn btn-sm red table-group-action-submit"><i class="fa fa-trash"></i></a>
                    </form>
                </td>
            </tr>
        @endforeach
    </table>
    <div class="row">
    </div>
@endsection
