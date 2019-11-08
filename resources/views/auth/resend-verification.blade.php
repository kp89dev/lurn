@extends('layouts.app')

@section('content')

    <div class="mini wrapper">
        <div id="content" class="shadow padded-twice">
            <h1><i class="refresh icon"></i> Resend Verification</h1>

            @include('auth.validation-errors')

            <hr>

            <form class="ui form" action="{{ route('resend-verification') }}" method="POST">
                {{ csrf_field() }}
                <input type="hidden" name="remember" value="1">

                <div class="field">
                    <label>E-Mail Address</label>
                    <input name="email" value="{{ $email or old('email') }}" required autofocus>
                </div>

                <div class="ui two columns grid fluid-at w560">
                    <div class="wide column">
                        <div><a href="{{ url('login') }}" tabindex="-1">I've already confirmed my account</a></div>
                    </div>
                    <div class="wide column right aligned">
                        <button class="ui primary right labeled icon button">
                            <b>Resend Verification</b>
                            <i class="refresh icon"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
