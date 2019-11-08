@extends('admin.layout')

@section('pagetitle')
    Categories
    <small>courses categories</small>
@endsection

@section('breadcrumb')
    <li>
        <i class="icon-tag"></i>
        <a href="{{ route('categories.index') }}">Categories</a>
    </li>
    <li>
        <i class="icon-edit"></i>
        @if ($category->name)
            <a href="{{ route('categories.edit', $category->id) }}">Edit</a>
        @else
            <a href="{{ route('categories.create') }}">Add New</a>
        @endif
    </li>
@endsection

@section('content')
    <form action="{{ $action }}" method="post" class="form-horizontal form-bordered" enctype="multipart/form-data">
        @csrf
        {{ $method }}

        <div class="row">
            <div class="col-md-12">
                <div class="portlet box grey">
                    <div class="portlet-title">
                        <div class="caption font-green">
                            <i class="icon-user font-green"></i>
                            <span class="caption-subject bold uppercase"> Category Details</span>
                        </div>
                    </div>
                    <div class="portlet-body form">
                        <div class="form-body">
                            <div class="form-group">
                                <label class="control-label col-md-3" for="thumbnail">
                                    Thumbnail <span class="required">*</span>
                                </label>
                                <div class='col-md-4'>
                                    <div class="fileinput fileinput-new" data-provides="fileinput">
                                        <style>.fileinput-preview img { width: 260px }</style>
                                        <div class="fileinput-preview fileinput-new thumbnail">
                                            <img src="{{ $category->getPrintableImageUrl() }}">
                                        </div>
                                        <div class="fileinput-preview fileinput-exists thumbnail"></div>

                                        @if (ends_with($category->getPrintableImageUrl(), 'images/onboarding/ob-default.png'))
                                            <div>
                                                <span class="btn default btn-file margin-top-10">
                                                    <span class="fileinput-new">Select image</span>
                                                    <span class="fileinput-exists"> Change</span>
                                                    <input type="file" name="thumbnail"
                                                           accept="image/gif,image/jpg,image/png,image/jpeg">
                                                </span>
                                                <a href="javascript:;" class="btn red margin-top-10 fileinput-exists"
                                                   data-dismiss="fileinput">Remove</a>
                                            </div>
                                        @else
                                            <span class="btn default btn-file margin-top-10">
                                                <span> Change </span>
                                                <input type="file" name="thumbnail"
                                                       accept="image/gif,image/jpg,image/png,image/jpeg">
                                            </span>
                                        @endif
                                    </div>

                                    <p><em>The thumbnail will be fit into a 16:9 aspect ratio.<br>For best results, use 640 x 360.</em></p>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-md-3" for="name">
                                    Category <span class="required">*</span>
                                </label>
                                <div class='col-md-9'>
                                    <input type="text" name="name" value="{{ old('name', $category->name) }}"
                                           class="form-control {{ old('name', $category->name) ? 'edited' : '' }}">
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
