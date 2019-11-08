<?php

namespace App\Http;

use App\Api\Http\Middleware\AuthorizeIP;
use App\Api\Http\Middleware\VerifiesSignature;
use App\Http\Middleware\CheckCourseAccess;
use App\Http\Middleware\CheckOnboardingStatus;
use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [
        \Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode::class,
        \App\Http\Middleware\SetAffiliateCookie::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        'api' => [
            'throttle:60,1',
            'bindings',
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth' => \Illuminate\Auth\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'bindings' => \Illuminate\Routing\Middleware\SubstituteBindings::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'admin' =>  \App\Http\Middleware\CheckIfIsAdmin::class,
        'super.admin' => \App\Http\Middleware\CheckIfIsSuperAdmin::class,
        'resolve.resources' => \App\Http\Middleware\ResolveResources::class,
        'enrollment.check' => \App\Http\Middleware\EnrollmentCheck::class,
        'api.authorize-ip' => AuthorizeIP::class,
        'api.verifies-signature' => VerifiesSignature::class,
        'onboarding' => CheckOnboardingStatus::class,
        'course.access' => CheckCourseAccess::class,
        'admin.role.auth' => \App\Http\Middleware\AdminRoleAuth::class,
    ];
}
