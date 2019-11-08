@php $isFeatured = isset($featured) @endphp

<a href="{{ $course->url }}" class="course mini {{ $isFeatured ? 'woopra-track' : '' }}" data-woopra='{ "type": {{ $isFeatured ? '"featuredCourse", "action":"follow", "title":"'.$course->title.'"' : '"course"'}} }'>
    <div class="photo">
        <img src="{{ $course->getPrintableImageUrl() }}">
        <div class="preview-icon">
            <i class="eye icon"></i>
        </div>
    </div>
</a>

@include('parts.course.hover-box', ['position' => $position ?? 'right center'])