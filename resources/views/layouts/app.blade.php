<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    {{ seo_helper()->renderHtml() }}

    <link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.3.0/semantic.min.css">
    <link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Lato:100,300,900|Pangolin">
    <link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/izitoast/1.1.1/css/iziToast.min.css">
    <link rel="stylesheet" type="text/css" href="{{ mix('css/app.css') }}">
    @yield('css')

    <script src="//code.jquery.com/jquery-3.2.1.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery-mousewheel/3.1.13/jquery.mousewheel.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/vue/2.2.4/vue.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/izitoast/1.1.1/js/iziToast.min.js"></script>
    <script src="{{ mix('js/helpers.js') }}"></script>

    @if (env('APP_ENV') != 'testing' && !session()->has('admin_impersonator'))
        @include('woopra.index')
    @endif

    <script>
        window.Laravel = {!! json_encode(['csrfToken' => csrf_token()]) !!};
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } });

        @if (user())
            window.user = {!! user()->toJson() !!};
        @else
            window.user = { id: 0, name: null, email: null };
        @endif
    </script>

    @include('messages')
    <script src="{{ mix('js/track.js') }}"></script>
</head>
<body>

@include('surveys.survey-base-modal')

@if (user() && user()->setting->getMessageState('show-recommendation-question', user()->isOlderThanOneMonth()))
    <div id="feedback">
        <div id="feedback-bar">
            <div class="ui two columns grid">
                <div class="column right aligned">
                    <div>On a scale of 1 to 10:</div>
                    <div><strong>How likely are you to recommend us to your family and friends?</strong></div>
                </div>
                <div class="column">
                    <ul class="recommend-grade">
                        <li>Less<br>Likely</li>
                        <li v-for="step in scale" @click="showModal(); grade = step;" v-text="step"></li>
                        <li>Most<br>Likely</li>
                    </ul>
                </div>
            </div>
        </div>

        <div id="feedback-modal" class="ui small modal">
            <i class="close icon"></i>
            <div class="header">
                Feedback
            </div>
            <div class="content">
                <div class="ui form" v-show="! sent">
                    <div class="center aligned">
                        <div>On a scale of 1 to 10:</div>
                        <div><strong>How likely are you to recommend us to your family and friends?</strong></div>

                        <ul class="recommend-grade">
                            <li>Less<br>Likely</li>
                            <li v-for="step in scale" :class="{ active: grade == step }"
                                @click="showModal(); grade = step;" v-text="step"></li>
                            <li>Most<br>Likely</li>
                        </ul>
                    </div>

                    <hr>

                    <div class="field">
                        <label>What could we do to improve?</label>
                        <textarea v-model="feedback" class="fluid" rows="4"
                                  placeholder="Give us your feedback"></textarea>
                    </div>
                </div>
                <div v-show="sent">
                    <h3>Thank you for your feedback!</h3>
                </div>
            </div>
            <div class="actions" v-show="! sent">
                <div class="ui teal right labeled icon button" @click="submitFeedback()" :class="{ loading: sending }">
                    Submit <b>Feedback</b>
                    <i class="check icon"></i>
                </div>
            </div>
        </div>
    </div>
@else
    @include('widgets.push-notifications')
@endif

<div id="header">
    <div class="wrapper">
        <table>
            <tr>
                <td id="logo">
                    <a href="{{ user() ? route('dashboard') : url('/') }}">
                        <img src="{{ asset('images/logo.svg') }}" alt="Lurn Nation Logo">
                    </a>
                </td>

                @if (view()->hasSection('header-slot'))
                    <td class="slot">
                        @yield('header-slot')
                    </td>
                @else
                    <td id="browser-courses">
                        <div class="ui top left dropdown">
                            <div class="lbl">
                                Browse <strong>Courses</strong>
                                <i class="dropdown icon"></i>
                            </div>

                            <div id="mega-menu" class="mega menu">
                                <div class="item">
                                    <table>
                                        <tr>
                                            <td id="courses">
                                                <div class="description">Courses</div>
                                                <ul>
                                                    <li v-for="item in menu" :class="{ active: course.id == item.id }"
                                                        @mouseover="course = item">
                                                        <a :href="item.url" v-text="item.title"></a>
                                                    </li>
                                                    <li class="padded-twice center aligned">
                                                        <a href="{{ route('classroom') }}">View All Courses</a>
                                                    </li>
                                                </ul>
                                            </td>
                                            <td id="preview">
                                                <div class="relative">
                                                    <div class="preview-image">
                                                        <img :src="course.image">
                                                    </div>
                                                    <div class="top-layer">
                                                        <div class="description">Preview</div>
                                                        <div class="padded">
                                                            <h2 v-text="course.title"></h2>
                                                            <div class="content mb-15">
                                                                <p class="stats">
                                                                    @{{ course.modules }} Modules •
                                                                    @{{ course.lessons }} Lesson •
                                                                    @{{ course.students }} Students
                                                                </p>

                                                                <div class="course-snippet" v-html="course.snippet"></div>
                                                            </div>

                                                            <div class="right aligned">
                                                                <a :href="course.url" class="ui secondary right labeled icon button">
                                                                    View <b>Course</b>
                                                                    <i class="caret right icon"></i>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td id="quick-search">
                        <form class="ui fluid input left icon" action="{{ url('classroom') }}" method="GET">
                            <i class="search icon"></i>
                            <input type="text" name="q" value="{{ request('q') }}"
                                   placeholder="Quick search: Look for the skills you want to learn. E.g. “copywriting”">
                        </form>
                    </td>
                @endif

                @if (! request('wasAborted'))
                    <td id="hello-user">
                        <div class="ui top right dropdown">
                            @if (user())
                                <div class="profile-picture-badge">
                                    {{ shorter_number(user()->points, 1) }}
                                </div>
                                <img src="{{ user()->getPrintableImageUrl() }}">

                                <div class="lbl">
                                    Hello
                                    <strong>{{ user()->firstName }} </strong>
                                    <i class="dropdown icon"></i>
                                </div>

                                <div class="menu">
                                    <div class="summary">
                                        {{--<div class="level-summary">
                                            <i class="shield icon"></i>
                                            <span>
                                                <span class="label">Level</span>
                                                <strong>Fighter</strong>
                                            </span>
                                        </div>--}}
                                        <div class="points-summary">
                                            <div>
                                                <i class="circle icon"></i>
                                                <span class="points">{{ shorter_number(user()->points, 1) }} Points</span>
                                            </div>
                                            <div class="learn-more">
                                                {{--<div>
                                                    Gain 800 more points to reach<br>
                                                    the <strong>Strategist</strong> level.
                                                </div>--}}
                                                <a href="{{ route('profile') }}">
                                                    What's This?
                                                </a>
                                            </div>
                                        </div>
                                    </div>

                                    @if (user()->isAdmin)
                                        <a href="{{ route('admin') }}" class="item">
                                            <i class="lock icon"></i>
                                            Admin
                                        </a>
                                    @endif
                                    <a href="{{ route('dashboard') }}" class="item">
                                        <i class="dashboard icon"></i>
                                        Dashboard
                                    </a>
                                    <a href="{{ route('classroom') }}" class="item">
                                        <i class="university icon"></i>
                                        Classroom
                                    </a>
                                    <a href="{{ route('profile') }}" class="item separator">
                                        <i class="user circle icon"></i>
                                        User Profile
                                    </a>
                                    <a href="{{ route('support') }}" class="item">
                                        <i class="life ring icon"></i>
                                        Support
                                    </a>
                                    <a href="{{ route('account-merge.index') }}" class="item">
                                        <i class="horizontally flipped rotated share alternate icon"></i>
                                        Merge Accounts
                                    </a>
                                    <a href="{{ route('logout') }}" class="item separator">
                                        <i class="sign out icon"></i>
                                        Logout
                                    </a>
                                </div>
                            @else
                                <a href="{{ route('login') }}" onclick="window.location = this.href" class="ui basic icon button">
                                    <i class="sign in icon"></i>
                                    <span class="hide w768">Login</span>
                                </a>
                            @endif
                        </div>
                    </td>
                @endif
            </tr>
        </table>
    </div>
</div>

@yield('content')

@include('parts.onboarding-popup')
@include('parts.course.completed-modal')
@include('parts.footer')

<script>
    var menuData = {!! \App\Models\Course::getHeaderData()->toJson() !!};
</script>
<script src="//cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.3.0/components/transition.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.3.0/components/accordion.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.3.0/components/checkbox.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.3.0/components/dropdown.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.3.0/components/dimmer.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.3.0/components/popup.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.3.0/components/modal.min.js"></script>
<script src="//unpkg.com/tippy.js@2.1.1/dist/tippy.all.min.js"></script>
<script src="{{ mix('js/header.js') }}"></script>
<script src="{{ mix('js/feedback.js') }}"></script>
<script src="{{ mix('js/push-notification.js') }}"></script>
<script src="{{ mix('js/thumbs-up.js') }}"></script>
<script src="{{ mix('js/global.js') }}"></script>

@if(Auth::user() and !Auth::user()->isAdmin)
    <script src="{{ mix('js/surveys.js') }}"></script>
@endif

@yield('js')

</body>
</html>
