@extends('layouts.app')

@section('header-slot')
    @include('parts.breadcrumbs', ['items' => [
        ['title' => $course->title, 'url' => route('course', $course->slug)],
        ['title' => $tool]
    ]])
@endsection

@section('content')
    <iframe src="http://digital-lead.lurntechnology.com/lurn/initiate" width="100%" height="100%" allowfullscreen="" frameborder="0" class="dla-creator-frame" id="dla-creator" style="margin-top: -15px; border: 0; overflow: hidden"></iframe>
@endsection

@section('css')
    <style>
        body {
            overflow: hidden;
        }
    </style>
@endsection

@section('js')
    <script>
        $(document).ready(function () {
            function updateFrameHeight () {
                var feedbackBar = $('#feedback-bar');
                var height = window.innerHeight - $('#header').height();

                if (feedbackBar.length) {
                    height -= feedbackBar.height();
                }

                $('#launchpad').css('height', height);
            }

            updateFrameHeight();
            $(window).resize(updateFrameHeight);
        });
    </script>
@endsection
