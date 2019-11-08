@extends('layouts.app')

@section('content')
    <div id="upsell" class="wrapper">
        <div id="content" class="shadow">
            {!!
                str_replace(
                    ['CART_URL', 'THANK_YOU_URL'],
                    [
                        route('enroll', ['course' => $course, 'token' => $upsellToken->token]),
                        route('enroll.thank-you', ['course' => $course]),
                    ],
                    $upsell->html
                )
            !!}
        </div>
    </div>
@endsection

@section('css')
    <style>
        {!! $upsell->css !!}}
    </style>
@endsection
