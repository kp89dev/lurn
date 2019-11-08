@extends('admin.layout')

@section('pagetitle')
    Lessons <small>lessons order</small>
@endsection

@section('breadcrumb')
    <li>
        <i class="icon-book-open"></i>
        <a href="{{ route('courses.index') }}">Course <b>{{ $course->title }}</b></a>
        <i class="fa fa-angle-right"></i>
    </li>
    <li>
        <i class="icon-book-open"></i>
        <a href="{{ route('modules.index', ['course' => $course->id ]) }}">Module <b>{{ $module->title }}</b></a>
        <i class="fa fa-angle-right"></i>
    </li>
    <li>
        <i class="fa fa-book"></i>
        <a href="/">Lessons Order</a>
    </li>
@endsection

@section('content')
    <?php $lessonNo = 1; ?>

    @if(count($lessons)>0)
        <form action="{{ route('lessons.order.store', ['course' => $course->id, 'module' => $module->id]) }}" method="POST">
            <div id="sortingSaveBtn" class="text-center">
                {{ csrf_field() }}
                <input type="hidden" name="ordered_lessons" id="ordered_lessons" value="">
                <button class="btn btn-primary btn-large btnSaveLessonOrder">Save Order</button>
            </div>
        </form>
        <br/>
    @endif
    <ul class='lessonList' style="list-style-type: none; margin: 0; padding: 0;">
        @foreach($lessons as $lesson)
            <li id="{{ $lesson->id }}" style="cursor: crosshair">
                <!-- BEGIN EXTRAS PORTLET-->
                <div class="portlet box blue">
                    <div class="portlet-title">
                        <div class="caption">
                            <b>Lesson #{{ $lessonNo++ }} : {{ $lesson->title }}</b>
                            @if ($lesson->type == "Link")
                                &nbsp; - &nbsp; <a href="javascript:;" style="color: #fff"><i class="fa fa-link" aria-hidden="true"></i> External Link</a>
                            @endif
                        </div>
                    </div>
                </div>
            </li>
        @endforeach
    </ul>
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            $(".lessonList").sortable();

            $('.btnSaveLessonOrder').click(function(){
                var ordered = "";
                $('.lessonList').children('li').each(function(key, elem) {
                    ordered += $(elem).attr('id')  + " ";
                });

                $('#ordered_lessons').val(ordered);
            });
        });
    </script>
@endsection
