@extends('layouts.app')

@section('content')
    <div class="wrapper">
        <div id="content" class="support-page padded-twice">
            <h1><i class="map signs icon"></i> 404: Page Not Found</h1>
            <hr>
            <p>Were you looking for a course? Try the <a href="{{ url('classroom') }}">Classroom</a>.</p>
        </div>
    </div>
@endsection
