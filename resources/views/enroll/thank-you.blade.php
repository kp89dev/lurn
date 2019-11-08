@extends('layouts.app')

@section('content')
    <div id="thank-you" class="wrapper">
        <div id="content" class="shadow">
            <div class="head padded-twice center aligned">
                <div class="icon">
                    <i class="check icon"></i>
                </div>
                <h1>Enrollment <strong>Completed</strong></h1>
                <p>
                    <strong>Congratulations!</strong> You now own
                    <strong><a href="{{ $course->url }}">{{ $course->title }}</a></strong>.
                </p>
            </div>
            @include('widgets.featured-courses', ['show' => 4, 'columns' => 6])
            @if ($recommended = $course->getRecommended(4))
                <div class="padded-twice">
                    <h3 class="center aligned mb-30">
                        People interested in this course, also own the following courses
                    </h3>
                    <div class="courses ui four columns grid">
                        @foreach ($recommended as $course)
                            <div class="column">
                                @include('parts.course.card', compact('course'))
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
