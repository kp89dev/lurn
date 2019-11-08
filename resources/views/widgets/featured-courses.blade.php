@if(!isset($show))
   <?php $show = 4; ?> 
@endif
@if(!isset($columns))
   <?php $columns = 'four'; ?>
@else
    <?php
    switch ($columns) {
        case 1:
            $columns = 'one';
            break;
        case 2:
            $columns = 'two';
            break;
        case 3:
            $columns = 'three';
            break;
        case 4:
            $columns = 'four';
            break;
        case 5:
            $columns = 'five';
            break;
        case 6:
            $columns = 'six';
            break;
        default:
            $columns = 'four';
            break;
    }
    ?>
@endif
<div class="padded-twice" id="featured-course-widget">
    <h3 class="center aligned mb-30">
       FEATURED COURSES WIDGET
    </h3>
    <div class="courses ui {{$columns}} columns grid">
        @foreach($featuredCourses as $course)
            @break($loop->iteration > $show)
            <div class="column">
                @include('parts.course.card', ['course' => $course, 'featured' => true])
            </div>
        @endforeach
    </div>
</div>