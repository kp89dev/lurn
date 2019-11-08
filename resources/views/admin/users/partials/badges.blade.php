<div>
    <h4 class="profile-desc-title">Gained Badges</h4>
    <span class="profile-desc-text">
        @forelse ($user->badges as $badge)
            <img class="img-responsive pull-left" width="50" height="50" src="{{ $badge->src }}" alt="User Badge"
                 title="{{ $badge->title }}" style="margin-right: 10px; margin-bottom: 20px">
        @empty
            <i>No Badges gained</i>
        @endforelse
    </span>
</div>
