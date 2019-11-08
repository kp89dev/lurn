@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12 niche-design">
                <div class="wrapper">
                    <h1 class="page-title">Pick your Niche</h1>
                    <h2 class="page-tagline">Which are you interested in?</h2>

                    <div class="vsep"></div>
                    <div class="pick-niche-button-block">
                        {{ csrf_field() }}
                        @foreach($categories as $category)
                            <div>
                                <div><a class="btn btn-hg btn-lg btn-primary btn-block niche-category " onclick="openNicheCategory(this, {{$category->id}})">{{$category->label}}</a></div>
                                <div class="niches"></div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="page-content-wrapper frontend-progress-bar">
                    <div class="page-content nopad niche-design load-screen">
                        <div class="message">5 seconds while we gather your info</div>
                        <div class="progressbar"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.10.2/css/lightness/jquery-ui-1.10.2.custom.min.css">
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href=" {{ mix('/css/niche.css') }}">
@endsection
@section('js')
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.10.2/jquery-ui.min.js"></script>

    <script type="text/javascript" src="{{ mix('js/niche-detective/nichetool.js') }}"></script>
@endsection
