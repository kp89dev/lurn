<div class="meta padded">
    <div class="ui four columns grid">
        <div class="wide column">
            <div class="value">{{ shorter_number($modulesNo = $course->getCounters()->modules) }}</div>
            <div class="lbl">{{ $modulesNo === 1 ? 'Module' : 'Modules' }}</div>
        </div>
        <div class="wide column">
            <div class="value">{{ shorter_number($lessonsNo = $course->getCounters()->lessons) }}</div>
            <div class="lbl">{{ $lessonsNo === 1 ? 'Lesson' : 'Lessons' }}</div>
        </div>
        <div class="wide column">
            <div class="value">{{ shorter_number($enrolledUsersNo = $course->getCounters()->students) }}</div>
            <div class="lbl">{{ $enrolledUsersNo === 1 ? 'Student' : 'Students' }}</div>
        </div>
        <div class="wide column">
            @component('components.thumbs-up', ['course' => $course, 'user' => user()])
            @endcomponent
        </div>
    </div>
</div>