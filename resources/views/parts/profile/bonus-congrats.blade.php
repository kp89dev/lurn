<section class="current-bonus">
    <h3>
        <i class="magic icon"></i>
        Congratulations!
    </h3>

    <p>
        We hope you enjoy your FREE course.<br>
        There are more rewards coming, so keep checking back here to get your next reward.
    </p>

    <div class="courses center aligned">
        @include('parts.course.mini-card', ['course' => $bonus->course])
    </div>
</section>