@extends('layouts.app')

@section('content')
    <div class="wrapper course-page">
        <table class="shadow">
            <tr>
                @include('parts.lesson.sidebar')

                <td id="content">
                    @include('parts.lesson.breadcrumbs')
                    @php
                        $mark = session()->has('mark');

                        function correctAnswer ($question, $answer) {
                            return session()->has('mark')
                                && in_array($question->id, session('correct'))
                                && is_array($questions = session()->get('questions'))
                                && $questions[$question->id]
                                && $question->question_type == 'Radio'
                                    ? ($questions[$question->id] == $answer->id)
                                    : isset($questions[$question->id][$answer->id]);
                        }
                    @endphp

                    <div id="course-content" class="padded-twice">
                        <p class="lesson">Test: {{ $currentTest->title }}</p>
                        <h2 class="mb-30">{{ $currentTest->title }}</h2>

                        @if ($testResult)
                            <hr>
                            @if ($currentTest->custom_completion_status == 1)
                                <style>{{ $currentTest->custom_completion_style }}</style>
                                <div id="ccbox" class="ccbox" style="background-image: url('/static/{{ $currentTest->custom_completion_background }}')">
                                    <div class="ccbox-header" style="background-image: url('/static/{{ $currentTest->custom_completion_header }}')"></div>
                                    <div class="ccbox-content">
                                        {!! $currentTest->custom_completion_body !!}
                                    </div>
                                    <div class="ccbox-badge" style="background-image: url('/static/{{ $currentTest->custom_completion_badge }}')"></div>
                                </div>
                            @else
                                <h1>Congratulations!</h1>
                                <p>You've passed the test.</p>
                            @endif
                        @elseif ($mark)
                            <div class="ui negative message mt-0" style="margin-bottom: -31px">
                                You scored {{ session('mark') }}%.
                                You need to score at least 75% to pass this test. Please try again!
                            </div>
                        @endif

                        <form action="{{ $action }}" method="post">
                            <input type="hidden" name="_method" value="{{ $method }}">
                            <input type="hidden" name="test" value="{{ $currentTest->id }}">
                            <input type="hidden" name="course" value="{{ $course->id }}">
                            <input type="hidden" name="module" value="{{ $module->id }}">
                            {{ csrf_field() }}

                            @foreach ($currentTest->getOrderedQuestions() as $i => $question)
                                <hr>
                                <div class="question">
                                    <h3 class="question-title">
                                        @if ($mark && in_array($question->id, session('incorrect')))
                                            <i class="remove red icon"></i>
                                        @elseif ($mark)
                                            <i class="check green icon"></i>
                                        @endif
                                        {{ $i + 1 }}. {{ $question->title }}
                                    </h3>

                                    @foreach ($question->answers()->get() as $answer)
                                        <div class="field">
                                            @if ($question->question_type == 'Radio')
                                                <input type="radio" id="answer_{{$answer->id}}"
                                                       name="question[{{$question->id}}]"
                                                       value="{{$answer->id}}"
                                                       {{ correctAnswer($question, $answer) ? 'checked' : '' }}>
                                            @else
                                                <input type="checkbox" id="answer_{{$answer->id}}"
                                                       name="question[{{$question->id}}][{{$answer->id}}]"
                                                       value="{{$answer->id}}"
                                                        {{ correctAnswer($question, $answer) ? 'checked' : '' }}>
                                            @endif

                                            <label class="ml-5" for="answer_{{$answer->id}}">
                                                {{$answer->title}}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            @endforeach

                            <hr>
                            <div class="center aligned">
                                <button type="submit" class="ui big secondary button">
                                    <i class="check icon"></i>
                                    <b>Submit</b> Answers
                                </button>
                            </div>
                        </form>
                    </div>

                    @include('parts.lesson.navigation', ['currentLesson' => $currentTest])
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

        $('form').on('submit', function (e) {
            $('div.question').each(function () {
                if ($(this).find('input:checked').length === 0) {
                    e.preventDefault();
                    alert('You must provide and answer to each question.');
                    return false;
                }
            });
        });
    </script>
    <script type="text/javascript" src="{{ mix('js/lesson.js') }}"></script>
@endsection
