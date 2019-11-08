@extends('layouts.app')

@section('content')
    <div class="wrapper">
        <table class="shadow">
            <tr>
                <td id="sidebar">
                    <div class="padded">
                        @if (isset($courseInProgress) && $currentLesson = $courseInProgress->getCurrentLesson())
                            <div id="continue-course" class="widget">
                                <div class="padded">
                                    <i class="angle double right icon"></i>
                                    Continue <b>Course</b>
                                </div>

                                @if ($progress = $courseInProgress->getProgress())
                                    <div class="progress-bar">
                                        <div class="bar" style="width: {{ $progress }}%">
                                            <span>{{ $progress }}%</span>
                                        </div>
                                    </div>
                                @else
                                    <div class="progress-bar">
                                        <div class="bar" style="width: {{ $progress }}%">
                                            <span>{{ $progress }}%</span>
                                        </div>
                                    </div>
                                @endif

                                <div class="padded">
                                    <h2 class="title">{{ $courseInProgress->title }}</h2>
                                    @php $isTest = $currentLesson instanceof App\Models\Test @endphp

                                    <div class="summary">
                                        @if ($isTest)
                                            Test: <b>{{ $currentLesson->title }}</b>
                                        @else
                                            Module <b>{{ $currentLesson->module->getIndex() }}</b>
                                            &bull; Lesson <b>{{ $currentLesson->getIndex() }}</b>
                                        @endif
                                    </div>

                                    <div class="cta">
                                        <a href="{{ route($isTest ? 'test' : 'lesson', [
                                                        $courseInProgress->slug,
                                                        $currentLesson->module->slug,
                                                        $isTest ? $currentLesson->id : $currentLesson->slug]) }}"
                                           class="ui basic button">
                                            Continue
                                            <i class="caret right icon"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <h3>Upcoming Events</h3>

                        @if (count($upcomingEvents))
                            <ul class="sidebar-events">
                                @foreach ($upcomingEvents as $event)
                                    <li>
                                        <span class="date">{{ $event->start->format('m/d') }}</span>
                                        <span class="title">{{ $event->title }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p>There are no events taking place in the following 15 days.</p>
                        @endif

                        <div class="fw-normal mt-15">
                            <a href="{{ route('calendar') }}">
                                <i class="calendar alternate outline icon"></i>
                                See the <strong>Calendar</strong>
                            </a>
                        </div>

                        @if (user()->tools()->count())
                            <hr>
                            <div class="widget tools">
                                <h3>Your Tools</h3>
                                <ul>
                                    @foreach (user()->tools as $tool)
                                        @if ($tool->slug != 'launchpad' || ($tool->slug == 'launchpad' && $tool->course->userIsBoarded()))
                                        <li>
                                            <a class="tool-{{ $tool->slug }}"
                                               href="{{ url('tools', $tool->slug) }}">
                                                <i class="plug icon"></i>
                                                {{ $tool->tool_name }}
                                            </a>
                                        </li>
                                        @endif
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if (user()->badges->count())
                            <hr>
                            <div class="widget">
                                <h3>Your Badges</h3>
                                <div class="badges center aligned">
                                    @foreach (user()->badges as $badge)
                                        <div class="badge">
                                            <img src="{{ $badge->src }}">
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <hr>
                        <div class="widget rewards-widget">
                            <h3 class="mb-0">Earn More Points</h3>

                            @if (user() && user()->pointsEarned < 1500)
                                <p class="ui info message">
                                    Get JUST {{ 1500 - user()->pointsEarned }} more points to reach your next REWARD (a free course)!
                                    Here are some of the steps that you can take to earn more points.
                                </p>
                            @endif

                            @include('parts.widgets.rewards')
                        </div>

                        @if ($adFirst->count())
                            <hr>
                            @foreach ($adFirst as $ad)
                                @include('parts.dashboard-ad', compact('ad'))
                            @endforeach
                        @endif

                        @if ($adSecond->count())
                            <hr>
                            @foreach ($adSecond as $ad)
                                @include('parts.dashboard-ad', compact('ad'))
                            @endforeach
                        @endif

                    </div>
                </td>

                <td id="content">
                    <div id="newsfeed">
                        @if (user()->setting->getMessageState('show-dashboard-congrats', true))
                            <div id="main-message" class="ui positive message">
                                <i class="close icon" @click="hideCongratsMessage($event)"></i>
                                <div class="header">Congratulations and Welcome to <strong>Lurn Central</strong>!</div>
                                <p>Make sure to browse through the courses to find out what is best suited for you.</p>
                            </div>
                        @endif

                        <button class="mark-as-read ui basic tiny left labeled icon button right floated"
                                :class="{ loading: loading }" v-show="news.length" @click="markNewsRead()">
                            <i class="eye icon"></i>
                            <b>Mark all Read</b>
                        </button>

                        <h2>
                            Newsfeed
                            <a href="{{ route('news') }}" class="all-news trackable"
                               data-event-name="Newsfeed View All">
                                View All &raquo;
                            </a>
                        </h2>

                        <ul v-cloak v-show="news.length" style="display: none;">
                            <li v-for="article in news">
                                <a :href="'/news/' + article.slug" class="trackable" data-event-name="News Item">
                                    <span class="date" v-text="article.date"></span>
                                    @{{ article.title }}
                                </a>
                            </li>
                        </ul>
                        <div v-cloak id="no-unread-news" v-show="! news.length" style="display: none;">
                            No unread news.
                        </div>
                    </div>

                    <div class="padded">
                        <h2>Your Courses</h2>

                        @if (user()->setting->getMessageState('show-dashboard-missing-course-question', true))
                            <div class="mb-15">
                                <p class="ui warning small message" style="padding: 12px 15px">
                                    <i class="close icon" @click="hideMissingCourseLink($event)"></i>
                                    Are you missing a course that you already had access to?
                                    <a href="{{ route('account-merge.index') }}">Click here to fix</a>
                                </p>
                            </div>
                        @endif

                        @if ($userCourses->count())
                            <div class="courses ui three columns grid">
                                @foreach($userCourses as $course)
                                    <div class="column">
                                        @include('parts.course.card', compact('course'))
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div>
                                <div class="ui info message">
                                    <i class="info icon"></i>
                                    You didn't enroll in any courses yet. Try the menu or search bar at the top. :)
                                </div>
                            </div>
                        @endif

                        @if ($recommendedCourses->count())
                            <hr style="margin-top: 40px; margin-bottom: 30px">

                            <h2>Recommendations for You</h2>

                            <div class="courses ui three columns grid">
                                @foreach ($recommendedCourses as $course)
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
    <script type="text/javascript" src="{{ mix('js/dashboard.js') }}"></script>
    <script type="text/javascript" src="{{ mix('js/forum.js') }}"></script>
@endsection
