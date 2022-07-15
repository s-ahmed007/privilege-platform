<?php

namespace Efemer\Higg\Helpers;

use Route;
use StrHelper as StringHelper;
use Illuminate\Routing\UrlGenerator;

class RouteHelper extends UrlGenerator {

    public function __construct(){
        $routes = app('router')->getRoutes();
        $request = app('request');
        parent::__construct($routes, $request);
    }

    protected function routeVerbs(){
        return [ 'get', 'post', 'put', 'patch', 'delete', 'options', 'any', 'match' ];
    }

    public function controller($prefix = null, $controller, $options = []){
        $methods = isset($options['methods']) ? $options['methods'] : [];
        $nativeMethods = get_native_class_methods($controller);
        $methods = array_merge($methods, $nativeMethods);

        if (!empty($methods)) {
            $verbs = $this->routeVerbs();
            $routes = [];
            foreach($methods as $method) {

                if (!StringHelper::startsWith($method, $verbs)) continue;
                $route = [];

                // route verb
                foreach($verbs as $verb) {
                    if (StringHelper::startsWith($method, $verb)) {
                        $route['verb'] = $verb;
                        break;
                    }
                }

                // route path
                $name = StringHelper::implodeCamelCase(str_replace($verbs, '', $method));
                if ($name == 'index') {
                    $path = $actionPath = $prefix;
                } else {
                    $actionPath = strtolower(empty($prefix) ? $name : ( $prefix == '/' ? '' : $prefix ) . '/' . $name);
                    $path = StringHelper::startsWith($actionPath, '/') ? $actionPath : '/' . $actionPath;
                }
                $route['path'] = $path;

                // route options
                $as = str_replace('/', '.', $actionPath);
                $route['as'] = strpos($as, '.') === 0 ? substr($as, 1) : $as;
                $route['uses'] = "$controller@{$method}";

                $routes[] = $route;

            } // end foreach

            // pr($routes);

            if (!empty($options)) {
                Route::group($options, function() use ($routes) {
                    foreach($routes as $route) {
                        $this->defineRoute($route);
                    }
                });
            } else {
                foreach($routes as $route) {
                    $this->defineRoute($route);
                }
            }

        } // end if

        return $this;
    }

    public function view($path, $viewLocation, $options = []){
        $options['context'] = 'view';
        return $this->handle($path, $viewLocation, $options);
    }

    // path getUserBooks
    // path GET:/user/books/{id}
    public function handle($path, $handle = null, $options = []){
        $verbs = $this->routeVerbs();
        $route = [];

        $route['verb'] = isset($options['verb']) ? $options['verb'] : 'get';
        foreach($verbs as $verb) {
            if (StringHelper::startsWith(strtolower($path), $verb)) {
                $route['verb'] = $verb;
                $path = str_replace($verb.':', '', strtolower($path));
            }
        }
        $route['path'] = $path;

        // route alias (named route)
        if (!isset($options['as'])) {
            if ($path == '/') $route['as'] = 'home';
            else {
                $parts = explode('/', $path); $as = [];
                foreach($parts as $part) {
                    if (empty($part)) continue;
                    if (strpos($part, '{') === FALSE) {
                        $as[] = $part;
                    }
                }
                $route['as'] = implode('.', $as);
            }
        } else {
            $route['as'] = $options['as'];
        }

        // route params
        if (isset($options['params'])) {
            if (!is_array($options['params'])) $options['params'] = [$options['params']];
            foreach ($options['params'] as $param) {
                $route['path'] .= "/{{$param}}";
            }
        }

        // handle context
        if (!isset($options['context'])) {
            if (is_string($handle)) {
                if (strpos($handle, '@')) {
                    $options['context'] = 'route';
                }
                else if (view()->exists($handle)) {
                    $options['context'] = 'view';
                } else {
                    abort(404, 'Confusing route handle for ' . $route['path']);
                }
            }
        }

        $route = array_merge($options, $route);

        $context = array_get($options, 'context', 'route');
        switch($context) {

            case 'view':
                $handle = function() use ($handle){
                    return view($handle);
                };
                $route[] = $handle;
                $this->defineRoute($route);
                break;

            default:
                if (is_callable($handle)) $route[] = $handle; else $route['uses'] = $handle;
                $this->defineRoute($route);
                
        } // end switch

        return $this;

    } // end handle


    public function defineRoute($route){
        $verb = array_get($route, 'verb', 'any');
        $handle = is_array($verb) ? 'match' : $verb;
        switch($handle) {
            case 'get': Route::get($route['path'], $route); break;
            case 'post': Route::post($route['path'], $route); break;
            case 'delete': Route::delete($route['path'], $route); break;
            case 'put': Route::put($route['path'], $route); break;
            case 'patch': Route::patch($route['path'], $route); break;
            case 'match': Route::match( $verb, $route['path'], $route); break;
            case 'any': Route::any( $route['path'], $route); break;
        }
    }

    function allRoutes($uriOnly = true){
        $routes = app('router')->getRoutes();
        $list = [];
        foreach($routes as $route){
            if ($uriOnly) $list[] = $route->getPath();
            else $list[$route->getPath()] = $route;
        }
        return $list;
    }


} // end Router