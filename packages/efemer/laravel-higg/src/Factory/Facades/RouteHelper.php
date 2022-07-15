<?php

namespace Efemer\Higg\Factory\Facades;

use Illuminate\Support\Facades\Facade;

class RouteHelper extends Facade {
    static function getFacadeAccessor(){
        return 'higg.router';
    }
}
