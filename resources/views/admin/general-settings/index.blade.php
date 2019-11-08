@extends('admin.layout')

@section('pagetitle')
    General Settings
@endsection

@section('breadcrumb')
    <li>
        <i class="fa fa-bookmark-o"></i>
        <span href="/">General Settings</span>
    </li>
@endsection


@section('content')
    <form action="{{ route('store.settings') }}" method="post" class="form-horizontal form-bordered">
        {{ csrf_field() }}
        <div class="row">
            <div class="col-md-6">
                <div class="portlet box grey">
                    <div class="portlet-title">
                        <div class="caption font-green">
                            <i class="icon-user font-green"></i>
                            <span class="caption-subject bold uppercase">Infusionsoft Merchant IDS</span>
                        </div>
                    </div>
                    <div class="portlet-body form">
                        <div class="form-body">
                            @foreach ($isAccounts as $key => $account)
                                <div class="form-group">
                                    <label class="control-label col-md-3" for="email">{{ $key }} <span class="required">*</span></label>
                                    <div class='col-md-9'>
                                        @if (is_array($account))
                                            @foreach ($account as $k => $merchantId)
                                                <input type="number" class="form-control" value="{{ $merchantId }}" name="id_{{$key}}[]">
                                            @endforeach
                                        @endif

                                        <input type="number" class="form-control" value="0" name="id_{{$key}}[]">
                                    </div>
                                    <br><br>
                                </div>
                            @endforeach

                            <div class="form-group text-center">
                                <input type="submit" class="btn blue" value="Save">
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
