@extends('layouts.app')

@section('content')
    <div class="classroom-course wrapper">
        <table class="shadow">
            <tr>
                <td id="sidebar">
                    <div class="relative">
                        @include('parts.course.sidebar', compact('course'))
                    </div>
                </td>

                <td id="content">
                    <div id="breadcrumbs">
                        <ul>
                            <li><a href="{{ route('classroom') }}">Classroom</a></li>
                            <li><a href="{{ route('course', $course->slug) }}">{{ $course->title }}</a></li>
                        </ul>
                    </div>

                    <div class="padded-twice">
                        <h1>{{ $currentModule->title }}</h1>

                        @if (user_enrolled($course))
                            <div class="unpadded">
                                <div class="progress-bar">
                                    <div class="bar" style="width: {{ $moduleProgress }}%">
                                        <span>{{ $moduleProgress }}%</span>
                                    </div>
                                </div>
                            </div>
                        @else
                            <hr>
                        @endif

                        @if ($currentModule->description)
                            <div>
                                {!! $currentModule->description !!}
                            </div>
                            <hr>
                        @endif

                        @if (! $currentModule->isLocked())
                            <p>Choose a lesson</p>

                            <ul class="lessons">
                                @foreach ($lessons as $lesson)
                                    @if ($lesson->type == 'Lesson')
                                        <li class="{{ $lesson->completed ? 'completed' : '' }}">
                                            <a href="{{ route('lesson', [$course->slug, $currentModule->slug, $lesson->slug]) }}">
                                                {{ $lesson->title }}
                                                @if ($lesson->completed)
                                                    <i class="check icon"></i>
                                                @endif
                                            </a>
                                        </li>
                                    @else
                                        <li class="link {{ $lesson->completed ? 'completed' : '' }}">
                                            <a target="_blank"
                                               href="{{ route('lesson', [$course->slug, $currentModule->slug, $lesson->slug]) }}">
                                                {{ $lesson->title }}
                                                <i class="external link square icon"></i>
                                            </a>
                                        </li>
                                    @endif

                                    @isset ($lesson->testId)
                                        <li class="test {{ $passed = test_passed($lesson->testMark) ? 'completed' : null }}">
                                            <a href="{{ route('test', [
                                                                $course->slug, $currentModule->slug, $lesson->testId]) }}">
                                                {{ $lesson->testTitle }}

                                                @if ($passed)
                                                    <span>&mdash; {{ number_format($lesson->testMark) }}%</span>
                                                    <i class="check icon"></i>
                                                @endif
                                            </a>
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
                        @else
                            <h2><i class="icon lock"></i>Sorry, this content is locked.</h2>
                            <p>The module you're trying to access is locked by an earlier module. Please go back
                                and pass all tests and quizzes to view this content.</p>
                        @endif
                    </div>
                </td>
            </tr>
        </table>
    </div>
@endsection
