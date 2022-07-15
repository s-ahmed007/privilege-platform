<?php

namespace Efemer\Higg\Factory\Facades;

use Illuminate\Support\Facades\Facade;

class AuthHandler extends Facade {
    static function getFacadeAccessor(){
        return 'higg.auth';
    }
}
