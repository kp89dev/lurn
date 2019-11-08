<div>
    @foreach($testResult->test->questions as $question)
        <div class="question">
            &bull; <span>{{ $question->title }}</span>
            <div class="answers">
                @foreach ($question->answers as $answer)
                    @if (isset($testResult->answer[$question->id], $testResult->answer[$question->id][$answer->id]))
                        <span style="{{ isset($testResult->answer[$question->id][$answer->id]) && $testResult->answer[$question->id][$answer->id] === 'correct' ? 'background-color: #7BB661' : 'background-color: #FF9899' }}">{{ $answer->title }}</span>
                    @else
                        <span>{{ $answer->title }}</span>
                    @endif
                @endforeach
            </div>
        </div>
        <br />
    @endforeach
</div>
