<?php

namespace Efemer\Higg\Factory\Core;

use Illuminate\Routing\Controller;
use Route;

class BaseController extends Controller {

    //

    function routeParams($key = null, $else = null){
        $route = Route::current();
        $params = $route->parameters();
        return is_null($key) ? $params : array_get($params, $key, $else);
    }

}
