@extends('layouts.app')

@section('content')
    <div class="wrapper course-page">
        <table class="shadow">
            <tr>
                @include('parts.lesson.sidebar')

                <td id="content">
                    @include('parts.lesson.breadcrumbs')
                    @if ( $testResult )
                    <div class="ui positive message center aligned">
                        <h1>Congratulations!</h1>
                        <p>You've passed the {{$currentTest->title}}!</p>
                        @if ($cert)
                        <a class="ui button primary" style="box-shadow: none !important;" href="{{route('test-certificate',['course'=>$course->slug,'module'=>$currentModule->slug, 'test'=>$currentTest->id ])}}" target="_blank"><i class="cloud download icon"></i> Download Your Certificate </a>
                        @else
                            <p>You may now continue on with the course.</p>
                        @endif
                    </div>
                    @elseif (session()->has('mark'))
                        <div class="ui error message">
                            You only scored {{ session('mark') }}%.
                            You need 75% to pass this test. Please try again below
                        </div>
                    @endif
                    <div id="course-content" class="padded-twice">

                        <p class="lesson">Test: {{ $currentTest->title }}</p>
                        <h2>{{ $currentTest->title }}</h2>
                        <form action="#">
                        @foreach ($currentTest->getOrderedQuestions() as $i => $question)
                           <div class="question" style="margin-top:15px;">
                                <div class="questionTitle">
                                    <i class="checkmark box icon green"></i>
                                {{ $i + 1 }}. {{ $question->title }}</div>
                                @foreach ($question->answers()->enabled()->get() as $answer)
                                    <div class="form-group" style="padding-left:15px;">
                                    @if ($question->question_type == "Radio")
                                        <input type="radio" id="answer_{{$answer->id}}" name="question[{{$question->id}}]" value="{{$answer->id}}" disabled="disabled" {{ $answer->is_answer ? 'checked' : '' }}>
                                        <label for="answer_{{$answer->id}}"> {{$answer->title}}</label>
                                    @else
                                        <input type="checkbox" id="answer_{{$answer->id}}" name="question[{{$question->id}}][{{$answer->id}}]" value="{{$answer->id}}" disabled="disabled" {{ $answer->is_answer ? 'checked' : '' }}>
                                        <label for="answer_{{$answer->id}}"> {{$answer->title}}</label>
                                    @endif
                                    </div>
                                @endforeach
                           </div>
                        @endforeach
                        </form>
                    </div>

                    @include('parts.lesson.navigation', ['currentLesson' => $relatedLessons->previous])
                </td>
            </tr>
        </table>
    </div>
@endsection

@section('js')
    <script type="text/javascript" src="{{ mix('js/dynamic-modal.js') }}"></script>
       <script type="text/javascript">
        var course = {{ $course->id }},
            lesson = {{ $relatedLessons->previous->id }},
            completed = {{ (int) user_completed($relatedLessons->previous->id) }};
    </script>
    <script type="text/javascript" src="{{ mix('js/lesson.js') }}"></script>
@endsection
