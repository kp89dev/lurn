<div class="padded-twice">
    <div id="course-navigation" class="ui two columns grid">
        <div class="wide column right aligned">
            @if ($relatedLessons->previous)
                @php $isTest = $relatedLessons->previous instanceof App\Models\Test @endphp

                <a target="{{ ($relatedLessons->previous->type == 'Link' ? '_blank' : '')}}"
                   class="ui prev fluid primary left labeled icon button ask-if-complete"
                   href="{{ route($isTest ? 'test' : 'lesson', [
                                $course->slug,
                                $relatedLessons->previous->module->slug,
                                $relatedLessons->previous->slug
                            ]) }}">
                    <i class="angle left icon"></i>
                    <div>{{ $relatedLessons->previous->module->title }}</div>
                    <b>{{ $relatedLessons->previous->title }}</b>
                </a>
            @endif
        </div>
        <div class="wide column">
            @if ($relatedLessons->next && $currentModule->slug != 'on-boarding')
                @php $isTest = $relatedLessons->next instanceof App\Models\Test @endphp

                <a target="{{ ($relatedLessons->next->type == 'Link' ? '_blank' : '')}}"
                   class="ui next fluid primary right labeled icon button {{
                            $course->confirm_after == 'L' ? 'ask-if-complete' : (
                                $currentLesson->module_id != $relatedLessons->next->module->id
                                    ? 'ask-if-complete mark-module'
                                    : 'silent-complete'
                            )
                       }}" href="{{ route($isTest ? 'test' : 'lesson', [
                                        $course->slug,
                                        $relatedLessons->next->module->slug,
                                        $isTest ? $relatedLessons->next->id : $relatedLessons->next->slug
                                    ]) }}">
                    <i class="angle right icon"></i>
                    <div>{{ $relatedLessons->next->module->title }}</div>
                    <b>{{ $relatedLessons->next->title }}</b>
                </a>
            @elseif ($relatedLessons->next && $currentModule->slug == 'on-boarding')
                <a href="{{ route('lesson', [$course->slug,
                                                                     $relatedLessons->next->module->slug,
                                                                     $relatedLessons->next->slug])
                                        }}" class="ui next fluid primary right labeled icon button ask-if-complete">
                    <i class="check icon"></i>
                    <div>Mark as complete</div>
                    <b>Click to mark On-boarding completed</b>
                </a>
            @elseif (user_completed($currentLesson))
                <a href="{{ $course->url }}" class="ui next fluid primary right labeled icon button ask-if-complete">
                    <i class="check icon"></i>
                    <div>Course completed</div>
                    <b>Click to go to the course page</b>
                </a>
            @else
                <a href="{{ $course->url }}" class="ui next fluid primary right labeled icon button ask-if-complete">
                    <i class="check icon"></i>
                    <div>Mark as complete</div>
                    <b>Click to mark the course as complete</b>
                </a>
            @endif
        </div>
    </div>
</div>
