@extends('admin.layout')

@section('pagetitle')
    Badge
@endsection

@section('breadcrumb')
    <li>
        <i class="book-open"></i>
        <a href="{{ route('badges.index', compact('course')) }}">Badge</a>
    </li>
    <li>
        <i class="icon-edit"></i>
        @if ($badge->title)
            <a href="{{ route('badges.edit', compact('course', 'badge')) }}">Edit</a>
        @else
            <a href="{{ route('badges.create', compact('course')) }}">Add New</a>
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
                            <span class="caption-subject bold uppercase"> Badge Details</span>
                        </div>
                    </div>
                    <div class="portlet-body form">
                        <div class="form-body">
                            <div class="form-group">
                                <label class="control-label col-md-3" for="title">Title <span class="required">*</span></label>
                                <div class='col-md-9'>
                                    <input type="text" name="title" value="{{ old('title', $badge->title) }}"
                                           class="form-control {{ old('title', $badge->title) ? 'edited' : '' }}">
                                </div>
                                <br><br>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3" for="title">Image <span class="required">*</span></label>
                                <div class='col-md-9'>
                                    <div class="fileinput fileinput-new" data-provides="fileinput">
                                        <div class="fileinput-new thumbnail" style="width: 150px; height: 150px;">
                                            @if ($badge->src)
                                                <img src="{{ $badge->src }}" width="100">
                                            @else
                                                <img src="http://www.placehold.it/150x150/EFEFEF/AAAAAA&amp;text=no+image" alt="">
                                            @endif
                                        </div>
                                        <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 150px; max-height: 150px;"></div>
                                        <div>
                                            <span class="btn default btn-file">
                                                <span class="fileinput-new">Select image </span>
                                                <span class="fileinput-exists"> Change </span>
                                                <input type="file" name="image" accept="image/gif,image/jpg,image/png,image/jpeg">
                                            </span>
                                            <a href="javascript:;" class="btn red fileinput-exists" data-dismiss="fileinput">Remove </a>
                                        </div>
                                        <div class="clearfix margin-top-10">
												<span class="label label-danger">NOTE! </span>&nbsp; Images wont be resized. Please don't upload images larger than 150x150
                                        </div>
                                    </div>
                                </div>
                                <br><br>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3" for="answer">Content <span class="required">*</span></label>
                                <div class="col-md-9">
                                    <textarea class="ckeditor form-control" name="content" rows="6"
                                              data-error-container="#editor2_error">{{ old('content', $badge->content) }}</textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3">Enabled</label>
                                <div class="col-md-9">
                                    <input type="checkbox" value="1" {{ $badge->status == 1 ? "CHECKED" : "" }} class="make-switch" data-size="small" name="status">
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
@endsection
