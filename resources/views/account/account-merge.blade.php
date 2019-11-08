@extends('layouts.app')

@section('content')
    <div class="wrapper">
        <div id="content" class="support-page padded-twice">
            <h1><i class="horizontally flipped share alternate icon"></i> Merge Accounts</h1>
            <p>Use this page to gain access to courses you have access to by other email addresses other than <strong>{{ Auth::user()->email }}</strong></p>

            <hr>

            <div id="merge-app" class="center aligned">
                <p>What other email addresses have you used before to access courses?</p>

                <div class="ui action input" style="max-width: 400px">
                    <input v-model="email" placeholder="e.g. myself@domain.com">
                    <button @click="check()" class="ui secondary left labeled icon button"
                    :class="{ loading: loadingCheck, disabled: ! validEmail || loadingCheck }">
                    <i class="search icon"></i>
                    Check
                    </button>
                </div>

                <div class="results ui segment" v-if="results">
                    <h3 class="title mb-30">
                        This email address has access to @{{ results }} courses.
                        After you click Merge Accounts button we'll send a confirmation email to @{{ email }}. Click the link
                        from that email to continue the merge. After the merge is completed you'll be unable to access @{{ email }}
                        because all your course access will be transfered to {{ Auth::user()->email }}.
                    </h3>
                    <button @click="merge" class="ui primary left labeled icon button"
                    :class="{ 'disabled loading': loadingMerge }">
                    <i class="horizontally flipped share alternate icon"></i>
                    Merge Accounts
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div id="confirmation-modal" class="ui small modal">
        <i class="close icon"></i>
        <div class="header">
            Merge Initiated
        </div>
        <div class="content">
            <p>We've sent you an email to verify that this is indeed your email address. Please click on the verification link in the email to finish the merging process.</p>
        </div>
        <div class="actions">
            <div class="ui positive left labeled icon button">
                <i class="checkmark icon"></i>
                OK
            </div>
        </div>
    </div>

    <div id="no-results-modal" class="ui small modal">
        <i class="close icon"></i>
        <div class="header">
            No Accounts
        </div>
        <div class="content">
            <p>There are no accounts associated with this email address.</p>
        </div>
        <div class="actions">
            <div class="ui positive left labeled icon button">
                <i class="checkmark icon"></i>
                OK
            </div>
        </div>
    </div>

@endsection

@section('js')
    <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.3.0/components/dimmer.min.js"></script>
    <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.3.0/components/modal.min.js"></script>
    <script type="text/javascript">
        new Vue({
            el: '#merge-app',
            data: {
                email: null,
                loadingCheck: false,
                loadingMerge: false,
                results: 0
            },
            computed: {
                validEmail: function () {
                    return this.email && this.email.match(/^[a-z0-9_.+-]+@[a-z0-9-]+(\.[a-z][a-z]+)+$/i);
                }
            },
            methods: {
                check: function () {
                    var that = this;
                    that.loadingCheck = true;
                    that.results = 0;

                    $.getJSON('/account/search', { email: this.email }, function (results) {
                        that.results = results.data.length;

                        if (that.results == 0) {
                            $('#no-results-modal').modal('show')
                        }
                    }).always(function () { that.loadingCheck = false });
                },
                merge: function () {
                    var that = this;
                    that.loadingMerge = true;

                    $.post('/account/initiate-merge', { email: this.email }, function (res) {
                        if (res.success) {
                            $('#confirmation-modal').modal('show');
                            that.results = 0;
                            that.email = null;
                        }
                    }).always(function () { that.loadingMerge = false });
                }
            },
            watch: {
                email: function () {
                    this.results = 0;
                }
            }
        });
    </script>
@endsection
