@extends('admin.layout')

@section('pagetitle')
    Modules <small>modules list</small>
@endsection

@section('breadcrumb')
    <li>
        <i class="icon-book-open"></i>
        <a href="{{ route('courses.index') }}">Course <b>{{ $course->title }}</b></a>
        <i class="fa fa-angle-right"></i>
    </li>
    <li>
        <i class="fa fa-book"></i>
        <a href="/">Modules Order</a>
    </li>
@endsection

@section('content')
    <?php $moduleNumber = 1; ?>

    @if(count($modules)>0)
        <form action="{{ route('modules.order.store', ['course' => $course->id ]) }}" method="POST">
            <div id="sortingSaveBtn" class="text-center">
                {{ csrf_field() }}
                <input type="hidden" name="ordered_modules" id="ordered_modules" value="">
                <button class="btn btn-primary btn-large btnSaveModuleOrder">Save Order</button>
            </div>
        </form>
        <br/>
    @endif
    <ul class='moduleList' style="list-style-type: none; margin: 0; padding: 0;">
        @foreach($modules as $module)
            <li id="{{ $module->id }}" style="cursor: crosshair">
                <!-- BEGIN EXTRAS PORTLET-->
                <div class="portlet box blue">
                    <div class="portlet-title">
                        <div class="caption">
                            <b>Module #{{ $moduleNumber++ }} : {{ $module->title }}</b>
                            @if ($module->type == "Link")
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
            $(".moduleList").sortable();

            $('.btnSaveModuleOrder').click(function(){
                var ordered = "";
                $('.moduleList').children('li').each(function(key, elem) {
                    ordered += $(elem).attr('id')  + " ";
                });


                $('#ordered_modules').val(ordered);
            });
        });
    </script>
@endsection
