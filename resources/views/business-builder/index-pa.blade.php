@extends('layouts.app')

@section('content')
<iframe src="{{ env('PABB_URL', 'https://pabb.lurn.com') }}/lurn/initiate" width="100%" height="100%" allowfullscreen="" frameborder="0" class="business-builder-frame"
        style="margin-top: -15px;border: 0; overflow: hidden"></iframe>
@endsection
@section('css')
<style>
    body {
        overflow: hidden;
    }
</style>
@endsection
