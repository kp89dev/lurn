<img src="{{ $course->getPrintableImageUrl() }}">

@if (user_enrolled($course))
    <div class="progress-bar">
        <div class="bar" style="width: {{ $progress = $course->getProgress() }}%">
            <span>{{ $progress }}%</span>
        </div>
    </div>
@elseif ($course->label)
    <span class="price">
        {{ $course->label->title }}
    </span>
@endif

@include('parts.course.sidebar-meta', compact('course'))

@if ($hasCategories = $course->categoryList->count())
    <div class="item-meta">
        <table>
            <tr>
                <th>{{ $course->categories()->count() === 1 ? 'Category' : 'Categories' }}</th>
                <td>{{ $course->categoryList->implode(', ') }}</td>
            </tr>
        </table>
    </div>
    <hr>
@endif

@if ($course->badgesEnabled->count())
    <div class="padded">
        <div class="center aligned">
            <h3>Course Badges</h3>

            <div class="badges">
                @foreach($course->badgesEnabled as $badge)
                    @if(isset($userBadges) and $userBadges->contains('id', $badge->id))
                        <div class="badge">
                            <div class="acquired">
                                <div class="checkmark">
                                    <i class="check huge icon large"></i>
                                </div>
                            </div>
                            <img src="{{ $badge->src }}">
                        </div>
                    @else
                        <div class="badge">
                            <img src="{{ $badge->src }}">
                        </div>
                    @endif

                    @break($loop->index >= 2)
                @endforeach
            </div>
            <a href="{{ route('front.badges.index', ['course' => $course->slug]) }}">View All Course Badges</a>
        </div>
    </div>
    <hr>
@endif

@if (! user_enrolled($course))
    <div class="padded">
        <a href="{{ route('enroll', $course->slug) }}" class="ui secondary fluid right labeled icon button">
            @if ($course->free)
                <b>Enroll</b>
            @else
                Buy Course (<b>Enroll</b>)
            @endif
            <i class="caret right icon"></i>
        </a>
    </div>
    <hr>
@else
    @if ($course->vanillaForum->url)
        <div class="padded">
            <form id="courseForum" action="{{ route('webhook.vanilla.request', $course->id) }}" method="post">
                {{ csrf_field() }}
                <button class="ui widget fluid right labeled icon button woopra-track"
                        data-woopra='{"title":"{{$course->title}} Forum", "action":"follow", "type":"courseForum" }'>
                    <b>Forum</b> <i class="caret right icon"></i>
                </button>
            </form>
        </div>
        <hr>
    @endif

    @if ($course->tools)
        <div class="padded center aligned">
            @foreach ($course->tools as $tool)
                @if ($tool->slug != 'launchpad' || ($tool->slug == 'launchpad' && $course->userIsBoarded()))
                <a class="ui big primary button {{ $loop->last ? '' : 'mb-15' }} tool-{{ $tool->slug }}" href="{{ url('tools', $tool->slug) }}">
                    <i class="plug icon"></i>
                    <b>{{ $tool->tool_name }}</b>
                </a>
                @endif
            @endforeach
        </div>
        <hr>
    @endif
@endif

<div class="padded not-top">
    @include('parts.widgets.rewards')
    <hr>
</div>

<div class="padded-twice not-top">
    <div class="center aligned">
        Looking for something else?<br>
        <a href="{{ route('classroom') }}"><b>Browse the classroom</b></a>
    </div>
</div>
