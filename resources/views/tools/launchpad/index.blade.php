@extends('layouts.app')

@section('header-slot')

@endsection

@section('content')
    <iframe src="http://launchpad.freedomfighterclub.com/lurn/initiate" width="100%" height="100%" allowfullscreen="" frameborder="0" class="business-builder-frame" id="launchpad" style="margin-top: -15px; border: 0; overflow: hidden"></iframe>
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
