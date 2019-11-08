@extends('admin.layout')

@section('pagetitle')
    Labels
    <small>label creation</small>
@endsection

@section('breadcrumb')
    <li>
        <i class="fa fa-bookmark-o"></i>
        <a href="{{ route('labels.index') }}">Labels</a>
    </li>
    <li>
        <i class="fa fa-angle-right"></i>
        @if ($label->title)
            <a href="{{ route('labels.edit', ['label' => $label->id]) }}">Edit</a>
        @else
            <a href="{{ route('labels.create') }}">Add New</a>
        @endif
    </li>
@endsection


@section('content')
    <form action="{{ $action }}" method="post" class="form-horizontal form-bordered">
        {{csrf_field() }}
        @if ($method != 'POST')
            {{ $method }}
        @endif
        <div class="row">
            <div class="col-md-12">
                <div class="portlet box grey">
                    <div class="portlet-title">
                        <div class="caption font-green">
                            <i class="icon-calendar font-green"></i>
                            <span class="caption-subject bold uppercase"> Label Details</span>
                        </div>
                    </div>
                    <div class="portlet-body form">
                        <div class="form-body">
                            <div class="form-group">
                                <label class="control-label col-md-3" for="title">Name <span class="required">*</span></label>
                                <div class='col-md-9'>
                                    <input type="text" class="form-control {{ old('title', $label->title) ? 'edited' : '' }}" id="title" value="{{ old('title', $label->title) }}" name="title" required="true">
                                </div>
                                <br><br>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-offset-4 col-md-3 text-center">
                <div class="form-actions noborder">
                    <input type="submit" class="btn blue" value="Save">
                </div>
            </div>
        </div>
    </form>
@endsection
