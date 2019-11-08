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
        <a href="{{ route('surveys.show', $survey->id) }}">Results</a>
    </li>
@endsection

@section('content')
    <div class="pull-right">
        <h4>
            {{ number_Format($survey->results->count()) }} Views &middot;
            {{ number_format($survey->results()->withAnswers()->count()) }} Responses
        </h4>
    </div>
    
    <h3>{{ $survey->title }}</h3>
    <p>{{ $survey->description }}</p>

    <hr>

    @foreach ($survey->questions as $n => $question)
    <div class="row question">
            <div class="col-md-12">
                <div class="portlet box grey">
                    <div class="portlet-title">
                        <div class="caption font-green">
                            <i class="icon-question font-green"></i>
                            <span class="caption-subject bold uppercase"> Question {{ $n + 1 }}: {{ $question->title }}</span>
                        </div>
                    </div>

                    <div class="portlet-body padded">
                        <div class="row">
                            <div class="col-md-9">
                                <table class="table table-striped table-bordered table-hover dataTable no-footer">
                                    <thead>
                                        <tr role="row" class="heading">
                                            <th width="80%">Answers</th>
                                            <th># Answered</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($question->answers as $answer)
                                        <tr class="answer" rel="{{ $answer->id }}">
                                            <td class="answer-title">{{ $answer->title }}</td>
                                            <td class="answer-count">{{ number_format($answer->results->count()) }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="col-md-3">
                                <canvas></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endsection

@section('js')
    <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.min.js"></script>
    <script type="text/javascript" src="{{ mix('js/admin/survey-results.js') }}"></script>
@endsection