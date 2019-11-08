@extends('layouts.app')

@section('content')
    <div class="wrapper profile-page">
        <div class="content-wrapper padded-twice">
            <h1><i class="fa fa-twitter"></i> Share a Tweet!</h1>
            <p>Post your Tweet about Lurn Nation and earn points!</p>

            <hr>

            <form action="{{ route('social-share.twitter') }}" method="post">
                {{ csrf_field() }}

                <input type="hidden" name="client" value="{{ $client }}">
                <input type="hidden" name="secret" value="{{ $secret }}">

                <textarea class="form-control" name="message" style="width: 100%">Lurn Nation is an awesome place to start your business! Check it out! http://lurn.com #LurnNation</textarea>

                <div class="center aligned mt-30">
                    <button class="ui secondary left labeled icon button">
                        <i class="check icon"></i>
                        <strong>Submit</strong>
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection