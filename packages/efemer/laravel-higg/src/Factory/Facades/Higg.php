<?php

namespace Efemer\Higg\Factory\Facades;

use Illuminate\Support\Facades\Facade;

class Higg extends Facade {
    static function getFacadeAccessor(){
        return 'higg';
    }
}
