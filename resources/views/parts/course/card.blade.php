@php
    $isFeatured = isset($featured);
    $subscription = user() ? user_enrolled($course, user(), true) : false;
@endphp

<a href="{{ $course->url }}" class="course {{ $subscription ? $subscription->statusName : '' }} {{ $isFeatured ? 'woopra-track' : '' }}" data-woopra='{"type":{{$isFeatured ? '"featuredCourse", "action":"follow", "title":"'.$course->title.'"' : '"course"'}}}'>
    <div class="photo">
        <img src="{{ $course->getPrintableImageUrl() }}" />
        <div class="preview-icon">
            <i class="eye icon"></i>
        </div>

        @if ($subscription && $subscription->statusName == 'completed')
            <div class="certified ui active inverted dimmer">
                <div class="content">
                    <div class="center">
                        <h4 class="ui icon header">
                            <i class="trophy icon"></i>
                            Certified!
                        </h4>
                    </div>
                </div>
            </div>
        @endif
    </div>

    @if ($subscription && $progress = $course->getProgress())
        <div class="progress-bar">
            <div class="bar" style="width: {{ $progress }}%">
                <span>{{ $progress }}%</span>
            </div>
        </div>
    @endif

    <div class="content">
        <div class="padded">
            <div style="max-width: 40px; float: right; text-align: center">
                @component('components.thumbs-up', ['course' => $course, 'user' => user()])
                @endcomponent
            </div>
            <div style="width: 100%; padding-right: 40px">
                <h3>{{ $course->title }}</h3>
            </div>

            @if (isset($course->summary))
                <div class="summary">{{ $course->summary }}</div>

            @elseif ($course->status === 'in-progress' && $currentLesson = $course->getCurrentLesson())
                <div class="summary">
                    <strong>In Progress</strong>
                    at Chapter {{ $currentLesson->module->getIndex() }},
                    Lesson {{ $currentLesson->getIndex() }}
                </div>

            @elseif ($course->status == 'completed')
                <div class="summary">
                    <strong>Completed</strong>
                    on {{ $course->updated_at->format('M j, Y') }}
                </div>

            @elseif ($course->categoryList->count())
                <div class="summary">{{ $course->categoryList->implode(', ') }}</div>

            @endif

            @if ($course->label)
                <span class="price">
                    {{ $course->label->title }}
                </span>
            @endif
        </div>

        @include('parts.course.meta', compact('course'))
    </div>
</a>
