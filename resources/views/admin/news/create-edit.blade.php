@extends('admin.layout')

@section('pagetitle')
    News
@endsection

@section('breadcrumb')
    <li>
        <i class="book-open"></i>
        <a href="{{ route('news.index') }}">News</a>
    </li>
    <li>
        <i class="icon-edit"></i>
        @if ($article->title)
            <a href="{{ route('news.edit', $article->id) }}">Edit</a>
        @else
            <a href="{{ route('news.create') }}">Add New</a>
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
                            <span class="caption-subject bold uppercase"> News Details</span>
                        </div>
                    </div>
                    <div class="portlet-body form">
                        <div class="form-body">
                            <div class="form-group">
                                <label class="control-label col-md-3" for="title">Title <span class="required">*</span></label>
                                <div class='col-md-9'>
                                    <input type="text" name="title" value="{{ old('title', $article->title) }}"
                                           class="form-control {{ old('title', $article->title) ? 'edited' : '' }}">
                                </div>
                                <br><br>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-md-3" for="answer">Content <span class="required">*</span></label>
                                <div class="col-md-9">
                                    <textarea class="ckeditor form-control" name="content" rows="6"
                                              data-error-container="#editor2_error">{{ old('content', $article->content) }}</textarea>
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
