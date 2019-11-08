@extends('layouts.app')

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

            <form class="ui form" action="{{ url('password/reset') }}" method="POST">
                {{ csrf_field() }}

                <input type="hidden" name="token" value="{{ $token }}">

                <div class="field">
                    <label>E-Mail Address</label>
                    <input type="email" class="form-control" name="email" value="{{ $email or old('email') }}" required autofocus>
                </div>

                <div class="field">
                    <label>New Password</label>
                    <input type="password" class="form-control" name="password" required>
                </div>

                <div class="field">
                    <label>Confirm Password</label>
                    <input type="password" class="form-control" name="password_confirmation" required>
                </div>

                <div class="center aligned">
                    <button class="ui primary right labeled icon button">
                        <b>Change Your Password</b>
                        <i class="repeat icon"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
