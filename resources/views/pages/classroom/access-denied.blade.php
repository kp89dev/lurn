@extends('layouts.app')

@section('content')
    <div class="wrapper">
        <div id="content" class="support-page padded-twice">
            <h1><i class="question circle icon"></i> Subscription Ended</h1>
            <p>Your subscription has ended.</p>

            <hr>

            <p>Please check to see if you've made all of the payments on time. Otherwise, please contact <a href="mailto:{{ config('support.email') }}">{{ config('support.email') }}</a> to clarify your situation.</p>
        </div>
    </div>
@endsection
