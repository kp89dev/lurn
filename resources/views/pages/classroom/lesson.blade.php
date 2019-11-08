@extends('layouts.app')

@section('content')
    <div class="wrapper course-page">
        <table class="shadow">
            <tr>
                @include('parts.lesson.sidebar')

                <td id="content">
                    @include('parts.lesson.breadcrumbs')

                    @if (! $currentModule->isLocked())
                        <div id="course-content" class="padded-twice">
                            @include('parts.lesson.notes')

                            <p class="lesson">Lesson #{{ $currentLesson->getIndex() }}</p>
                            <h2>{{ $currentLesson->title }}</h2>

                            {!! $currentLesson->description !!}
                            <div class="clear"></div>
                        </div>

                        @include('parts.lesson.navigation', ['currentLesson' => $currentLesson])
                    @else
                        <div class="padded-twice">
                            <h2><i class="icon lock"></i>Sorry, this content is locked.</h2>
                            <p>The lesson you're trying to access is locked by an earlier module. Please go back
                                and pass all tests and quizzes to view this content.</p>
                        </div>
                    @endif
                </td>
            </tr>
        </table>
    </div>
@endsection

@section('js')
    <script src="//player.vimeo.com/api/player.js"></script>
    <script type="text/javascript" src="{{ mix('js/dynamic-modal.js') }}"></script>
    <script type="text/javascript">
        var notes = {!! json_encode($currentLesson->unsafeNotes) !!},
            course = {{ $course->id }},
            lesson = {{ $currentLesson->id }},
            completed = {{ (int) user_completed($currentLesson) }};
    </script>
    <script type="text/javascript" src="{{ mix('js/lesson.js') }}"></script>
    <script type="text/javascript" src="{{ mix('js/forum.js') }}"></script>
    <script type="text/javascript" src="{{ mix('js/countdown.js') }}"></script>
@endsection
