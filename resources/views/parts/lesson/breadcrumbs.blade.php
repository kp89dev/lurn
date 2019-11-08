<div id="breadcrumbs">
    <ul>
        <li><a href="{{ route('classroom') }}">Classroom</a></li>
        <li><a href="{{ route('course', $course->slug) }}">{{ $course->title }}</a></li>
        <li>
            <a href="{{ route('module', [$course->slug, $currentModule->slug]) }}">
                {{ $currentModule->title }}
            </a>
        </li>
    </ul>
</div>