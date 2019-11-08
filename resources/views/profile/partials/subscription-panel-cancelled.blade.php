<tr>
    <td>
        <a href="{{ $sub->course->url }}">
            {{ $sub->course->title }}
        </a>
    </td>
    <td>{{ $sub->paid_at ? $sub->paid_at->format('D, F jS, Y') : 'N/A' }}</td>
    <td class="right aligned">
        Cancelled on {{ $sub->cancelled_at->format('D, F jS, Y') }}
    </td>
</tr>