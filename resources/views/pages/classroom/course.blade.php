@extends('layouts.app')

@section('content')
    <div class="classroom-course wrapper">
        <table class="shadow">
            <tr>
                <td id="sidebar">
                    <div class="relative">
                        @include('parts.course.sidebar', compact('course') + ['isCourse' => true])
                    </div>
                </td>

                <td id="content">
                    <div class="padded-twice">
                        <h1>
                            <i class="university icon"></i>
                            {{ $course->title }}
                        </h1>
                        <hr>

                        @if (! user_enrolled($course) && ! $course->free)
                            <div class="ui info message" style="padding: 20px 30px">
                                <i class="info circle icon"></i>
                                Get INSTANT access to the course now! Just click the “Buy Course” button on the left
                            </div>
                        @endif

                        <div id="course-description">
                            {!! $course->courseDescription !!}
                        </div>

                        <hr>

                        @if (user_enrolled($course))
                            <h2>Modules</h2>
                            <ol class="chapters">
                                @foreach ($modules as $module)
                                    @continue ($module->hidden == 1)

                                    @php $locked = $module->locked_by_test && ! $module->unlocked @endphp

                                    <li>
                                        <div class="chapter">
                                            @if ($module->progress)
                                                <div class="progress-bar">
                                                    <div class="bar" style="width: {{ $module->progress }}%">
                                                        <span>{{ $module->progress }}%</span>
                                                    </div>
                                                </div>
                                            @endif

                                            <a href="{{ route('module', [$course->slug, $module->slug]) }}">
                                                {{ $module->title }}

                                                @if ($locked)
                                                    <i class="lock icon"></i>
                                                @elseif ($module->type == 'Link')
                                                    <i class="square external link icon"></i>
                                                @endif
                                            </a>
                                        </div>

                                        @isset ($module->orderedLessons)
                                            <ul>
                                                @foreach ($module->orderedLessons as $lesson)
                                                    @php $url = route('lesson', [
                                                         $course->slug, $module->slug, $lesson->slug]) @endphp

                                                    @if ($locked)
                                                        <li><a>{{ $lesson->title }} <i class="lock icon"></i></a></li>
                                                    @elseif ($lesson->type == 'Lesson')
                                                        <li>
                                                            <a href="{{ $url }}">
                                                                {{ $lesson->title }}

                                                                @if ($lesson->completed)
                                                                    <i class="check icon"></i>
                                                                @endif
                                                            </a>
                                                        </li>
                                                    @else
                                                        <li class="{{ $lesson->completed ? 'completed' : '' }}">
                                                            <a href="{{ $url }}" target="_blank">
                                                                {{ $lesson->title }}
                                                                <i class="square external link icon"></i>
                                                            </a>
                                                        </li>
                                                    @endif

                                                    @if ($lesson->testId)
                                                        <li class="test {{ $passed = test_passed($lesson->testMark) ? 'completed' : null }}">
                                                            <a href="{{ route('test', [
                                                                    $course->slug, $module->slug, $lesson->testId]) }}">
                                                                {{ $lesson->testTitle }}

                                                                @if ($passed)
                                                                    <span class="right floated">
                                                                        {{ number_format($lesson->testMark) }}%
                                                                    </span>
                                                                @endif
                                                            </a>
                                                        </li>
                                                    @endif
                                                @endforeach
                                            </ul>
                                        @endisset
                                    </li>
                                @endforeach
                            </ol>
                            <hr>
                        @endif

                        @if (count($course->bonuses) && $bonusenrollment)
                            <h2>Additional Content</h2>
                            <div class="courses ui three columns grid">
                                @foreach ($course->bonuses as $bonus)
                                    @if (user_enrolled($bonus->bonusCourse))
                                        <div class="column">
                                            @include('parts.course.card', ['course' => $bonus->bonusCourse])
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                            <hr>
                        @endif

                        @if (count($recommended))
                            <h3>Similar Courses</h3>

                            <div class="courses ui three columns grid">
                                @foreach ($recommended as $course)
                                    <div class="column">
                                        @include('parts.course.card', compact('course'))
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </td>
            </tr>
        </table>
    </div>
@endsection

@section('js')
    <script type="text/javascript" src="{{ mix('js/forum.js') }}"></script>
@endsection
