@extends('admin.layout')

@section('pagetitle')
    Certficate
@endsection

@section('breadcrumb')
    <li>
        <i class="book-open"></i>
        <a href="{{ route('certs.index', compact('course')) }}">Certificate</a>
    </li>
    <li>
        <i class="icon-edit"></i>
        @if ($cert->title)
            <a href="{{ route('certs.edit', compact('course', 'cert')) }}">Edit</a>
        @else
            <a href="{{ route('certs.create', compact('course')) }}">Add New</a>
        @endif
    </li>
@endsection

@section('content')
    <form action="{{ $action }}" method="post" class="form-horizontal form-bordered" enctype="multipart/form-data">
        {{csrf_field() }}
        {{ $method }}
        <div class="row">
            <div class="col-md-12">
                <div class="portlet box grey">
                    <div class="portlet-title">
                        <div class="caption font-green">
                            <i class="icon-user font-green"></i>
                            <span class="caption-subject bold uppercase"> Certificate Details</span>
                        </div>
                    </div>
                    <div class="portlet-body form">
                        <div class="form-body">
                            <div class="form-group">
                                <label class="control-label col-md-2" for="title">Title <span class="required">*</span></label>
                                <div class='col-md-9'>
                                    <input type="text" name="title" value="{{ old('title', $cert->title) }}"
                                           class="form-control {{ old('title', $cert->title) ? 'edited' : '' }}">
                                </div>
                                <br><br>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-2" for="background">Background</label>
                                <div class='col-md-2'>
                                    <div class="fileinput fileinput-new" data-provides="fileinput">
                                        <div class="fileinput-new thumbnail" style="width: 150px; height: 150px;">
                                            @if ($cert->getSrc('background') && $cert->getSrc('background') !== '/static/')
                                                <img src="{{ $cert->getSrc('background') }}" width="100">
                                            @else
                                                <img src="http://www.placehold.it/150x150/EFEFEF/AAAAAA&amp;text=no+image" alt="">
                                            @endif
                                        </div>
                                        <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 150px; max-height: 150px;"></div>
                                        @if ($cert->getSrc('background') && $cert->getSrc('background') !== '/static/')
                                            <div class="clearfix margin-top-10">
                                                <span class="btn default btn-file margin-top-10">
                                                    <span class="fileinput-new"> Change </span>
                                                    <input type="file" name="background" accept="image/gif,image/jpg,image/png,image/jpeg">
                                                </span>
                                                <a href="javascript:;" class="btn red margin-top-10 fileinput-exists" data-dismiss="fileinput">Remove </a>
                                                <a href="{{route('certs.removeImage',['course'=>$course->id,'cert'=>$cert->id,'image'=>'background'])}}" class="btn red margin-top-10 fileinput-new"> Remove </a>
                                            </div>
                                        @else
                                            <div class="clearfix margin-top-10">
                                                <span class="btn default btn-file margin-top-10">
                                                    <span class="fileinput-new">Select image </span>
                                                    <span class="fileinput-exists"> Change </span>
                                                    <input type="file" name="background" accept="image/gif,image/jpg,image/png,image/jpeg">
                                                </span>
                                                <a href="javascript:;" class="btn red margin-top-10 fileinput-exists" data-dismiss="fileinput">Remove </a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <label class="control-label col-md-1" for="logo">Course Logo<span class="required">*</span></label>
                                <div class='col-md-2'>
                                    <div class="fileinput fileinput-new" data-provides="fileinput">
                                        <div class="fileinput-new thumbnail" style="width: 150px; height: 150px;">
                                            @if ($cert->getSrc('logo') && $cert->getSrc('logo') !== '/static/')
                                                <img src="{{ $cert->getSrc('logo') }}" width="100">
                                            @else
                                                <img src="http://www.placehold.it/150x150/EFEFEF/AAAAAA&amp;text=no+image" alt="">
                                            @endif
                                        </div>
                                        <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 150px; max-height: 150px;"></div>
                                        @if ($cert->getSrc('logo') && $cert->getSrc('logo') !== '/static/')
                                            <div class="clearfix margin-top-10">                                        
                                                <span class="btn default btn-file margin-top-10">
                                                    <span class="fileinput-new"> Change </span>
                                                    <input type="file" name="logo" accept="image/gif,image/jpg,image/png,image/jpeg">
                                                </span>
                                                <a href="javascript:;" class="btn red margin-top-10 fileinput-exists" data-dismiss="fileinput">Remove </a>
                                                <a href="{{route('certs.removeImage',['course'=>$course->id,'cert'=>$cert->id,'image'=>'logo'])}}" class="btn red margin-top-10 fileinput-new"> Remove </a>
                                            </div>
                                        @else
                                            <div class="clearfix margin-top-10">
                                                <span class="btn default btn-file margin-top-10">
                                                    <span class="fileinput-new">Select image </span>
                                                    <span class="fileinput-exists"> Change </span>
                                                    <input type="file" name="logo" accept="image/gif,image/jpg,image/png,image/jpeg">
                                                </span>
                                                <a href="javascript:;" class="btn red margin-top-10 fileinput-exists" data-dismiss="fileinput">Remove </a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <label class="control-label col-md-1" for="border">Border</label>
                                <div class='col-md-2'>
                                    <div class="fileinput fileinput-new" data-provides="fileinput">
                                        <div class="fileinput-new thumbnail" style="width: 150px; height: 150px;">
                                            @if ($cert->getSrc('border') && $cert->getSrc('border') !== '/static/')
                                                <img src="{{ $cert->getSrc('border') }}" width="100">
                                            @else
                                                <img src="http://www.placehold.it/150x150/EFEFEF/AAAAAA&amp;text=no+image" alt="">
                                            @endif
                                        </div>
                                        <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 150px; max-height: 150px;"></div>
                                        @if ($cert->getSrc('border') && $cert->getSrc('border') !== '/static/')
                                            <div class="clearfix margin-top-10">
                                                <span class="btn default btn-file margin-top-10">
                                                    <span class="fileinput-new"> Change </span>
                                                    <input type="file" name="border" accept="image/gif,image/jpg,image/png,image/jpeg">
                                                </span>
                                                <a href="javascript:;" class="btn red margin-top-10 fileinput-exists" data-dismiss="fileinput">Remove </a>
                                                <a href="{{route('certs.removeImage',['course'=>$course->id,'cert'=>$cert->id,'image'=>'border'])}}" class="btn red margin-top-10 fileinput-new"> Remove </a>
                                            </div>
                                        @else
                                            <div class="clearfix margin-top-10">
                                                <span class="btn default btn-file margin-top-10">
                                                    <span class="fileinput-new">Select image </span>
                                                    <span class="fileinput-exists"> Change </span>
                                                    <input type="file" name="border" accept="image/gif,image/jpg,image/png,image/jpeg">
                                                </span>
                                                <a href="javascript:;" class="btn red margin-top-10 fileinput-exists" data-dismiss="fileinput">Remove </a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <br><br>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-5" for="logo_style">Logo Style</label>
                                <div class='col-md-2'>
                                    <input type="text" name="logo_style" value="{{ old('logo_style', ($cert->logo_style ?: 'max-width:200px;')) }}" class="form-control {{ old('logo_style', $cert->logo_style) ? 'edited' : '' }}">
                                </div>
                                <label class="control-label col-md-1" for="border_style">Border Style</label>
                                <div class='col-md-2'>
                                    <input type="text" name="border_style" value="{{ old('border_style', ($cert->border_style ?: 'max-width:300px;')) }}" class="form-control {{ old('border_style', $cert->border_style) ? 'edited' : '' }}">
                                </div>
                                <br><br>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-2" for="date_bg">Date Background</label>
                                <div class='col-md-2'>
                                    <div class="fileinput fileinput-new" data-provides="fileinput">
                                        <div class="fileinput-new thumbnail" style="width: 150px; height: 150px;">
                                            @if ($cert->getSrc('date_bg') && $cert->getSrc('date_bg') !== '/static/')
                                                <img src="{{ $cert->getSrc('date_bg') }}" width="100">
                                            @else
                                                <img src="http://www.placehold.it/150x150/EFEFEF/AAAAAA&amp;text=no+image" alt="">
                                            @endif
                                        </div>
                                        <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 150px; max-height: 150px;"></div>
                                        @if ($cert->getSrc('date_bg') && $cert->getSrc('date_bg') !== '/static/')
                                            <div class="clearfix margin-top-10">
                                                <span class="btn default btn-file margin-top-10">
                                                    <span class="fileinput-new"> Change </span>
                                                    <input type="file" name="date_bg" accept="image/gif,image/jpg,image/png,image/jpeg">
                                                </span>
                                                <a href="javascript:;" class="btn red margin-top-10 fileinput-exists" data-dismiss="fileinput">Remove </a>
                                                <a href="{{route('certs.removeImage',['course'=>$course->id,'cert'=>$cert->id,'image'=>'date_bg'])}}" class="btn red margin-top-10 fileinput-new"> Remove </a>
                                            </div>
                                        @else
                                            <div class="clearfix margin-top-10">
                                                <span class="btn default btn-file margin-top-10">
                                                    <span class="fileinput-new">Select image </span>
                                                    <span class="fileinput-exists"> Change </span>
                                                    <input type="file" name="date_bg" accept="image/gif,image/jpg,image/png,image/jpeg">
                                                </span>
                                                <a href="javascript:;" class="btn red margin-top-10 fileinput-exists" data-dismiss="fileinput">Remove </a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <label class="control-label col-md-1" for="badge">Badge</label>
                                <div class='col-md-2'>
                                    <div class="fileinput fileinput-new" data-provides="fileinput">
                                        <div class="fileinput-new thumbnail" style="width: 150px; height: 150px;">
                                            @if ($cert->getSrc('badge') && $cert->getSrc('badge') !== '/static/')
                                                <img src="{{ $cert->getSrc('badge') }}" width="100">
                                            @else
                                                <img src="http://www.placehold.it/150x150/EFEFEF/AAAAAA&amp;text=no+image" alt="">
                                            @endif
                                        </div>
                                        <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 150px; max-height: 150px;"></div>
                                        @if ($cert->getSrc('badge') && $cert->getSrc('badge') !== '/static/')
                                            <div class="clearfix margin-top-10">                                        
                                                <span class="btn default btn-file margin-top-10">
                                                    <span class="fileinput-new"> Change </span>
                                                    <input type="file" name="badge" accept="image/gif,image/jpg,image/png,image/jpeg">
                                                </span>
                                                <a href="javascript:;" class="btn red margin-top-10 fileinput-exists" data-dismiss="fileinput">Remove </a>
                                                <a href="{{route('certs.removeImage',['course'=>$course->id,'cert'=>$cert->id,'image'=>'badge'])}}" class="btn red margin-top-10 fileinput-new"> Remove </a>
                                            </div>
                                        @else
                                            <div class="clearfix margin-top-10">
                                                <span class="btn default btn-file margin-top-10">
                                                    <span class="fileinput-new">Select image </span>
                                                    <span class="fileinput-exists"> Change </span>
                                                    <input type="file" name="badge" accept="image/gif,image/jpg,image/png,image/jpeg">
                                                </span>
                                                <a href="javascript:;" class="btn red margin-top-10 fileinput-exists" data-dismiss="fileinput">Remove </a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <label class="control-label col-md-1" for="sign">Signature</label>
                                <div class='col-md-2'>
                                    <div class="fileinput fileinput-new" data-provides="fileinput">
                                        <div class="fileinput-new thumbnail" style="width: 150px; height: 150px;">
                                            @if ($cert->getSrc('sign') && $cert->getSrc('sign') !== '/static/')
                                                <img src="{{ $cert->getSrc('sign') }}" width="100">
                                            @else
                                                <img src="http://www.placehold.it/150x150/EFEFEF/AAAAAA&amp;text=no+image" alt="">
                                            @endif
                                        </div>
                                        <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 150px; max-height: 150px;"></div>
                                        @if ($cert->getSrc('sign') && $cert->getSrc('sign') !== '/static/')
                                            <div class="clearfix margin-top-10">
                                                <span class="btn default btn-file margin-top-10">
                                                    <span class="fileinput-new"> Change </span>
                                                    <input type="file" name="sign" accept="image/gif,image/jpg,image/png,image/jpeg">
                                                </span>
                                                <a href="javascript:;" class="btn red margin-top-10 fileinput-exists" data-dismiss="fileinput">Remove </a>
                                                <a href="{{route('certs.removeImage',['course'=>$course->id,'cert'=>$cert->id,'image'=>'sign'])}}" class="btn red margin-top-10 fileinput-new"> Remove </a>
                                            </div>
                                        @else
                                            <div class="clearfix margin-top-10">
                                                <span class="btn default btn-file margin-top-10">
                                                    <span class="fileinput-new">Select image </span>
                                                    <span class="fileinput-exists"> Change </span>
                                                    <input type="file" name="sign" accept="image/gif,image/jpg,image/png,image/jpeg">
                                                </span>
                                                <a href="javascript:;" class="btn red margin-top-10 fileinput-exists" data-dismiss="fileinput">Remove </a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <br><br>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-2" for="date_style">Date Wrap Style</label>
                                <div class='col-md-2'>
                                    <input type="text" name="date_style" value="{{ old('date_style', ($cert->date_style ?: 'width:255px;height:133px;')) }}" class="form-control {{ old('date_style', $cert->date_style) ? 'edited' : '' }}">
                                </div>
                                <label class="control-label col-md-1" for="badge_style">Badge Style</label>
                                <div class='col-md-2'>
                                    <input type="text" name="badge_style" value="{{ old('badge_style', ($cert->badge_style ?: 'max-width:140px;')) }}" class="form-control {{ old('badge_style', $cert->badge_style) ? 'edited' : '' }}">
                                </div>
                                <label class="control-label col-md-1" for="sign_style">Signature Style</label>
                                <div class='col-md-2'>
                                    <input type="text" name="sign_style" value="{{ old('sign_style', ($cert->sign_style ?: 'max-width:180px;')) }}" class="form-control {{ old('sign_style', $cert->sign_style) ? 'edited' : '' }}">
                                </div>
                                <br><br>
                            </div>
                            <div class="clearfix col-md-12 form-group margin-top-10">
                                <p style="text-align: center;"><span class="label label-danger">NOTE! </span>&nbsp; Images won't be resized.</p>
                                <br><br>
                            </div>                      
                            <div class="form-group">
                                <label class="control-label col-md-2" for="title">Style</label>
                                <div class='col-md-9'>
                                    @if(old('style', $cert->style))
                                        <textarea name="style" class="form-control edited" rows="10">{{$cert->style}}</textarea>
                                    @else
                                        <textarea name="style" class="form-control" rows="10">body {
	background:#FFF;
	font-family: 'Allura', cursive;
	font-size:20px;
	color:#2f2f2f;
}
.crbox{
	max-width:1000px;
	width:100%;
	min-height:710px;
	margin:0;
	padding:50px 50px 10px;
	text-align:center;
	position:relative;
	border-left:30px solid #5d789a;
	border-right:30px solid #5d789a;
}
.crbox .cr-logo{
	padding:80px 0 10px 0;
}
.crbox .cr-logo img{
	max-width:300px;
	width:100%;
}
.crbox .cr-border{
	margin-top:-15px;
}
.crbox .cr-border img{
	max-width:300px;
	width:100%;
}
.crbox h3{
	font-size:38px;
	font-weight:normal;
	margin:0;
	padding:10px 0 10px;
	font-family: 'Allura', cursive;
}
.crbox  h2{
	font-family: 'Playfair Display', serif;
	margin:0;
	font-size:52px;
	padding:0 0 10px;
}
.crbox p{
	margin:0 0 20px;
	font-size:38px;
}
.crbox .crbottom{
	position: relative;
	text-align:center;
}
.crbox .cr-date{
        padding-top:100px;
        float: left;
        width:25%;
        height:133px;
}
.crbox .cr-date .current-date{
        padding-top:30px;
	font-size:30px;
	text-align:left;
        margin-top: -50%;
}
.crbox .cr-badge{
        float: left;
	text-align:center;
        width:50%;
        padding-bottom:70px;
}
.crbox .cr-badge img{
	width:100%;
}
.crbox .cr-sign{
        padding-top:100px;
	width:25%;
	height:133px;
        float:right;
}
.crbox .cr-sign img{
 	max-width:255px;
	width:100%;
}</textarea>
                                    @endif
                                </div>
                                <br><br>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-2" for="title">Body</label>
                                <div class='col-md-9'>
                                    <p class="note">$$USERNAME$$</p>
                                    @if(old('body', $cert->body))
                                        <textarea name="body" class="form-control edited" rows="20">{{$cert->body}}</textarea>
                                    @else
                                        @php
                                            $default_body = '
    <h3>Watch Out World</h3>
    <h2>$$USERNAME$$</h2>
    <p>Just <strong>Graduated</strong> from <strong>Lurn’s Publish Academy</strong><br/>and is now ready to <br/>Launch a Rewarding and Profitable Business!</p>';
                                        @endphp
                                        <textarea name="body" class="form-control" rows="20">{{$default_body}}</textarea>
                                    @endif
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