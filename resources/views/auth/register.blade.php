@extends('layouts.app')

@section('content')
    <div class="mini wrapper">
        <div id="content" class="shadow padded-twice">
            <h1><i class="user add icon"></i> Register</h1>
            <p>Join the community by creating a new account</p>

            @include('auth.validation-errors')

            <hr>

            @if (config('auth.registration_enabled'))
                <form class="ui form" action="{{ url('register') }}" method="POST">
                    {{ csrf_field() }}

                    <div class="field">
                        <label>Name</label>
                        <input type="text" name="name" value="{{ old('name') }}" required autofocus>
                    </div>

                    <div class="field">
                        <label>Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" required id="email">
                    </div>

                    <div class="field">
                        <label>Password</label>
                        <input type="password" name="password" required>
                    </div>

                    <div class="field">
                        <label>Password Confirmation</label>
                        <input type="password" name="password_confirmation" required>
                    </div>

                    <div class="center aligned mt-30">
                        <button type="submit" class="ui primary right labeled icon button">
                            Create <b>My Account</b>
                            <i class="user add icon"></i>
                        </button>
                    </div>
                </form>
                <hr>
            @else
                <div class="ui info message" style="font-size: 18px">
                    <i class="info circle icon"></i>
                    The registration process is closed at the moment.
                </div>
            @endif

            <div class="center aligned">
                <div><a href="{{ url('login') }}">I already have an account</a></div>
                <div><a href="{{ url('password/reset') }}">I've lost my password</a></div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>

        $('document').ready(function() {
            document.cookie.split('; ').reduce(function(i, v) {
                var parts = v.split('=');
                if (parts[0] == 'lurn_email') {
                    $('#email').val(parts[1]);
                }
            })
        });
    </script>
@endsection
