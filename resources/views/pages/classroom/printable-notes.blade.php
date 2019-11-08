<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />

    <title>{{ $course->title }} Notes Taken</title>

    <style>
        body {
            margin: 15px;
            font-family: Roboto, sans-serif;
        }

        hr {
            margin: 30px 0;
        }

        h3 {
            color: #3e68bb;
        }

        .lesson {
            padding: 15px;
        }

        .lesson:last-child {
            border-bottom: none;
        }

        .lesson > p {
            margin: 0 0 15px;
            font-weight: bold;
        }
    </style>
</head>
<body onload="print()">

<h1>{{ $course->title }} Notes Taken</h1>

<?php $module = null ?>

@foreach ($notes as $note)
    @if ($module != $note->moduleIndex)
        <?php $module = $note->moduleIndex ?>
        <hr>
        <h3>Module #{{ $note->moduleIndex }} - {{ $note->lesson->module->title }}</h3>
    @endif

    <div class="lesson">
        <p>Lesson #{{ $note->lessonIndex }} - {{ $note->lesson->title }}</p>
        <div>{!! $note->notes !!}</div>
    </div>
@endforeach

</body>
</html>