@extends('layouts.chute')

@section('content')
    <form action="{{ route('onboarding.interests') }}" method="POST">
        @csrf
        <div class="ui grid container">
            <div class="column sixteen wide extra">
                <h1>Tell Us What You're Most Interested In&hellip;</h1>
                <p>This will help us to identify which courses are most relevant to you & your business.</p>
                <p class="note">Note: You can update your interest preferences in your profile at any time.</p>
            </div>

            @foreach ($categories as $category)
                <div class="column four wide extra">
                    <div class="card">
                        <div class="category-picture">
                            <img src="{{ $category->getPrintableImageUrl() }}">
                        </div>
                        <div class="name">
                            <span class="category_name">{{ $category->name }}</span>
                            <i class="picked fa fa-check" style="display:none;"></i>
                            <input type="checkbox" class="catform" id="cb_{{ $category->id }}" name="categories[]"
                                   value="{{ $category->id }}">
                        </div>
                    </div>
                </div>
            @endforeach

            <div class="three column row">
                <div class="left floated column">
                </div>
                <div class="right aligned floated column">
                    <button type="submit" class="ui button next">
                        Next Step
                    </button>
                </div>
            </div>
        </div>
    </form>
@endsection

@section('js')
    <script type="text/javascript">
        jQuery(document).ready(function ($) {
            $('.card').click(function (event) {
                $('.catform:checked').prop('checked', false);
                $('.picked:visible').hide();
                $(event.target).parentsUntil('.column.four.wide').find('.catform').prop('checked', !$(event.target).parentsUntil('.column.four.wide').find('.catform').prop('checked'));
                $(event.target).parentsUntil('.column.four.wide').find('.picked').toggle();
            });

            if (top.location !== location) {
                top.location.href = document.location.href;
            }
        });
    </script>
@endsection
