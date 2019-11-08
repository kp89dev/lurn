@extends('admin.layout')

@section('pagetitle')
    Modules <small>modules list</small>
@endsection

@section('breadcrumb')
    <li>
        <i class="icon-book-open"></i>
        <a href="{{ route('courses.index') }}">Course <b>{{ $course->title }}</b></a>
        <i class="fa fa-angle-right"></i>
    </li>
    <li>
        <i class="fa fa-book"></i>
        <a href="/">Add New Module</a>
    </li>
@endsection

@section('content')
    <div class="portlet-body">
        <!-- BEGIN FORM-->
        <form class="form-horizontal" action="{{ $action }}" method="post">
            {{ csrf_field() }}
            {{ $method }}
            <div class="form-body">
                <input type="hidden" id="order" name="order" value="{{ old('order',  $module->order) }}" class="form-control">
                <div class="form-group">
                    <label class="col-md-3 control-label">Module Title <span aria-required="true" class="required"> * </span></label>
                    <div class="col-md-4">
                        <input type="text" id="title" name="title" value="{{ old('title',  $module->title) }}" placeholder="Enter Module Title" class="form-control">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label">Type <span aria-required="true" class="required"> * </span></label>
                    <div class="col-md-4">
                        <select class="form-control" name="type" onchange="showProperField()">
                            <option value="Module" {{ $module->type === 'Module' ? "SELECTED" : "" }}>Module</option>
                            <option value="Link" {{ $module->type === 'Link' ? "SELECTED" : "" }}>Link</option>
                        </select>
                    </div>
                </div>
                <div class="form-group" id="type_Link">
                    <label class="control-label col-md-3" for="description">Link <span class="required">*</span></label>
                    <div class="col-md-9">
                        <input class="form-control" type='text' name="link" value="{{ old('link', $module->link) }}">
                    </div>
                </div>
                <div class="form-group" id="type_Module">
                    <label class="col-md-3 control-label">Module Description <span aria-required="true" class="required"> * </span></label>
                    <div class="col-md-9">
                        <textarea class="ckeditor form-control" name="description" rows="6">{{ old('description', $module->description) }}</textarea>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3">Enabled</label>
                    <div class="col-md-2">
                        <input type="checkbox" value="1" {{ $module->status == 1 ? "CHECKED" : "" }} class="make-switch" data-size="small" name="status">
                    </div>
                    <label class="control-label col-md-1">Hidden</label>
                    <div class="col-md-2">
                        <input type="checkbox" value="1" {{ $module->hidden == 1 ? "CHECKED" : "" }} class="make-switch" data-size="small" name="hidden">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label">Locked By <span aria-required="true" class="required"> * </span></label>
                    <div class="col-md-4">
                    <select class="form-control" name="locked_by_test">
                        <option value="" {{ $module->locked_by_test ? "" : "SELECTED" }}>Unlocked</option>
                        @foreach($lockableTest as $test)
                        <option value="{{ $test->id }}" {{ $module->locked_by_test === $test->id ? "SELECTED" : "" }}>{{ $test->title  }}</option>
                        @endforeach
                    </select>
                    </div>
                </div>
            </div>
            <div class="form-actions fluid">
                <div class="row">
                    <div class="col-md-offset-3 col-md-9">
                        <button class="btn btn-success btn-large btnModuleForm" id="btnModuleForm" type="submit">Save</button>
                    </div>
                </div>
            </div>
        </form>
        <!-- END FORM-->
    </div>

@endsection

@section('js')
    <script type="text/javascript" src="/assets/global/plugins/ckeditor/ckeditor.js"></script>
    <script>
        $(document).ready(function(){
            showProperField();
        });

        function showProperField() {
            var selectValue = $('select[name="type"] option:selected').text();

            $('#type_Module, #type_Link').hide();
            $('#type_'+ selectValue).show();
        }
    </script>
@endsection
