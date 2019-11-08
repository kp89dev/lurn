@if ($subscriptions->count())
    <h3>
        <i class="rss flipped icon"></i>
        Course Subscriptions
    </h3>
    <p>All the courses that you're enrolled in.</p>

    <table class="ui blue table">
        <thead>
            <tr>
                <th>Course Name</th>
                <th>Next Bill Date</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($subscriptions as $sub)
                @continue ($sub->course->infusionsoft->subscription != 1)

                @if ($sub->refunded_at)
                    @include('profile.partials.subscription-panel-cancelled')
                    @include('profile.partials.subscription-panel-refunded')
                @elseif ($sub->cancelled_at)
                    @include('profile.partials.subscription-panel-cancelled')
                @else
                    @include('profile.partials.subscription-panel-active')
                @endif
            @endforeach
        </tbody>
    </table>
@endif