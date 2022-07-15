<?php

namespace Efemer\Royalty\Controllers;

use App\Http\Controllers\Controller;

class BaseController extends Controller {

    function routeParams($key = null){
        $route = \Route::current();
        $params = $route->parameters();
        return is_null($key) ? $params : array_get($params, $key);
    }

}
