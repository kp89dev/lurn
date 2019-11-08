@extends('layouts.app')

@section('content')
    <div class="wrapper">
        @if(!user()->badges->count())
            <div id="content" class="badges-page padded-twice">
                <h1><i class="certificate icon"></i> Badges &mdash; <span>{{ $course->title }}</span></h1>

                <hr>

                <div class="badges">
                    @foreach($badges as $badge)
                        <div class="badge">
                            <table>
                                <tr>
                                    <td class="photo">
                                        <img src="{{ $badge->src }}" width="90">
                                    </td>
                                    <td>
                                        <h3>{{ $badge->title }}</h3>
                                        <p>{!! $badge->content !!}</p>
                                    </td>

                                    <td class="action">
                                        <a href="{{ route('front.badges.request', ['badge' => $badge->id, 'course' => $course->slug]) }}" class="ui secondary right labeled icon button">
                                            Get <b>Badge</b>
                                            <i class="caret right icon"></i>
                                        </a>
                                        <p> Earned by students! </p>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <table class="shadow">
                <tr>
                    <td id="sidebar">
                        <div class="padded">
                            <hr>
                            <div class="widget">
                                <h3>Your Badges</h3>
                                <div class="badges center aligned">
                                    @foreach (user()->badges as $badge)
                                        <div class="badge">
                                            <img src="{{ $badge->src }}">
                                        </div>
                                        @if(($loop->index+1)%3 === 0 and $loop->index !== 0)
                                            <br>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </td>

                    <td id="content" class="badges-page padded-twice">
                        <h1><i class="certificate icon"></i> Badges &mdash; <span>{{ $course->title }}</span></h1>

                        <hr>

                        <div class="badges">
                            @foreach($badges as $badge)
                                <div class="badge">
                                    <table>
                                        <tr>
                                            <td class="photo">
                                                <img src="{{ $badge->src }}" width="90">
                                            </td>
                                            <td>
                                                <h3>{{ $badge->title }}</h3>
                                                <p>{!! $badge->content !!}</p>
                                            </td>

                                            <td class="action">
                                                <a href="{{ route('front.badges.request', ['badge' => $badge->id, 'course' => $course->slug]) }}" class="ui secondary right labeled icon button">
                                                    Get <b>Badge</b>
                                                    <i class="caret right icon"></i>
                                                </a>
                                                <p> Earned by students! </p>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            @endforeach
                        </div>
                    </td>
                </tr>
            </table>
        @endif
    </div>
@endsection
