@extends('layouts.app')

@section('content')

    <div class="mini wrapper">
        <div id="content" class="shadow padded-twice">
            <h1><i class="sign in icon"></i> Login</h1>

            @include('auth.validation-errors')

            <hr>

            <form class="ui form" action="{{ url('login') }}" method="POST">
                {{ csrf_field() }}
                <input type="hidden" name="remember" value="1">

                <div class="ui two columns grid fluid-at w560">
                    <div class="column">
                        <div class="field">
                            <label>E-Mail Address</label>
                            <input name="email" value="{{ $email or old('email') }}" required autofocus>
                        </div>
                    </div>
                    <div class="column">
                        <div class="field left aligned">
                            <label>Password</label>
                            <input type="password" name="password" required>
                        </div>
                    </div>
                </div>
                <div class="ui two columns grid fluid-at w560">
                    <div class="column">
                        <div><a href="{{ url('password/reset') }}" tabindex="-1">I've lost my password</a></div>
                        <div><a href="{{ url('register') }}" tabindex="-1">Register a new account</a></div>
                        <div><a href="{{ url('resend-verification') }}" tabindex="-1">Resend verification</a></div>
                    </div>
                    <div class="column right aligned">
                        <button class="ui primary right labeled icon button">
                            <b>Login</b>
                            <i class="sign in icon"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @if (request()->oldMembersArea)
        <div class="ui modal">
            <div class="header">Please watch this video to understand why you are here</div>
            <div class="image content">
                <iframe src="https://player.vimeo.com/video/76979871" width="100%" height="480" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
            </div>
            <div class="actions">
                <div class="ui positive right labeled icon button">
                    Yes, I understand
                    <i class="checkmark icon"></i>
                </div>
            </div>
        </div>

        @section('js')
            <script src="https://player.vimeo.com/api/player.js"></script>
            <script>
                $('.ui.modal').modal('show');
                $(document).ready(function(){
                    var iframe = document.querySelector('iframe');
                    var iframePlayer = new Vimeo.Player(iframe);

                    iframePlayer.play();
                });
            </script>
        @endsection
    @endif
@endsection


