@extends('admin.layout')

@section('pagetitle')
    Frontend Dashboard Banners
@endsection

@section('breadcrumb')
    <li>
        <i class="fa fa-book"></i>
        <a href="/">Dashboard banners</a>
    </li>
@endsection

@section('content')
    <form action="{{ route('banners.store') }}" method="post" class="form-horizontal form-bordered">
        {{csrf_field() }}
        {{ method_field('POST') }}
        <div class="row">
            <div class="col-md-12">
                <div class="portlet box grey">
                    <div class="portlet-title">
                        <div class="caption font-green">
                            <i class="icon-user font-green"></i>
                            <span class="caption-subject bold uppercase"> Banners</span>
                        </div>
                    </div>
                    <div class="portlet-body form">
                        <div class="form-body">
                            <div class="form-group" id="type_Lesson">
                                <label class="control-label col-md-3" for="description">Description <span class="required">*</span></label>
                                <div class="col-md-9">
                                    <textarea class="ckeditor form-control" name="content" rows="16" data-error-container="#editor2_error">{{ old('content', $banners->content ) }}</textarea>
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
