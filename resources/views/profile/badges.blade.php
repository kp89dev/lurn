@if ($badges = user()->badges)
    <div class="column">
        <h3>
            <i class="shield icon"></i>
            Earned Badges
        </h3>

        <div class="badges">
            @foreach ($badges as $badge)
                <div class="badge">
                    <img src="{{ $badge->src }}" width="90">
                </div>
            @endforeach
        </div>
    </div>
@endif
