<div class="meta padded">
    <div class="ui three columns grid">
        <div class="wide column">
            <div class="value">{{ shorter_number($modulesNo = $course->getCounters()->modules) }}</div>
            <div class="lbl">{{ str_plural('Module', $modulesNo) }}</div>
        </div>
        <div class="wide column">
            <div class="value">{{ shorter_number($lessonsNo = $course->getCounters()->lessons) }}</div>
            <div class="lbl">{{ str_plural('Lesson', $lessonsNo) }}</div>
        </div>
        <div class="wide column">
            <div class="value">{{ shorter_number($studentsNo = $course->getCounters()->students) }}</div>
            <div class="lbl">{{ str_plural('Student', $studentsNo) }}</div>
        </div>
    </div>
</div>