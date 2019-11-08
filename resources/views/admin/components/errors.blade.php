<div class="flash-message">
    @foreach (['danger', 'warning', 'success', 'info'] as $msg)
        @if(Session::has('alert-' . $msg))
            <div class="Metronic-alerts alert fade in alert-{{ $msg }}">{{ Session::get('alert-' . $msg) }} <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></div>
        @endif
    @endforeach
</div> <!-- end .flash-message -->

@if (isset($errors) && $errors->count())
    <div class="Metronic-alerts alert fade in alert-danger">
        @foreach ($errors->all() as $message)
            {{ $message }} <br/>
        @endforeach
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
    </div>
@endif


