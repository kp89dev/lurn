<!-- BEGIN SIDEBAR -->
<div class="page-sidebar-wrapper">
    <!-- DOC: Set data-auto-scroll="false" to disable the sidebar from auto scrolling/focusing -->
    <!-- DOC: Change data-auto-speed="200" to adjust the sub menu slide up/down speed -->
    <div class="page-sidebar navbar-collapse collapse">
        <!-- BEGIN SIDEBAR MENU -->
        <!-- DOC: Apply "page-sidebar-menu-light" class right after "page-sidebar-menu" to enable light sidebar menu style(without borders) -->
        <!-- DOC: Apply "page-sidebar-menu-hover-submenu" class right after "page-sidebar-menu" to enable hoverable(hover vs accordion) sub menu mode -->
        <!-- DOC: Apply "page-sidebar-menu-closed" class right after "page-sidebar-menu" to collapse("page-sidebar-closed" class must be applied to the body element) the sidebar sub menu mode -->
        <!-- DOC: Set data-auto-scroll="false" to disable the sidebar from auto scrolling/focusing -->
        <!-- DOC: Set data-keep-expand="true" to keep the submenues expanded -->
        <!-- DOC: Set data-auto-speed="200" to adjust the sub menu slide up/down speed -->
        <ul class="page-sidebar-menu" data-keep-expanded="false" data-auto-scroll="true" data-slide-speed="200">
            <!-- DOC: To remove the sidebar toggler from the sidebar you just need to completely remove the below "sidebar-toggler-wrapper" LI element -->
            <li class="sidebar-toggler-wrapper">
                <!-- BEGIN SIDEBAR TOGGLER BUTTON -->
                <div class="sidebar-toggler">
                </div>
                <!-- END SIDEBAR TOGGLER BUTTON -->
            </li>
            <!-- DOC: To remove the search box from the sidebar you just need to completely remove the below "sidebar-search-wrapper" LI element -->
            <li class="sidebar-search-wrapper">
                <!-- BEGIN RESPONSIVE QUICK SEARCH FORM -->
                <!-- DOC: Apply "sidebar-search-bordered" class the below search form to have bordered search box -->
                <!-- DOC: Apply "sidebar-search-bordered sidebar-search-solid" class the below search form to have bordered & solid search box -->
                <form class="sidebar-search " action="extra_search.html" method="POST">
                    <a href="javascript:;" class="remove">
                        <i class="icon-close"></i>
                    </a>
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Search...">
                        <span class="input-group-btn">
							<a href="javascript:;" class="btn submit"><i class="icon-magnifier"></i></a>
							</span>
                    </div>
                </form>
                <!-- END RESPONSIVE QUICK SEARCH FORM -->
            </li>
            <li class="start">
                <a href="{{ route('admin') }}" class="active">
                    <i class="icon-settings"></i>
                    <span class="title">Dashboard</span>
                </a>
            </li>
            @if(user()->isSuperAdmin)
                @if (user()->hasAdminAccess('users', 'read'))
                    <li class="start">
                        <a href="{{ route('users.index') }}" class="active">
                            <i class="icon-users"></i>
                            <span class="title">Users</span>
                        </a>
                    </li>
                @endif
                @if (user()->hasAdminAccess('logins', 'read'))
                    <li class="start">
                        <a href="{{ route('user-logins.index') }}" class="active">
                            <i class="icon-login"></i>
                            <span class="title">User Logins</span>
                        </a>
                    </li>
                @endif
                @if (user()->hasAdminAccess('badge-requests', 'write'))
                    <li class="start">
                        <a href="{{ route('badge.requests.new') }}" class="active">
                            <i class="icon-badge"></i>
                            <span class="title">Badge Requests</span>
                        </a>
                    </li>
                @endif
            @endif
            @if (user()->hasAdminAccess('course-containers', 'read'))
                <li class="start">
                    <a href="{{ route('course-containers.index') }}" class="active">
                        <i class="icon icon-bag"></i>
                        <span class="title">Course Containers</span>
                    </a>
                </li>
            @endif
            @if (user()->hasAdminAccess('labels', 'read'))
                <li class="start">
                    <a href="{{ route('labels.index') }}" class="active">
                        <i class="icon icon-tag"></i>
                        <span class="title">Labels</span>
                    </a>
                </li>
            @endif
            @if (user()->hasAdminAccess('categories', 'read'))
                <li class="start">
                    <a href="{{ route('categories.index') }}" class="active">
                        <i class="icon-tag"></i>
                        <span class="title">Categories</span>
                    </a>
                </li>
            @endif
            @if (user()->hasAdminAccess('courses', 'read'))
                <li class="start">
                    <a href="{{ route('courses.index') }}" class="active">
                        <i class="icon-book-open"></i>
                        <span class="title">Courses</span>
                    </a>
                </li>
            @endif
            @if (user()->hasAdminAccess('course-upsells', 'read'))
                <li class="start">
                    <a href="{{ route('upsells.index') }}" class="active">
                        <i class="icon-like"></i>
                        <span class="title">Course Upsells</span>
                    </a>
                </li>
            @endif
            @if (user()->hasAdminAccess('courses', 'read'))
                <li class="start">
                    <a href="{{ route('bonuses.index') }}" class="active">
                        <i class="icon-star"></i>
                        <span class="title">Bonuses</span>
                    </a>
                </li>
            @endif
            @if(user()->isSuperAdmin)
                @if (user()->hasAdminAccess('homepage', 'read'))
                    <li class="start">
                        <a href="{{ route('homepage.index') }}" class="active">
                            <i class="icon-picture"></i>
                            <span class="title">Homepage Settings</span>
                        </a>
                    </li>
                @endif
                @if (user()->hasAdminAccess('events', 'read'))
                    <li class="start">
                        <a href="{{ route('events.index') }}" class="active">
                            <i class="icon-calendar"></i>
                            <span class="title">Events</span>
                        </a>
                    </li>
                @endif
                @if (user()->hasAdminAccess('ads', 'read'))
                    <li class="start">
                        <a href="{{ route('ads.index') }}" class="active">
                            <i class="icon-folder-alt"></i>
                            <span class="title">Ads</span>
                        </a>
                    </li>
                @endif
                @if (user()->hasAdminAccess('news', 'read'))
                    <li class="start">
                        <a href="{{ route('news.index') }}" class="active">
                            <i class="icon-feed"></i>
                            <span class="title">News</span>
                        </a>
                    </li>
                @endif
                @if (user()->hasAdminAccess('push-notifications', 'read'))
                    <li class="start">
                        <a href="{{ route('push-notifications.index') }}" class="active">
                            <i class="icon-microphone"></i>
                            <span class="title">Push Notifications</span>
                        </a>
                    </li>
                @endif
                @if (user()->hasAdminAccess('faq', 'read'))
                    <li class="start">
                        <a href="{{ route('faq.index') }}" class="active">
                            <i class="icon-question"></i>
                            <span class="title">FAQ</span>
                        </a>
                    </li>
                @endif
                @if (user()->hasAdminAccess('feedback', 'read'))
                    <li class="start">
                        <a href="{{ route('feedback.index') }}" class="active">
                            <i class="icon-bubble"></i>
                            <span class="title">Feedback</span>
                        </a>
                    </li>
                @endif
                @if (user()->hasAdminAccess('sendlane', 'read'))
                    <li class="start">
                        <a href="{{ route('sendlane.index') }}" class="active">
                            <i class="icon-envelope"></i>
                            <span class="title">Sendlane</span>
                        </a>
                    </li>
                @endif
                @if (user()->hasAdminAccess('tools', 'read'))
                    <li class="start">
                        <a href="{{ route('tools.index') }}" class="active">
                            <i class="icon-wrench"></i>
                            <span class="title">Tools</span>
                        </a>
                    </li>
                @endif
                @if (user()->hasAdminAccess('stats', 'read'))
                    <li class="start">
                        <a href="{{ route('stats.index') }}" class="active">
                            <i class="icon-wrench"></i>
                            <span class="title">Revenue Reports</span>
                        </a>
                    </li>
                @endif
                @if (user()->hasAdminAccess('seo', 'read'))
                    <li class="start">
                        <a href="{{ route('seo.index') }}" class="active">
                            <i class="icon-equalizer"></i>
                            <span class="title">SEO</span>
                        </a>
                    </li>
                @endif
                @if (user()->hasAdminAccess('workflows', 'read'))
                    <li class="start">
                        <a href="{{ route('workflows.index') }}" class="active">
                            <i class="fa fa-code-fork"></i>
                            <span class="title">Workflows</span>
                        </a>
                    </li>
                @endif
                @if (user()->hasAdminAccess('user-roles', 'read'))
                    <li class="start">
                        <a href="{{ route('roles.index') }}" class="active">
                            <i class="fa fa-legal"></i>
                            <span class="title">Admin User Roles</span>
                        </a>
                    </li>
                @endif

                @if (user()->hasAdminAccess('user-roles', 'read'))
                    <li class="start">
                        <a href="{{ route('view.settings') }}" class="active">
                            <i class="fa fa-chain"></i>
                            <span class="title">General Settings</span>
                        </a>
                    </li>
                @endif

                 @if (user()->hasAdminAccess('surveys', 'read'))
                    <li class="start">
                        <a href="{{ route('surveys.index') }}" class="active">
                            <i class="fa fa-question"></i>
                            <span class="title">Surveys</span>
                        </a>
                    </li>
                @endif

                @if (user()->hasAdminAccess('test-results', 'read'))
                    <li class="start">
                        <a href="{{ route('test-results.index') }}" class="active">
                            <i class="fa fa-check-square"></i>
                            <span class="title">Test Results</span>
                        </a>
                    </li>
                @endif

                @if (user()->isSuperAdmin)
                    <li class="start">
                        <a href="{{ route('log-viewer::logs.list') }}" target="_blank" class="active">
                            <i class="icon-book-open"></i>
                            <span class="title">View Logs</span>
                        </a>
                    </li>
                @endif
            @endif
        </ul>
        <!-- END SIDEBAR MENU -->
    </div>
</div>
<!-- END SIDEBAR -->
