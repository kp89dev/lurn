@extends('layouts.app')

@section('content')
    <div class="wrapper">
        <div id="content" class="support-page padded-twice">
            <h1><i class="life ring icon"></i> Support</h1>

            <div class="ui two columns grid fluid-at w768">
                <div class="column">
                    <a href="{{ route('faq') }}" class="ui fluid blue left labeled icon button" style="text-align: left">
                        <i class="question circle icon"></i>
                        <b>FAQs:</b>
                        Find quick answers to your most common questions here
                    </a>
                </div>
                <div class="column">
                    <a href="{{ route('account-merge.index') }}" class="ui fluid grey left labeled icon button" style="text-align: left">
                        <i class="horizontally flipped share alternate icon"></i>
                        Are you <b>missing a course?</b> Click here
                    </a>
                </div>
            </div>

            <hr>

            <p>Didn't find an answer on the above FAQs page? Send us your issue using the form below.</p>

            <div id="support" class="ui form mt-30">
                <div class="ui grid fluid-at w768">
                    <div class="ten wide column">
                        <div class="field">
                            <label>Your Message</label>
                            <textarea v-model="message" name="message"></textarea>
                        </div>
                    </div>
                    <div class="six wide column">
                        <div class="field">
                            <label>Your Name</label>
                            <input v-model="user.name" name="user-name" placeholder="John Doe">
                        </div>

                        <div class="field">
                            <label>Your Email</label>
                            <input type="email" v-model="user.email" name="user-email" placeholder="you@domain.com">
                        </div>

                        <div class="right aligned">
                            <button @click="sendMessage()" class="ui primary right labeled icon button"
                                    :class="{ loading: loading }">
                                Submit <b>Message</b>
                                <i class="send icon"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script type="text/javascript" src="{{ mix('js/support.js') }}"></script>
@endsection
