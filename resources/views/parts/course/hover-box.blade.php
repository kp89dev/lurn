<div class="hover-box" data-position="{{ $position ?? '' }}">
    <div class="heading">
        <h3>{{ $course->title }}</h3>

        @if ($course->categoryList->count())
            <div class="categs">
                <i class="share alternate flipped icon"></i>
                in <a href="{{ route('classroom') }}">
                    {{ $course->categoryList->implode(', ') }}
                </a>
            </div>
        @endif

        <div class="stats">
            <div class="item">
                <i class="list icon"></i>
                <span class="value">{{ shorter_number($modulesNo = $course->getCounters()->modules) }}</span>
                <span class="lbl">{{ str_plural('module', $modulesNo) }}</span>
            </div>
            <div class="item">
                <i class="file text icon"></i>
                <span class="value">{{ shorter_number($lessonsNo = $course->getCounters()->lessons) }}</span>
                <span class="lbl">{{ str_plural('lesson', $lessonsNo) }}</span>
            </div>
            <div class="item">
                <i class="users icon"></i>
                <span class="value">{{ shorter_number($studentsNo = $course->getCounters()->students) }}</span>
                <span class="lbl">{{ str_plural('students', $studentsNo) }}</span>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="inside-wrapper">
            {!! $course->snippet !!}
        </div>
    </div>
    <div class="footer">
        @if (user_enrolled($course))
            <a href="{{ $course->url }}" class="ui primary button">
                View <b>Course</b>
                <i class="angle right icon"></i>
            </a>
        @else
            <a href="{{ route('enroll', $course->slug) }}" class="ui secondary button">
                <b>Enroll</b>
                <i class="caret right icon"></i>
            </a>
        @endif

        <div class="right floated">
            <div class="center aligned">
                @component('components.thumbs-up', ['course' => $course, 'user' => user()]) @endcomponent
            </div>
        </div>
    </div>
</div>