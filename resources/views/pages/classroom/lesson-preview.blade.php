@extends('layouts.app')

@section('content')
    <div class="wrapper course-page">
        <table class="shadow">
            <tr>
                <td id="content">
                    <div id="breadcrumbs">
                        <ul>
                            <li><a href="{{ route('classroom') }}">Classroom</a></li>
                            <li><a href="{{ route('course', $course->slug) }}">{{ $course->title }}</a></li>
                            <li>
                                <a href="{{ route('module', [$course->slug, $currentModule->slug]) }}">
                                    {{ $currentModule->title }}
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div id="course-content" class="padded-twice">
                        <p class="lesson">Lesson #{{ $currentLesson->getIndex() }}</p>
                        <h2>{{ $currentLesson->title }}</h2>

                        {!! $currentLesson->description !!}
                    </div>

                    <div class="padded-twice">
                        <div id="course-navigation" class="ui two columns grid">
                            <div class="wide column right aligned">
                                @if ($relatedLessons->previous && $relatedLessons->previous->module->slug != 'on-boarding')
                                    @if($relatedLessons->previous instanceOf App\Models\Lesson)
                                    <a
                                        target ="{{ ($relatedLessons->previous->type == 'Link' ? '_blank' : '')}}"
                                        href="{{ route('lesson', [
                                                    $course->slug,
                                                    $relatedLessons->previous->module->slug,
                                                    $relatedLessons->previous->slug]
                                                ) }}"
                                       class="ui prev fluid primary left labeled icon button ask-if-complete">
                                        <i class="angle left icon"></i>
                                        <b>{{ $relatedLessons->previous->module->title }}</b>
                                        <div>{{ $relatedLessons->previous->title }}</div>
                                    </a>
                                    @else
                                    <a href="{{ route('test', [
                                                    $course->slug,
                                                    $relatedLessons->previous->getModule()->slug,
                                                    $relatedLessons->previous->id]
                                                ) }}"
                                       class="ui prev fluid primary left labeled icon button ask-if-complete">
                                        <i class="angle left icon"></i>
                                        <b>{{ $relatedLessons->previous->getModule()->title }}</b>
                                        <div>{{ $relatedLessons->previous->title }}</div>
                                    </a>
                                    @endif
                                @endif
                            </div>
                            <div class="wide column">
                                
                            </div>
                        </div>
                    </div>
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
