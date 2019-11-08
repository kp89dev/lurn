<tr>
    <td>
        <a href="{{ $sub->course->url }}">
            {{ $sub->course->title }}
        </a>
    </td>
    <td>{{ $sub->paid_at ? $sub->paid_at->format('D, F jS, Y') : 'N/A' }}</td>
    <td class="right aligned">
        <form action="{{ route('cancel.subscription') }}" method="POST">
            @csrf
            <input type="hidden" value="{{ $sub->course->infusionsoft->is_product_id }}" name="subscription">
            <input type="hidden" value="{{ $sub->course->id }}" name="course_id">
            <button class="ui red button trackable" data-event-name="Cancel Subscription">
                <i class="remove icon"></i>
                Cancel
            </button>
        </form>
    </td>
</tr>