<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('admin.landing');
})->name('admin');

Route::post('/', 'CourseController@featured')->name('courses.admin.featured')
    ->middleware('admin.role.auth:courses,write');

Route::get('file/download', 'PrivateFileDownloadController@download')->name('file.download');

Route::get('courses/search', 'CourseSearchController@index')->name('courses.search');
Route::post('courses/search', 'CourseSearchController@search')->name('courses.admin.search');

Route::resource('categories', 'CategoriesController', ['except' => 'show']);

Route::group(['prefix' => 'tests/{test}'], function () {
    Route::post('question/create', 'Tests\TestsController@storeQuestion')
        ->name('tests.create.question');

    Route::get('question/edit/{questionId?}', 'Tests\TestsController@editQuestion')
        ->name('tests.edit.question');

    Route::delete('question/delete/{questionId?}', 'Tests\TestsController@removeQuestion')
        ->name('tests.delete.question');

    Route::delete('delete', 'Tests\TestsController@destroy')
        ->name('tests.destroy');
});

Route::get('test-results', 'TestResults\TestResultsController@index')->name('test-results.index');
Route::get('test-results/{testResult}', 'TestResults\TestResultsController@show')->name('test-results.show');
Route::get('test-results/{testResult}/download-pdf', 'TestResults\TestResultsController@downloadPdf')->name('test-results.download-pdf');

Route::resource('course/upsells', 'Upsells\UpsellController', ['except' => 'show']);

Route::resource('courses', 'CourseController', [
    'except' => ['show', 'destroy'],
]);

Route::resource('bonuses', 'Bonuses\ResourceController');

Route::group(['prefix' => 'courses/{course}'], function () {
    Route::resource('tests', 'Tests\TestsController', [
        'except' => ['destroy'],
    ]);
    Route::get('tests/{test}/download-pdf', 'Tests\TestsController@downloadPdf')->name('tests.download-pdf');

    Route::get('tests/{test}/removeImage/{image}', 'Tests\TestsController@removeImage')
        ->name('tests.removeImage')
        ->middleware('admin.role.auth:courses,write');

    Route::resource('modules', 'Modules\ModuleController', [
        'except' => ['show'],
    ]);
    Route::get('modules/order', 'Modules\OrderModulesController@index')
        ->name('modules.order')
        ->middleware('admin.role.auth:courses,read');
    Route::post('modules/order', 'Modules\OrderModulesController@store')
        ->name('modules.order.store')
        ->middleware('admin.role.auth:courses,write');

    Route::group(['prefix' => 'modules/{module}'], function () {
        Route::any('lessons/preview', 'Lessons\LessonController@preview')
            ->name('lessons.preview')
            ->middleware('admin.role.auth:courses,read');

        Route::get('lessons/{lesson}/remove', 'Lessons\LessonController@remove')
            ->name('lessons.remove')
            ->middleware('admin.role.auth:courses,write');

        Route::get('lessons/order', 'Lessons\OrderLessonsController@index')
            ->name('lessons.order')
            ->middleware('admin.role.auth:courses,read');
        Route::post('lessons/order', 'Lessons\OrderLessonsController@store')
            ->name('lessons.order.store')
            ->middleware('admin.role.auth:courses,write');

        Route::resource('lessons', 'Lessons\LessonController', [
            'except' => ['destroy'],
        ]);
    });

    Route::resource('badges', 'Badges\BadgesController', [
        'except' => ['show'],
    ]);

    Route::resource('course-bonuses', 'CourseBonuses\ResourceController', ['except' => 'show']);

    Route::resource('certs', 'Certificates\CertificatesController', ['except' => 'show']);
    Route::get('certs/{cert}/removeImage/{image}', 'Certificates\CertificatesController@removeImage')
        ->name('certs.removeImage');
    Route::get('certs/{cert}/previewCert', 'Certificates\CertificatesController@previewCert')
        ->name('certs.previewCert');
});

Route::post('lessons/drip', 'Lessons\LessonController@updateDrip')
    ->name('lessons.drip')
    ->middleware('admin.role.auth:courses,write');

Route::resource('course-containers', 'CourseContainerController', [
    'except' => ['show', 'destroy'],
]);

Route::group(['namespace' => 'Questionnaires'], function () {
    Route::resource('surveys', 'SurveyController');
    Route::get('surveys/stats/{survey}', 'SurveyController@stats')->name('surveys.stats');
});

Route::group(['middleware' => ['super.admin']], function () {
    Route::get('users/search', 'Users\SearchController@index')->name('users.search')
        ->middleware('admin.role.auth:users,read');
    Route::post('users/search', 'Users\SearchController@search')->name('users.admin.search')
        ->middleware('admin.role.auth:users,read');

    Route::get('users/{user}', 'Users\UserProfileController@index')
        ->name('users.show')
        ->where(['user' => '[0-9]+'])
        ->middleware('admin.role.auth:users,read');

    Route::post('users/{user}/cert/{cert}/view', 'Certificates\CertificatesController@viewUserCert')
        ->name('user.view.cert')
        ->middleware('admin.role.auth:users,read');

    Route::post('users/merge', 'Users\UserMergeController@index')->name('users.merge')
        ->middleware('admin.role.auth:users,write');

    Route::post('users/impersonate', 'Users\UserController@impersonate')
        ->name('users.impersonate');
    Route::post('users/{user}/toggle-onboarding', 'Users\UserController@toggleOnboarding')->name('users.toggle-onboarding');
    Route::resource('users', 'Users\UserController', ['except' => 'show']);

    Route::resource('faq', 'FaqController', ['except' => 'show']);

    Route::resource('news', 'NewsController');

    Route::get('feedback/download-csv', 'FeedbackController@downloadCsv')->name('feedback.download-csv');
    Route::resource('feedback', 'FeedbackController');

    Route::get('homepage', 'Homepage\HomepageController@index')->name('homepage.index');
    Route::post('homepage/store-featured', 'Homepage\HomepageController@storeFeatured')
        ->name('homepage.store-featured');

    Route::get('user-logins', 'Users\LoginsController@index')->name('user-logins.index')
        ->middleware('admin.role.auth:logins,read');

    Route::resource('events', 'Events\EventsController', [
        'except' => ['show'],
    ]);

    Route::resource('ads', 'Ads\AdController', [
        'except' => ['show', 'destroy'],
    ]);

    Route::get('general-settings', 'GeneralSettings\GeneralSettingsController@index')->name('view.settings');
    Route::post('general-settings', 'GeneralSettings\GeneralSettingsController@store')->name('store.settings');

    Route::group(['prefix' => 'badge-requests'], function () {
        Route::get('/', 'Badges\BadgeRequestController@requireAttention')
            ->name('badge.requests.new')
            ->middleware('admin.role.auth:badge-requests,read');
        Route::get('old', 'Badges\BadgeRequestController@oldRequests')
            ->name('badge.requests.old')
            ->middleware('admin.role.auth:badge-requests,read');
        Route::post('{badgeRequest}/approve', 'Badges\BadgeRequestController@approve')
            ->name('badge.requests.approve')
            ->middleware('admin.role.auth:badge-requests,write');
        Route::post('{badgeRequest}/reject', 'Badges\BadgeRequestController@reject')
            ->name('badge.requests.reject')
            ->middleware('admin.role.auth:badge-requests,write');
    });

    Route::resource('sendlane', 'Sendlane\SendlaneController', [
        'except' => ['show', 'destroy'],
    ]);

    Route::resource('tools', 'Tools\ToolsController', [
        'except' => ['show'],
    ]);

    Route::get('tools/launchpad', 'Tools\ToolsController@launchpad')->name('tools.launchpad.admin');

    Route::group(['prefix' => 'seo'], function () {
        Route::get('/', 'SEO\SEOController@index')->name('seo.index')
            ->middleware('admin.role.auth:seo,read');
        Route::post('default/update', 'SEO\SEOController@updateDefault')->name('seo.update.default')
            ->middleware('admin.role.auth:seo,write');
        Route::post('course/update', 'SEO\SEOController@updateCourse')->name('seo.update.course')
            ->middleware('admin.role.auth:seo,write');
    });

    Route::resource('workflows', 'Workflows\WorkflowController', ['except' => ['update', 'destroy', 'show']]);
    Route::post('workflows/update-status', 'Workflows\WorkflowController@updateStatus')->name('workflows.update-status');
    Route::get('workflows/{workflow}/emails', 'Workflows\EmailStatsController@index')->name('workflows.email-stats');


    /* What permissions should be used for templates? */
    Route::resource('templates', 'Templates\TemplateController');
    Route::post('templates/preview', 'Templates\TemplateController@preview')->name('templates.preview');

    Route::resource('labels', 'Labels\LabelsController');

    //Route::group(['prefix' => 'push-notifications'], function() {
    //Route::get('/', 'PushNotifications\PushNotificationsController@index')->name('push-notifications.index');
    //Route::get('new', 'PushNotifications\PushNotificationsController@create')->name('push-notifications.create');
    //Route::get('{pushNotification}/edit', 'PushNotifications\PushNotificationsController@edit')->name('push-notifications.edit');
    //Route::post('{pushNotification}/edit', 'PushNotifications\PushNotificationsController@update')->name('push-notifications.update');
    //Route::post('{pushNotification}/remove', 'PushNotifications\PushNotificationsController@destroy')->name('push-notifications.destroy');
    //});

    Route::resource('push-notifications', 'PushNotifications\PushNotificationsController', ['except' => ['show']]);

    Route::group(['prefix' => 'stats', 'middleware' => 'admin.role.auth:stats,read'], function () {
        Route::get('/', 'Stats\StatsController@index')
            ->name('stats.index');
        Route::post('/', 'Stats\StatsController@detailed')
            ->name('stats.detailed');
        Route::post('/average', 'Stats\StatsController@average')
            ->name('stats.average');
    });

    Route::resource('roles', 'RolesController');

});
