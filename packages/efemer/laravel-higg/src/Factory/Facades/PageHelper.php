<?php

namespace Efemer\Higg\Factory\Facades;

use Illuminate\Support\Facades\Facade;

class PageHelper extends Facade {
    static function getFacadeAccessor(){
        return 'higg.page';
    }
}

