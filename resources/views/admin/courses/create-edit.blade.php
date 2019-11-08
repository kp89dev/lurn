@extends('admin.layout')

@section('pagetitle')
Courses
<small>courses list</small>
@endsection

@section('breadcrumb')
<li>
    <i class="fa fa-bookmark-o"></i>
    <a href="{{ route('courses.index') }}">Courses</a>
</li>
<li>
    <i class="fa fa-angle-right"></i>
    @if ($course->title)
    <a href="{{ route('courses.edit', ['course' => $course->id]) }}">Edit</a>
    @else
    <a href="{{ route('courses.create') }}">Add New</a>
    @endif
</li>
@endsection

@section('content')
<form action="{{ $action }}" method="post" class="form-horizontal form-bordered margin-bottom-40" enctype="multipart/form-data">
    {{csrf_field() }}
    {{ $method }}
    <div class="row">
        <div class="col-md-12">
            <div class="portlet box grey">
                <div class="portlet-title">
                    <div class="caption font-green">
                        <i class="icon-user font-green"></i>
                        <span class="caption-subject bold uppercase"> Course Details</span>
                    </div>
                </div>
                <div class="portlet-body form">
                    <div class="form-body">
                        <div class="form-group">
                            <label class="control-label col-md-3">Sendlane</label>
                            <div class="col-md-9">
                                @if ($course->sendlane)
                                <b>Using <a href="javascript;">{{ $course->sendlane->list_name }}</a></b>
                                @endif

                                <span>Select Account</span>
                                <select name="sendlaneAccount" onchange="changeSendlaneAccount(this)" class="form-control inline" style="width: auto">
                                    <option value=""></option>
                                    @foreach ($sendlaneAccounts as $acc)
                                    <option value="{{ $acc->id }}" {{ request()->sendlane == $acc->id ? "SELECTED" : "" }}>{{ $acc->email }}</option>
                                    @endforeach
                                </select>

                                @if($lists)
                                <select name="sendlaneList" class="form-control inline" style="width: auto">
                                    <option value=""></option>
                                    @foreach ($lists as $list)
                                        <option value="{{ $list['list_id'] }}|{{$list['list_name']}}">{{ $list['list_name']}}</option>
                                    @endforeach
                                </select>
                                @endif
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3" for="title">Title <span class="required">*</span></label>
                            <div class='col-md-9'>
                                <input type="text" class="form-control {{ old('title', $course->title) ? 'edited' : '' }}" id="form_control_1" value="{{ old('title', $course->title) }}" name="title">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3" for="description">Pre Registration Description <span class="required">*</span></label>
                            <div class="col-md-9">
                                <textarea class="ckeditor form-control" name="description" rows="6" data-error-container="#editor2_error">{{ old('description', $course->description) }}</textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3" for="post-registration-description">Post Registration Description <span class="required">*</span></label>
                            <div class="col-md-9">
                                <textarea class="ckeditor form-control" name="post-registration-description" rows="6" data-error-container="#editor2_error">{{ old('post-registration-description', isset($postRegistrationDescription) ? $postRegistrationDescription : '') }}</textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3" for="snippet">Snippet </label>
                            <div class="col-md-9">
                                <textarea class="ckeditor form-control" name="snippet" rows="6" data-error-container="#editor2_error">{{ old('snippet', $course->snippet) }}</textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3" for="thumbnail">Thumbnail<span class="required">*</span></label>
                            <div class='col-md-4'>
                                <div class="fileinput fileinput-new" data-provides="fileinput">
                                    <div class="fileinput-new thumbnail">
                                        <img src="{{ $course->getPrintableImageUrl() }}">
                                    </div>
                                    <div class="fileinput-preview fileinput-exists thumbnail"></div>
                                    @if (!ends_with($course->getPrintableImageUrl(),'default-course-thumbnail.png'))
                                    <span class="btn default btn-file margin-top-10">
                                        <span> Change </span>
                                        <input type="file" name="thumbnail" accept="image/gif,image/jpg,image/png,image/jpeg">
                                    </span>
                                    @else
                                    <div>
                                        <span class="btn default btn-file margin-top-10">
                                            <span class="fileinput-new">Select image </span>
                                            <span class="fileinput-exists"> Change </span>
                                            <input type="file" name="thumbnail" accept="image/gif,image/jpg,image/png,image/jpeg">
                                        </span>
                                        <a href="javascript:;" class="btn red margin-top-10 fileinput-exists" data-dismiss="fileinput">Remove </a>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            <br><br>
                        </div>

                        <div class="form-group ">
                            <label class="control-label col-md-3">Label</label>
                            <div class="col-md-4">
                                <select name="label_id" class="form-control">
                                    <option value=""></option>
                                    @foreach(\App\Models\Labels::all() as $label)
                                        <option value="{{ $label->id }}" {{ $course->label_id == $label->id ? "SELECTED" : "" }}>{{ $label->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label class="control-label col-md-3">Container</label>
                            <div class="col-md-4">
                                <select name="course_container_id" class="form-control">
                                    @foreach(\App\Models\CourseContainer::all() as $container)
                                    <option value="{{ $container->id }}" {{ $course->course_container_id == $container->id ? "SELECTED" : "" }}>{{ $container->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @if(!$hasBonus)
                            {{-- <label class="control-label col-md-1">Bonus Of</label>
                            <div class="col-md-4">
                                <select name="bonus_of" class="form-control">
                                    <option value="none">Select Course</option>
                                    @foreach(\App\Models\Course::whereNotIn('id', $excludeFromBonus)->get() as $bonusOf)
                                    <option value="{{ $bonusOf->id }}" {{ $course->bonusOf() == $bonusOf->id ? "SELECTED" : "" }}>{{ $bonusOf->title }}</option>
                                    @endforeach
                                </select>
                            </div> --}}
                            @endif
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Enabled</label>
                            <div class="col-md-2">
                                <input type="checkbox" value="1" {{ $course->status==1 ? "checked" : "" }} class="make-switch" data-size="small" name="status">
                            </div>
							<label class="control-label col-md-2">Purchasable</label>
							<div class="col-md-2">
                                <input type="checkbox" value="1" {{ $course->purchasable==1 ? "checked" : "" }} class="make-switch" data-size="small" name="purchasable">
                            </div>
						</div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Drip Content</label>
                            <div class="col-md-2">
                                <input type="checkbox" value="1" {{ $course->drip==1 ? "checked" : "" }} class="make-switch" data-size="small" name="drip">
                            </div>
                            <label class="control-label col-md-2">Confirm Completed</label>
                            <div class="col-md-1">
                                <select name="confirm_after" class="form-control">
                                @if($course->confirm_after == 'L')
                                    <option value="L" selected>Lessons</option>
                                    <option value="M">Modules</option>
                                @else
                                    <option value="L">Lessons</option>
                                    <option value="M" selected>Modules</option>
                                @endif
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Category</label>
                            <div class="col-md-2">
                                @foreach (\App\Models\Category::all() as $category)
                                <input type="checkbox" name="category[]" id="{{'category_'.$category->id}}" value="{{$category->id}}" @if($course->categories->contains($category->id)) checked=checked @endif">{{$category->name}}<br>
                                @endforeach
                            </div>
                        </div>

                        <hr>

                        <div class="form-group">
                            <label class="control-label col-md-3">Free Course</label>
                            <div class="col-md-2">
                                <input type="checkbox" value="1" {{ $course->free ? 'checked' : '' }} class="make-switch" data-size="small" name="free">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-md-3">Purchase Options</label>
                            <div class="col-md-2">
                                <span>Buy with Points</span>
                                <i class="fa fa-info-circle" title="Use this option with free courses to require the user to spend this amount of points to enroll in the course. Leave blank or 0 to disable." onclick="alert('Use this option with free courses to require the user to spend this amount of points to enroll in the course. Leave blank or 0 to disable.')"></i>
                                <input type="number" min="0" step="100" name="buy_with_points" value="{{ old('buy_with_points', $course->buy_with_points) }}" class="form-control inline" style="width: auto">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-md-3">Infusionsoft</label>

                            <div class="col-md-2">
                                <span>Price (One time payment)</span><br>
                                <input type="number" min=".01" step=".01" class="form-control inline" value="{{ old('price', $course->infusionsoft->price) }}" name="price" style="width: auto">
                            </div>

                            <div class="col-md-2">
                                <span>Product ID</span><br>
                                <input type="number" min=".01" step=".01"  name="is_product_id" class="form-control inline" placeholder="Product ID" value="{{ $course->infusionsoft->is_product_id }}" style="width: auto">
                            </div>

                            <div class="col-md-2">
                                <span>Account ID</span><br>
                                <select name="is_account" class="form-control inline" style="width: 100%; max-width: 174px">
                                    <option value="">Select One</option>
                                    @foreach(config('is_accounts') as $account)
                                    <option value="{{ $account }}" {{ $course->infusionsoft->is_account == $account ? "SELECTED" : "" }}>{{ strtoupper($account) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="control-label col-md-3" style="padding-top: 0">
                                <span>Subscription</span><br>
                                <input type="checkbox" value="1" {{ $course->infusionsoft->subscription ? "checked" : "" }} class="make-switch" data-size="small" name="subscription">
                            </div>

                            <div class="col-md-2">
                                <span>Subscription Monthly Price</span><br>
                                <input type="number" name="subscription_price" class="form-control inline" placeholder="Monthly Price" value="{{ $course->infusionsoft->subscription_price or '' }}" style="width: auto">
                            </div>

                            <div class="col-md-2">
                                <span>Subscription Product ID</span><br>
                                <input type="number" name="is_subscription_product_id" class="form-control inline" placeholder="Sub. Product ID" value="{{ $course->infusionsoft->is_subscription_product_id or '' }}" style="width: auto">
                            </div>

                            <div class="col-md-2">
                                <span>
                                    Subscription Payments Required
                                    <i class="fa fa-info-circle" title="Enter how many payments are required, leaving this empty means that this is an every month payment subscription." onclick="alert('Enter how many payments are required, leaving this empty means that this is an every month payment subscription.')"></i>
                                </span><br>
                                <input type="number" min="0" name="payments_required" class="form-control inline" placeholder="Sub. Payments Required" value="{{ $course->infusionsoft->payments_required or '' }}" style="width: auto">
                            </div>

                            <div class="col-md-2">
                                <span>
                                    Subscription Payment Form URL
                                    <i class="fa fa-info-circle" title="An email is going to be sent when a failed recharge happens, with this payment form URL, so they can proceed with the payment." onclick="alert('An email is going to be sent when a failed recharge happens, with this payment form URL, so they can proceed with the payment.')"></i>
                                </span><br>
                                <input type="url" min="0" name="subscription_payment_url" class="form-control inline" placeholder="Sub. Payment URL" value="{{ $course->infusionsoft->subscription_payment_url or '' }}" style="width: auto">
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-3"></div>
                            <div class="col-md-9">
                                <span>
                                    Used only for 3 payment options
                                    <i class="fa fa-info-circle" title="A link to a payment page where user can pay the remaining payments"></i>
                                </span>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-3"></div>
                            <div class="col-md-2">
                                <span>Discount Product ID</span><br>
                                <input type="number" name="is_subscription_discount_product_id" class="form-control inline" placeholder="Sub. Product ID" value="{{ $course->infusionsoft->is_subscription_product_id or '' }}" style="width: auto">
                            </div>
                            <div class="col-md-2">
                                <span>Discount Order Form URL</span><br>
                                <input type="text" name="is_subscription_discount_product_url" class="form-control inline" placeholder="http://..." value="{{ $course->infusionsoft->is_subscription_discount_product_url or '' }}" style="width: auto">
                            </div>
                        </div>

                        <hr>

                        <div class="form-group">
                            <label class="control-label col-md-3">Recomended Courses (ordered)</label>
                            <div class="col-md-2">
                                <select name="recommended1" class="form-control inline" style="width: auto">
                                    <option value="none">Select Course</option>
                                    @foreach($recommendations as $recommendation)
                                    <option value="{{ $recommendation['courseID'] }}" {{ $recommendation['order'] == 1 ? "SELECTED" : "" }}>{{$recommendation['courseTitle']}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="recommended2" class="form-control inline" style="width: auto">
                                    <option value="none">Select Course</option>
                                    @foreach($recommendations as $recommendation)
                                    <option value="{{ $recommendation['courseID'] }}" {{ $recommendation['order'] == 2 ? "SELECTED" : "" }}>{{$recommendation['courseTitle']}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="recommended3" class="form-control inline" style="width: auto">
                                    <option value="none">Select Course</option>
                                    @foreach($recommendations as $recommendation)
                                    <option value="{{ $recommendation['courseID'] }}" {{ $recommendation['order'] == 3 ? "SELECTED" : "" }}>{{$recommendation['courseTitle']}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="recommended4" class="form-control inline" style="width: auto">
                                    <option value="none">Select Course</option>
                                    @foreach($recommendations as $recommendation)
                                    <option value="{{ $recommendation['courseID'] }}" {{ $recommendation['order'] == 4 ? "SELECTED" : "" }}>{{$recommendation['courseTitle']}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Vanilla Forum</label>
                            <div class="col-md-2">
                                <span>Client Id</span><br>
                                <input type="text" name="client_id" class="form-control inline" placeholder="Client Id" value="{{ $course->vanillaForum->client_id }}" style="width: auto">
                            </div>
                            <div class="col-md-2">
                                <span>Client Secret</span><br>
                                <input type="text" name="client_secret" class="form-control inline" placeholder="Client Secret" value="{{ $course->vanillaForum->client_secret }}" style="width: auto">
                            </div>
                            <div class="col-md-2">
                                <span>Url</span><br>
                                <input type="text" name="url" class="form-control inline" placeholder="Url" value="{{ $course->vanillaForum->url }}" style="width: auto">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Vanilla Forum Rules</label>
                            <div class="col-md-9">
                                <textarea class="ckeditor form-control" name="forum_rules" rows="6" data-error-container="#editor2_error">{{ old('forum_rules', $course->vanillaForum->forum_rules) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-offset-6 col-md-2 text-center">
            <div class="form-actions noborder">
                <input type="submit" class="btn blue" value="Save">
            </div>
        </div>
    </div>
</form>
@if($seoaction !== 'create')
    <table class="margin-top-20 table table-striped table-bordered table-hover dataTable no-footer">
        <thead>
            <tr role="row" class="heading">
            <th rowspan="1" colspan="1">Course SEO Settings</th>
            </tr>
        </thead>
        <tr role="row" class="filter">
            <td rowspan="1" colspan="1">
                <form action="{{ $seoaction }}" method="post" class="form-horizontal form-bordered">
                    {{csrf_field() }}
                    <div class="row">
                        <div class="col-md-12">
                            <div class="portlet box grey">
                                <div class="portlet-title">
                                    <div class="caption font-green">
                                        <i class="icon-info font-green"></i>
                                        <span class="caption-subject bold uppercase"> Title</span>
                                    </div>
                                </div>
                                <div class="portlet-body form">
                                    <div class="form-body">
                                        <div class="form-group">
                                            <label class="control-label col-md-3" for="title">Page Title <span class="required">*</span></label>
                                            <div class='col-md-4'>
                                                <input type="text" class="form-control {{ old('title', $courseSEO['title']) ? 'edited' : '' }}" id="title" value="{{ old('title', $courseSEO['title']) }}" name="title" required="true">
                                                <span class="help-block">Lurn Nation</span>
                                            </div>
                                            <br><br>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-3" for="site_name">Site Name <span class="required">*</span></label>
                                            <div class='col-md-4'>
                                                <input type="text" class="form-control {{ old('site_name', $courseSEO['site_name']) ? 'edited' : '' }}" id="site_name" value="{{ old('site_name', $courseSEO['site_name']) }}" name="site_name" required="true">
                                                <span class="help-block">Lurn.com</span>
                                            </div>
                                            <br><br>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-3" for="separator">Separator <span class="required">*</span></label>
                                            <div class='col-md-1'>
                                                <input type="text" class="form-control {{ old('separator', $courseSEO['separator']) ? 'edited' : '' }}" id="separator" value="{{ old('separator', $courseSEO['separator']) }}" name="separator" required="true">
                                                <span class="help-block">Single character placed between page title and site name. - </span>
                                            </div>
                                            <br><br>
                                        </div>
                                    </div>
                                </div>
                                <div class="portlet-title">
                                    <div class="caption font-green">
                                        <i class="icon-info font-green"></i>
                                        <span class="caption-subject bold uppercase"> Credits</span>
                                    </div>
                                </div>
                                <div class="portlet-body form">
                                    <div class="form-body">
                                        <div class="form-group">
                                            <label class="control-label col-md-3" for="author">Page Author </label>
                                            <div class='col-md-4'>
                                                <input type="text" class="form-control {{ old('author', $courseSEO['author']) ? 'edited' : '' }}" id="author" value="{{ old('author', $courseSEO['author']) }}" name="author">
                                                <span class="help-block">https://plus.google.com/[G+ PROFILE HERE]</span>
                                            </div>
                                            <br><br>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-3" for="publisher">Page Publisher </label>
                                            <div class='col-md-4'>
                                                <input type="text" class="form-control {{ old('publisher', $courseSEO['publisher']) ? 'edited' : '' }}" id="publisher" value="{{ old('publisher', $courseSEO['publisher']) }}" name="publisher">
                                                <span class="help-block">https://plus.google.com/[G+ PROFILE HERE]</span>
                                            </div>
                                            <br><br>
                                        </div>
                                    </div>
                                </div>
                                <div class="portlet-title">
                                    <div class="caption font-green">
                                        <i class="icon-info font-green"></i>
                                        <span class="caption-subject bold uppercase"> Description</span>
                                    </div>
                                </div>
                                <div class="portlet-body form">
                                    <div class="form-body">
                                        <div class="form-group">
                                            <label class="control-label col-md-3" for="description">Page Description <span class="required">*</span></label>
                                            <div class='col-md-4'>
                                                <input type="text" class="form-control {{ old('description', $courseSEO['description']) ? 'edited' : '' }}" id="description" value="{{ old('description', $courseSEO['description']) }}" name="description" required="true">
                                            </div>
                                            <br><br>
                                        </div>
                                    </div>
                                </div>
                                <div class="portlet-title">
                                    <div class="caption font-green">
                                        <i class="icon-info font-green"></i>
                                        <span class="caption-subject bold uppercase"> Keywords</span>
                                    </div>
                                </div>
                                <div class="portlet-body form">
                                    <div class="form-body">
                                        <div class="form-group">
                                            <label class="control-label col-md-3" for="keywords">Page Keywords <span class="required">*</span></label>
                                            <div class='col-md-4'>
                                                <textarea class="form-control {{ old('keywords', $courseSEO['keywords']) ? 'edited' : '' }}" id="keywords" name="keywords" required="true">{{ old('keywords', $courseSEO['keywords']) }}</textarea>
                                                <span class="help-block">Comma separated list of words/phrases.<br>keyword,this phrase </span>
                                            </div>
                                            <br><br>
                                        </div>
                                    </div>
                                </div>
                                <div class="portlet-title">
                                    <div class="caption font-green">
                                        <i class="icon-eyeglasses font-green"></i>
                                        <span class="caption-subject bold uppercase"> Viewability</span>
                                    </div>
                                </div>
                                <div class="portlet-body form">
                                    <div class="form-body">
                                        <div class="form-group">
                                            <label class="control-label col-md-3" for="robots">Block Robots</label>
                                            <div class='col-md-4'>
                                                <input type="checkbox" class="make-switch" value="1" data-size="small" id="robots" name="robots" @if ($courseSEO['robots'] == 1) checked=checked @endif >
                                            </div>
                                            <br><br>
                                        </div>
                                    </div>
                                </div>
                                <div class="portlet-title">
                                    <div class="caption font-green">
                                        <i class="icon-social-facebook font-green"></i>
                                        <span class="caption-subject bold uppercase"> Open Graph</span>
                                    </div>
                                </div>
                                <div class="portlet-body form">
                                    <div class="form-body">
                                        <div class="form-group">
                                            <label class="control-label col-md-3" for="og_enabled">Enabled</label>
                                            <div class='col-md-4'>
                                                <input type="checkbox" class="make-switch" value="1" data-size="small" name="og_enabled" id="og_enabled" @if ($courseSEO['og_enabled'] == 1) checked=checked @endif >
                                            </div>
                                            <br><br>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-3" for="og_prefix">Prefix</label>
                                            <div class='col-md-1'>
                                                <input type="text" class="form-control {{ old('og_prefix', $courseSEO['og_prefix']) ? 'edited' : '' }}" id="og_prefix" value="{{ old('og_prefix', $courseSEO['og_prefix']) }}" name="og_prefix">
                                                <span class="help-block">og:</span>
                                            </div>
                                            <br><br>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-3" for="og_type">Type</label>
                                            <div class='col-md-1'>
                                                <input type="text" class="form-control {{ old('og_type', $courseSEO['og_type']) ? 'edited' : '' }}" id="og_type" value="{{ old('og_type', $courseSEO['og_type']) }}" name="og_type">
                                                <span class="help-block">website</span>
                                            </div>
                                            <br><br>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-3" for="og_title">Page Title </label>
                                            <div class='col-md-4'>
                                                <input type="text" class="form-control {{ old('og_title', $courseSEO['og_title']) ? 'edited' : '' }}" id="og_title" value="{{ old('og_title', $courseSEO['og_title']) }}" name="og_title">
                                                <span class="help-block">Lurn Nation</span>
                                            </div>
                                            <br><br>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-3" for="og_description">Page Description </label>
                                            <div class='col-md-4'>
                                                <input type="text" class="form-control {{ old('og_description', $courseSEO['og_description']) ? 'edited' : '' }}" id="og_description" value="{{ old('og_description', $courseSEO['og_description']) }}" name="og_description">
                                            </div>
                                            <br><br>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-3" for="og_site_name">Site Name </label>
                                            <div class='col-md-4'>
                                                <input type="text" class="form-control {{ old('og_site_name', $courseSEO['og_site_name']) ? 'edited' : '' }}" id="og_site_name" value="{{ old('og_site_name', $courseSEO['og_site_name']) }}" name="og_site_name">
                                                <span class="help-block">Lurn.com</span>
                                            </div>
                                            <br><br>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-3" for="og_properties">Properties </label>
                                            <div class='col-md-4'>
                                                <textarea class="form-control {{ old('og_properties', $courseSEO['og_properties']) ? 'edited' : '' }}" id="og_properties" name="og_properties">{{ old('og_properties', $courseSEO['og_properties']) }}</textarea>
                                                <span class="help-block">Comma separated list of key=value pairs.<br>image=http://example.com/rock.jpg,audio=http://example.com/sound.mp3 </span>
                                            </div>
                                            <br><br>
                                        </div>
                                    </div>
                                </div>
                                <div class="portlet-title">
                                    <div class="caption font-green">
                                        <i class="icon-social-twitter font-green"></i>
                                        <span class="caption-subject bold uppercase"> Twitter </span>
                                    </div>
                                </div>
                                <div class="portlet-body form">
                                    <div class="form-body">
                                        <div class="form-group">
                                            <label class="control-label col-md-3" for="twitter_enabled">Enabled </label>
                                            <div class='col-md-4'>
                                                <input type="checkbox" class="make-switch" value="1" data-size="small" id="twitter_enabled" name="twitter_enabled" @if ($courseSEO['twitter_enabled'] == 1) checked=checked @endif >
                                            </div>
                                            <br><br>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-3" for="twitter_card">Card Type</label>
                                            <div class='col-md-2'>
                                                <select name="twitter_card" id="twitter_card" class="form-control inline" style="width: auto">
                                                    <option value="summary" {{ $courseSEO['twitter_card']== "summary" ? "SELECTED" : "" }}>summary</option>
                                                    <option value="summary_large_image" {{ $courseSEO['twitter_card']== "summary_large_image" ? "SELECTED" : "" }}>summary large image</option>
                                                    <option value="product" {{ $courseSEO['twitter_card']== "product" ? "SELECTED" : "" }}>product</option>
                                                    <option value="player" {{ $courseSEO['twitter_card']== "player" ? "SELECTED" : "" }}>player</option>
                                                    <option value="photo" {{ $courseSEO['twitter_card']== "photo" ? "SELECTED" : "" }}>photo</option>
                                                    <option value="gallery" {{ $courseSEO['twitter_card']== "gallery" ? "SELECTED" : "" }}>gallery</option>
                                                    <option value="app" {{ $courseSEO['twitter_card']== "app" ? "SELECTED" : "" }}>app</option>
                                                </select>
                                            </div>
                                            <br><br>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-3" for="twitter_site">Site </label>
                                            <div class='col-md-2'>
                                                <input type="text" class="form-control {{ old('twitter_site', $courseSEO['twitter_site']) ? 'edited' : '' }}" id="twitter_site" value="{{ old('twitter_site', $courseSEO['twitter_site']) }}" name="twitter_site">
                                                <span class="help-block">Username</span>
                                            </div>
                                            <br><br>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-3" for="twitter_title">Card Title </label>
                                            <div class='col-md-4'>
                                                <input type="text" class="form-control {{ old('twitter_title', $courseSEO['twitter_title']) ? 'edited' : '' }}" id="twitter_title" value="{{ old('twitter_title', $courseSEO['twitter_title']) }}" name="twitter_title">
                                            </div>
                                            <br><br>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-3" for="twitter_meta">Metas </label>
                                            <div class='col-md-4'>
                                                <textarea class="form-control {{ old('twitter_meta', $courseSEO['twitter_meta']) ? 'edited' : '' }}" id="twitter_meta" name="twitter_meta">{{ old('twitter_meta', $courseSEO['twitter_meta']) }}</textarea>
                                                <span class="help-block">Comma separated list of key=value pairs.<br>description=Your awesome description,creator=@Lurn </span>
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
            </td>
        </tr>
    </table>
@endif
@endsection

@section('js')
<script type="text/javascript" src="/assets/global/plugins/ckeditor/ckeditor.js"></script>
<script type="text/javascript">
    function  changeSendlaneAccount(dropdown) {
        if (dropdown.value == '') {
            return;
        }
        window.location = location.protocol + '//' + location.host + location.pathname + '?sendlane=' + dropdown.value;
    }
</script>
@endsection
