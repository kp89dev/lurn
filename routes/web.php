<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Remotes
Route::group(['prefix' => 'remote', 'namespace' => 'Remote'], function () {
    Route::post('register', 'RegisterController@index')->name('remote.register');
    Route::post('extend-suscription/{productId}', 'SubscriptionController@extend')->name('remote.extend-subscription');
});

Route::group(['namespace' => 'Home'], function () {
    Route::get('/', 'HomeController@index')->name('home');
    Route::get('about', 'AboutController@index')->name('about');
    Route::get('contact', 'ContactController@index')->name('contact');
    Route::get('education', 'EducationController@index')->name('education');
    Route::get('outreach', 'OutreachController@index')->name('outreach');

    Route::group(['prefix' => 'legal'], function () {
        Route::get('sms-privacy', 'Legal\LegalController@smsPrivacy')->name('sms-privacy');
        Route::get('anti-spam', 'Legal\LegalController@antispam')->name('anti-spam');
        Route::get('refund', 'Legal\LegalController@refund')->name('refund');
        Route::get('dmca', 'Legal\LegalController@dmca')->name('dmca');
        Route::get('terms', 'Legal\LegalController@terms')->name('terms');
        Route::get('privacy', 'Legal\LegalController@privacy')->name('privacy');
    });

    Route::group(['prefix' => 'career'], function () {
        Route::get('/', 'Career\CareerController@index')->name('career');
        Route::get('webdeveloper', 'Career\CareerController@webdeveloper')->name('webdeveloper');
        Route::get('juniorcopywriter', 'Career\CareerController@juniorcopywriter')->name('juniorcopywriter');
        Route::get('lurncentermanager', 'Career\CareerController@lurncentermanager')->name('lurncentermanager');
        Route::get('customerhappinessspecialist', 'Career\CareerController@customerhappinessspecialist')->name('customerhappinessspecialist');
        Route::get('associatecontentmanager', 'Career\CareerController@associatecontentmanager')->name('associatecontentmanager');
        Route::get('designer', 'Career\CareerController@designer')->name('designer');
    });
});

Route::group(['middleware' => 'auth'], function () {
    Route::get('dashboard', 'DashboardController@index')->name('dashboard');
    Route::get('calendar', 'CalendarController@index')->name('calendar');
});

//Tools 
Route::group(['prefix' => 'tools', 'middleware' => 'auth'], function () {
    Route::group(['prefix' => 'niche-detective'], function () {
        Route::get('/', 'Tools\NicheDetective\NicheController@index')
            ->name('niche-tool')
            ->middleware('can:access-niche-detective');
        Route::post('get-niche-categories', 'Tools\NicheDetective\NicheController@getNicheCategories')
            ->name('get-niche-categories');
        Route::get('niche/{id}', 'Tools\NicheDetective\NicheController@nicheReport')->name('niche-detail');
    });
    
    Route::get('business-builder', 'Tools\BusinessBuilderController@index')->name('business-builder');
    Route::get('business-builder-publish-academy', 'Tools\BusinessBuilderController@index_pa')->name('business-builder-publish-academy');
    Route::get('business-builder-digital-profit-engine', 'Tools\BusinessBuilderController@index_dpe')->name('business-builder-digital-profit-engine');
    Route::get('launchpad', 'Tools\LaunchpadController@index')->name('launchpad');
    Route::get('digital-lead-academy-creator', 'Tools\DlaCreatorController@index')->name('dla-creator');
    Route::post('zeroup-lab', 'Tools\ZeroUpLabController@prepRequest')->name('zeroup-lab');
});

// Account
Route::group(['prefix' => 'account', 'middleware' => 'auth'], function () {
    Route::get('merge', 'Account\AccountMergeController@index')->name('account-merge.index');
    Route::get('search', 'Account\AccountMergeController@search')->name('account-merge.search');
    Route::post('initiate-merge', 'Account\AccountMergeController@initiateMerge')->name('account-merge.initiate');
    Route::get('proceed-merge/{token}', 'Account\AccountMergeController@proceedMerge')->name('account-merge.confirm');

    Route::group(['prefix' => 'settings'], function () {
        Route::post('/cancel-subscription', 'Account\CancelSubscriptionController@index')->name('cancel.subscription');
    });
});

// Profile
Route::group(['prefix' => 'profile', 'middleware' => 'auth'], function () {
    Route::get('/', 'Profile\SettingsController@index')->name('profile');
    Route::post('/', 'Profile\SettingsController@store')->name('profile.store');
    Route::post('certificate', 'Profile\SettingsController@certificate')->name('user-certificate');
});

// Social sharing
Route::group(['middleware' => 'auth'], function () {
    Route::get('/login/{service}', 'Auth\Social\LoginController@redirectToProvider')
        ->where('service', '(twitter|facebook|instagram)')
        ->name('social-login');
    Route::get('/login/{service}/callback', 'Auth\Social\LoginController@handleProviderCallback')
        ->where('service', '(twitter|facebook|instagram)');

    Route::get('/share/twitter', 'Social\TwitterController@index')->name('social-share.twitter');
    Route::post('/share/twitter', 'Social\TwitterController@share');
    Route::get('/share/facebook', 'Social\FacebookController@index')->name('social-share.facebook');
    Route::post('/share/facebook', 'Social\FacebookController@share');

    // Route::get('/share/instagram', 'Social\InstagramController@index')->name('social-share.instagram');
    // Route::post('/share/instagram', 'Social\InstagramController@share');
});

// Classroom
Route::group(['prefix' => 'classroom', 'namespace' => 'Classroom'], function () {
    Route::get('/', 'ResourceController@index')->name('classroom');
    Route::get('{course}', 'ResourceController@course')->name('course');

    Route::get('{course}/forum', 'ForumController@showRules')->name('course.forum');

    Route::get('{course}/notes', 'ResourceController@notes')->name('notes');
    Route::get('{course}/notes/print', 'ResourceController@printNotes')->name('print-notes');

    Route::get('{course}/enroll', 'EnrollController@index')->name('enroll');
    Route::post('{course}/enroll', 'EnrollController@enroll')->name('do.enrollment');
    Route::get('{course}/finish', 'AfterSaleController@index')->name('enroll.after-sale');
    Route::get('{course}/thank-you', 'AfterSaleController@thankYou')->name('enroll.thank-you');

    Route::get('{course}/badges', 'BadgesController@index')->name('front.badges.index');
    Route::get('{course}/badge/{badge}/request', 'BadgesController@request')->name('front.badges.request');
    Route::post('{course}/badge/{badge}/request', 'BadgesController@requestStore')->name('front.badges.requestStore');

    Route::get('{course}/access-denied', 'ResourceController@accessDenied')->name('access.denied');
    Route::get('{course}/{module}', 'ResourceController@module')->name('module');
    Route::get('{course}/{module}/{lesson}', 'ResourceController@lesson')->name('lesson');
    Route::get('test/{course}/{module}/{test}', 'ResourceController@test')->name('test');
    Route::post('test/{course}/{module}/{test}', 'ResourceController@checkTest')->name('test-submit');
    Route::get('test/{course}/{module}/{test}/certificate', 'ResourceController@testCertificate')->name('test-certificate');
});

Route::get('support', 'SupportController@index')->name('support');
Route::get('faq', 'SupportController@faq')->name('faq');

Route::get('news', 'NewsController@index')->name('news');
Route::get('news/{news}', 'NewsController@show')->name('news-article');

// Sitemap
Route::get('sitemap_index', 'SitemapsController@index')->name('sitemap.index');
Route::get('sitemap_general', 'SitemapsController@general')->name('sitemap.general');
Route::get('sitemap_niches', 'SitemapsController@niches')->name('sitemap.niches');
Route::get('sitemap_courses', 'SitemapsController@courses')->name('sitemap.courses');
Route::get('sitemap_news', 'SitemapsController@news')->name('sitemap.news');
Route::get('sitemap_legal', 'SitemapsController@legal')->name('sitemap.legal');
Route::get('sitemap_career', 'SitemapsController@career')->name('sitemap.career');

// API
Route::group(['prefix' => 'api', 'namespace' => 'Api'], function () {
    Route::post('notes/{course}', 'ClassroomController@notes');
    Route::post('complete-lesson/{course}', 'ClassroomController@completeLesson');
    Route::post('support-message', 'SupportController@message');
    Route::post('hide-message/{message}', 'DashboardController@hideMessage');
    Route::get('unread-news', 'DashboardController@unreadNews');
    Route::post('mark-news-read', 'DashboardController@markNewsRead');
    Route::post('feedback', 'DashboardController@feedback');
    Route::get('events', 'CalendarController@events');
    Route::get('unread-push-notifications', 'PushNotificationsController@unreadPushNotifications')
        ->name('unread-push-notifications');
    Route::post('mark-push-notification-read', 'PushNotificationsController@markPushNotificationRead')
        ->name('mark-push-notification-read');
    Route::post('click-thumbs-up', 'ThumbsUpController@click');
    Route::post('onboarding-complete', 'OnboardingController@markScenarioComplete');
    Route::post('onboarding-email', 'OnboardingController@sendReferralEmail');
    Route::post('onboarding-survey/{survey}', 'OnboardingController@saveSurvey');
    Route::post('onboarding-picture', 'OnboardingController@uploadProfilePicture');
    Route::post('first-course', 'OnboardingController@firstCourse');
    Route::post('save-cart-reminder', 'CartReminderController@save');
    Route::post('remove-cart-reminder', 'CartReminderController@remove');
});

Route::group(['namespace' => 'Api'], function () {
    Route::get('refer/{code}', 'ReferralController@index')->name('referral.index');
});

Route::group(['prefix' => 'webhook', 'namespace' => 'Webhook'], function () {
    Route::post('vanilla/{course}/request', 'VanillaForumController@prepRequest')->name('webhook.vanilla.request');
    Route::post('forum-rules/', 'VanillaForumController@userRules')->name('webhook.forum.rules');
    Route::get('vanilla/sso', 'VanillaForumController@sso')->name('webhook.vanilla');
});

// Authentication
Auth::routes();

Route::get('logout', 'Auth\LoginController@logout')->name('logout');
Route::get('verify/{encryptedId}', 'Auth\VerifyController@index')->name('verify');
Route::get('resend-verification', 'Auth\VerifyController@showResendForm')->name('resend-verification');
Route::post('resend-verification', 'Auth\VerifyController@resend');

Route::get('idp-login', 'AuthProvider\GenerateTokenController@index')->name('idp.login');

Route::group(['middleware' => 'auth'], function () {
    Route::get('onboarding', 'ExternalOnboardingController@index')->name('onboarding.index');
    Route::post('onboarding', 'ExternalOnboardingController@saveInterests')->name('onboarding.interests');
    Route::get('onboarding/courses', 'ExternalOnboardingController@courseChoice')->name('onboarding.courses');
    Route::post('onboarding/enroll', 'ExternalOnboardingController@enrollInChoices')->name('onboarding.enroll');
    Route::get('onboarding/demo/{course?}', 'ExternalOnboardingController@demo')->name('onboarding.demo');
});

Route::get('remote-form', 'ExternalOnboardingController@remoteRegister')->name('onboarding.signup');
