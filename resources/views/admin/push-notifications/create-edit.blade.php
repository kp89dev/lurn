@extends('admin.layout')

@section('pagetitle')
Push Notifications
<small>push notification creation</small>
@endsection

@section('breadcrumb')
<li>
    <i class="fa fa-bookmark-o"></i>
    <a href="{{ route('push-notifications.index') }}">Push Notifications</a>
</li>
<li>
    <i class="fa fa-angle-right"></i>
    @if ($pushNotification->admin_title)
    <a href="{{ route('push-notifications.edit', compact('pushNotification')) }}">Edit</a>
    @else
    <a href="{{ route('push-notifications.create') }}">Add New</a>
    @endif
</li>
@endsection


@section('content')
<form action="{{ $action }}" method="POST" class="form-horizontal form-bordered">
    {{csrf_field() }}
    {{ $method }}
    <div class="row">
        <div class="col-md-12">
            <div class="portlet box grey">
                <div class="portlet-title">
                    <div class="caption font-green">
                        <i class="icon-microphone font-green"></i>
                        <span class="caption-subject bold uppercase"> Push Notification Details</span>
                    </div>
                </div>
                <div class="portlet-body form">
                    <div class="form-body">
                        <div class="form-group">
                            <label class="control-label col-md-3" for="admin_title">Admin Title <span class="required">*</span></label>
                            <div class='col-md-4'>
                                <input type="text" class="form-control {{ old('admin_title', $pushNotification->admin_title) ? 'edited' : '' }}" id="admin_title" value="{{ old('admin_title', $pushNotification->admin_title) }}" name="admin_title" required="true">
                            </div>
                            <br><br>
                        </div>
                        <div class="input-daterange">
                            <div class="form-group">
                                <label class="control-label col-md-3" for="start_date">Start Date <span class="required">*</span></label>
                                <div class="col-md-4">
                                    <input type="text" class="form-control" id="start_date" name="start_date"
                                           @if($pushNotification->start_date)
                                           value="{{ date('m/d/Y',strtotime($pushNotification->start_date)) }}"
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
                                           @if($pushNotification->end_date)
                                           value="{{ date('m/d/Y',strtotime($pushNotification->end_date)) }}"
                                           @else
                                           value="{{ old('end_date') }}"
                                           @endif
                                           required="true">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3" for="start_time">Start Time</label>
                            <div class="col-md-4">
                                <div class="input-group">
                                    <input type="text" class="form-control time-picker" id="start_time" name="start_time" value="{{ old('start_time', date('g:i:s A',strtotime($pushNotification->start_time))) }}">
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
                                    <input type="text" class="form-control time-picker" id="end_time" name="end_time" value="{{ old('end_time', date('g:i:s A',strtotime($pushNotification->end_time))) }}">
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-time"></span>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Timezone</label>
                            <div class="col-md-4">
                                <select name="timezone" class="form-control">
                                    @foreach($timezones as $value => $timezone)
                                    <option value="{{ $value }}" {{ $pushNotification->timezone == $value ? "SELECTED" : "" }}>{{ $timezone }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">All Visitors</label>
                            <div class="col-md-4">
                                <input type="checkbox" value="1" {{ $pushNotification->all_visitors == 1 ? "CHECKED" : "" }} class="make-switch" data-size="small" name="all_visitors">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3" for="content">Content <span class="required">*</span></label>
                            <div class='col-md-9'>
                                <input maxlength="165" type="text" class="form-control {{ old('content', $pushNotification->content) ? 'edited' : '' }}" id="content" value="{{ old('content', $pushNotification->content) }}" name="content" required="true">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3" for="button_text">Button Text <span class="required">*</span></label>
                            <div class='col-md-4'>
                                <input type="text" class="form-control {{ old('button_text', $pushNotification->button_text) ? 'edited' : '' }}" id="button_text" value="{{ old('button_text', $pushNotification->button_text) }}" name="button_text" required="true">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-3 control-label">Call to Action Type <span aria-required="true" class="required"> * </span></label>
                            <div class="col-md-4">
                                <select class="form-control" name="cta_type" onchange="showProperFields()">
                                    <option value="Internal" {{ $pushNotification->cta_type === 'Internal' ? "SELECTED" : "" }}>Internal</option>
                                    <option value="External" {{ $pushNotification->cta_type === 'External' ? "SELECTED" : "" }}>External</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group" id="type_Internal">
                            <label class="control-label col-md-3" for="internal_cta_type">Internal Call to Action Type <span class="required">*</span></label>
                            <div class="col-md-4">
                                <select class="form-control" name="internal_cta_type" onchange="showProperInternalFields()">
                                    <option value="Course" {{ $pushNotification->internal_cta_type === 'Course' ? "SELECTED" : "" }}>Course</option>
                                    <option value="News" {{ $pushNotification->internal_cta_type === 'News' ? "SELECTED" : "" }}>News</option>
                                    <option value="Link" {{ $pushNotification->internal_cta_type === 'Link' ? "SELECTED" : "" }}>Link</option>
                                </select>
                            </div>
                            <br/><br/>
                            <div class="clearfix"></div>
                            <div id="type_internal_Course">
                                <label class="control-label col-md-3" for="internal_course_slug">Course <span class="required">*</span></label>
                                <div class="col-md-4">
                                    <select class="form-control" name="internal_course_slug">
                                        @foreach(\App\Models\Course::all() as $course)
                                            <option value="{{ $course->slug }}" {{ $pushNotification->internal_course_slug == $course->slug ? "SELECTED" : "" }}>{{ $course->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div id="type_internal_News">
                                <label class="control-label col-md-3" for="internal_news_slug">News <span class="required">*</span></label>
                                <div class="col-md-4">
                                    <select class="form-control" name="internal_news_slug">
                                        @foreach(\App\Models\News::orderBy('updated_at', 'desc') as $news)
                                            <option value="{{ $news->slug }}" {{ $pushNotification->internal_news_slug == $news->slug ? "SELECTED" : "" }}>{{ $news->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div id="type_internal_Link">
                                <label class="control-label col-md-3" for="internal_link">Link <span class="required">*</span></label>
                                <div class="col-md-4">
                                    <input type="text" class="form-control {{ old('internal_link', $pushNotification->internal_link) ? 'edited' : '' }}" id="internal_link" value="{{ old('internal_link', $pushNotification->internal_link) }}" name="internal_link">
                                </div>
                            </div>
                        </div>
                        <div class="form-group" id="type_External">
                            <label class="control-label col-md-3" for="external_link">External Link<span class="required">*</span></label>
                            <div class="col-md-4">
                                <input type="text" class="form-control {{ old('external_link', $pushNotification->external_link) ? 'edited' : '' }}" id="external_link" value="{{ old('external_link', $pushNotification->external_link) }}" name="external_link">
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
    jQuery(document).ready(function ($) {
        $('.input-daterange').datepicker({autoclose: true, todayBtn: 'linked'});
        $('.time-picker').timepicker({showSeconds: true});
        
        showProperFields();
        showProperInternalFields();
    });
    function showProperFields() {
        var selectValue = $('select[name="cta_type"] option:selected').text();

        $('#type_Internal, #type_External').hide();
        $('#type_' + selectValue).show();
    }
    function showProperInternalFields() {
        var selectValue = $('select[name="internal_cta_type"] option:selected').text();

        $('#type_internal_Course, #type_internal_News, #type_internal_Link').hide();
        $('#type_internal_' + selectValue).show();
    }
</script>
@endsection
