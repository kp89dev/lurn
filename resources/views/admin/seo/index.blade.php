@extends('admin.layout')

@section('pagetitle')
SEO
<small>Site / Courses SEO</small>
@endsection

@section('breadcrumb')
<li>
    <i class="icon-equalizer"></i>
    <a href="{{ route('seo.index') }}">SEO</a>
</li>
@endsection

@section('content')
<table class="table table-striped table-bordered table-hover dataTable no-footer">
    <thead>
        <tr role="row" class="heading">
            <th rowspan="1" colspan="1">SEO Base Settings</th>
        </tr>
    </thead>
    <tr role="row" class="filter">
        <td rowspan="1" colspan="1">
            <form action="{{ $action }}" method="post" class="form-horizontal form-bordered">
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
                                        <label class="control-label col-md-3" for="title">Default Page Title <span class="required">*</span></label>
                                        <div class='col-md-4'>
                                            <input type="text" class="form-control {{ old('title', $seoDefaults['title']) ? 'edited' : '' }}" id="title" value="{{ old('title', $seoDefaults['title']) }}" name="title" required="true">
                                            <span class="help-block">Lurn Nation</span>
                                        </div>
                                        <br><br>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3" for="site_name">Site Name <span class="required">*</span></label>
                                        <div class='col-md-4'>
                                            <input type="text" class="form-control {{ old('site_name', $seoDefaults['site_name']) ? 'edited' : '' }}" id="site_name" value="{{ old('site_name', $seoDefaults['site_name']) }}" name="site_name" required="true">
                                            <span class="help-block">Lurn.com</span>
                                        </div>
                                        <br><br>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3" for="separator">Separator <span class="required">*</span></label>
                                        <div class='col-md-1'>
                                            <input type="text" class="form-control {{ old('separator', $seoDefaults['separator']) ? 'edited' : '' }}" id="separator" value="{{ old('separator', $seoDefaults['separator']) }}" name="separator" required="true">
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
                                        <label class="control-label col-md-3" for="author">Default Author </label>
                                        <div class='col-md-4'>
                                            <input type="text" class="form-control {{ old('author', $seoDefaults['author']) ? 'edited' : '' }}" id="author" value="{{ old('author', $seoDefaults['author']) }}" name="author">
                                            <span class="help-block">https://plus.google.com/[G+ PROFILE HERE]</span>
                                        </div>
                                        <br><br>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3" for="publisher">Default Publisher </label>
                                        <div class='col-md-4'>
                                            <input type="text" class="form-control {{ old('publisher', $seoDefaults['publisher']) ? 'edited' : '' }}" id="publisher" value="{{ old('publisher', $seoDefaults['publisher']) }}" name="publisher">
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
                                        <label class="control-label col-md-3" for="description">Default Page Description <span class="required">*</span></label>
                                        <div class='col-md-4'>
                                            <input type="text" class="form-control {{ old('description', $seoDefaults['description']) ? 'edited' : '' }}" id="description" value="{{ old('description', $seoDefaults['description']) }}" name="description" required="true">
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
                                        <label class="control-label col-md-3" for="keywords">Default Page Keywords <span class="required">*</span></label>
                                        <div class='col-md-4'>
                                            <textarea class="form-control {{ old('keywords', $seoDefaults['keywords']) ? 'edited' : '' }}" id="keywords" name="keywords" required="true">{{ old('keywords', $seoDefaults['keywords']) }}</textarea>
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
                                            <input type="checkbox" class="make-switch" value="1" data-size="small" id="robots" name="robots" @if ($seoDefaults['robots'] == 1) checked=checked @endif >
                                        </div>
                                        <br><br>
                                    </div>
                                </div>
                            </div>
                            <div class="portlet-title">
                                <div class="caption font-green">
                                    <i class="icon-graph font-green"></i>
                                    <span class="caption-subject bold uppercase"> Webmasters</span>
                                </div>
                            </div>
                            <div class="portlet-body form">
                                <div class="form-body">
                                    <div class="form-group">
                                        <label class="control-label col-md-3" for="webmasters_google">Google</label>
                                        <div class='col-md-4'>
                                            <input type="text" class="form-control {{ old('webmasters_google', $seoDefaults['webmasters_google']) ? 'edited' : '' }}" id="webmasters_google" value="{{ old('webmasters_google', $seoDefaults['webmasters_google']) }}" name="webmasters_google">
                                        </div>
                                        <br><br>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3" for="webmasters_bing">Bing</label>
                                        <div class='col-md-4'>
                                            <input type="text" class="form-control {{ old('webmasters_bing', $seoDefaults['webmasters_bing']) ? 'edited' : '' }}" id="webmasters_bing" value="{{ old('webmasters_bing', $seoDefaults['webmasters_bing']) }}" name="webmasters_bing">
                                        </div>
                                        <br><br>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3" for="webmasters_alexa">Alexa</label>
                                        <div class='col-md-4'>
                                            <input type="text" class="form-control {{ old('webmasters_alexa', $seoDefaults['webmasters_alexa']) ? 'edited' : '' }}" id="webmasters_alexa" value="{{ old('webmasters_alexa', $seoDefaults['webmasters_alexa']) }}" name="webmasters_alexa">
                                        </div>
                                        <br><br>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3" for="webmasters_pinterest">Pinterest</label>
                                        <div class='col-md-4'>
                                            <input type="text" class="form-control {{ old('webmasters_pinterest', $seoDefaults['webmasters_pinterest']) ? 'edited' : '' }}" id="webmasters_pinterest" value="{{ old('webmasters_pinterest', $seoDefaults['webmasters_pinterest']) }}" name="webmasters_pinterest">
                                        </div>
                                        <br><br>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3" for="webmasters_yandex">Yandex</label>
                                        <div class='col-md-4'>
                                            <input type="text" class="form-control {{ old('webmasters_yandex', $seoDefaults['webmasters_yandex']) ? 'edited' : '' }}" id="webmasters_yandex" value="{{ old('webmasters_yandex', $seoDefaults['webmasters_yandex']) }}" name="webmasters_yandex">
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
                                            <input type="checkbox" class="make-switch" value="1" data-size="small" name="og_enabled" id="og_enabled" @if ($seoDefaults['og_enabled'] == 1) checked=checked @endif >
                                        </div>
                                        <br><br>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3" for="og_prefix">Prefix</label>
                                        <div class='col-md-1'>
                                            <input type="text" class="form-control {{ old('og_prefix', $seoDefaults['og_prefix']) ? 'edited' : '' }}" id="og_prefix" value="{{ old('og_prefix', $seoDefaults['og_prefix']) }}" name="og_prefix">
                                            <span class="help-block">og:</span>
                                        </div>
                                        <br><br>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3" for="og_type">Type</label>
                                        <div class='col-md-1'>
                                            <input type="text" class="form-control {{ old('og_type', $seoDefaults['og_type']) ? 'edited' : '' }}" id="og_type" value="{{ old('og_type', $seoDefaults['og_type']) }}" name="og_type">
                                            <span class="help-block">website</span>
                                        </div>
                                        <br><br>
                                    </div>                                   
                                    <div class="form-group">
                                        <label class="control-label col-md-3" for="og_title">Default Page Title </label>
                                        <div class='col-md-4'>
                                            <input type="text" class="form-control {{ old('og_title', $seoDefaults['og_title']) ? 'edited' : '' }}" id="og_title" value="{{ old('og_title', $seoDefaults['og_title']) }}" name="og_title">
                                            <span class="help-block">Lurn Nation</span>
                                        </div>
                                        <br><br>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3" for="og_description">Default Page Description </label>
                                        <div class='col-md-4'>
                                            <input type="text" class="form-control {{ old('og_description', $seoDefaults['og_description']) ? 'edited' : '' }}" id="og_description" value="{{ old('og_description', $seoDefaults['og_description']) }}" name="og_description">
                                        </div>
                                        <br><br>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3" for="og_site_name">Site Name </label>
                                        <div class='col-md-4'>
                                            <input type="text" class="form-control {{ old('og_site_name', $seoDefaults['og_site_name']) ? 'edited' : '' }}" id="og_site_name" value="{{ old('og_site_name', $seoDefaults['og_site_name']) }}" name="og_site_name">
                                            <span class="help-block">Lurn.com</span>
                                        </div>
                                        <br><br>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3" for="og_properties">Properties </label>
                                        <div class='col-md-4'>
                                            <textarea class="form-control {{ old('og_properties', $seoDefaults['og_properties']) ? 'edited' : '' }}" id="og_properties" name="og_properties">{{ old('og_properties', $seoDefaults['og_properties']) }}</textarea>
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
                                            <input type="checkbox" class="make-switch" value="1" data-size="small" id="twitter_enabled" name="twitter_enabled" @if ($seoDefaults['twitter_enabled'] == 1) checked=checked @endif >
                                        </div>
                                        <br><br>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3" for="twitter_card">Card Type</label>
                                        <div class='col-md-2'>
                                            <select name="twitter_card" id="twitter_card" class="form-control inline" style="width: auto">
                                                <option value="summary" {{ $seoDefaults['twitter_card']== "summary" ? "SELECTED" : "" }}>summary</option>
                                                <option value="summary_large_image" {{ $seoDefaults['twitter_card']== "summary_large_image" ? "SELECTED" : "" }}>summary large image</option>
                                                <option value="product" {{ $seoDefaults['twitter_card']== "product" ? "SELECTED" : "" }}>product</option>
                                                <option value="player" {{ $seoDefaults['twitter_card']== "player" ? "SELECTED" : "" }}>player</option>
                                                <option value="photo" {{ $seoDefaults['twitter_card']== "photo" ? "SELECTED" : "" }}>photo</option>
                                                <option value="gallery" {{ $seoDefaults['twitter_card']== "gallery" ? "SELECTED" : "" }}>gallery</option>
                                                <option value="app" {{ $seoDefaults['twitter_card']== "app" ? "SELECTED" : "" }}>app</option>                                                
                                            </select>
                                        </div>
                                        <br><br>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3" for="twitter_site">Site </label>
                                        <div class='col-md-2'>
                                            <input type="text" class="form-control {{ old('twitter_site', $seoDefaults['twitter_site']) ? 'edited' : '' }}" id="twitter_site" value="{{ old('twitter_site', $seoDefaults['twitter_site']) }}" name="twitter_site">
                                            <span class="help-block">Username</span>
                                        </div>
                                        <br><br>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3" for="twitter_title">Default Card Title </label>
                                        <div class='col-md-4'>
                                            <input type="text" class="form-control {{ old('twitter_title', $seoDefaults['twitter_title']) ? 'edited' : '' }}" id="twitter_title" value="{{ old('twitter_title', $seoDefaults['twitter_title']) }}" name="twitter_title">
                                        </div>
                                        <br><br>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3" for="twitter_meta">Metas </label>
                                        <div class='col-md-4'>
                                            <textarea class="form-control {{ old('twitter_meta', $seoDefaults['twitter_meta']) ? 'edited' : '' }}" id="twitter_meta" name="twitter_meta">{{ old('twitter_meta', $seoDefaults['twitter_meta']) }}</textarea>
                                            <span class="help-block">Comma separated list of key=value pairs.<br>description=Your awesome description,creator=@Lurn </span>
                                        </div>
                                        <br><br>
                                    </div>
                                </div>
                            </div>
                            <div class="portlet-title">
                                <div class="caption font-green">
                                    <i class="icon-globe-alt font-green"></i>
                                    <span class="caption-subject bold uppercase"> Analytics </span>
                                </div>
                            </div>
                            <div class="portlet-body form">
                                <div class="form-body">
                                    <div class="form-group">
                                        <label class="control-label col-md-3" for="analytics_google">Google </label>
                                        <div class='col-md-2'>
                                            <input type="text" class="form-control {{ old('analytics_google', $seoDefaults['analytics_google']) ? 'edited' : '' }}" id="analytics_google" value="{{ old('analytics_google', $seoDefaults['analytics_google']) }}" name="analytics_google">
                                            <span class="help-block">UA-XXXXXXXX-X</span>
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
@endsection