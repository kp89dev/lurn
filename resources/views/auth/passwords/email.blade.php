@extends('layouts.app')

<!-- Main Content -->
@section('content')
    <div class="mini wrapper">
        <div id="content" class="shadow padded-twice">
            <h1><i class="repeat icon"></i> Recover Your Account</h1>
            <p>Insert your email to recover your account</p>

            @if (session('status'))
                <div id="validation-message" class="ui inverted green segment">
                    <ul>
                        <li>{{ session('status') }}</li>
                    </ul>
                </div>
            @endif

            <hr>

            <form class="ui form" action="{{ url('password/email') }}" method="POST">
                {{ csrf_field() }}

                <div class="field">
                    <label>E-Mail Address</label>
                    <input type="email" class="form-control" name="email" value="{{ $email or old('email') }}" required autofocus>
                </div>

                <div class="ui two columns grid fluid-at w560">
                    <div class="wide column">
                        <div><a href="{{ url('login') }}">I remember my credentials</a></div>
                        <div><a href="{{ url('register') }}">I want a new account</a></div>
                    </div>
                    <div class="wide column right aligned">
                        <button class="ui primary right labeled icon button">
                            <b>Recover</b>
                            <i class="repeat icon"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
