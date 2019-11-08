@extends('admin.layout')

@section('pagetitle')
    Events
    <small>event creation</small>
@endsection

@section('breadcrumb')
    <li>
        <i class="fa fa-bookmark-o"></i>
        <a href="{{ route('events.index') }}">Events</a>
    </li>
    <li>
        <i class="fa fa-angle-right"></i>
        @if ($event->title)
            <a href="{{ route('events.edit', ['event' => $event->id]) }}">Edit</a>
        @else
            <a href="{{ route('events.create') }}">Add New</a>
        @endif
    </li>
@endsection


@section('content')
    <form action="{{ $action }}" method="post" class="form-horizontal form-bordered">
        {{csrf_field() }}
        @if ($method != 'POST')
        {{ $method }}
        @endif
        <div class="row">
            <div class="col-md-12">
                <div class="portlet box grey">
                    <div class="portlet-title">
                        <div class="caption font-green">
                            <i class="icon-calendar font-green"></i>
                            <span class="caption-subject bold uppercase"> Event Details</span>
                        </div>
                    </div>
                    <div class="portlet-body form">
                        <div class="form-body">
                            <div class="form-group">
                                <label class="control-label col-md-3" for="title">Title <span class="required">*</span></label>
                                <div class='col-md-9'>
                                    <input type="text" class="form-control {{ old('title', $event->title) ? 'edited' : '' }}" id="title" value="{{ old('title', $event->title) }}" name="title" required="true">
                                </div>
                                <br><br>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3" for="description">Description</label>
                                <div class="col-md-9">
                                    <textarea class="ckeditor form-control" id="description" name="description" rows="6" data-error-container="#editor2_error">{{ old('description', $event->description) }}</textarea>
                                </div>

                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3" for="course_container">Container <span class="required">*</span></label>
                                <div class="col-md-9">
                                    <select name="course_container_id" id="course_container" class="form-control">
                                        @foreach(\App\Models\CourseContainer::all() as $container)
                                            <option value="{{ $container->id }}" {{ $event->course_container_id == $container->id ? "SELECTED" : "" }}>{{ $container->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
                            <div class="input-daterange">
                                <div class="form-group">
                                    <label class="control-label col-md-3" for="start_date">Start Date <span class="required">*</span></label>
                                    <div class="col-md-4">
                                        <input type="text" class="form-control" id="start_date" name="start_date"
                                        @if($event->start_date)
                                             value="{{ $event->start_date->format('m/d/Y') }}"
                                        @else
                                             value="{{ old('start_date') }}"
                                        @endif
                                         required="true">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-3" for="end_date">End Date <span class="required">*</span></label>
                                    <div class="col-md-4">
                                        <input type="text" class="form-control" id="end_date" name="end_date"
                                        @if($event->end_date)
                                             value="{{ $event->end_date->format('m/d/Y') }}"
                                        @else
                                             value="{{ old('end_date') }}"
                                        @endif
                                         required="true">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3" for="allday">All Day</label>
                                <div class="col-md-4">
                                @if($event->all_day)
                                    <input type="checkbox" class="form-control" id="allday" name="all_day" checked>
                                @else
                                    <input type="checkbox" class="form-control" id="allday" name="all_day">
                                @endif
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3" for="start_time">Start Time</label>
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <input type="text" class="form-control time-picker" id="start_time" name="start_time" value="{{ old('start_time', date('g:i:s A',strtotime($event->start_time))) }}">
                                        <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-time"></span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3" for="end_time">End Time</label>
                                <div class="col-md-4 bootstrap-timepicker timepicker">
                                    <div class="input-group">
                                        <input type="text" class="form-control time-picker" id="end_time" name="end_time" value="{{ old('end_time', date('g:i:s A',strtotime($event->end_time))) }}">
                                        <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-time"></span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3" for="location">Location</label>
                                <div class='col-md-4'>
                                    <input type="text" class="form-control {{ old('location', $event->location) ? 'edited' : '' }}" id="location" value="{{ old('location', $event->location) }}" name="location">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3" for="address">Address</label>
                                <div class='col-md-4'>
                                    <input type="text" class="form-control {{ old('address', $event->address) ? 'edited' : '' }}" id="address" value="{{ old('address', $event->address) }}" name="address">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3" for="city">City</label>
                                <div class='col-md-4'>
                                    <input type="text" class="form-control {{ old('city', $event->city) ? 'edited' : '' }}" id="city" value="{{ old('city', $event->city) }}" name="city">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3" for="state">State</label>
                                <div class='col-md-4'>
                                    <input type="text" class="form-control {{ old('state', $event->state) ? 'edited' : '' }}" id="state" value="{{ old('state', $event->state) }}" name="state">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3" for="region">Region</label>
                                <div class='col-md-4'>
                                    <input type="text" class="form-control {{ old('region', $event->region) ? 'edited' : '' }}" id="region" value="{{ old('region', $event->region) }}" name="region">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3" for="country">Country</label>
                                <div class='col-md-4'>
                                    <input type="text" class="form-control {{ old('country', $event->country) ? 'edited' : '' }}" id="country" value="{{ old('country', $event->country) }}" name="country">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3" for="postcode">Postal Code</label>
                                <div class='col-md-4'>
                                    <input type="text" class="form-control {{ old('postcode', $event->postcode) ? 'edited' : '' }}" id="postcode" value="{{ old('postcode', $event->postcode) }}" name="postcode">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-offset-4 col-md-3 text-center">
                <div class="form-actions noborder">
                    <input type="submit" class="btn blue" value="Save">
                </div>
            </div>
        </div>
    </form>
@endsection

@section('js')
    <script type="text/javascript" src="/assets/global/plugins/ckeditor/ckeditor.js"></script>
    <script type="text/javascript">
        jQuery(document).ready(function($){
            $('.input-daterange').datepicker({ autoclose: true, todayBtn: 'linked'});
            $('.time-picker').timepicker({showSeconds: true});
            $('#allday').click(function(){
                if ($('.time-picker').attr('disabled')) $('.time-picker').removeAttr('disabled');
                else $('.time-picker').attr('disabled', 'disabled');
            });
        });
    </script>
@endsection
