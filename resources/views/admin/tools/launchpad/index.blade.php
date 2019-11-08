@extends('layouts.app')

@section('header-slot')
    @include('parts.breadcrumbs', ['items' => [
        ['title' => 'Admin', 'url' => route('tools.index')],
        ['title' => $tool]
    ]])
@endsection

@section('content')
    <iframe src="http://launchpad.fredomfighterclub.com/lurn/initiate" width="100%" height="100%" allowfullscreen="" frameborder="0" class="business-builder-frame" id="launchpad" style="margin-top: -15px; border: 0; overflow: hidden"></iframe>
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
            $('#launchpad').css(
                'height',
                window.innerHeight - $('#header').height()
            );
        });
    </script>
@endsection
