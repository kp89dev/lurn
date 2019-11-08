<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.3.0/semantic.min.css">
    <link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Lato:100,300,900|Pangolin">
    <link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/izitoast/1.1.1/css/iziToast.min.css">

    @yield('css')

    <script type="text/javascript" src="//code.jquery.com/jquery-3.2.1.min.js"></script>
    <script type="text/javascript"
            src="//cdnjs.cloudflare.com/ajax/libs/jquery-mousewheel/3.1.13/jquery.mousewheel.min.js"></script>
    <script type="text/javascript" src="https://use.fontawesome.com/1e851d58a1.js"></script>

    <style type="text/css">
        @font-face {
            font-family: 'gt_walsheim_probold';
            src: url('/css/fonts/gt-walsheim-pro-bold-webfont.eot');
            src: url('/css/fonts/gt-walsheim-pro-bold-webfont.eot?#iefix') format('embedded-opentype'), url('/css/fonts/gt-walsheim-pro-bold-webfont.woff2') format('woff2'), url('/css/fonts/gt-walsheim-pro-bold-webfont.woff') format('woff'), url('/css/fonts/gt-walsheim-pro-bold-webfont.ttf') format('truetype'), url('/css/fonts/gt-walsheim-pro-bold-webfont.svg#gt_walsheim_probold') format('svg');
            font-weight: normal;
            font-style: normal;
        }

        @font-face {
            font-family: 'gt_walsheim_promedium';
            src: url('/css/fonts/gt-walsheim-pro-medium-webfont.eot');
            src: url('/css/fonts/gt-walsheim-pro-medium-webfont.eot?#iefix') format('embedded-opentype'), url('/css/fonts/gt-walsheim-pro-medium-webfont.woff2') format('woff2'), url('/css/fonts/gt-walsheim-pro-medium-webfont.woff') format('woff'), url('/css/fonts/gt-walsheim-pro-medium-webfont.ttf') format('truetype'), url('/css/fonts/gt-walsheim-pro-medium-webfont.svg#gt_walsheim_promedium') format('svg');
            font-weight: normal;
            font-style: normal;
        }

        @font-face {
            font-family: 'gt_walsheim_proregular';
            src: url('/css/fonts/gt-walsheim-pro-regular-webfont.eot');
            src: url('/css/fonts/gt-walsheim-pro-regular-webfont.eot?#iefix') format('embedded-opentype'), url('/css/fonts/gt-walsheim-pro-regular-webfont.woff2') format('woff2'), url('/css/fonts/gt-walsheim-pro-regular-webfont.woff') format('woff'), url('/css/fonts/gt-walsheim-pro-regular-webfont.ttf') format('truetype'), url('/css/fonts/gt-walsheim-pro-regular-webfont.svg#gt_walsheim_proregular') format('svg');
            font-weight: normal;
            font-style: normal;
        }

        @font-face {
            font-family: 'gt_walsheim_prolight';
            src: url('/css/fonts/gt-walsheim-pro-light-webfont.eot');
            src: url('/css/fonts/gt-walsheim-pro-light-webfont.eot?#iefix') format('embedded-opentype'), url('/css/fonts/gt-walsheim-pro-light-webfont.woff2') format('woff2'), url('/css/fonts/gt-walsheim-pro-light-webfont.woff') format('woff'), url('/css/fonts/gt-walsheim-pro-light-webfont.ttf') format('truetype'), url('/css/fonts/gt-walsheim-pro-light-webfont.svg#gt_walsheim_prolight') format('svg');
            font-weight: normal;
            font-style: normal;
        }

        body {
            background-color: rgba(0, 0, 0, .5);
            padding: 40px;
            margin: 0;
            font-size: 1.75em;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        form {
            background-color: #eff1f4;
            padding: 1.5em;
            border-radius: 10px;
        }

        h1.down {
            color: #1a3e6f;
            font-size: 2.8em;
            font-weight: bold;
            font-family: "gt_walsheim_probold", sans-serif;
            margin-top: 50px;
        }

        h2 {
            font-family: 'gt_walsheim_prolight', sans-serif;
            font-size: 2em;
            font-weight: bold;
            color: #3d444e;
            text-align: center;
        }

        form .ui.button {
            color: #1a3e6f;
            background-color: #ebc71d;
            font-weight: bold;
            font-size: 1.7em;
            width: 70%;
            border-radius: 2em;
            text-align: center;
            margin: 0 auto;
            display: block;
            font-family: 'gt_walsheim_promedium', sans-sefif;
            padding: .5em .5em;
        }

        form .ui.button:hover {
            background-color: #ffd81f;
            color: #1a3e6f;
        }

        .ui.grid {
            background-color: #fff;
            width: 100%;
            max-width: 1200px;
            min-height: 490px;
            border-radius: 15px;
            padding: 40px;
            margin: 15px;
            box-shadow: 0 5px 10px 0 rgba(0, 0, 0, .2);
        }

        .ui.grid .ui.form .field {
            margin-bottom: 0.5em;
        }

        .ui.grid .ui.form button[type=submit] {
            margin-top: 1em;
        }

        .ui.grid .ui.form .field input {
            border-radius: 12px;
            font-family: 'gt_walsheim_prolight', sans-serif;
            padding: 0.75em;
        }

        .ui.grid .ui.form .field input::placeholder {
            font-family: 'gt_walsheim_prolight', sans-serif;
            font-size: 14px;
            color: #3d444e;
        }

        p {
            font-size: 1em;
            font-family: 'gt_walsheim_prolight', sans-serif;
        }

        div.ui.grid {
            max-width: 1200px;
        }

        p span {
            text-decoration: underline;
            font-family: 'gt_walsheim_promedium', sans-serif;
        }
    </style>
</head>
<body>
<div class="ui grid">
    <div class="column ten wide">
        @if ($errors)
            @foreach ($errors as $e)
                <span class="help-block">
                  <strong>{{ $e->getMessage() }}</strong>
              </span>
            @endforeach
        @endif
        <h1 class="down">Join Lurn Nation!</h1>
        <p>Sign Up for Your FREE Lurn Nation&trade; Membership & Get Access to
            <span class="training">Over 300 Hours of Free Training</span>
            from some of the World’s Leading Entrepreneurial Experts! Just Enter Your Information &amp;
            Click the Button to Get Started&hellip;</p>
    </div>
    <div class="column six wide">
        <form action="{{ route('register') }}" method="post" class="ui form" target="_top">
            @csrf
            <h2>Create My FREE Lurn Nation Account&hellip;</h2>
            <div class="field">
                <input type="text" name="name" placeholder="Your Name" required="required">
            </div>
            <div class="field">
                <input type="email" name="email" placeholder="Email Address" required="required" value="{{ request('email') }}">
            </div>
            <div class="field">
                <input type="password" name="password" placeholder="Password" required="required">
            </div>
            <div class="field">
                <input type="password" name="password_confirmation" placeholder="Confirm Password" required="required">
            </div>
            <button class="ui button" type="submit">GET STARTED</button>
        </form>
    </div>
</div>

</body>
</html>