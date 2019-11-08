@extends('admin.layout')

@section('pagetitle')
    Upsells
    <small>courses list</small>
@endsection

@section('breadcrumb')
    <li>
        <i class="fa fa-bookmark-o"></i>
        <a href="{{ route('upsells.index') }}">Upsells</a>
    </li>
    <li>
        <i class="fa fa-angle-right"></i>
        @if ($upsell->succeedingCourse)
            <a href="{{ route('upsells.edit', ['upsell' => $upsell->id]) }}">Edit</a>
        @else
            <a href="{{ route('upsells.create') }}"">Add New</a>
        @endif
    </li>
@endsection


@section('content')
    <form action="{{ $action }}" method="post" class="form-horizontal form-bordered">
        {{csrf_field() }}
        {{ $method }}
        <div class="row">
            <div class="col-md-12">
                <div class="portlet box grey">
                    <div class="portlet-title">
                        <div class="caption font-green">
                            <i class="icon-user font-green"></i>
                            <span class="caption-subject bold uppercase"> Upsale Details</span>
                        </div>
                    </div>
                    <div class="portlet-body form">
                        <div class="form-body">
                            <div class="form-group">
                                <label class="control-label col-md-3" for="title">Upsell For <span class="required">*</span></label>
                                <div class='col-md-9'>
                                    <select name="course_id" class="form-control inline">
                                        @foreach (App\Models\Course::all() as $course)
                                            <option value="{{ $course->id }}" {{ old('course_id', $upsell->infusionsoft->course_id) == $course->id ? "SELECTED" : "" }}> {{ $course->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <br><br>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3" for="title">Appears After <span class="required">*</span></label>
                                <div class='col-md-9'>
                                    <select name="succeeds_course_id" class="form-control inline">
                                        @foreach (App\Models\Course::all() as $course)
                                            <option value="{{ $course->id }}" {{ old('succeeds_course_id', $upsell->succeeds_course_id) == $course->id ? "SELECTED" : "" }}> {{ $course->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <br><br>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3" for="html">Html <span class="required">*</span></label>
                                <div class="col-md-9">
                                    <p>Use CART_URL where you should place the link to the cart page</p>
                                    <p>Use THANK_YOU_URL where you should place the link to the the thank you page</p>
                                    <textarea class="ckeditor form-control" name="html" rows="6" data-error-container="#editor2_error">{{ old('html', $upsell->html) }}</textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3" for="css">CSS <span class="required">*</span></label>
                                <div class="col-md-9">
                                    <textarea class="form-control" name="css" rows="6">{{ old('css', $upsell->css) }}</textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3">Price</label>
                                <div class="col-md-9">
                                   <input type="text" class="form-control" value="{{ old('price', $upsell->infusionsoft->price) }}" name="price">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3">Infusionsoft</label>
                                <div class="col-md-9">
                                    <span>Product Id</span>
                                    <input type="number" name="is_product_id" class="form-control inline" placeholder="Product Id" value="{{ $upsell->infusionsoft->is_product_id }}" style="width: auto">
                                    <select name="is_account" class="form-control inline" style="width: auto">
                                        @foreach(config('is_accounts') as $account)
                                            <option value="{{ $account }}" {{ $upsell->infusionsoft->is_account == $account ? "SELECTED" : "" }}>{{ $account }}</option>
                                        @endforeach
                                    </select>
                                    <span>Subscription</span>
                                    <input type="checkbox" value="1" name="subscription" {{ $upsell->infusionsoft->subscription ? "CHECKED" : "" }}/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3">Enabled</label>
                                <div class="col-md-9">
                                    <input type="checkbox" value="1" {{ $upsell->status == 1 ? "CHECKED" : "" }} class="make-switch" data-size="small" name="status">
                                </div>
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


@section('js')
    <script type="text/javascript" src="/assets/global/plugins/ckeditor/ckeditor.js"></script>
@endsection
