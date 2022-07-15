<?php

namespace Efemer\Higg\Factory\Providers;

use Illuminate\Support\ServiceProvider;
use Efemer\Higg\Factory\Traits\ServiceProviderTrait;

class HiggServiceProvider extends ServiceProvider {

    use ServiceProviderTrait;

    public $packageName = 'higg';
    public $packageNamespace = '\Efemer\Higg';
    public $packageDir = __DIR__ . '/../../..';

    public $views = false;
    public $configs = [
        'higg' => 'higg'
    ];
    public $required = [];

    public $actions = [];
    public $facades = [];
    public $bindings = [
        'higg' => '\Efemer\Higg\Factory\Core\Higg',
        'higg.str' => '\Efemer\Higg\Helpers\StrHelper',
        'higg.router' => '\Efemer\Higg\Helpers\RouteHelper',
        'higg.asset' => '\Efemer\Higg\Helpers\AssetHelper',
        'higg.web' => '\Efemer\Higg\Helpers\WebHelper',
        'higg.page' => '\Efemer\Higg\Helpers\PageHelper',
        'higg.dom' => '\Efemer\Higg\Helpers\DomHelper',
        'higg.calendar' => '\Efemer\Higg\Helpers\CalendarHelper',

        'higg.redis' => '\Efemer\Higg\Factory\Core\RedisStore',

        'higg.auth' => '\Efemer\Higg\Factory\Handlers\AuthHandler',
        'higg.action' => '\Efemer\Higg\Factory\Handlers\ActionHandler',
        'higg.browser' => '\Efemer\Higg\Factory\Handlers\BrowserHandler',
        'higg.form' => '\Efemer\Higg\Factory\Handlers\FormHandler',

        'higg.session' => '\Efemer\Higg\Factory\Models\DisplaySession',
        'higg.user' => '\Efemer\Higg\Factory\Models\User',
        'higg.identity' => '\Efemer\Higg\Factory\Models\UserIdentity',

    ];


    public function boot(){

        $this->loadConfigFiles();
        $this->registerActions();
        $this->registerViews();
        $this->loadRoutes();

        higg()->boot();

    }

} // end

