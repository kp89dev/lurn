@extends('layouts.app')

@section('content')
<div id="forum" class="wrapper">
    {!! $vanillaForum->forum_rules !!}
    
    <div class="rules-response">
        <form class="response" id="agree" name="agree" action="{{ route('webhook.forum.rules') }}" >
            <input type="hidden" name="userId" value="{{ user()->id }}" />
            <input type="hidden" name="courseId" value="{{$vanillaForum->course_id }}" />
            <input type="hidden" name="link" value="{{$vanillaForum->url }}" />
            <input type="hidden" name="status" value="1" />
            <button type="submit">Agree</button>
        </form>
        
        <form class="response" id="disagree" name="disagree" action="{{ route('webhook.forum.rules') }}" >
            <input type="hidden" name="userId" value="{{ user()->id }}" />
            <input type="hidden" name="courseId" value="{{$vanillaForum->course_id }}" />
            <input type="hidden" name="link" value="#" />
            <input type="hidden" name="status" value="0" />
            <button type="submit">Disagree</button>
        </form>
    </div>
</div>
@endsection

@section('js')
<script type="text/javascript" src="{{ mix('js/forum.js') }}"></script>
@endsection
