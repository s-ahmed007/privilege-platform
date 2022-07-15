<?php

namespace Efemer\Higg\Factory\Facades;

use Illuminate\Support\Facades\Facade;

class StrHelper extends Facade {
    static function getFacadeAccessor(){
        return 'higg.str';
    }
}
