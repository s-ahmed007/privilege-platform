<?php

namespace Efemer\Higg\Controllers;

use Illuminate\Routing\Controller;
use Route;

class BaseController extends Controller {

    //

    function routeParams($key = null){
        $route = Route::current();
        $params = $route->parameters();
        return is_null($key) ? $params : array_get($params, $key);
    }

}
