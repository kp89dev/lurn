@extends('admin.layout')

@section('pagetitle')
    Surveys
@endsection

@section('breadcrumb')
    <li>
        <i class="book-open"></i>
        <a href="{{ route('surveys.index') }}">Surveys</a>
    </li>
    <li>
        <i class="icon-edit"></i>
        @if ($survey->title)
            <a href="{{ route('surveys.edit', $survey->id) }}">Edit</a>
        @else
            <a href="{{ route('surveys.create') }}">Add Survey</a>
        @endif
    </li>
@endsection

@section('content')
    <form action="{{ $action }}" method="post" class="form-horizontal form-bordered">
        {{ csrf_field() }}
        {{ $method }}

        <input type="hidden" name="question_data" value="">

        <div class="row">
            <div class="col-md-12">
                <div class="portlet box grey">
                    <div class="portlet-title">
                        <div class="caption font-green">
                            <i class="icon-settings font-green"></i>
                            <span class="caption-subject bold uppercase"> Survey Details</span>
                        </div>
                    </div>

                    <div class="portlet-body form">
                        <div class="form-body">
                            <div class="form-group">
                                <label class="control-label col-md-3" for="title">Title <span class="required">*</span></label>
                                <div class='col-md-9'>
                                    <input type="text" name="title" value="{{ old('title', $survey->title) }}"
                                           class="form-control {{ old('title', $survey->title) ? 'edited' : '' }}">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-md-3" for="description">Description <span class="required">*</span></label>
                                <div class='col-md-9'>
                                    <input type="text" name="description" value="{{ old('description', $survey->description) }}"
                                           class="form-control {{ old('description', $survey->description) ? 'edited' : '' }}">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-md-3" for="survey_type">Survey Type</label>
                                <div class='col-md-9'>
                                    <select name="survey_type_id" class="form-control">
                                        @foreach ($surveyTypes as $type => $title)
                                            <option value="{{ $type }}" {{ $survey->survey_type_id == $type ? 'selected' : '' }}>{{ $title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-md-3" for="survey_type">Survey Trigger Type</label>
                                <div class='col-md-9'>
                                    <select name="survey_trigger_type_id" class="form-control">
                                        @foreach ($surveyTriggerTypes as $type => $title)
                                            <option value="{{ $type }}" {{ $survey->survey_trigger_type_id == $type ? 'selected' : '' }}>{{ $title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-md-3" for="survey_type">Survey Question Ordering</label>
                                <div class='col-md-9'>
                                    <select name="survey_question_ordering_id" class="form-control">
                                        @foreach ($surveyQuestionOrderings as $type => $title)
                                            <option value="{{ $type }}" {{ $survey->survey_question_ordering_id == $type ? 'selected' : '' }}>{{ $title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="input-daterange">
                                <div class="form-group">
                                    <label class="control-label col-md-3" for="start_date">Available From</label>
                                    <div class="col-md-4">
                                        <input type="text" class="form-control" id="start_date" name="start_date"
                                        @if ($survey->start_date)
                                            value="{{ $survey->start_date->format('m/d/Y') }}"
                                        @else
                                            value="{{ old('start_date') }}"
                                        @endif
                                        >
                                    </div>
                                
                                    <label class="control-label col-md-1 until" for="end_date">Until</label>

                                    <div class="col-md-4">
                                        <input type="text" class="form-control" id="end_date" name="end_date"
                                        @if ($survey->end_date)
                                            value="{{ $survey->end_date->format('m/d/Y') }}"
                                        @else
                                            value="{{ old('end_date') }}"
                                        @endif
                                        >
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-md-3" for="require_login">Login Required</label>
                                <div class='col-md-9'>
                                    <input type="checkbox" value="1" {{ is_null($survey->require_login) || $survey->require_login ? "checked" : "" }} class="make-switch" data-size="small" name="require_login">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-md-3" for="enabled">Enabled</label>
                                <div class='col-md-9'>
                                    <input type="checkbox" value="1" {{ is_null($survey->enabled) || $survey->enabled ? "checked" : "" }} class="make-switch" data-size="small" name="enabled">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="portlet box grey">
                    <div class="portlet-title">
                        <div class="caption font-green">
                            <i class="icon-user font-green"></i>
                            <span class="caption-subject bold uppercase"> Questions &amp; Answers</span>
                        </div>
                    </div>
                    
                    <div class="portlet-body form">
                        <div class="form-body sortable list question-list">
                            @foreach ($survey->questions as $question)
                                <div class="form-group sortable-item question-item">
                                    <label class="control-label col-md-3"><button class="btn question-handle"><i class="fa fa-bars"></i></button></label>
                                    <div class="col-md-7">
                                        <input type="hidden" name="question_id[]" value="{{ $question->id }}">

                                        <div class="input-group">
                                            <input type="text" name="question_title[]" value="{{ $question->title }}" class="form-control" placeholder="Enter question">
                                            <div class="input-group-addon btn remove-question">
                                                <i class="fa fa-trash"></i>
                                            </div>
                                        </div>

                                        <div class="input-group">
                                            <select name="question_answer_choice[]" class="form-control" style="margin-top: 1em;">
                                                <option value="single" {{ $question->answer_choice == 'single' ? 'selected' : '' }}>Single Answer</option>
                                                <option value="multiple" {{ $question->answer_choice == 'multiple' ? 'selected' : '' }}>Multiple Answers</option>
                                            </select>
                                        </div>

                                        <div class="panel panel-default" style="margin-top: 1em">
                                            <div class="panel-body answer-list">
                                                @foreach ($question->answers as $answer)
                                                    <div class="row answer-item">
                                                        <div class="col-md-10">
                                                            <div class="input-group">
                                                                <div class="input-group-addon btn answer-handle">
                                                                    <i class="fa fa-bars"></i>
                                                                </div>
                                                                <input type="hidden" name="answer_id[]" value="{{ $answer->id }}">
                                                                <input type="text" name="answer_title[]" value="{{ $answer->title }}" class="form-control" placeholder="Enter answer">
                                                                <div class="input-group-addon btn">
                                                                    <i class="fa fa-trash remove-answer"></i>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-2">
                                                            <input type="checkbox" value="1" class="make-switch" name="answer_enabled[]" {{ $answer->enabled ? 'checked' : '' }}>
                                                        </div>
                                                    </div>
                                                @endforeach

                                            </div>
                                            <div class="panel-body">
                                                <button class="btn green add-answer"><i class="fa fa-plus"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="checkbox" value="1" class="make-switch" name="question_enabled[]" {{ $question->enabled ? 'checked' : '' }}>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-offset-4 col-md-3 text-center">
                <div class="form-actions noborder">
                    <button class="btn green add-question">Add Question</button>
                    <input type="submit" class="btn blue" value="Save">
                </div>
            </div>
        </div>
    </form>

    {{-- 
        Template: Question box form.
        --}}
    <template id="question-template" style="display: none">
        <div class="form-group sortable-item question-item">
            <label class="control-label col-md-3"><button class="btn question-handle"><i class="fa fa-bars"></i></button></label>
            <div class="col-md-7">
                <input type="hidden" name="question_id[]" value="">

                <div class="input-group">
                    <input type="text" name="question_title[]" value="" class="form-control" placeholder="Enter question">
                    <div class="input-group-addon btn remove-question">
                        <i class="fa fa-trash"></i>
                    </div>
                </div>

                <div class="input-group">
                    <select name="question_answer_choice[]" class="form-control" style="margin-top: 1em;">
                        <option value="single">Single Answer</option>
                        <option value="multiple">Multiple Answers</option>
                    </select>
                </div>

                <div class="panel panel-default" style="margin-top: 1em">
                    <div class="panel-body answer-list">
                        <div class="row answer-item">
                            <div class="col-md-10">
                                <div class="input-group">
                                    <div class="input-group-addon btn answer-handle">
                                        <i class="fa fa-bars"></i>
                                    </div>
                                    <input type="hidden" name="answer_id[]" value="">
                                    <input type="text" name="answer_title[]" value="" class="form-control" placeholder="Enter answer">
                                    <div class="input-group-addon btn">
                                        <i class="fa fa-trash remove-answer"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-2">
                                <input type="checkbox" value="1" class="make-switch" name="answer_enabled[]" checked>
                            </div>
                        </div>

                    </div>
                    <div class="panel-body">
                        <button class="btn green add-answer"><i class="fa fa-plus"></i></button>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <input type="checkbox" value="1" class="make-switch" name="question_enabled[]" checked>
            </div>
        </div>
    </template>

    {{-- 
        Template: Answer box form.
        --}}
    <template id="answer-template" style="display: none">
        <div class="row answer-item">
            <div class="col-md-10">
                <div class="input-group">
                    <div class="input-group-addon btn answer-handle">
                        <i class="fa fa-bars"></i>
                    </div>
                    <input type="hidden" name="answer_id[]" value="">
                    <input type="text" name="answer_title[]" value="" class="form-control" placeholder="Enter answer">
                    <div class="input-group-addon btn">
                        <i class="fa fa-trash remove-answer"></i>
                    </div>
                </div>
            </div>

            <div class="col-md-2">
                <input type="checkbox" value="1" class="make-switch" name="answer_enabled[]" checked>
            </div>
        </div>
    </template>
@endsection

@section('css')
    <style type="text/css">
        .answer-item {
            margin-top: .5em;
            margin-bottom: .5em;
        }

        .until {
            text-align: center !important;
        }
    </style>
@endsection

@section('js')
    <script type="text/javascript" src="//cdn.jsdelivr.net/npm/sortablejs@1.6.1/Sortable.min.js"></script>
    <script type="text/javascript" src="{{ mix('js/admin/surveys.js') }}"></script>
@endsection
