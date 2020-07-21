<?php

namespace App\Http;

use App\Http\Middleware\AuthorizedMemberDashboardRedirect;
use App\Http\Middleware\PublicMemberProfile;
use App\Http\Middleware\RelatedMemberRequired;
use App\Http\Middleware\RelatedProfile;
use App\Http\Middleware\UsersOnline;
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
        // \App\Http\Middleware\HttpsProtocol::class,
        \Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
        \App\Http\Middleware\TrustProxies::class,
        UsersOnline::class,
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
            // \Illuminate\Session\Middleware\AuthenticateSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            'related-profile'
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
        'not-for-production' => \App\Http\Middleware\NotForProduction::class,
        'auth' => \Illuminate\Auth\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'bindings' => \Illuminate\Routing\Middleware\SubstituteBindings::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'api-qa' => \App\Http\Middleware\ApiQA::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'authorized-member-dashboard' => AuthorizedMemberDashboardRedirect::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'admin' => \App\Http\Middleware\SentinelAdmin::class,
        'backend' => \App\Http\Middleware\SentinelBackend::class,
        'complete-profile' => \App\Http\Middleware\MissedProfileInformation::class,
	    'user' => \App\Http\Middleware\SentinelUser::class,
        'authorized-member' => \App\Http\Middleware\SentinelAuthorizedMember::class,
        'member' => \App\Http\Middleware\SentinelMember::class,
        'member-owner' => \App\Http\Middleware\SentinelMemberOwner::class,
        'subscribed' => \App\Http\Middleware\SentinelSubscribed::class,
        'un-subscribed' => \App\Http\Middleware\SentinelUnSubscribed::class,
	    'check-permission' => \App\Http\Middleware\CheckPermission::class,
        'can-contact-to' => \App\Http\Middleware\SentinelCanContactTo::class,
        'can-send-review' => \App\Http\Middleware\SentinelCanSendReview::class,
        'check-role' => \App\Http\Middleware\CheckRole::class,
        'deny-role' => \App\Http\Middleware\DenyRole::class,
        'public-member-profile' => PublicMemberProfile::class,
        'related-profile' => RelatedProfile::class,
        'related-required' => RelatedMemberRequired::class,
        'localize' => \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRoutes::class,
        'localizationRedirect' => \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter::class,
        'localeSessionRedirect' => \Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect::class,
        'localeViewPath' => \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationViewPath::class,
    ];
}
