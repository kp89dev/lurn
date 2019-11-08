<?php

namespace App\Providers;

use App\Events\Course\CourseCompleted;
use App\Events\Onboarding\EvaluationCompleted;
use App\Events\Onboarding\OrientationCompleted;
use App\Events\Onboarding\ProfileCompleted;
use App\Events\User\ImportedUserLoggedIn;
use App\Events\User\ImportedUserMerged;
use App\Events\User\UserCreatedThroughInfusionsoft;
use App\Events\User\UserCreatedThroughAdmin;
use App\Events\User\UserCreatedThroughExternalFunnel;
use App\Events\User\UserEmailChanged;
use App\Events\User\UserEnrolled;
use App\Events\User\UserMerged;
use App\Events\User\UserRecruited;
use App\Events\Social\FacebookPostShared;
use App\Events\Social\TwitterPostShared;
use App\Listeners\Account\Imported\AdjustCourseAccess;
use App\Listeners\Account\Imported\AdjustLessonSubscription;
use App\Listeners\Account\Imported\AdjustTestUsers;
use App\Listeners\Account\Imported\AdjustUserBadges;
use App\Listeners\Account\SendAccountEmail;
use App\Listeners\Account\SendEnrollmentEmail;
use App\Listeners\Auth\IncrementFailedLogins;
use App\Listeners\Auth\IncrementSuccessfulLogins;
use App\Listeners\Auth\SendVerificationEmail;
use App\Listeners\CPA\TrackConversion;
use App\Listeners\Gamification\AwardBonuses;
use App\Listeners\Gamification\AwardCourseCompletionPoints;
use App\Listeners\Gamification\AwardCourseEnrollmentPoints;
use App\Listeners\Gamification\AwardEvaluationCompletionPoints;
use App\Listeners\Gamification\AwardFacebookPostPoints;
use App\Listeners\Gamification\AwardMemberRecruitmentPoints;
use App\Listeners\Gamification\AwardOrientationCompletionPoints;
use App\Listeners\Gamification\AwardProfileCompletionPoints;
use App\Listeners\Gamification\AwardTwitterPostPoints;
use App\Listeners\HandleInpageCss;
use App\Listeners\Sendlane\AddToSendlane;
use App\Listeners\SentMessage\Logger;
use App\Listeners\Tracking\TrackEnrollments;
use App\Listeners\Tracking\TrackInfusionsoftRegisteredUser;
use App\Listeners\Tracking\TrackLogouts;
use App\Listeners\Tracking\TrackRegisteredUser;
use App\Listeners\Tracking\TrackSuccessfullLogins;
use App\Listeners\User\SetReferralId;
use App\Models\SurveyUserAnswer;
use App\Models\UserSetting;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Listeners\Account\Imported\HandleImportedSimilarEmails;
use App\Listeners\Account\ToolEmailUpdater;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Support\Facades\DB;
use SocialiteProviders\Manager\SocialiteWasCalled;


class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'eloquent.saving: App\Models\Course' => [
            HandleInpageCss::class,
        ],

        'eloquent.saving: App\Models\CourseDescription' => [
            HandleInpageCss::class,
        ],

        'eloquent.saving: App\Models\User' => [
            SetReferralId::class,
        ],

        'eloquent.saving: App\Models\UserPointActivity' => [
            AwardBonuses::class,
        ],

        ImportedUserMerged::class => [
            AdjustCourseAccess::class,
            AdjustLessonSubscription::class,
            AdjustTestUsers::class,
            AdjustUserBadges::class
        ],

        UserMerged::class => [
            AdjustCourseAccess::class,
            AdjustTestUsers::class,
            AdjustUserBadges::class
        ],

        UserRecruited::class => [
            AwardMemberRecruitmentPoints::class,
        ],
        
        ImportedUserLoggedIn::class => [
            HandleImportedSimilarEmails::class
        ],

        UserEnrolled::class => [
            TrackEnrollments::class,
            AddToSendlane::class,
            SendEnrollmentEmail::class,
            AwardCourseEnrollmentPoints::class
        ],

        UserCreatedThroughInfusionsoft::class => [
            SendAccountEmail::class,
            TrackInfusionsoftRegisteredUser::class
        ],
        
        UserCreatedThroughAdmin::class => [
            SendAccountEmail::class,
            TrackRegisteredUser::class
        ],

        UserCreatedThroughExternalFunnel::class => [
             \App\Listeners\Gamification\AwardLurn10xFunnelPoints::class
        ],

        Failed::class => [
            IncrementFailedLogins::class
        ],

        Login::class => [
            IncrementSuccessfulLogins::class,
            TrackSuccessfullLogins::class
        ],
        Logout::class => [
            TrackLogouts::class
        ],

        UserEmailChanged::class => [
            ToolEmailUpdater::class
        ],

        Registered::class => [
            TrackRegisteredUser::class,
            TrackConversion::class,
            SendVerificationEmail::class
        ],

        CourseCompleted::class => [
            AwardCourseCompletionPoints::class
        ],

        EvaluationCompleted::class => [
            AwardEvaluationCompletionPoints::class
        ],

        OrientationCompleted::class => [
            AwardOrientationCompletionPoints::class
        ],

        ProfileCompleted::class => [
            AwardProfileCompletionPoints::class
        ],

        TwitterPostShared::class => [
            AwardTwitterPostPoints::class
        ],

        FacebookPostShared::class => [
            AwardFacebookPostPoints::class
        ],

        SocialiteWasCalled::class => [
            'SocialiteProviders\Twitter\TwitterExtendSocialite@handle',
            'SocialiteProviders\Instagram\InstagramExtendSocialite@handle',
        ],
        
        MessageSent::class => [
            Logger::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        UserSetting::saving(function ($settings) {
            if ($settings->isDirty('receive_updates')) {
                $settings->opt_out_at = $settings->receive_updates == 1 ? null : now();
            }
        });

        SurveyUserAnswer::creating(function ($answer) {
        	$answer->user_ip = request()->ip();
        });
    }
}
