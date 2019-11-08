@extends('admin.layout')

@section('pagetitle')
    Lessons <small>lessons for module {{ $module->title }}</small>
@endsection

@section('breadcrumb')
    <li>
        <i class="icon-book-open"></i>
        <a href="{{ route('courses.index') }}">Course <b>{{ $course->title }}</b></a>
        <i class="fa fa-angle-right"></i>
    </li>
    <li>
        <i class="fa fa-book"></i>
        <a href="{{ route('modules.index', ['course' => $course->id]) }}">Module <b>{{ $module->title }}</b></a>
    </li>
    <li>
        <i class="fa fa-angle-right"></i>
        @if ($lesson->title)
            <a href="/">Edit</a>
        @else
            <a href="/">Add New Lesson</a>
        @endif
    </li>
@endsection

@section('content')
    <form action="{{ $action }}" method="post" class="form-horizontal form-bordered">
        {{csrf_field() }}
        {{ $method }}
        <input type="hidden" name="course_id" value="{{ $course->id }}">
        <input type="hidden" name="module_id" value="{{ $module->id }}">
        <input type="hidden" name="lesson_id" value="{{ $lesson->id }}">
        <div class="row">
            <div class="col-md-12">
                <div class="portlet box grey">
                    <div class="portlet-title">
                        <div class="caption font-green">
                            <i class="icon-user font-green"></i>
                            <span class="caption-subject bold uppercase"> Lesson Details</span>
                        </div>
                    </div>
                    <div class="portlet-body form">
                        <div class="form-body">
                            <div class="form-group">
                                <label class="control-label col-md-3" for="title">Title <span class="required">*</span></label>
                                <div class='col-md-9'>
                                    <input type="text" class="form-control {{ old('title', $lesson->title) ? 'edited' : '' }}" id="form_control_1" value="{{ old('title', $lesson->title) }}" name="title">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 control-label">Type <span aria-required="true" class="required"> * </span></label>
                                <div class="col-md-4">
                                    <select class="form-control" name="type" onchange="showProperField()">
                                        <option value="Lesson" {{ $lesson->type === 'Lesson' ? "SELECTED" : "" }}>Lesson</option>
                                        <option value="Link" {{ $lesson->type === 'Link' ? "SELECTED" : "" }}>Link</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group" id="type_Lesson">
                                <label class="control-label col-md-3" for="description">Description <span class="required">*</span></label>
                                <div class="col-md-9">
                                    <textarea class="ckeditor form-control" name="description" rows="6" data-error-container="#editor2_error">{{ old('description', $lesson->description) }}</textarea>
                                </div>
                            </div>
                            <div class="form-group" id="type_Link">
                                <label class="control-label col-md-3" for="description">Link <span class="required">*</span></label>
                                <div class="col-md-9">
                                    <input class="form-control" type='text' name="link" value="{{ old('link', $lesson->link) }}">
                                </div>
                            </div>
                            <br><br>
                            <div class="form-group">
                                <label class="control-label col-md-3">Enabled</label>
                                <div class="col-md-9">
                                    <input type="checkbox" value="1" {{ $lesson->status == 1 ? "CHECKED" : "" }} class="make-switch" data-size="small" name="status">
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
                    <button class="btn blue" id="preview-lesson">Preview</button>
                </div>
            </div>
        </div>
    </form>
@endsection

@section('js')
    <script type="text/javascript" src="/assets/global/plugins/ckeditor/ckeditor.js"></script>
    <script>
        $(document).ready(function(){
            showProperField();
        });

        function showProperField() {
            var selectValue = $('select[name="type"] option:selected').text();

            $('#type_Lesson, #type_Link').hide();
            $('#type_'+ selectValue).show();
        }

        $('select[name="type"]').on('change', function (e) {
            if ($(this).val() === 'Link') {
                $('#preview-lesson').prop('disabled', true);
            } else {
                $('#preview-lesson').prop('disabled', false);
            }
        });

        $('#preview-lesson').on('click', function (e) {
            e.preventDefault();

            var form = $('form').clone();
            
            form.attr({
                action: window.location.href.replace(/\/([0-9]+\/edit|create)/, '/preview'),
                method: 'post',
                target: '_blank',
                style: 'display: none'
            });

            $('input[name="_method"]', form).val('POST');
            $('textarea', form).val(CKEDITOR.instances.description.getData());
            $('body').append(form);

            form.submit();

            form.remove();
        });
    </script>
@endsection
