@if (isset($errors) && count($errors))
    <script>
        $(document).ready(function () {
            if (typeof onPageValidationErrors !== 'undefined' && onPageValidationErrors) {
                return;
            }

            @if (count($errors))
                iziError('Error', '{{ implode('\n', $errors->all()) }}');
            @endif
        });
    </script>
@endif

@if (session()->has('success'))
    <script>
        $(document).ready(function () {
            iziSuccess('Success', "{{ session()->get('success') }}");
        });
    </script>
@endif


