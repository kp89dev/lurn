<script type="text/javascript">
    var onPageValidationErrors = false;
</script>
@if (count($errors))
    <div id="validation-message" class="ui inverted red segment">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>

    <script type="text/javascript">
        var onPageValidationErrors = true;
    </script>
@endif
