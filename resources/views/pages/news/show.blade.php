@extends('layouts.app')

@section('title', $news->title)

@section('content')
    <div class="wrapper">
        <table class="shadow">
            <tr>
                <td id="sidebar">
                    <div class="padded">
                        <h3>Other News</h3>

                        <hr>

                        <div id="news">
                            <ul>
                                @foreach ($otherNews as $article)
                                    <li>
                                        <a href="{{ route('news-article', $article->slug) }}">
                                            {{ $article->title }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </td>

                <td id="content">
                    <div id="breadcrumbs">
                        <ul>
                            <li><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li><a href="{{ route('news') }}">News</a></li>
                        </ul>
                    </div>

                    <div class="padded-twice">
                        <h1><i class="newspaper icon"></i> {{ $news->title }}</h1>
                        <p>
                            <span style="font-weight: 300">Posted on:</span>
                            {{ $news->created_at->format('M j, Y') }}
                        </p>

                        <hr>

                        <div id="page-content">
                            {!! $news->content !!}
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </div>
@endsection
