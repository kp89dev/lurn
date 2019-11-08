@extends('admin.layout')

@section('pagetitle')
    FAQ
    <small>frequently asked questions</small>
@endsection

@section('breadcrumb')
    <li>
        <i class="icon-question"></i>
        <a href="{{ route('faq.index') }}">FAQ</a>
    </li>
    <li>
        <i class="icon-edit"></i>
        @if ($faq->question)
            <a href="{{ route('faq.edit', $faq->id) }}">Edit</a>
        @else
            <a href="{{ route('faq.create') }}">Add New</a>
        @endif
    </li>
@endsection

@section('content')
    <form action="{{ $action }}" method="post" class="form-horizontal form-bordered">
        {{csrf_field() }}
        {{ $method }}
        <div class="row">
            <div class="col-md-12">
                <div class="portlet box grey">
                    <div class="portlet-title">
                        <div class="caption font-green">
                            <i class="icon-user font-green"></i>
                            <span class="caption-subject bold uppercase"> FAQ Details</span>
                        </div>
                    </div>
                    <div class="portlet-body form">
                        <div class="form-body">
                            <div class="form-group">
                                <label class="control-label col-md-3" for="question">Question <span class="required">*</span></label>
                                <div class='col-md-9'>
                                    <input type="text" name="question" value="{{ old('question', $faq->question) }}"
                                           class="form-control {{ old('question', $faq->question) ? 'edited' : '' }}">
                                </div>
                                <br><br>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-md-3" for="answer">Answer <span class="required">*</span></label>
                                <div class="col-md-9">
                                    <textarea class="ckeditor form-control" name="answer" rows="6"
                                              data-error-container="#editor2_error">{{ old('answer', $faq->answer) }}</textarea>
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
