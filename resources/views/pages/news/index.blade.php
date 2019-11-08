@extends('layouts.app')

@section('title', 'Latest News')

@section('content')
    <div class="wrapper">
        <table class="shadow">
            <tr>
                <td id="sidebar">
                    <div class="padded">
                        @if ($adFirst->count())
                            @foreach ($adFirst as $ad)
                                @include('parts.dashboard-ad', compact('ad'))
                            @endforeach
                        @endif

                        @if ($adSecond->count())
                            @foreach ($adSecond as $ad)
                                @include('parts.dashboard-ad', compact('ad'))
                            @endforeach
                        @endif
                    </div>
                </td>

                <td id="content">
                    <div class="padded-twice">
                        <h1><i class="newspaper icon"></i> News</h1>

                        <hr>

                        <div id="news-expanded">
                            @foreach ($news as $article)
                                <div class="news-excerpt">
                                    <h2>
                                        <i class="newspaper icon"></i>
                                        <a href="{{ route('news-article', $article->slug) }}">
                                            {{ $article->title }}
                                        </a>
                                    </h2>
                                    <div class="date">
                                        <span>Published on:</span>
                                        {{ $article->created_at->format('M j, Y') }}
                                    </div>
                                    <div class="excerpt">
                                        {!! $article->excerpt !!}

                                        <a href="{{ route('news-article', $article->slug) }}">Read more &raquo;</a>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{ $news->links() }}
                    </div>
                </td>
            </tr>
        </table>
    </div>
@endsection
