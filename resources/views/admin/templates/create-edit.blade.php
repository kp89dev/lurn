@extends('admin.layout')

@section('pagetitle')
    Templates
@endsection

@section('breadcrumb')
    <li>
        <i class="book-open"></i>
        <a href="{{ route('templates.index') }}">Templates</a>
    </li>
    <li>
        <i class="icon-edit"></i>
        @if ($template->title)
            <a href="{{ route('templates.edit', $template->id) }}">Edit</a>
        @else
            <a href="{{ route('templates.create') }}">Add Template</a>
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
                            <span class="caption-subject bold uppercase"> Template Details</span>
                        </div>
                    </div>
                    <div class="portlet-body form">
                        <div class="form-body">
                            <div class="form-group">
                                <label class="control-label col-md-3" for="title">Title <span class="required">*</span></label>
                                <div class='col-md-9'>
                                    <input type="text" name="title" value="{{ old('title', $template->title) }}"
                                           class="form-control {{ old('title', $template->title) ? 'edited' : '' }}">
                                </div>
                                <br>
                                <br>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3" for="subject">Subject Line <span class="required">*</span></label>
                                <div class='col-md-9'>
                                    <input type="text" name="subject" value="{{ old('subject', $template->subject) }}"
                                           class="form-control {{ old('subject', $template->subject) ? 'edited' : '' }}">
                                </div>
                                <br>
                                <br>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3" for="variables">Variables</label>
                                <div class="col-md-9">
                                    <p class="note">
                                        $$USERNAME$$ &mdash; the user's full name<br>
                                        $$FIRSTNAME$$ &mdash; the user's first name<br>
                                    </p>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-md-3" for="content">Content <span class="required">*</span></label>
                                <div class="col-md-9">
                                    <textarea class="ckeditor form-control" name="content" rows="6"
                                              data-error-container="#editor2_error">
                                        @if (empty(old('content', $template->content)))
                                            Hi $$FIRSTNAME$$,
                                        @else
                                            {{ old('content', $template->content) }}
                                        @endif        
                                    </textarea>
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
                    <button class="btn green" id="show-preview" data-toggle="modal" data-target="#preview-modal">Preview</button>
                    <input type="submit" class="btn blue" value="Save">
                </div>
            </div>
        </div>
    </form>

    <div class="modal fade" id="preview-modal" tabindex="-1" role="dialog" aria-labelledby="preview-modal-label" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="preview-modal-label">Modal title</h4>
                </div>
                <div class="modal-body">
                    <iframe src="{{ route('templates.preview') }}" id="preview-content" style="width: 100%; height: 500px; border: 0"></iframe>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script type="text/javascript" src="/assets/global/plugins/ckeditor/ckeditor.js"></script>
    <script type="text/javascript">
        $(function () {
            $('#show-preview').on('click', function (e) {
                e.preventDefault();
                
                $('#preview-content').contents().find('html').html('testing')
                
                var content = CKEDITOR.instances.content.getData();

                $.post("{{ route('templates.preview') }}", {content: content}, function (content) {
                    $('#preview-content').contents().find('html').html(content);
                });
            });
        });
    </script>
@endsection
