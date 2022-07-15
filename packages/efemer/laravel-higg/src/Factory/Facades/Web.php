<?php

namespace Efemer\Higg\Factory\Facades;

use Illuminate\Support\Facades\Facade;

class Web extends Facade {
    static function getFacadeAccessor(){
        return 'higg.web';
    }
}
