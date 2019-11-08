@extends('admin.layout')

@section('pagetitle')
    Sendlane
    <small>add a new account</small>
@endsection

@section('breadcrumb')
    <li>
        <i class="fa fa-bookmark-o"></i>
        <a href="{{ route('sendlane.index') }}">Sendlane</a>
    </li>
    <li>
        <i class="fa fa-angle-right"></i>
        @if ($sendlane->email)
            <a href="{{ route('sendlane.edit', compact('sendlane'))}}">Edit</a>
        @else
            <a href="{{ route('sendlane.create') }}">Add New</a>
        @endif
    </li>
@endsection


@section('content')
    <form action="{{ $action }}" method="post" class="form-horizontal form-bordered">
        {{ csrf_field() }}
        {{ $method }}
        <div class="row">
            <div class="col-md-12">
                <div class="portlet box grey">
                    <div class="portlet-title">
                        <div class="caption font-green">
                            <i class="icon-user font-green"></i>
                            <span class="caption-subject bold uppercase"> Account Details</span>
                        </div>
                    </div>
                    <div class="portlet-body form">
                        <div class="form-body">
                            <div class="form-group">
                                <label class="control-label col-md-3" for="email">Email <span class="required">*</span></label>
                                <div class='col-md-9'>
                                    <input type="text" class="form-control" id="email" value="{{ old('email', $sendlane->email) }}" name="email">
                                </div>
                                <br><br>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3" for="subdomain">Subdomain <span class="required">*</span></label>
                                <div class='col-md-9'>
                                    <input type="text" class="form-control" id="subdomain" value="{{ old('subdomain', $sendlane->subdomain) }}" name="subdomain">
                                </div>
                                <br><br>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3" for="api">Api <span class="required">*</span></label>
                                <div class='col-md-9'>
                                    <input type="text" class="form-control" id="api" value="{{ old('api', $sendlane->api) }}" name="api">
                                </div>
                                <br><br>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3" for="hash">Hash <span class="required">*</span></label>
                                <div class='col-md-9'>
                                    <input type="text" class="form-control" id="hash" value="{{ old('hash', $sendlane->hash) }}" name="hash">
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
