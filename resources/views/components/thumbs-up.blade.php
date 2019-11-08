<div class="thumbs-up-container">
    @php
        $userLiked = user() && user()->likes()->withCourse($course->id)->sum('likes');
        $courseLikes = $course->getCounters()->likes;
    @endphp

    <span class="thumbs-up value {{ $userLiked ? 'active' : '' }}"
          data-course-id="{{ $course->id }}" data-user-id="{{ optional(user())->id }}"
          title="{{ isset($display_count) ? shorter_number($courseLikes) : '' }}">
        <i class="thumbs up {{ $userLiked ? '' : 'outline' }} icon"></i>
    </span>

    <span class="thumbs-up-counter lbl" data-count="{{ $courseLikes }}" style="{{ $courseLikes ? '' : 'display: none' }}">
        {{ shorter_number($courseLikes) }}
    </span>
</div>