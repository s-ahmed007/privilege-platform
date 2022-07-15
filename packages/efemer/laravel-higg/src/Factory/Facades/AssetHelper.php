<?php

namespace Efemer\Higg\Factory\Facades;

use Illuminate\Support\Facades\Facade;

class AssetHelper extends Facade {
    static function getFacadeAccessor(){
        return 'higg.asset';
    }
}
