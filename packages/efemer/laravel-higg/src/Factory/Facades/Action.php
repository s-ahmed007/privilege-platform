<?php

namespace Efemer\Higg\Factory\Facades;

use Illuminate\Support\Facades\Facade;

class Action extends Facade {
    static function getFacadeAccessor(){
        return 'higg.action';
    }
}
