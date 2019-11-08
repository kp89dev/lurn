@isset ($course)
    <div id="completed-course" class="ui modal">
        <div class="content">
            <div class="thumbnail">
                <img src="{{ $course->getPrintableImageUrl() }}">
            </div>

            <i class="green check icon"></i>
            <h1>Congratulations!</h1>
            <p>You've completed <strong>{{ $course->title }}</strong>!</p>

            <a href="{{ route('course', $course->slug) }}" class="ui big secondary button">
                <i class="check icon"></i>
                <b>Awesome!</b>
            </a>
        </div>
    </div>
@endisset