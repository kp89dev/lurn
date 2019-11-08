@extends('layouts.app')

@section('content')
    <div class="wrapper">
        <div id="content" class="padded-twice shadow">
            <h1><i class="university icon"></i> Enrollment</h1>
            
            @if (user()->points >= $course->buy_with_points)
            <p>
                Excellent choice! You may enroll in this course for <strong>{{ number_format($course->buy_with_points) }}</strong> points
                that you have collected through engagement with Lurn Nation. You have <strong>{{ number_format(user()->points) }}</strong> points
                that you can spend. After enrolling in this course, you will have a balance of {{ number_format(user()->points - $course->buy_with_points) }}
                leftover.
            </p>
            <p>If you would like to continue, please press the <strong>Enroll</strong> button and you will receive immediate access to this awesome course!</p>
            @else
            <p>
                Excellent choice! You may enroll in this course for <strong>{{ number_format($course->buy_with_points) }}</strong> points
                that you collect through your engagement with Lurn Nation. Unfortunately, it looks like you haven't earned enough points to
                purchase this course.
            </p>
            <p>
                Participate in activities around Lurn Nation to earn points, such as completing other free or premium courses. 
                <a href="{{ route('profile') }}">Learn more here</a>.
            </p>
            @endif

            <hr />

            <form id="enroll" class="ui form" action="{{ route('do.enrollment', $course->slug) }}" method="POST">
                {{ csrf_field() }}

                <table id="cart-item">
                    <tr>
                        <th colspan="2">Course</th>
                        <th>Total Now</th>
                    </tr>
                    <tr class="info">
                        <td class="image">
                            <img src="{{ $course->getPrintableImageUrl() }}">
                        </td>
                        <td class="title">
                            @if ($course->categoryList->count())
                                <p>{{ $course->categoryList->implode(', ') }}</p>
                            @endif
                            <h2>{{ $course->title}}</h2>
                        </td>
                        <td class="course-price">
                            {{ number_format($course->buy_with_points, 0) }} pts
                        </td>
                    </tr>
                </table>

                <hr>

                @if (user()->points >= $course->buy_with_points)
                <div class="center aligned mt-30">
                    <button class="ui secondary left labeled icon button">
                        <i class="check icon"></i>
                        <strong>Enroll{{--  with {{ number_format($course->buy_with_points) }} Points --}}</strong>
                    </button>
                </div>
                @endif
            </form>
        </div>
    </div>
@endsection