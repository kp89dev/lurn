@if ($course->userIsBoarded())
    <td id="sidebar">
        <div>
            <div class="course-title padded">
                <span class="ui label">Course</span>
                <div>{{ $course->title }}</div>
            </div>

            <div class="progress-bar">
                <div class="bar" style="width: {{ $courseProgress }}%">
                    <span>{{ $courseProgress }}%</span>
                </div>
            </div>
            <ol id="chapters">
                @foreach ($modules as $module)
                    @continue ($module->hidden == 1)

                    @php $locked = $module->locked_by_test && ! $module->unlocked @endphp

                    <li class="{{ $module->slug == $currentModule->slug ? 'open' : '' }}">
                        <div class="chapter">
                            @if ($locked)
                                <span class="percentage"><i class="lock icon"></i></span>
                                <a>{{ $module->title }}</a>
                            @elseif ($module->type == 'Module')
                                <span class="percentage">{{ $module->progress }}%</span>

                                <a href="{{ route('module', [$course->slug, $module->slug]) }}">
                                    {{ $module->title }}
                                </a>

                                <div class="counter">
                                    <span>{{ $module->lessonsSidebarCounter }}</span>
                                </div>

                                <div class="module-toggle">
                                    <i class="chevron left icon"></i>
                                </div>
                            @else
                                <span class="percentage"><i class="external square link icon"></i></span>

                                <a href="{{ $module->link }}" target="_blank">
                                    {{ $module->title }}
                                </a>
                            @endif
                        </div>

                        @isset ($module->orderedLessons)
                            <ul>
                                @foreach ($module->orderedLessons as $lesson)
                                    @php $isLink = $lesson->type == 'Link' @endphp

                                    <li class="{{ ! isset($currentTest) && $currentLesson->slug == $lesson->slug ? 'active' : '' }} {{ $lesson->completed }} {{ $isLink ? 'link' : '' }}">
                                        <a href="{{ route('lesson', [$course->slug, $module->slug, $lesson->slug]) }}" target="{{ $isLink ? '_blank' : '_self' }}">
                                            {{ $lesson->title }}
                                        </a>
                                    </li>

                                    @if ($lesson->testId)
                                        @php
                                            $passed = test_passed($lesson->testMark) ? 'completed' : null;
                                            $active = isset($currentTest) && $currentTest->id == $lesson->testId
                                                ? 'active' : null;
                                        @endphp
                                        <li class="test {{ $passed }} {{ $active }}">
                                            <a href="{{ route('test', [$course->slug, $module->slug, $lesson->testId]) }}">
                                                {{ $lesson->testTitle }}

                                                @if ($passed)
                                                    <span class="thin">
                                                        &mdash; {{ number_format($lesson->testMark) }}%
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
        </div>
    </td>
@endif