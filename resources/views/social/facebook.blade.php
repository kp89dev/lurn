@extends('layouts.app')

@section('content')
    <div class="wrapper profile-page">
        <div class="content-wrapper padded-twice">
            <h1><i class="fa fa-facebook"></i> Share a Post!</h1>
            <p>Post on Facebook about Lurn Nation and earn points!</p>

            <hr>

            <form action="{{ route('social-share.facebook') }}" method="post">
                {{ csrf_field() }}

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