<div class="col-md-6">
    <!-- BEGIN PORTLET -->
    <div class="portlet light">
        <div class="portlet-title tabbable-line">
            <div class="caption caption-md">
                <i class="icon-globe theme-font hide"></i>
                <span class="caption-subject font-blue-madison bold uppercase">User Activity</span>
            </div>
            <ul class="nav nav-tabs">
                <li class="active">
                    <a href="#tab_1_1" data-toggle="tab">Activities </a>
                </li>
                <li>
                    <a href="#tab_1_2" data-toggle="tab">Emails </a>
                </li>
                <li>
                    <a href="#tab_1_3" data-toggle="tab">Engagement </a>
                </li>
            </ul>
        </div>
        <div class="portlet-body">
            <!--BEGIN TABS-->
            <div class="tab-content">
                <div class="tab-pane active" id="tab_1_1">
                    <div class="scroller" style="height: 320px;" data-always-visible="1" data-rail-visible1="0" data-handle-color="#D7DCE2">
                        <ul class="feeds">
                            @if (! count($activity['visits']))
                                <li>
                                    <div class="col1">
                                        <i>No activity registered for this user</i>
                                    </div>
                                </li>
                            @else
                                @foreach ($activity['visits'] as $act)
                                    <li style="font-weight: bold; margin-top: 20px">
                                        <span style="width: 50%">
                                            <i class="fa fa-calendar"></i> <span>{{ $act['date'] }}</span>
                                        </span>
                                        <span style="width: 50%; text-align:right; float:right">
                                            <span class="date">
                                                <span class="flag-icon flag-icon-{{ strtolower($act['country']) }}"></span>
                                                {{ $act['region'] ?? "?" }} / {{ $act['city'] ?? "?" }}
                                            </span>
                                        </span>
                                    </li>
                                    @foreach ($act['actions'] as $action)
                                        <li style="padding-left: 20px">
                                            <span>
                                                <img src="http://static.woopra.com/live/icons/events/{{ $action['icon'] }}" style="display: inline-block">
                                                <span>
                                                    {{ $action['date'] }} -
                                                    @if ($action['name'] === 'pv')
                                                        Viewed <a href="{{ $action['properties']['url'] }}">{{ $action['properties']['url'] }}</a>
                                                    @else
                                                        {{ $action['description'] }}
                                                    @endif
                                                </span>
                                            </span>
                                        </li>
                                    @endforeach
                                @endforeach
                            @endif
                        </ul>
                    </div>
                </div>

                <div class="tab-pane" id="tab_1_2">
                    <div class="scroller" style="height: 320px;" data-always-visible="1" data-rail-visible1="0" data-handle-color="#D7DCE2">
                        <ul class="feeds">
                            @if (!count($emails))
                                <li>
                                    <div class="col1">
                                        <i>No email records for this user</i>
                                    </div>
                                </li>
                            @else
                                @foreach ($emails as $mail)
                                    <li style="font-weight: bold; margin-top: 20px">
                                        <span style="width: 50%">
                                            <i class="fa fa-calendar"></i> <span>{{ $mail->last_timestamp->format('M j, Y') }}</span>
                                        </span>
                                        <span style="width: 50%; text-align:right; float:right">
                                            <span style="font-weight:normal; font-style:italic;">
                                                Originally sent {{ $mail->created_at->format('M j, Y') }}
                                            </span>
                                        </span>
                                    </li>
                                    <li style="padding-left: 20px">


                                        @if($mail->status == "Send")
                                            <i class="fa fa-send-o"></i>
                                            {{$mail->last_timestamp->format('g:i:s A')}}
                                            <span class="badge badge-warning">Sent</span>
                                        @elseif($mail->status == "Delivery")
                                            <i class="fa fa-envelope-o"></i>
                                            {{$mail->last_timestamp->format('g:i:s A')}}
                                            <span class="badge badge-primary">Delivered</span>
                                        @elseif($mail->status == "Open")
                                            <i class="fa fa-envelope-open-o"></i>
                                            {{$mail->last_timestamp->format('g:i:s A')}}
                                            <span class="badge badge-success">Opened</span>
                                        @elseif($mail->status == "Click")
                                            <i class="fa fa-bullseye"></i>
                                            {{$mail->last_timestamp->format('g:i:s A')}}
                                            <span class="badge badge-info">Clicked</span>
                                        @elseif($mail->status == "Bounce")
                                            <i class="fa fa-exclamation-triangle"></i>
                                            {{$mail->last_timestamp->format('g:i:s A')}}
                                            <span class="badge badge-danger">Bounced</span>
                                        @endif
                                        <span class="important">{{$mail->subject}}</span>
                                    </li>
                                @endforeach

                            @endif
                        </ul>
                    </div>
                </div>

                <div class="tab-pane" id="tab_1_3">
                    <div class="scroller" style="height: 320px;" data-always-visible="1" data-rail-visible1="0" data-handle-color="#D7DCE2">
                        <ul class="feeds">
                            @if (!count($engagements))
                                <li>
                                    <div class="col1">
                                        <i>No engagement records for this user</i>
                                    </div>
                                </li>
                            @else
                                @foreach ($engagements as $activity)
                                    <li>
                                        <span style="display: block; padding: .5em; font-weight: bold">
                                            {{ $activity->created_at->format('M j, Y H:iA') }}
                                        </span>
                                        <span style="display: block; padding: 0 0 .5em 2em">
                                            {{ $activity->transaction }} ({{ $activity->points > 0 ? "+{$activity->points}" : $activity->points }} pts)
                                        </span>
                                    </li>
                                @endforeach

                            @endif
                        </ul>
                    </div>
                </div>
            </div>

            <!--END TABS-->
        </div>
    </div>
    <!-- END PORTLET -->
</div>

@section('css')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/2.8.0/css/flag-icon.min.css" rel="stylesheet" type="text/css">
@endsection
