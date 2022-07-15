<?php

namespace Efemer\Higg\Factory\Facades;

use Illuminate\Support\Facades\Facade;

class DomHelper extends Facade {
    static function getFacadeAccessor(){
        return 'higg.dom';
    }
}
