@extends('admin.layout')

@section('pagetitle')
Ads
<small>@if ($ad->admin_title) Edit Ad @else Create Ad @endif</small>
@endsection

@section('breadcrumb')
<li>
    <i class="fa fa-bookmark-o"></i>
    <a href="{{ route('ads.index') }}">Ads</a>
</li>
<li>
    <i class="fa fa-angle-right"></i>
    @if ($ad->admin_title)
    <a href="{{ route('ads.edit', ['ad' => $ad]) }}">Edit</a>
    @else
    <a href="{{ route('ads.create') }}">Add New</a>
    @endif
</li>
@endsection

@section('content')
<form action="{{ $action }}" method="post" class="form-horizontal form-bordered" enctype="multipart/form-data">
    {{csrf_field() }}
    {{ $method }}
    <div class="row">
        <div class="col-md-12">
            <div class="portlet box grey">
                <div class="portlet-title">
                    <div class="caption font-green">
                        <i class="icon-user font-green"></i>
                        <span class="caption-subject bold uppercase"> Ad Details</span>
                    </div>
                </div>
                <div class="portlet-body form">
                    <div class="form-body">
                        <div class="form-group">
                            <label class="control-label col-md-3" for="admin_title"> Admin Title<span class="required">*</span></label>
                            <div class='col-md-4'>
                                <input type="text" class="form-control {{ old('admin_title', $ad->admin_title) ? 'edited' : '' }}" id="admin_title" value="{{ old('admin_title', $ad->admin_title) }}" name="admin_title">
                            </div>
                            <br><br>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3" for="image"> Image<span class="required">*</span></label>
                            <div class='col-md-3'>
                                <div class="fileinput fileinput-new" data-provides="fileinput">
                                    <div class="fileinput-new thumbnail">
                                        <img src="{{ $ad->getPrintableImageUrl() }}">
                                    </div>
                                    <div class="fileinput-preview fileinput-exists thumbnail"></div>
                                    @if (!ends_with($ad->getPrintableImageUrl(),'default-ad-thumbnail.png'))
                                    <span class="btn default btn-file margin-top-10">
                                        <span> Change </span>
                                        <input type="file" name="image" accept="image/gif,image/jpg,image/png,image/jpeg">
                                    </span>
                                    @else
                                    <div>
                                        <span class="btn default btn-file margin-top-10">
                                            <span class="fileinput-new">Select image </span>
                                            <span class="fileinput-exists"> Change </span>
                                            <input type="file" name="image" accept="image/gif,image/jpg,image/png,image/jpeg">
                                        </span>
                                        <a href="javascript:;" class="btn red margin-top-10 fileinput-exists" data-dismiss="fileinput">Remove </a>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            <label class="control-label col-md-1" for="hover_image">Hover Image (optional)</label>
                            <div class='col-md-3'>
                                <div class="fileinput fileinput-new" data-provides="fileinput">
                                    <div class="fileinput-new thumbnail">
                                        <img src="{{ $ad->getPrintableImageUrl('hover') }}">
                                    </div>
                                    <div class="fileinput-preview fileinput-exists thumbnail"></div>
                                    @if (!ends_with($ad->getPrintableImageUrl('hover'),'default-ad-thumbnail.png'))
                                    <span class="btn default btn-file margin-top-10">
                                        <span> Change </span>
                                        <input type="file" name="hover_image" accept="image/gif,image/jpg,image/png,image/jpeg">
                                    </span>
                                    @else
                                    <div>
                                        <span class="btn default btn-file margin-top-10">
                                            <span class="fileinput-new">Select image </span>
                                            <span class="fileinput-exists"> Change </span>
                                            <input type="file" name="hover_image" accept="image/gif,image/jpg,image/png,image/jpeg">
                                        </span>
                                        <a href="javascript:;" class="btn red margin-top-10 fileinput-exists" data-dismiss="fileinput">Remove </a>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3" for="link"> Link <span class="required">*</span></label>
                            <div class='col-md-4'>
                                <p class="note">Provide full URL including any additional tracking information
                                <input type="text" class="form-control {{ old('link', $ad->link) ? 'edited' : '' }}" id="link" value="{{ old('link', $ad->link) }}" name="link"></p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3"> Location<span class="required">*</span></label>
                            <div class="col-md-4">
                                <select name="location" class="form-control">
                                    <option value="none">Select Location</option>
                                    @foreach($locations as $value => $location)
                                    <option value="{{ $value }}" {{ $ad->location == $value ? "SELECTED" : "" }}>{{ $location }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3"> Position<span class="required">*</span></label>
                            <div class="col-md-4">
                                <select name="position" class="form-control">
                                    <option value="none">Select Position</option>
                                    @foreach($positions as $value => $position)
                                    <option value="{{ $value }}" {{ $ad->position == $value ? "SELECTED" : "" }}>{{ $position }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Enabled</label>
                            <div class="col-md-9">
                                <input type="checkbox" value="1" {{ $ad->status==1 ? "CHECKED" : "" }} class="make-switch" data-size="small" name="status">
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
