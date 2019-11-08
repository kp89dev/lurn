@extends('layouts.app')

@section('content')
    <div class="wrapper">
        <div id="content" class="support-page padded-twice">
            <h1><i class="question circle icon"></i> Frequently Asked Questions</h1>
            <p>Find quick answers to your most common questions here</p>

            <hr>

            <ul id="faq">
                <li v-for="qa in qas" :class="{ open: qa.open }">
                    <div class="question" @click="toggle(qa)">
                        <i class="angle icon" :class="{ down: qa.open, left: ! qa.open }"></i>
                        @{{ qa.question }}
                    </div>
                    <div class="answer" v-html="qa.answer"></div>
                </li>
            </ul>
        </div>
    </div>
@endsection

@section('js')
    <script type="text/javascript">
        new Vue({
            el: '#faq',
            data: {
                qas: {!! $items->toJson() !!}
            },
            methods: {
                toggle: function (qa) {
                    typeof qa.open === 'undefined'
                        ? this.$set(qa, 'open', true)
                        : (qa.open =! qa.open);
                }
            }
        });
    </script>
@endsection
