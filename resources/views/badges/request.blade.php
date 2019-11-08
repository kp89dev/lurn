@extends('layouts.app')

@section('content')
    <div class="wrapper badge">
        <table class="shadow">
            <tr>
                <td id="sidebar">
                    <div class="padded-twice">
                        <div class="photo">
                            <img src="{{ $badge->src }}">
                        </div>
                        <h2>{{ $badge->title }}</h2>
                        <p>{!! $badge->content !!}</p>
                    </div>
                </td>

                <td id="content">
                    <div class="padded-twice">
                        <h1>Request Badge</h1>
                        <hr>

                        @if ($userHasBadge)
                            <div class="ui inverted green segment message">
                                <i class="check icon"></i>
                                Your already gained this badge.
                            </div>
                        @endif

                        <p>To receive a badge, send us your proof of accomplishment and describe the experience.</p>

                        <form method="POST" class="ui form" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <hr>
                            <div class="field">
                                <label>Upload Proof</label>
                                <input type="file" name="proof[]" multiple {{ $userHasBadge ? "DISABLED" : "" }} accept="image/*, application/pdf, application/msword">
                            </div>
                            <p>You can select multiple files at once for upload. Acceptable file types: JPG, PNG, PDF, DOC</p>

                            <hr>
                            <div class="field">
                                <label>Add Comments</label>
                                <p>Describe how the attached documents should earn you this badge (optional)</p>
                                <textarea name="comment"></textarea>
                            </div>

                            <div class="center aligned mt-30 btn-toolbar">
                                <button class="ui primary left labeled icon button {{ $userHasBadge ? "disabled" : "" }}" >
                                    <i class="check icon"></i>
                                    Request <b>Badge</b>
                                </button>
                                <a href="{{ route('front.badges.index', ['course' => $course->slug]) }}" class="ui secondary left labeled icon button">
                                    <i class="caret left icon"></i>
                                    All <b>Badges</b>
                                </a>
                            </div>
                        </form>
                    </div>
                </td>
            </tr>
        </table>
    </div>
@endsection
