<?php

namespace App\Http;

use App\Http\Middleware\PreOrderController;
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
        \App\Http\Middleware\TrustProxies::class,
        \Fruitcake\Cors\HandleCors::class,
        \App\Http\Middleware\CheckForMaintenanceMode::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
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
        ],

        'api' => [
            'throttle:60,1',
            // 'bindings', // before laravel 6.18
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
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
        // default on 8.18
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'bindings' => \Illuminate\Routing\Middleware\SubstituteBindings::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
        'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        // custom middlewares
        'customerLoginCheck' => \App\Http\Middleware\customerLoginCheck::class,
        'partnerLoginCheck' => \App\Http\Middleware\partnerLoginCheck::class,
        'userRegistrationCheck' => \App\Http\Middleware\user_registration::class,
        'rbdAdminLoginCheck' => \App\Http\Middleware\rbdAdminLoginCheck::class,
        'RbdSuperAdminLoginCheck' => \App\Http\Middleware\RbdSuperAdminLoginCheck::class,
        'partnerAdminLoginCheck' => \App\Http\Middleware\partnerAdminLoginCheck::class,
        'checkCouponNumber' => \App\Http\Middleware\checkCouponNumber::class,
        'userActiveCheck' => \App\Http\Middleware\userActiveCheck::class,
        'partnerActiveCheck' => \App\Http\Middleware\partnerActiveCheck::class,
        'HostCheck' => \App\Http\Middleware\HostCheck::class,
        'jwt.verify' => \App\Http\Middleware\JwtMiddleware::class,
        'PreOrderCheck' => \App\Http\Middleware\PreOrderCheck::class,
        'DenyBuyCardPage' => \App\Http\Middleware\DenyBuyCardPage::class,
        'B2b2cAdminLoginCheck' => \App\Http\Middleware\B2b2cAdminLoginCheck::class,
        'cors' => \App\Http\Middleware\Cors::class,
        'BranchUserLoginCheck' => \App\Http\Middleware\BranchUserLoginCheck::class,
        'BranchLoginCheck' => \App\Http\Middleware\BranchLoginCheck::class,
        'BranchOwnerLoginCheck' => \App\Http\Middleware\BranchOwnerLoginCheck::class,
        'setLocal' => \App\Http\Middleware\SetLocale::class,
        'WebsiteUnderConstruction' => \App\Http\Middleware\WebsiteUnderConstruction::class,
        'IsUserLoggedIn' => \App\Http\Middleware\IsUserLoggedIn::class,
    ];
}
