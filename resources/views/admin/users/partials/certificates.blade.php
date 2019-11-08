<div>
    <h4 class="profile-desc-title">Certificates</h4>
    @if ($user->certificates()->count())
        @foreach ($user->certificates as $certificate)
            <form method="post" action="{{route('user.view.cert', ['user'=>$user->id, 'cert'=>$certificate->id])}}" target="_blank">
                {{ csrf_field() }}
                <a onclick="$(this).closest('form').submit()"><span class="profile-desc-text">{{ $certificate->certificate_title }}</span></a>
            </form>
        @endforeach
    @endif
</div>
