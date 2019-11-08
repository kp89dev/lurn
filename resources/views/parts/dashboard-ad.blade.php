<div class="widget">
    <a class="woopra-track user-dash-ad trackable" href="{{ $ad->link }}" data-woopra='{"type":"userDashAd", "action":"follow", "title":"{{ $ad->admin_title }}"}'>
        <img class="primary" src="{{$ad->getPrintableImageUrl()}}" />
        <img class="hover" src="{{($ad->getPrintableImageUrl('hover') ?: $ad->getPrintableImageUrl())}}" />
    </a>
</div>