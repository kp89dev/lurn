<ol>
@foreach($test->questions as $question)
    <li>
        <span>{{ $question->title }}</span>
        <ul>
            @foreach ($question->answers as $answer)
                @if ($answer->is_answer == 1)
                    <li style='background-color: #7BB661'>{{ $answer->title }}</li>
                @else
                    <li>{{ $answer->title }}</li>
                @endif
            @endforeach
        </ul>
    </li>
    <br />
@endforeach
</ol>
