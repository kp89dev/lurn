@extends('layouts.app')

@section('content')
    <div class="wrapper profile-page">
        <div class="content-wrapper shadow">
            <div class="top-section">
                <div class="profile-picture">
                    <img src="{{ user()->getPrintableImageUrl() }}" alt="{{ user()->firstName }}'s Profile Picture">
                </div>
                <div class="content">
                    <h1>
                        Hey, <strong>{{ user()->firstName }}</strong>!
                        <i class="hand peace icon"></i>
                    </h1>

                    <div class="progress-bar full">
                        @php $percentage = user()->getMission()->getCompletePercentage() @endphp
                        <div class="bar" style="width: {{ $percentage }}%">
                            <span>{{ $percentage }}%</span>
                        </div>
                        <div class="label right">
                            Onboarding
                            @if ($percentage == 100)
                                Completed
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <table>
                <tr>
                    <td id="content">
                        <div class="padded-twice">
                            @if ($nextBonus)
                                @if (user_enrolled($nextBonus))
                                    @include('parts.profile.bonus-congrats', ['bonus' => $nextBonus])
                                @else
                                    <section class="unlock">
                                        <div class="center aligned">
                                            <h3>
                                                <i class="unlock alternate icon"></i>
                                                Unlock Your Next Bonus With Just
                                            </h3>

                                            <div class="points">
                                                {{ $pointsRequired = number_format($nextBonus->points_required - user()->pointsEarned, 0) }} Points
                                            </div>

                                            <div class="progress-bar full">
                                                @php $percentage = number_format(user()->pointsEarned / $nextBonus->points_required * 100, 0) @endphp
                                                <div class="bar" style="width: {{ $percentage }}%">
                                                    <span>{{ $percentage }}%</span>
                                                </div>
                                                @if (user()->pointsEarned)
                                                    <div class="label larger floating" style="left: {{ $percentage }}%">
                                                        <strong>{{ user()->pointsEarned }}</strong> POINTS
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        <h3 class="mt-45">Your Next Bonus Is...</h3>
                                        <div class="bonus-course">
                                            <div class="thumbnail">
                                                <img src="{{ $nextBonus->course->getPrintableImageUrl() }}"
                                                     alt="{{ $nextBonus->course->title }}'s Thumbnail">
                                            </div>
                                            <div class="description">
                                                <p>You're only {{ $pointsRequired }} Points away from getting access to your Premium Course, "<strong>{{ $nextBonus->course->title }}</strong>." (${{ number_format($nextBonus->course->price, 0) }} Value)</p>
                                            </div>
                                        </div>
                                    </section>
                                @endif
                            @elseif ($currentBonus)
                                @include('parts.profile.bonus-congrats', ['bonus' => $currentBonus])
                            @endif

                            <hr>

                            <div class="achievements ui four columns grid">
                                <div class="column">
                                    <div class="achievement">
                                        <div class="icon">
                                            <i class="ellipsis horizontal icon"></i>
                                        </div>
                                        <div class="value">
                                            {{ number_format(user()->pointsEarned, 0) }}
                                        </div>
                                        <div class="label">
                                            Total Points<br>Earned
                                        </div>
                                    </div>
                                </div>
                                <div class="column">
                                    <div class="achievement">
                                        <div class="icon">
                                            <i class="book icon"></i>
                                        </div>
                                        <div class="value">
                                            {{ user()->courses()->count() }}
                                        </div>
                                        <div class="label">
                                            Courses<br>Completed
                                        </div>
                                    </div>
                                </div>
                                <div class="column">
                                    <div class="achievement">
                                        <div class="icon">
                                            <i class="shield icon"></i>
                                        </div>
                                        <div class="value">
                                            {{ user()->badges()->count() }}
                                        </div>
                                        <div class="label">
                                            Badges<br>Earned
                                        </div>
                                    </div>
                                </div>
                                <div class="column">
                                    <div class="achievement">
                                        <div class="icon">
                                            <i class="users icon"></i>
                                        </div>
                                        <div class="value">
                                            {{ user()->referees()->count() }}
                                        </div>
                                        <div class="label">
                                            Friends<br>Referred
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <h3>
                                <i class="clock icon"></i>
                                My Latest Activity
                            </h3>

                            @php $engagements = user()->engagements()->paginate(10) @endphp

                            <table class="ui green table">
                                <thead>
                                    <tr>
                                        <th>Points</th>
                                        <th>Action</th>
                                        <th>Date &amp; Time</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (! $engagements->count())
                                        <tr>
                                            <td colspan="3">No information available. Complete tasks to earn points!</td>
                                        </tr>
                                    @endif

                                    @foreach ($engagements as $engagement)
                                        <tr {!! $engagement->pending ? ' class="pending" data-content="The points are waiting to be verified by our automated systems."' : '' !!}>
                                            <td>{{ intval($engagement->points) }}</td>
                                            <td>
                                                {{ $engagement->transaction }}
                                                @if ($engagement->pending)
                                                    <span class="ui outline label">pending</span>
                                                @endif
                                            </td>
                                            <td>
                                                <small>{{ $engagement->created_at->format('m/d/y g:iA') }}</small>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                @if ($engagements->hasPages())
                                    <tfoot>
                                        <tr>
                                            <td colspan="10">
                                                {{ $engagements->links() }}
                                            </td>
                                        </tr>
                                    </tfoot>
                                @endif
                            </table>
                        </div>
                    </td>

                    <td id="sidebar">
                        <div class="padded">
                            <div class="widget rewards-widget">
                                <h3 class="mb-0"><strong>Earn More Points</strong></h3>
                                <p class="mt-15">
                                    Here's a list of the most common activities to take the earn more points.
                                    <em>Click an action to get your points.</em>
                                </p>

                                @include('parts.widgets.rewards')
                            </div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Accomplishments -->
        <div class="content-wrapper shadow padded-twice mt-30">
            <h1><i class="star icon"></i> Accomplishments</h1>
            <p>A complete list of the resources that you have access to, as well as your achievements.</p>

            <hr>
            <div class="ui two columns grid fluid-at w768">
                <div class="column">
                    @include('account.subscriptions')
                </div>
                <div class="column">
                    @include('account.tools')
                </div>
            </div>

            <hr>
            <div class="ui two columns grid fluid-at w768">
                <div class="column">
                    @include('profile.certificates')
                </div>
                <div class="column">
                    @include('profile.badges')
                </div>
            </div>
        </div>

        <!-- Account Settings -->
        <div class="content-wrapper shadow padded-twice mt-30">
            <h1><i class="wrench icon"></i>Account Settings</h1>
            <p>You can use this area to change your basic account settings.</p>

            <hr>
            <form class="ui form" method="POST" enctype="multipart/form-data" name="main">
                {{ csrf_field() }}

                <div class="ui two columns grid fluid-at w768">
                    <div class="column">
                        <h3>
                            <i class="user circle icon"></i>
                            Profile Picture
                        </h3>

                        <table id="profile-picture">
                            <tr>
                                <td><img src="{{ user()->getPrintableImageUrl() }}"></td>
                                <td>
                                    <input type="file" name="image" accept="image/*" />
                                </td>
                            </tr>
                        </table>
                    </div>

                    <div class="column">
                        <h3>
                            <i class="bullhorn icon"></i>
                            Notifications
                        </h3>

                        <div class="field">
                            <div class="ui checkbox">
                                <input type="checkbox" name="receive_updates"
                                       value="1" {{ $settings->receive_updates ? 'checked' : '' }}>
                                <label>Receive Lurn Nation updates</label>
                            </div>
                        </div>

                        <div class="field">
                            @if (! $settings->receive_updates && $settings->opt_out_at)
                                Opted out on {{ date('jS F, Y', strtotime($settings->opt_out_at)) }}
                            @endif
                        </div>
                    </div>
                </div>

                <hr>

                <h3>
                    <i class="info circle icon"></i>
                    Account Information
                </h3>
                <p>To change any of the below information you need to provide the current password first</p>

                <div class="ui two columns grid mt-15">
                    <div class="wide field column">
                        <label>Current Password</label>
                        <input type="password" name="password_old">
                    </div>
                </div>

                <div class="ui two columns grid fluid-at w560">
                    <div class="wide column">
                        <div class="field">
                            <label>Name</label>
                            <input name="name" value="{{ user()->name }}">
                        </div>
                        <div class="field">
                            <label>Email Address</label>
                            <input name="email" value="{{ user()->email }}">
                        </div>
                    </div>
                    <div class="wide column">
                        <div class="field">
                            <label>New Password</label>
                            <input type="password" name="password_new">
                        </div>
                        <div class="field">
                            <label>New Password Confirmation</label>
                            <input type="password" name="password_new_confirmation">
                        </div>
                    </div>
                </div>

                <hr>

                <div class="center aligned mt-30">
                    <button class="ui primary left labeled icon button">
                        <i class="check icon"></i>
                        Save <b>Changes</b>
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('js')
    <script type="text/javascript"
            src="//cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.3.0/components/checkbox.min.js"></script>
    <script type="text/javascript"
            src="//cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.3.0/components/popup.min.js"></script>
    <script type="text/javascript">$(function () {
            $('.checkbox').checkbox();
        });</script>
    <script type="text/javascript" src="{{ mix('js/dynamic-modal.js') }}"></script>
    <script type="text/javascript" src="{{ mix('js/profile.js') }}"></script>
@endsection