@extends('layouts.app')

@section('content')
    <div class="wrapper">
        <div class="shadow">
            <div id="content" class="padded-twice">
                <h1><i class="university icon"></i> Lurn Classroom</h1>
                @if ($terms = request('q'))
                    <p>Searching for "<strong><em>{{ $terms }}</em></strong>"</p>
                @else
                    <p>Browse through all of our courses and start improving your skills today!</p>
                @endif

                <hr>
                
                <h2>Categories</h2>

                <ul class="categories">
                    <li v-for="(category, index) in categories" @click="activeCategory = index"
                        :class="{ active: activeCategory == index }" v-text="category"></li>
                </ul>

                <hr>

                <div class="courses ui four columns grid">
                    @foreach ($courses as $course)
                        <div class="column" v-show='isActiveCategory({!! $course->categoryListWithLabels->toJson() !!})'>
                            @include('parts.course.card', compact('course'))
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script type="text/javascript">
        new Vue({
            el: '#content',
            data: {
                activeCategory: 0,
                categories: {!! $categories->toJson() !!}
            },
            methods: {
                isActiveCategory(names) {
                    if (! this.activeCategory) return true;

                    for (var i = 0; i < names.length; i++)
                        if (this.activeCategory === this.categories.indexOf(names[i]))
                            return true;

                    return false;
                }
            }
        });
    </script>
@endsection
