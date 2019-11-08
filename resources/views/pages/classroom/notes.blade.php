@extends('layouts.app')

@section('content')
    <div class="classroom-course wrapper">
        <table class="shadow">
            <tr>
                <td id="sidebar">
                    <div>
                        <div class="relative">
                            @include('parts.course.sidebar', compact('course'))
                        </div>
                    </div>
                </td>

                <td id="content">
                    <div id="breadcrumbs">
                        <ul>
                            <li><a href="{{ route('classroom') }}">Classroom</a></li>
                            <li><a href="{{ route('course', $course->slug) }}">{{ $course->title }}</a></li>
                        </ul>
                    </div>

                    <div class="padded-twice">
                        <h1>
                            <i class="text file icon"></i>
                            <span>{{ $course->title }}</span> Notes Taken

                            <a href="{{ route('print-notes', $course->slug) }}"
                               class="ui primary small icon button right floated">
                                <i class="print icon"></i>
                                <b>Print</b> Notes
                            </a>
                        </h1>

                        <p>A list of all your notes taken on this course.</p>

                        @if (count($notes))
                            <ul class="notes">
                                @foreach ($notes as $note)
                                    <li>
                                        <div class="lesson">
                                            <a href="{{ route('lesson', [$note->course->slug, $note->lesson->module->slug, $note->lesson->slug]) }}">
                                                <span class="ui label">Module <strong>#{{ $note->moduleIndex }}</strong></span>
                                                <span class="ui label">Lesson <strong>#{{ $note->lessonIndex }}</strong></span>
                                                {{ $note->lesson->title }}
                                            </a>
                                        </div>
                                        <div class="note">
                                            {!! $note->notes !!}
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p>You haven't taken any notes yet.</p>
                        @endif
                    </div>
                </td>
            </tr>
        </table>
    </div>
@endsection
