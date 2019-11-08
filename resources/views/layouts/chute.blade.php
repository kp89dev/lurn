<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.3.0/semantic.min.css">
    <link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Lato:100,300,900|Pangolin">
    <link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/izitoast/1.1.1/css/iziToast.min.css">
    <link rel="stylesheet" type="text/css" href="{{ mix('css/app.css') }}">
    @yield('css')

    <title>Lurn Nation On-boarding</title>

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
            font-family: 'gt_walsheim_promedium_oblique';
            src: url('/css/fonts/gt-walsheim-pro-medium-oblique-webfont.eot');
            src: url('/css/fonts/gt-walsheim-pro-medium-oblique-webfont.eot?#iefix') format('embedded-opentype'), url('/css/fonts/gt-walsheim-pro-medium-oblique-webfont.woff2') format('woff2'), url('/css/fonts/gt-walsheim-pro-medium-oblique-webfont.woff') format('woff'), url('/css/fonts/gt-walsheim-pro-medium-oblique-webfont.ttf') format('truetype'), url('/css/fonts/gt-walsheim-pro-medium-oblique-webfont.svg#gt_walsheim_promedium_oblique') format('svg');
            font-weight: normal;
            font-style: normal;
        }

        body.onboarding #ext-onboarding {
            max-width: 1200px;
            margin: auto;
        }

        body.onboarding #ext-onboarding li.active {
            color: #49a510;
        }

        body.onboarding .menu.ob {
            background-color: #eff1f4;
            margin-bottom: 3em;
        }

        body.onboarding .menu.ob ul {
            margin: 0;
            padding: 0;
        }

        body.onboarding .menu.ob ul li {
            display: inline-block;
            font-size: 1em;
            padding: 1em .6em;
            width: 17%;
            text-align: center;
            font-family: 'gt_walsheim_proregular', sans-serif;
        }

        body.onboarding {
            background-color: #fff;
            margin-bottom: 3em;
            color: #3d444e;
        }

        body.onboarding .fa {
            padding-right: 0.25em;
        }

        body.onboarding #header {
            margin-bottom: 0;
        }

        .card {
            border-radius: 4px;
            background-color: #eff1f4;
            overflow: hidden;
            cursor: pointer;
            transition: all .2s;
        }

        .card:hover {
            opacity: .8;
        }

        .card .picked {
            color: #49a510;
            font-size: 24px;
            float: right;
            margin-top: -3px;
            padding-right: 0 !important;
        }

        .card .category-picture {
            font-size: 0;
            line-height: 0;
        }

        .card .name {
            padding: 1em;
            color: #1e4f94;
            font-weight: bold;
        }

        .card .name input {
            visibility: hidden;
        }

        .card img {
            width: 100%;
        }

        h1 {
            font-family: 'gt_walsheim_probold', sans-serif;
            font-size: 3.5em;
            margin-bottom: 1em;
            color: #2c4b86;
            text-align: center;
        }

        p {
            margin-left: 0;
            font-family: 'gt_walsheim_proregular', sans-serif;
            font-size: 1.4em;
            margin-bottom: .3em;
        }

        p.note {
            margin-left: 0;
            font-family: 'gt_walsheim_promedium_oblique', sans-serif;
        }

        .ui.grid.container .extra {
            padding: 1.5rem;
        }

        form {
            margin-bottom: 60px;
        }

        form .ui.button {
            color: #1a3e6f !important;
            background-color: #ebc71d;
            font-weight: bold;
            font-size: 1.3em;
            width: 70%;
            border-radius: 2em;
            text-align: center;
            margin: 0;
            display: block;
            font-family: gt_walsheim_promedium, sans-serif;
            padding: .2em .5em;
            text-transform: uppercase;
        }

        form .ui.button.next {
            float: right;
            padding: 15px 15px 10px;
            line-height: normal;
            vertical-align: middle;
        }

        form .ui.button.muted {
            background-color: #f5e38e;
            padding: 15px 15px 10px;
        }

        form .ui.button:hover {
            background-color: #ffd81f;
        }

        div.ui.grid > div.column.five.wide:first-child {
            padding-left: 0;
        }

        div.ui.grid > div.column.five.wide {
            margin-left: auto;
            margin-right: auto;
        }

        .course-column a {
            display: block;
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .course-column .description,
        .course-column .description p {
            font-size: 16px !important;
            font-weight: normal !important;
        }

        .course-column .description {
            height: 132px;
            overflow: hidden;
        }
    </style>
</head>
<body class="onboarding">
<div id="header">
    <div class="wrapper">
        <table>
            <tr>
                <td id="logo">
                    <img src="{{ url('images/logo.svg') }}" alt="Lurn Central Logo">
                </td>
            </tr>
        </table>
    </div>
</div>
<div class="menu ob">
    <div id="ext-onboarding">
        <ul>
            <li><i class="fa fa-check"></i> Account Created</li>
            <li class="{{ route_is('onboarding.index') ? 'active' : '' }}">
                <i class="fa fa-heart"></i> Interests
            </li>
            <li class="{{ route_is('onboarding.courses') ? 'active' : '' }}">
                <i class="fa fa-book"></i> Courses
            </li>
            <li class="{{ route_is('onboarding.demo') ? 'active' : '' }}">
                <i class="fa fa-play-circle-o"></i> Demo
            </li>
            <li><i class="fa fa-flag"></i> Access Lurn Nation</li>
        </ul>
    </div>
</div>
<div class="content">
    @if (optional($errors)->count())
        <div class="ui container" style="margin-bottom: 30px;">
            <div class="ui negative message">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    @yield('content')
</div>
@yield('js')
</body>
</html>