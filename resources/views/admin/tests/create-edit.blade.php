@extends('admin.layout')

@section('pagetitle')
    Tests <small>tests for {{ $course->title }}</small>
@endsection

@section('breadcrumb')
    <li>
        <i class="icon-book-open"></i>
        <a href="{{ route('courses.index') }}">Course <b>{{ $course->title }}</b></a>
        <i class="fa fa-angle-right"></i>
    </li>
    <li>
        <i class="fa fa-book"></i>
    @if($test->title)
        <a href="/">Edit Test</a>
    @else
        <a href="/">Add New Test</a>
    @endif
    </li>
@endsection

@section('content')
    <div class="portlet-body">
        <!-- BEGIN FORM-->
        <form class="form-horizontal" action="{{ $action }}" method="post" enctype="multipart/form-data">
            {{ csrf_field() }}
            {{ $method }}
            <div class="form-body">
                <div class="form-group">
                    <label class="col-md-3 control-label">Test Title <span aria-required="true" class="required"> * </span></label>
                    <div class="col-md-4">
                        <input type="text" id="title" name="title" value="{{ old('title',  $test->title) }}" placeholder="Enter Test Title" class="form-control">
                    </div>
                </div>
                @if ($lessons->count() > 0)
                    <div class="form-group">
                        <label class="col-md-3 control-label">Lesson <span aria-required="true" class="required"> * </span></label>
                        <div class="col-md-4">
                            <select class="form-control" name="after_lesson_id">
                            @foreach($lessons as $lesson)
                                @if($lesson->id == $test->after_lesson_id)
                                <option value="{{$lesson->id}}" selected="selected">{{$lesson->title}}</option>
                                @else
                                <option value="{{$lesson->id}}">{{$lesson->title}}</option>
                                @endif
                            @endforeach
                            </select>
                        </div>
                    </div>
                @endif
                @if ($course->certificates->count() > 0) 
                    <div class="form-group">
                        <label class="col-md-3 control-label">Certificate </label>
                        <div class="col-md-4">
                            <select class="form-control" name="certificate_id">
                                <option value="">No Certificate</option>
                                @foreach($course->certificates as $certificate)
                                    @if($certificate->id == $test->certificate_id)
                                    <option value="{{$certificate->id}}" selected="selected">{{$certificate->title}}</option>
                                    @else
                                    <option value="{{$certificate->id}}">{{$certificate->title}}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                @endif
                <div class="form-group">
                    <label class="control-label col-md-3">Enabled</label>
                    <div class="col-md-9">
                        <input type="checkbox" value="1" {{ $test->status == 1 ? "CHECKED" : "" }} class="make-switch" data-size="small" name="status">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3">Custom Completion</label>
                    <div class="col-md-2">
                        <input type="checkbox" value="1" {{ $test->custom_completion_status==1 ? "CHECKED" : "" }} class="make-switch" data-size="small" name="custom_completion_status">
                    </div>
                </div>               
                <div id="custom_completion_form_group" style="display:{{ $test->custom_completion_status==1 ? "" : "none" }}">
                    <div class="form-group">
                        <label class="control-label col-md-3" for="custom_completion_background">Background Image</label>
                        <div class='col-md-4'>
                            <div class="fileinput fileinput-new" data-provides="fileinput">
                                <div class="fileinput-new thumbnail" style="width: 100%; height: auto;">
                                    @if ($test->getSrc('custom_completion_background') && $test->getSrc('custom_completion_background') !== '/static/')
                                        <img src="{{ $test->getSrc('custom_completion_background') }}" width="100%">
                                    @else
                                        <img src="http://www.placehold.it/150x150/EFEFEF/AAAAAA&amp;text=no+image" alt="" width="100%">
                                    @endif
                                </div>
                                <div class="fileinput-preview fileinput-exists thumbnail" style="width: 100%; height: auto;"></div>
                                @if ($test->getSrc('custom_completion_background') && $test->getSrc('custom_completion_background') !== '/static/')
                                    <div class="clearfix margin-top-10">
                                        <span class="btn default btn-file margin-top-10">
                                            <span class="fileinput-new"> Change </span>
                                            <input type="file" name="custom_completion_background" accept="image/gif,image/jpg,image/png,image/jpeg">
                                        </span>
                                        <a href="javascript:;" class="btn red margin-top-10 fileinput-exists" data-dismiss="fileinput">Remove </a>
                                        <a href="{{route('tests.removeImage',['course'=>$course->id,'test'=>$test->id,'image'=>'custom_completion_background'])}}" class="btn red margin-top-10 fileinput-new"> Remove </a>
                                    </div>
                                @else
                                    <div class="clearfix margin-top-10">
                                        <span class="btn default btn-file margin-top-10">
                                            <span class="fileinput-new">Select image </span>
                                            <span class="fileinput-exists"> Change </span>
                                            <input type="file" name="custom_completion_background" accept="image/gif,image/jpg,image/png,image/jpeg">
                                        </span>
                                        <a href="javascript:;" class="btn red margin-top-10 fileinput-exists" data-dismiss="fileinput">Remove </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3" for="custom_completion_header">Header Image</label>
                        <div class='col-md-2'>
                            <div class="fileinput fileinput-new" data-provides="fileinput">
                                <div class="fileinput-new thumbnail" style="width: 100%; height: auto;">
                                    @if ($test->getSrc('custom_completion_header') && $test->getSrc('custom_completion_header') !== '/static/')
                                        <img src="{{ $test->getSrc('custom_completion_header') }}" width="100%">
                                    @else
                                        <img src="http://www.placehold.it/150x150/EFEFEF/AAAAAA&amp;text=no+image" alt="" width="100%">
                                    @endif
                                </div>
                                <div class="fileinput-preview fileinput-exists thumbnail" style="width: 100%; height: auto;"></div>
                                @if ($test->getSrc('custom_completion_header') && $test->getSrc('custom_completion_header') !== '/static/')
                                    <div class="clearfix margin-top-10">
                                        <span class="btn default btn-file margin-top-10">
                                            <span class="fileinput-new"> Change </span>
                                            <input type="file" name="custom_completion_header" accept="image/gif,image/jpg,image/png,image/jpeg">
                                        </span>
                                        <a href="javascript:;" class="btn red margin-top-10 fileinput-exists" data-dismiss="fileinput">Remove </a>
                                        <a href="{{route('tests.removeImage',['course'=>$course->id,'test'=>$test->id,'image'=>'custom_completion_header'])}}" class="btn red margin-top-10 fileinput-new"> Remove </a>
                                    </div>
                                @else
                                    <div class="clearfix margin-top-10">
                                        <span class="btn default btn-file margin-top-10">
                                            <span class="fileinput-new">Select image </span>
                                            <span class="fileinput-exists"> Change </span>
                                            <input type="file" name="custom_completion_header" accept="image/gif,image/jpg,image/png,image/jpeg">
                                        </span>
                                        <a href="javascript:;" class="btn red margin-top-10 fileinput-exists" data-dismiss="fileinput">Remove </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <label class="control-label col-md-3" for="custom_completion_badge">Badge Image</label>
                        <div class='col-md-2'>
                            <div class="fileinput fileinput-new" data-provides="fileinput">
                                <div class="fileinput-new thumbnail" style="width: 100%; height: auto;">
                                    @if ($test->getSrc('custom_completion_badge') && $test->getSrc('custom_completion_badge') !== '/static/')
                                        <img src="{{ $test->getSrc('custom_completion_badge') }}" width="100%">
                                    @else
                                        <img src="http://www.placehold.it/150x150/EFEFEF/AAAAAA&amp;text=no+image" alt="" width="100%">
                                    @endif
                                </div>
                                <div class="fileinput-preview fileinput-exists thumbnail" style="width: 100%; height: auto;"></div>
                                @if ($test->getSrc('custom_completion_badge') && $test->getSrc('custom_completion_badge') !== '/static/')
                                    <div class="clearfix margin-top-10">
                                        <span class="btn default btn-file margin-top-10">
                                            <span class="fileinput-new"> Change </span>
                                            <input type="file" name="custom_completion_badge" accept="image/gif,image/jpg,image/png,image/jpeg">
                                        </span>
                                        <a href="javascript:;" class="btn red margin-top-10 fileinput-exists" data-dismiss="fileinput">Remove </a>
                                        <a href="{{route('tests.removeImage',['course'=>$course->id,'test'=>$test->id,'image'=>'custom_completion_badge'])}}" class="btn red margin-top-10 fileinput-new"> Remove </a>
                                    </div>
                                @else
                                    <div class="clearfix margin-top-10">
                                        <span class="btn default btn-file margin-top-10">
                                            <span class="fileinput-new">Select image </span>
                                            <span class="fileinput-exists"> Change </span>
                                            <input type="file" name="custom_completion_badge" accept="image/gif,image/jpg,image/png,image/jpeg">
                                        </span>
                                        <a href="javascript:;" class="btn red margin-top-10 fileinput-exists" data-dismiss="fileinput">Remove </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    
                    
                    <div class="form-group">
                        <label class="control-label col-md-3" for="custom_completion_style">Style</label>
                        <div class='col-md-9'>
                            @if(old('custom_completion_style', $test->custom_completion_style))
                                <textarea name="custom_completion_style" class="form-control edited" rows="10">{{$test->custom_completion_style}}</textarea>
                            @else
                                <textarea name="custom_completion_style" class="form-control" rows="10">#ccbox{width:100%;margin-top:40px;text-align:center;position:relative;}
#ccbox .ccbox-header{width:100%;min-height:100px;height:auto;text-align:center;position:relative;background-color:rgba(277,48,98,0.65); background-size: contain; background-repeat: no-repeat;background-position: center;}
#ccbox .ccbox-content{}
#ccbox .ccbox-content h1 {color:#CCD454; font-size: 4em;}
#ccbox .ccbox-badge{height:65px;width:100%;background-repeat:no-repeat;background-position:right center;}</textarea>
                            @endif
                        </div>
                        <br><br>
                    </div>
                    
                    
                    
                    
                    
                    <div class="form-group">
                        <label class="control-label col-md-3" for="custom_completion_body">Body</label>
                        <div class="col-md-9">
                            <textarea class="ckeditor form-control" name="custom_completion_body" rows="10">{{ old('custom_completion_body', $test->custom_completion_body) }}</textarea>
                        </div>
                    </div> 
                </div>
                <div class="row">
                    <div class="col-md-offset-3 col-md-9">
                        <button class="btn btn-success btn-large">Save</button> 
                        <a href="{{url()->current()}}" class="btn btn-danger btn-large">Cancel</a>
                    </div>
                </div>
            </div>
        </form>
        <!-- END FORM-->
        @if($test->title)
        <div class="portlet box blue" style="margin-top:25px;">
            <div class="tools" style="float:right;margin-top:10px;margin-right:10px;background-color:#fff;padding:3px 6px;">
                <a href="#" class="newQuestionBtn"><i class="fa fa-plus"> </i> Add New Question </a>
            </div>
            <div class="portlet-title">
                <div class="caption">
                    <span><i class="fa fa-question-circle"> </i> Questions </span>
                </div>
                <div class="tools" style="float:right;margin-top:10px;margin-right:10px;background-color:#fff;padding:3px 6px;">
                    <a href="{{ route('tests.show', compact('course', 'test')) }}" class="newQuestionBtn"><i class="fa fa-eye"> </i> View</a>
                </div>
            </div>
        </div>
        <div class="portlet-body form form-horizontal" id="questions">
            @foreach($test->getOrderedQuestions() as $i => $question)
            <div class="well question" id="question-{{$question->id}}">
                Question {{$i + 1}}: {{$question->title}}
                <span class="pull-right">
                    <a class="btn btn-sm blue table-group-action-submit editQuestion" href="#" data-question-id="{{$question->id}}" data-question-no="{{$i + 1}}">
                        <i class="fa fa-pencil"> </i>
                    </a>
                    <form action="{{ route('tests.delete.question', ['test' => $test, 'questionId' => $question->id]) }}" style="display: inline" method="POST">
                        {{ method_field('DELETE') }}
                        {{ csrf_field() }}
                        <a onclick="$(this).closest('form').submit()" class="btn btn-sm red table-group-action-submit"><i class="fa fa-trash"></i></a>
                    </form>
                </span>
            </div>
            @endforeach
        </div>
        @endif
    </div>
@endsection

@section('js')
<script type="text/javascript" src="/assets/global/plugins/ckeditor/ckeditor.js"></script>
<script type="text/javascript">
jQuery(document).ready(function($){
    $("[name='custom_completion_status']").bootstrapSwitch({
        onSwitchChange: function(e, state) {
          if (state){
              $('#custom_completion_form_group').show('slow');
          }else{
              $('#custom_completion_form_group').hide('slow');
          }
        }
    });     
});
</script>
@if($test->id)
<script type="text/javascript">
jQuery(document).ready(function($){
    
    $('a.editQuestion').click( function(evt){
        evt.preventDefault();
        var qID = $(evt.target).closest('a.editQuestion').data('question-id');
        if ($('#q_edit_'+qID).length > 0){
            $(evt.target).closest('.well.question').find('#q_edit_'+qID).detach();
        }else{
            $.ajax({
                dataType: "json",
                url: "{{ route('tests.edit.question', ['test' => $test]) }}/"+$(evt.target).closest('a.editQuestion').data('question-id'),
                success: function(data){
                    var elem = getEditForm($(evt.target).closest('a.editQuestion').data('question-no'), data.id, data.title, data.question_type, data.answers);

                    $(evt.target).closest('.well.question').append(elem);
                }
            });
        }  
    });

    $('a.newQuestionBtn').click(function(evt){
        
        var elem = getEditForm($(evt.target).closest('a.editQuestion').data('question-no'), "", "", 'Radio');
        elem = '<div class="well question">'+elem+'</div>';
        
        $('#questions').prepend($(elem));
    });

    $('.portlet-body').on('click', 'a.addAnswerOption', function(evt){
        evt.preventDefault();
        evt.stopImmediatePropagation();
        
        var type = $(evt.target).closest('.form-body').find(':selected').val() || 'Radio';
        var elem = $(getAnswerRow('', type, 0, $(evt.target).closest('.answersDiv')
                .find('.answerOptionDiv').last().data('answer-no')+1));
        $(elem).insertAfter($(evt.target).closest('.answersDiv').find('.answerOptionDiv').last());
       
    });

    $('.portlet-body').on('click', '.removeAnswerOption', function(evt) {
        $(evt.target).closest('.answerOptionDiv').remove();
    });

    $('.portlet-body').on('change', 'select[name=question_type]', function(evt) {
        if($(evt.target).val() == 'Checkbox') {
            $(evt.target).closest('.form-body')
                .find('.answersDiv input[type=radio]').attr('type', 'checkbox');
        } else {
            $(evt.target).closest('.form-body')
                .find('.answersDiv input[type=checkbox]')
                .attr('type', 'radio')
                .each(function(index, elem){
                    $(elem).prop('checked', false);
                });
        }
    });

    $('.portlet-body').on('click', '.submitNewQuestion', function(evt) {
        evt.preventDefault();
        if( ! $(evt.target).closest('form').find('textarea.title').val()) {
            alert('You must include question text!');
            return false;
        }

        if( $(evt.target).closest('form').find('input.answerOption').each(function(){
            if( ! $(this).val()) {
                alert('All answers must have a text value!');
                return false;
            }
        }));
        if( $(evt.target).closest('form').find('.isAnswer:checked').length <= 0 ) {
            alert('You must select a correct answer!');
            return false;
        }

        $(evt.target).closest('form').submit();        
    });

    $('.portlet-body').on('click','input[type=radio]', function(evt){
        $(evt.target).closest('form').find('input[type="radio"]').each(function(){
            $(this).prop('checked',false);
        });
        $(this).prop('checked',true);        
    });    
});

function getEditForm(qNo, id, title, type, answers = false){
    var iType = (type == "Radio")? "radio" : "checkbox";

    var typeRadio = (iType == "radio") ? ' selected' : '';
    var typeCheckbox = (iType == "checkbox") ? ' selected' : '';
    
    var questionListHtml = '<form method="POST" action="{{route('tests.create.question', ['test' => $test->id])}}" id="q_edit_'+id+'" name="q_edit_'+id+'">';
    questionListHtml += '<div class="form-body"><div class="form-group">';
    questionListHtml += '{{ csrf_field() }}';
    questionListHtml += '<input type="hidden" name="question_id" value="'+id+'">';
    questionListHtml += '<div class="controls text-warning">';
    questionListHtml += '*Choose correct answer by selecting respective radio button of correct answer.';
    questionListHtml += '</div>';
    questionListHtml += '</div>';
    
    questionListHtml += '<div class="form-group">';
    questionListHtml += '<label class="col-md-3 control-label">Question '+qNo+'</label>';
    questionListHtml += '<div class="col-md-4">';
    questionListHtml += '<textarea name="title" class="form-control title" cols="35" rows="2" style="width: 452px; height: 55px;">'+title+'</textarea>';
    questionListHtml += '</div>';
    questionListHtml += '</div>';
    
    questionListHtml += '<div class="form-group">';
    questionListHtml += '<label class="col-md-3 control-label">Question Type</label>';
    questionListHtml += '<div class="col-md-4">';
    questionListHtml += '<select name="question_type">';
    questionListHtml += '<option value="Radio"'+typeRadio+'>Radio</option>';
    questionListHtml += '<option value="Checkbox"'+typeCheckbox+'>Checkbox</option>';
    questionListHtml += '</select>';
    questionListHtml += '</div>';
    questionListHtml += '</div>';
    
    
    questionListHtml += '<div class="answersDiv">';
    questionListHtml += '<div class="form-group">';
    questionListHtml += '<label class="col-md-3 control-label">Question '+qNo+'</label>';
    questionListHtml += '<div class="col-md-4">';
    
    questionListHtml += '</div>';
    if(answers) {
        for( var i = 0, len = answers.length; i < len; i++) {
            var isAnswer = (answers[i].is_answer == 1)? true:false;
            questionListHtml += getAnswerRow(answers[i].title, iType, answers[i].is_answer, i, answers[i].id);
        }
    } else {
        questionListHtml += getAnswerRow('', iType, 0, 0, '');
    }
    questionListHtml += '</div>';
    questionListHtml += '<div class="control-group">';
    questionListHtml += '<label class="control-label"></label>';
    questionListHtml += '<div class="controls">';
    questionListHtml += '<a href="#" class="addAnswerOption">Add One More Option</a>';
    questionListHtml += '</div>';
    questionListHtml += '</div>'; 
    
    questionListHtml += '<div class="control-group">';
    questionListHtml += '<label class="control-label"></label>';
    questionListHtml += '<div class="controls">';
    questionListHtml += '<a href="javascript:;" class="btn btn-success btn-large submitNewQuestion" rel="'+id+'" data-no="'+qNo+'">Save</a>&nbsp;&nbsp;';
    if(id==""){
        questionListHtml += '<a href="javascript:;" class="btn btn-danger btn-large cancelNewQuestion" rel="'+id+'" data-no="'+qNo+'">Cancel</a>';
    }
    questionListHtml += '</div>';
    questionListHtml += '</div></div></form>';

    return questionListHtml;
}

function getAnswerRow(title, iType, isAnswer, count, id ='') {
    var answerRow = '<div class="form-group answerOptionDiv" data-answer-no="'+count+'">';
    isAnswer = (isAnswer)? ' checked':'';
    answerRow += '<radiogroup>';
    answerRow += '<label class="col-md-3 control-label"></label>';
    answerRow += '<div class="col-md-7">';
    answerRow += '<input type="'+iType.toLowerCase()+'" class="isAnswer icheck" name="answers['+count+'][is_answer]" value="1"'+isAnswer+'>&nbsp;&nbsp;';
    answerRow += '<input type="text" value="'+title +'" id="answerOption" name="answers['+count+'][title]" placeholder="" data-value="" class="answerOption">';
    answerRow += '<a href="javascript:;"><i class="glyphicon glyphicon-remove removeAnswerOption"></i></a>';
    answerRow += '<input type="hidden" name="answers['+count+'][id]" value="'+id+'">';
    answerRow += '</div></div></radiogroup>';

    return answerRow;
}

function getAnswerDisplay(title, qNo, id) {
    var answerDisplay = '<div class="well question">';
    answerDisplay += 'Question '+qNo+': '+title;
    answerDisplay += '<span class="pull-right">';
    answerDisplay += '<a class="btn btn-sm blue table-group-action-submit editQuestion" href="#" data-question-id="'+id+'" data-question-no="'+qNo+'">';
    answerDisplay += '<i class="fa fa-pencil"> </i>';
    answerDisplay += '</a>';
    answerDisplay += '<form action="{{ route('tests.delete.question', ['test' => $test]) }}" style="display: inline" method="POST">';
    answerDisplay += '{{ method_field('DELETE') }}';
    answerDisplay += '{{ csrf_field() }}';
    answerDisplay += '<a onclick="$(this).closest(\'form\').submit()" class="btn btn-sm red table-group-action-submit"><i class="fa fa-trash"></i></a>';
    answerDisplay += '</form>';
    answerDisplay += '</span>';
    answerDisplay += '</div>';
}

</script>
@endif
@endsection
