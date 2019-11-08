@extends('admin.layout')

@section('pagetitle')
    Viewing User
@endsection

@section('breadcrumb')
    <li>
        <i class="fa fa-user"></i>
        <a href="{{ route('users.index') }}">Users</a>
        <i class="fa fa-angle-right"></i>
    </li>
    <li>
        <i class="fa fa-user"></i>
        <a href="{{ route('users.show', $user->id) }}">Viewing {{ $user->name }}</a>
    </li>
@endsection

@section('content')
    <!-- BEGIN PAGE CONTENT-->
    <div class="row margin-top-20">
        <div class="col-md-12">
            <!-- BEGIN PROFILE SIDEBAR -->
            <div class="profile-sidebar">
                <!-- PORTLET MAIN -->
                <div class="portlet light profile-sidebar-portlet">
                    <!-- SIDEBAR USERPIC -->
                    <div class="profile-userpic">
                        <img src="{{ $user->getPrintableImageUrl() }}" class="img-responsive" alt="">
                    </div>
                    <!-- END SIDEBAR USERPIC -->
                    <!-- SIDEBAR USER TITLE -->
                    <div class="profile-usertitle">
                        <div class="profile-usertitle-name">
                            {{ $user->name }}
                        </div>
                        <div class="profile-usertitle-job">
                            {{ $user->email }}
                        </div>
                        @if( ! $user->isAdmin || (user()->isSuperAdmin && ! $user->isSuperAdmin))
                        <div class="actions">
                        <form action="{{ route('users.impersonate') }}" style="display: inline" method="POST">
                                {{ method_field('POST') }}
                                {{ csrf_field() }}
                                <input type="hidden" name="user" value="{{ $user->id }}">
                                <a onclick="$(this).closest('form').submit()" class="btn btn-circle green-haze btn-sm">Login As User</a>
                            </form>
                        </div>
                        @endif
                    </div>
                    <!-- END SIDEBAR USER TITLE -->
                </div>
                <!-- END PORTLET MAIN -->
                <!-- PORTLET MAIN -->
                <div class="portlet light">
                    @include('admin.users.partials.badges')

                    @include('admin.users.partials.certificates')
                </div>
                <!-- END PORTLET MAIN -->
            </div>
            <!-- END BEGIN PROFILE SIDEBAR -->
            <!-- BEGIN PROFILE CONTENT -->
            <div class="profile-content">
                <div class="row">
                    @include('admin.users.partials.purchase-history')

                    @include('admin.users.partials.activity')
                </div>
                <div class="row">
                    @include('admin.users.partials.support')
                    
                    <div class="col-md-6">
                        <!-- BEGIN PORTLET -->
                        <div class="portlet light tasks-widget">
                            <div class="portlet-title">
                                <div class="caption caption-md">
                                    <i class="icon-bar-chart theme-font hide"></i>
                                    <span class="caption-subject font-blue-madison bold uppercase">User merges</span>
                                </div>
                                <div class="actions">
                                    <a class="btn btn-circle green-haze btn-sm" data-toggle="modal" href="#user-merge-popup">Merge Account</a>
                                </div>
                            </div>
                            <div class="portlet-body">
                                <div class="task-content">
                                    <div class="scroller" style="height: 282px;" data-always-visible="1" data-rail-visible1="0" data-handle-color="#D7DCE2">
                                        <!-- START TASK LIST -->
                                        <ul class="task-list">
                                            @foreach($user->mergedAccounts as $mergeAcc)
                                                <li>
                                                    <div class="task-checkbox">
                                                        <i class='fa fa-share-alt'></i>
                                                    </div>
                                                    <div class="task-title">
                                                        <span class="task-title-sp">User with email <b>{{ $mergeAcc->email }}</b> was merged on {{ $mergeAcc->created_at->toCookieString() }}</span>
                                                    </div>
                                                </li>
                                            @endforeach
                                            @foreach($user->mergedImportedAccounts as $mergeAcc)
                                                <li>
                                                    <div class="task-checkbox">
                                                        <i class='fa fa-share-alt'></i>
                                                    </div>
                                                    <div class="task-title">
                                                        <span class="task-title-sp">Imported account <b>{{ $mergeAcc->email }}</b> was merged on {{ $mergeAcc->created_at->toCookieString() }}</span>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </ul>
                                        <!-- END START TASK LIST -->
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- END PORTLET -->
                    </div>
                </div>
            </div>
            <!-- END PROFILE CONTENT -->
        </div>
    </div>
    <!-- END PAGE CONTENT-->
    <!-- /.modal -->
    <div class="modal fade bs-modal-lg" id="user-merge-popup" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title">Account Merge</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="input-icon right">
                                <i class="fa fa-spin"></i>
                                <input type="text" class="form-control input-circle" placeholder="Type user email address" id='user-email'>
                            </div>
                        </div>
                    </div>
                    <div class=="row">
                        <div class="table-scrollable table-scrollable-borderless" id="result-container">
                            <table class="table table-hover table-light">
                                <thead>
                                    <tr class="uppercase">
                                        <th colspan="2">
                                            Name
                                        </th>
                                        <th>
                                            Email
                                        </th>
                                        <th>
                                            Course
                                        </th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn default" data-dismiss="modal">Cancel</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <div id="confirmation" class="modal fade" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title">Confirmation</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <h4>Are you sure you want to proceed ?</h4>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" data-dismiss="modal" class="btn">No</button>
                    <button type="button" class="btn yellow" data-dismiss="modal" onclick="proceedMerging()">Yes</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        function UserService()
        {
            var user = {!! $user->toJson() !!};
            var $ch;
            function init($channel) {
                $ch = $channel;
            }

            function search(e) {
                $ch.trigger({type: 'search_request-started'});
                var request = $.getJSON('/admin/users/search', {
                    'term': e.term
                });

                request.done(function(data){
                    $ch.trigger({type: 'search_request-finished', items: data});
                });
            }

            function merge(userToMerge) {
                if (userToMerge.id == user.id) {
                    return alert('Merging a user to itself is not allowed');
                }

                return $.post('/admin/users/merge', {
                    main_user: user,
                    user_to_merge: userToMerge
                });
            }

            return {
                init: init,
                search: search,
                merge: merge
            }
        }

        function SearchResultDrawer()
        {
            var $drawerContainer = $('#result-container');
            function init($channel, UserService){
                $channel.on('search_request-finished', drawResult);
                $channel.on('user-typed', UserService.search);
            }

            function drawResult(event)
            {
                $drawerContainer.find('tbody').children('tr').remove();

                if (event.items.length < 1) {
                    return $drawerContainer.find('tbody').append('<tr><td colspan="4" class="text-center"><i>No results found! Please search something else</i></td>');
                }

                $.each(event.items, drawUserRow);
            }


            function drawUserRow(i, user)
            {
                var userString = JSON.stringify(user);
                var row = '\
                    <tr> \
                        <td class="fit" colspan="2">';

                if (typeof user['user_id'] == "undefined") {
                    row += '<a href="/admin/users/'+ user.id +'" class="primary-link" target="_blank"><i class="fa fa-user"></i> '+ user.name +'</a>';
                } else {
                    row += user.name;
                }

                    row +=    '</td> \
                        <td> \
                            <span class="bold theme-font">'+ user.email +'</span> \
                        </td> \
                        <td>';

                if (typeof user['user_id'] == "undefined") {
                    row += ' Not filled ';
                } else {
                    row += user['from_table'].replace('users_', '');
                }

                row += '</td> \
                        <td class="text-right" id="user_'+ user.id +'"> \
                            <a data-toggle="modal" href="#confirmation" onclick=\'userToMerge.set('+userString+');\' class="btn btn-circle blue-dark btn-sm">Merge</a> \
                            <i class="fa fa-spinner fa-spin" aria-hidden="true" style="display: none"></i> \
                        </td> \
                    </tr>';

                $drawerContainer.find('tbody').append(row);
            }

            return {
                init: init
            }
        }

        function InputSpinnerLoad()
        {
            var $spinnerContainer = $('#user-email');
            function init($channel) {
                $channel.on('search_request-finished', function() {
                    $spinnerContainer.removeClass('spinner');
                });
                $channel.on('search_request-started', function(){
                    $spinnerContainer.addClass('spinner');
                });
            }

            return {
                init: init
            }
        }

        function proceedMerging()
        {

            var $cnt = $('#user_'+ userToMerge.get().id);
            $cnt.children('a').hide();
            $cnt.children('i').show();

            var reqResult = userService.merge(userToMerge.get());

            reqResult.done(function(result){
                alert(result.message);
            });

            reqResult.fail(function() {
                alert('Failed! Please try again in a few seconds.')
            });

            reqResult.always(function() {
                $cnt.children('a').show();
                $cnt.children('i').hide();
            });
        }

        function UserToMerge()
        {
            var userToMerge;

            function set(user) {
                userToMerge = user;
            }

            function get() {
                return userToMerge;
            }

            return {
                set: set,
                get: get
            }
        }
        var userService = new UserService();
        var userToMerge = new UserToMerge();

        $(document).ready(function(){
            var $channel = $('body');

            userService.init($channel);
            (new SearchResultDrawer()).init($channel, userService);
            (new InputSpinnerLoad()).init($channel);

            $('#user-email').on('keyup', _.debounce(function(e) {
                $('body').trigger({ type: 'user-typed', term: $(e.target).val() });
            }, 400));

        });
    </script>
@endsection
