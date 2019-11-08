<ul class="rewards-widget-steps">
    <li>
        <a href="{{ route('social-login', ['service' => 'facebook']) }}">
            <span>Facebook Post</span>
            <span class="label"><span>50</span> points</span>
        </a>
    </li>
    <li>
        <a href="{{ route('social-login', ['service' => 'twitter']) }}">
            <span>Post a Tweet</span>
            <span class="label"><span>50</span> points</span>
        </a>
    </li>
    <li>
        <a href="{{ route('social-login', ['service' => 'instagram']) }}">
            <span>Instagram Post</span>
            <span class="label"><span>50</span> points</span>
        </a>
    </li>
    <li data-title="25 points for every $1 of the course's value.">
        <a href="{{ route('classroom') }}">
            <span>Enroll Into a Premium Course</span>
            <span class="label">
                <i class="info circle icon"></i>
                <span>25</span> points
            </span>
        </a>
    </li>
    <li data-title="<b>Free Course</b> — 300 points.<br><b>Premium Course</b> — 10 points for every $1 of the course's value.">
        <a href="{{ route('dashboard') }}">
            <span>Finish a Course</span>
            <span class="label">
                <i class="info circle icon"></i>
                <span>300 / 10</span> points
            </span>
        </a>
    </li>
    <li>
        <a href="#todo">
            <span>Finish Lurn Orientation</span>
            <span class="label"><span>50</span> points</span>
        </a>
    </li>
</ul>