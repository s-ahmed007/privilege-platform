<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * The path to the "home" route for your application.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        //

        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();

        $this->mapApi2Routes();
        ///////////////////////////////////////////
        $this->mapWebRoutes();

        $this->mapWeb2Routes();

        $this->mapWeb3Routes();

        $this->mapWeb4Routes();

        $this->mapWeb5Routes();

        $this->mapWeb6Routes();
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
             ->namespace($this->namespace)
             ->group(base_path('routes/web.php'));
    }

    /**
     * Define the "unused" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWeb2Routes()
    {
        Route::middleware('web')
             ->namespace($this->namespace)
             ->group(base_path('routes/unused.php'));
    }

    /**
     * Define the "b2b2cAdminDashboard" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWeb3Routes()
    {
        Route::prefix('client')
             ->middleware('web')
             ->namespace($this->namespace)
             ->group(base_path('routes/b2b2cAdminDashboard.php'));
    }

    /**
     * Define the "partner" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWeb4Routes()
    {
        Route::middleware('web')
             ->namespace($this->namespace)
             ->group(base_path('routes/partner.php'));
    }

    /**
     * Define the "rbd admin" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWeb5Routes()
    {
        Route::middleware('web')
             ->namespace($this->namespace)
             ->group(base_path('routes/rbdAdminDashboard.php'));
    }

    /**
     * Define the "app" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWeb6Routes()
    {
        Route::middleware('web')
             ->namespace($this->namespace)
             ->group(base_path('routes/app.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::prefix('api')
             ->middleware('api')
             ->namespace($this->namespace)
             ->group(base_path('routes/api.php'));
    }

    /**
     * Define the "api.b2b2cAdminDashboard" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApi2Routes()
    {
        Route::prefix('api/client')
             ->middleware('api')
             ->namespace($this->namespace)
             ->group(base_path('routes/api.b2b2cAdminDashboard.php'));
    }
}
