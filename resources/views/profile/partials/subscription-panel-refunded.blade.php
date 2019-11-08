<tr>
    <td>
        <a href="{{ $sub->course->url }}">
            {{ $sub->course->title }}
        </a>
    </td>
    <td>{{ $sub->paid_at ? $sub->paid_at->format('D, F jS, Y') : 'N/A' }}</td>
    <td class="right aligned">
        Refunded on {{ $sub->paid_at ? $sub->refunded_at->format('D, F jS, Y') : 'N/A' }}
    </td>
</tr>