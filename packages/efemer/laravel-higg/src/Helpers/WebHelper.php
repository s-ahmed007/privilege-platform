<?php

namespace Efemer\Higg\Helpers;

use Config;
use Higg;

class WebHelper {

    public $app = null;

    // get config key
    function config($key, $else = null){
        // TODO add database storage for config
        return Config::get($key, $else);
    }

    // set config key
    function setConfig($key, $value, $reset = false){
        if (strpos($key, 'web') === false) $key = 'web.'.$key;
        // TODO add database storage for config
        if (is_array($value) && !$reset) {
            $current = Config::get($key, []);
            $value = array_merge($current, $value);
        }
        Config::set($key, $value);
    }

    function mergeConfig($configFile){
        if (file_exists($configFile)) {
            $merge = require $configFile;
            $this->setConfig('web', $merge);
        }
    }

    /*
    // load config under web key (file must be in config/web folder)
    protected function loadWebConfig($configKey, $namespace = null){
        if (is_null($namespace)) $namespace = WEB_CONTEXT;
        $configPath = config_path(WEB_CONTEXT . '/'.$configKey.'.php');
        if (file_exists($configPath)) {
            $config = app('config')->get($namespace, []);
            $moreConfig = require $configPath;
            $mergeConfig = array_merge($config, $moreConfig);
            app('config')->set(WEB_CONTEXT, $mergeConfig);
        }
    }
    */

    function hello(){
        $context = config('web.name');
        return 'hello ' . $context;
    }


    /**
     *
     * ASSET HELPER METHODS
     *
     */

    /**
     * @param mixed $assets list of css asset paths
     * @return string
     */
    function styles($assets = null){
        if (is_null($assets)) $assets = config('web.styles');
        return asset_helper()->styles($assets);
    }

    function addStyles($assets, $reset = false){
        $this->setConfig('web.styles', $assets, $reset);
    }

    /**
     * @param mixed $assets list of js asset paths
     * @return string
     */
    function scripts($assets = null){
        if (is_null($assets)) $assets = config('web.scripts');
        return asset_helper()->scripts($assets);
    }

    function addScripts($assets, $reset = false){
        $this->setConfig('web.scripts', $assets, $reset);
    }

    /***** END ASSET HELPER *****/


    function homeUrl(){
        return Higg::contextHomeUrl();
    }





} // END CLASS
